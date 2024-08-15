<?php

namespace App\Http\Controllers;

use App\Exceptions\Error;
use App\Helpers\Code;
use App\Helpers\Message;
use App\Models\Siswa;
use App\Traits\PaginationResponse;
use App\Traits\RequestFilter;
use App\Traits\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
  use ResponseFormatter, PaginationResponse, RequestFilter;

  public function index(Request $request)
  {
    try {
      $siswas = Siswa::query();
      $siswas->with('category');

      $siswas = $siswas->get();
      $totalData = $siswas->count();
      $perPage = 10;
      $totalPages = (int) ceil($totalData / $perPage);

      if ($siswas->isEmpty()) {
        throw new Error(422, 'Data Not Found');
      }

      $latitude = $request->input('latitude');
      $longitude = $request->input('longitude');

      if ($latitude && $longitude) {
        $siswas = $siswas->map(function ($siswa) use ($latitude, $longitude) {
          $distance = $siswa->calculateDistance($latitude, $longitude);
          return [
            'id' => $siswa->id,
            'nama_siswa' => $siswa->nama_siswa,
            'jenis_kelamin' => $siswa->jenis_kelamin,
            'NISN' => $siswa->NISN,
            'alamat' => $siswa->alamat,
            'tempat_lahir' => $siswa->tempat_lahir,
            'tanggal_lahir' => $siswa->tanggal_lahir,
            'latitude' => $siswa->latitude,
            'longitude' => $siswa->longitude,
            'category' => $siswa->category,
            'distance' => $distance
          ];
        });
      }

      $response = [
        'data' => $siswas,
        'meta' => [
          'per_page' => $perPage,
          'total_data' => $totalData,
          'total_pages' => $totalPages,
        ]
      ];

      return $this->success(Code::SUCCESS, $response, Message::successGet);
    } catch (Error | \Exception $e) {
      return $this->error(new Error(Code::SERVER_ERROR, Message::internalServerError, $e->getMessage()), false);
    }
  }


  public function store(Request $request)
  {
    DB::beginTransaction();
    try {
      $validator = Validator::make($request->all(), [
        'nama_siswa' => 'required|string',
        'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
        'NISN' => 'required|integer|unique:siswas,NISN',
        'alamat' => 'required|string',
        'tempat_lahir' => 'required|string',
        'tanggal_lahir' => 'required|date',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'category_id' => 'required|exists:categories,id',
      ]);

      if ($validator->fails()) {
        throw new Error($validator['code'], $validator['message'], $validator['error']);
        // return $this->error(new Error(Code::VALIDATION_ERROR, Message::errorCreate, $validator->errors()->first()), false);
      }

      $siswa = Siswa::create([
        'nama_siswa' => $request->nama_siswa,
        'jenis_kelamin' => $request->jenis_kelamin,
        'NISN' => $request->NISN,
        'tempat_lahir' => $request->tempat_lahir,
        'tanggal_lahir' => $request->tanggal_lahir,
        'alamat' => $request->alamat,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'category_id' => $request->category_id,
      ]);
      if (!$siswa) {
        throw new Error(422, 'Data Not Found');
      }

      DB::commit();
      return $this->success(Code::POST_SUCCESS, $siswa, Message::successCreate);
    } catch (Error | \Exception $e) {
      DB::rollBack();
      return $this->error(new Error(Code::SERVER_ERROR, Message::errorCreate, $e->getMessage()), false);
    }
  }


  public function show(Request $request, $id)
  {
    try {
      $siswa = Siswa::with('category')->findOrFail($id);
      if (!$siswa) {
        throw new Error(422, 'Data Not Found');
        // throw new Error($siswa['code'], $siswa['message'], $siswa['error']);
      }

      if ($request->has('latitude') && $request->has('longitude')) {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $siswa->distance = $siswa->calculateDistance($latitude, $longitude);
      }

      return $this->success(Code::SUCCESS, $siswa, Message::successGet);
    } catch (Error | \Exception $e) {
      return $this->error(new Error(Code::NOT_FOUND, Message::notFound, $e->getMessage()), false);
    }
  }


  public function update(Request $request, $id)
  {
    DB::beginTransaction();
    try {
      $validator = Validator::make($request->all(), [
        'nama_siswa' => 'required|string',
        'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
        'NISN' => 'required|integer|unique:siswas,NISN,' . $id,
        'tempat_lahir' => 'required|string',
        'tanggal_lahir' => 'required|date',
        'alamat' => 'required|string',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'category_id' => 'required|exists:categories,id',
      ]);

      if ($validator->fails()) {
        return $this->error(new Error(Code::VALIDATION_ERROR, Message::errorUpdate, $validator->errors()->first()), false);
      }

      $siswa = Siswa::findOrFail($id);
      $siswa->update([
        'nama_siswa' => $request->nama_siswa,
        'jenis_kelamin' => $request->jenis_kelamin,
        'NISN' => $request->NISN,
        'tempat_lahir' => $request->tempat_lahir,
        'tanggal_lahir' => $request->tanggal_lahir,
        'alamat' => $request->alamat,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'category_id' => $request->category_id,
      ]);
      if (!$siswa) {
        throw new Error($siswa['code'], $siswa['message'], $siswa['error']);
      }

      DB::commit();
      return $this->success(Code::SUCCESS, $siswa, Message::successUpdate);
    } catch (Error | \Exception $e) {
      DB::rollBack();
      return $this->error(new Error(Code::SERVER_ERROR, Message::errorUpdate, $e->getMessage()), false);
    }
  }


  public function destroy($id)
  {
    DB::beginTransaction();
    try {
      $siswa = Siswa::findOrFail($id);
      $siswa->delete();
      if (!$siswa) {
        throw new Error($siswa['code'], $siswa['message'], $siswa['error']);
      }

      DB::commit();
      return $this->success(Code::SUCCESS, null, Message::successDelete);
    } catch (Error | \Exception $e) {
      DB::rollBack();
      return $this->error(new Error(Code::SERVER_ERROR, Message::errorDelete, $e->getMessage()), false);
    }
  }


  public function destroyMultiple(Request $request)
  {
    DB::beginTransaction();
    try {
      $validator = Validator::make($request->all(), [
        'ids' => 'required|array',
        'ids.*' => 'exists:siswas,id',
      ]);

      if ($validator->fails()) {
        return $this->error(new Error(Code::VALIDATION_ERROR, Message::errorDelete, $validator->errors()->first()), false);
      }

      Siswa::whereIn('id', $request->ids)->delete();


      DB::commit();
      return $this->success(Code::SUCCESS, null, Message::successDelete);
    } catch (Error | \Exception $e) {
      DB::rollBack();
      return $this->error(new Error(Code::SERVER_ERROR, Message::errorDelete, $e->getMessage()), false);
    }
  }
}
