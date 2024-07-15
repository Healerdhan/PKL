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
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    use ResponseFormatter, PaginationResponse, RequestFilter;

    public function index(Request $request)
    {
        try {
            $query = Siswa::query();
            $query = $this->filter($query, $request->all());

            $query->join('categories', 'siswas.category_id', '=', 'categories.id')
                ->select('siswas.*', 'categories.jurusan');

            if ($request->has('nama_siswa')) {
                $query->where('siswas.nama_siswa', 'like', '%' . $request->nama_siswa . '%');
            }

            if ($request->has('NISN')) {
                $query->where('siswas.NISN', '=', $request->NISN);
            }

            $perPage = $this->getLimit();
            $page = $this->getPage();

            if ($perPage) {
                $siswas = $query->paginate($perPage, ['*'], 'page', $page);
                throw new Error($siswas['code'], $siswas['message'], $siswas['error']);
            }

            $siswas = $query->get();
            if (!$siswas) {
                throw new Error(422, 'Data Not Found');
                // throw new Error($data['code'], $data['message'], $data['error']);
            }
            return $this->success(Code::SUCCESS, $siswas, Message::successGet);
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
                'jenis_kelamin' => 'required|string',
                'NISN' => 'required|integer|unique:siswas,NISN',
                'tempat_lahir' => 'required|string',
                'tanggal_lahir' => 'required|date',
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


    public function show($id)
    {
        try {
            $siswa = Siswa::with('category')->findOrFail($id);
            if (!$siswa) {
                throw new Error(422, 'Data Not Found');
                // throw new Error($siswa['code'], $siswa['message'], $siswa['error']);
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
                'jenis_kelamin' => 'required|string',
                'NISN' => 'required|integer|unique:siswas,NISN,' . $id,
                'tempat_lahir' => 'required|string',
                'tanggal_lahir' => 'required|date',
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
