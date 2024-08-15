<?php

namespace App\Http\Controllers;

use App\Exceptions\Error;
use App\Helpers\Code;
use App\Helpers\Message;
use App\Models\Sertifikat;
use App\Traits\PaginationResponse;
use App\Traits\RequestFilter;
use App\Traits\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SertifikatController extends Controller
{
    use ResponseFormatter, RequestFilter, PaginationResponse;

    public function index(Request $request)
    {
        try {
            $query = Sertifikat::with(['siswa:id,nama_siswa', 'dudi:id,tempat', 'nilai:id,nilai']);

            $sertifikat = $query->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_siswa' => $item->siswa ? $item->siswa->nama_siswa : null,
                    'tempat' => $item->dudi ? $item->dudi->tempat : null,
                    'kompetensi_keahlian' => $item->kompetensi_keahlian,
                    'alamat_tempat_pkl' => $item->alamat_tempat_pkl,
                    'tanggal_mulai' => $item->tanggal_mulai,
                    'tanggal_selesai' => $item->tanggal_selesai,
                    'nilai' => $item->nilai ? $item->nilai->nilai : null,
                    'predikat' => $item->predikat
                ];
            });


            $filteredData = $this->filter($sertifikat, $request->all());
            $totalData = $filteredData->count();
            $perPage = $totalData;
            $totalPages = 1;

            $response = [
                'data' => $filteredData,
                'meta' => [
                    'per_page' => $perPage,
                    'total_data' => $totalData,
                    'total_pages' => $totalPages,
                ]
            ];

            return $this->success(Code::SUCCESS, $response, Message::successGet);
        } catch (Error | \Exception $e) {
        }
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'siswa_id' => 'required|uuid|exists:siswas,id',
                'dudi_id' => 'required|uuid|exists:dudis,id',
                'kompetensi_keahlian' => 'required|string|max:255',
                'alamat_tempat_pkl' => 'required|string',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'nilai_id' => 'required|uuid|exists:nilais,id',
                'predikat' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                throw new Error($validator['code'], $validator['message'], $validator['error']);
            }

            $sertifikat = Sertifikat::create($request->all());
            if (!$sertifikat) {
                throw new Error($sertifikat['code'], $sertifikat['message'], $sertifikat['error']);
                // throw new Error(422, 'Failed To Add Data');
            }

            DB::commit();
            return $this->success(Code::POST_SUCCESS, $sertifikat, Message::successCreate);
        } catch (Error | \Exception $e) {
            DB::commit();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorCreate, $e->getMessage()), false);
        }
    }


    public function show($id)
    {
        try {
            $query = Sertifikat::with(['siswa:id,nama_siswa', 'dudi:id,tempat', 'nilai:id,nilai'])
                ->findOrFail($id);

            $result = [
                'id' => $query->id,
                'nama_siswa' => $query->siswa ? $query->siswa->nama_siswa : null,
                'tempat' => $query->dudi ? $query->dudi->tempat : null,
                'kompetensi_keahlian' => $query->kompetensi_keahlian,
                'alamat_tempat_pkl' => $query->alamat_tempat_pkl,
                'tanggal_mulai' => $query->tanggal_mulai,
                'tanggal_selesai' => $query->tanggal_selesai,
                'nilai' => $query->nilai ? $query->nilai->nilai : null,
                'predikat' => $query->predikat
            ];

            if (!$result) {
                throw new Error($result['code'], $result['message'], $result['error']);
            }
            return $this->success(Code::SUCCESS, $result, Message::successGet);
        } catch (Error | \Exception $e) {
            $errorMessage = $e->getMessage() ?: Message::notFound;
            return $this->error(new Error(Code::NOT_FOUND, Message::notFound, $errorMessage), false);
        }
    }


    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'siswa_id' => 'required|uuid|exists:siswas,id',
                'dudi_id' => 'required|uuid|exists:dudis,id',
                'kompetensi_keahlian' => 'required|string|max:255',
                'alamat_tempat_pkl' => 'required|string',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'nilai_id' => 'required|uuid|exists:nilais,id',
                'predikat' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                throw new Error($validator['code'], $validator['message'], $validator['error']);
            }

            $ser = Sertifikat::findOrFail($id);
            $ser->update($request->all());
            if (!$ser) {
                throw new Error($ser['code'], $ser['message'], $ser['error']);
            }

            DB::commit();
            return $this->success(Code::SUCCESS, $ser, Message::successUpdate);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorUpdate, $e));
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $ser = Sertifikat::findOrFail($id);
            $ser->delete();
            if (!$ser) {
                throw new Error($ser['code'], $ser['message'], $ser['error']);
            }

            DB::commit();
            return $this->success(Code::SUCCESS, null, Message::successDelete);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorDelete, $e));
        }
    }


    public function destroyMultiple(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'exists:sertifikats,id',
            ]);
            if ($validator->fails()) {
                return $this->error(new Error(Code::VALIDATION_ERROR, Message::errorDelete, $validator->errors()->first()), false);
            }

            $ids = $request->input('ids');
            Sertifikat::destroy($ids);

            DB::commit();
            return $this->success(Code::SUCCESS, null, Message::successDelete);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorDelete, $e));
        }
    }
}
