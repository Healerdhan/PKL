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
            $pembimbing = Pembimbing::with(['dudi1', 'dudi2', 'dudi3', 'dudi4', 'dudi5']);

            $filters = $request->except(['limit', 'page']);
            $pembimbingQuery = $this->filter($pembimbing, $filters);

            if ($request->has('nama_pegawai')) {
                $pembimbingQuery->where('nama_pegawai', 'like', '%' . $request->nama_pegawai . '%');
            }

            $totalData = $pembimbing->count();
            $perPage = $request->input('limit', 10);
            $page = $request->input('page', 1);
            $totalPages = (int) ceil($totalData / $perPage);

            // if ($totalData === 0 || $page > $totalPages) {
            //     return $this->success(Code::SUCCESS, [
            //         'data' => [],
            //         'per_page' => $perPage,
            //         'total_data' => $totalData,
            //         'total_pages' => $totalPages,
            //         'current_page' => $page,
            //     ], Message::successGet);
            // }

            $pembimbing = $pembimbingQuery->skip(($page - 1) * $perPage)->take($perPage)->get();

            $pembimbing->transform(function ($bimbing) {
                return [
                    'id' => $bimbing->id,
                    'nama_pegawai' => $bimbing->nama_pegawai,
                    'dudi1' => optional($bimbing->dudi1)->tempat,
                    'dudi2' => optional($bimbing->dudi2)->tempat,
                    'dudi3' => optional($bimbing->dudi3)->tempat,
                    'dudi4' => optional($bimbing->dudi4)->tempat,
                    'dudi5' => optional($bimbing->dudi5)->tempat,
                ];
            });

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Berhasil mendapatkan data',
                'error' => null,
                'data' => $pembimbing->toArray(),
                'per_page' => $perPage,
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
                'dudi_id1' => 'nullable|uuid|exists:dudis,id',
                'dudi_id2' => 'nullable|uuid|exists:dudis,id',
                'dudi_id3' => 'nullable|uuid|exists:dudis,id',
                'dudi_id4' => 'nullable|uuid|exists:dudis,id',
                'dudi_id5' => 'nullable|uuid|exists:dudis,id',
            ]);
            if ($validated->fails()) {
                return response()->json(['errors' => $validated->errors()], 422);
            }

            $pembimbing = Pembimbing::create($request->all());
            if (!$pembimbing) {
                throw new Error(422, 'Data Not Found');
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
            $bimbings = Pembimbing::with(
                'dudi1',
                'dudi2',
                'dudi3',
                'dudi4',
                'dudi5',
            )->findOrFail($id);
            if (!$bimbings) {
                throw new Error(422, 'Data Not Found');
                // throw new Error($siswa['code'], $siswa['message'], $siswa['error']);
            }

            $transform = [
                'id' => $bimbings->id,
                'nama_pegawai' => $bimbings->nama_pegawai,
                'dudi1' => optional($bimbings->dudi1)->tempat,
                'dudi2' => optional($bimbings->dudi2)->tempat,
                'dudi3' => optional($bimbings->dudi3)->tempat,
                'dudi4' => optional($bimbings->dudi4)->tempat,
                'dudi5' => optional($bimbings->dudi5)->tempat,
            ];
            return $this->success(Code::SUCCESS, $transform, Message::successGet);
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
                'dudi_id1' => 'nullable|uuid|exists:dudis,id',
                'dudi_id2' => 'nullable|uuid|exists:dudis,id',
                'dudi_id3' => 'nullable|uuid|exists:dudis,id',
                'dudi_id4' => 'nullable|uuid|exists:dudis,id',
                'dudi_id5' => 'nullable|uuid|exists:dudis,id',
            ]);
            if ($validated->fails()) {
                return response()->json(['errors' => $validated->errors()], 422);
            }

            $bimbing = Pembimbing::findOrFail($id);
            $bimbing->update($request->all());
            if (!$bimbing) {
                throw new Error($bimbing['code'], $bimbing['message'], $bimbing['error']);
            }

            DB::commit();
            return $this->success(Code::SUCCESS, $bimbing, Message::successUpdate);
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

            Pembimbing::whereIn('id', $request->ids)->delete();

            DB::commit();
            return $this->success(Code::SUCCESS, null, Message::successDelete);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorDelete, $e->getMessage()), false);
        }
    }
}
