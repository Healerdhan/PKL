<?php

namespace App\Http\Controllers;

use App\Exceptions\Error;
use App\Helpers\Code;
use App\Helpers\Message;
use App\Models\Pembimbing;
use App\Traits\PaginationResponse;
use App\Traits\RequestFilter;
use App\Traits\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PembimbingController extends Controller
{
    use ResponseFormatter, PaginationResponse, RequestFilter;

    public function index(Request $request)
    {
        try {
            $pembimbing = Pembimbing::with('dudis');

            if ($request->has('cari')) {
                $searchTerm = $request->input('cari');
                $pembimbing->where('nama_pegawai', 'like', "%{$searchTerm}%");
            }

            $perPage = $request->input('limit');
            $page = $request->input('page', 1);
            $totalData = $pembimbing->count();
            $totalPages = $perPage ? (int) ceil($totalData / $perPage) : 1;
            $pembimbing = $pembimbing->forPage($page, $perPage ? $perPage : $totalData)->get();

            $pembimbings = $pembimbing->forPage($page, $perPage)->get();

            $pembimbings = $pembimbings->map(function ($pembimbing) {
                return [
                    'id' => $pembimbing->id,
                    'nama_pegawai' => $pembimbing->nama_pegawai,
                    'dudis' => $pembimbing->dudis->map(function ($dudi) {
                        return [
                            'id' => $dudi->id,
                            'tempat' => $dudi->tempat,
                        ];
                    }),
                ];
            });

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Berhasil mendapatkan data',
                'error' => null,
                'data' => $pembimbings->toArray(),
                // 'per_page' => $perPage,
                'total_data' => $totalData,
                'total_pages' => $totalPages,
                'current_page' => $page,

            ]);
        } catch (Error | \Exception $e) {
            return $this->error(new Error(Code::SERVER_ERROR, Message::internalServerError, $e->getMessage()), false);
        }
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = Validator::make($request->all(), [
                'nama_pegawai' => 'required|string|max:255',
                'dudis_ids' => 'nullable|array',
                'dudis_ids.*' => 'uuid|exists:dudis,id',
            ]);
            if ($validated->fails()) {
                return response()->json(['errors' => $validated->errors()], 422);
            }

            $pembimbing = Pembimbing::create([
                'nama_pegawai' => $request->input('nama_pegawai')
            ]);

            if ($request->has('dudis_ids') && is_array($request->dudis_ids)) {
                $pembimbing->dudis()->sync($request->dudis_ids);
            }

            DB::commit();
            return $this->success(Code::POST_SUCCESS, $pembimbing, Message::successCreate);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorCreate, $e->getMessage()), false);
        }
    }


    public function show($id)
    {
        try {
            $bimbings = Pembimbing::with('dudis')->findOrFail($id);
            if (!$bimbings) {
                throw new Error(422, 'Data Not Found');
            }

            $dudiData = $bimbings->dudis->map(function ($dudi, $index) {
                return [
                    'dudi' . ($index + 1) => $dudi->tempat
                ];
            })->collapse()->all();

            $result = array_merge([
                'id' => $bimbings->id,
                'nama_pegawai' => $bimbings->nama_pegawai,
            ], $dudiData);


            return $this->success(Code::SUCCESS, $result, Message::successGet);
        } catch (Error | \Exception $e) {
            return $this->error(new Error(Code::NOT_FOUND, Message::notFound, $e->getMessage()), false);
        }
    }


    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validated = Validator::make($request->all(), [
                'nama_pegawai' => 'required|string|max:255',
                'dudis_ids' => 'nullable|array',
                'dudis_ids.*' => 'uuid|exists:dudis,id',
            ]);
            if ($validated->fails()) {
                return response()->json(['errors' => $validated->errors()], 422);
            }

            $bimbing = Pembimbing::findOrFail($id);
            if (!$bimbing) {
                throw new Error($bimbing['code'], $bimbing['message'], $bimbing['error']);
            }

            $bimbing->update($request->only([
                'nama_pegawai',
            ]));

            if ($request->has('dudis_ids')) {
                $bimbing->dudis()->sync($request->input('dudis_ids'));
            }

            DB::commit();
            return $this->success(Code::SUCCESS, $bimbing->load('dudis'), Message::successUpdate);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorUpdate, $e->getMessage()), false);
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $bing = Pembimbing::findOrFail($id);
            $bing->dudis()->detach();
            $bing->delete();
            if (!$bing) {
                throw new Error($bing['code'], $bing['message'], $bing['error']);
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
                'ids.*' => 'exists:pembimbings,id',
            ]);
            if ($validator->fails()) {
                return $this->error(new Error(Code::VALIDATION_ERROR, Message::errorDelete, $validator->errors()->first()), false);
            }

            Pembimbing::whereIn('id', $request->ids)->each(function ($pembimbing) {
                $pembimbing->dudis()->detach();
            });

            Pembimbing::whereIn('id', $request->ids)->delete();

            DB::commit();
            return $this->success(Code::SUCCESS, null, Message::successDelete);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorDelete, $e->getMessage()), false);
        }
    }
}
