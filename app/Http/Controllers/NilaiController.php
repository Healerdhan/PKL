<?php

namespace App\Http\Controllers;

use App\Exceptions\Error;
use App\Helpers\Code;
use App\Helpers\Message;
use App\Models\Nilai;
use App\Traits\PaginationResponse;
use App\Traits\RequestFilter;
use App\Traits\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NilaiController extends Controller
{

    use ResponseFormatter, PaginationResponse, RequestFilter;

    public function index(Request $request)
    {
        try {
            $query = Nilai::with(['siswa' => function ($query) {
                $query->select('id', 'nama_siswa');
            }, 'subject' => function ($query) {
                $query->select('id', 'nama', 'type');
            }]);

            $nilai = $query->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nilai' => $item->nilai,
                    'siswa' => $item->siswa->nama_siswa,
                    'subject' => [
                        'nama' => $item->subject->nama,
                        'type' => $item->subject->type
                    ]
                ];
            });

            $nilai = $this->filter($nilai, $request->all());
            $totalData = $nilai->count();
            $perPage = $totalData;
            $totalPages = 1;

            $response = [
                'data' => $nilai,
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
                'siswa_id' => 'required|uuid|exists:siswas,id',
                'subject_id' => 'required|uuid|exists:subjects,id',
                'nilai' => 'required|integer|min:0|max:100',
            ]);

            if ($validator->fails()) {
                throw new Error($validator['code'], $validator['message'], $validator['error']);
            }

            $nilai = Nilai::create($request->all());
            if (!$nilai) {
                throw new Error(422, 'Failed To Add Data');
            }

            DB::commit();
            return $this->success(Code::POST_SUCCESS, $nilai, Message::successCreate);
        } catch (Error | \Exception $e) {
            DB::commit();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorCreate, $e->getMessage()), false);
        }
    }


    public function show($id)
    {
        try {
            $nilai = Nilai::with(['siswa' => function ($query) {
                $query->select('id', 'nama_siswa');
            }, 'subject' => function ($query) {
                $query->select('id', 'nama', 'type');
            }])->findOrFail($id);

            $result = [
                'id' => $nilai->id,
                'nilai' => $nilai->nilai,
                'siswa' => $nilai->siswa->nama_siswa,
                'subject' => [
                    'nama' => $nilai->subject->nama,
                    'type' => $nilai->subject->type
                ]
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
                'siswa_id' => 'sometimes|required|uuid|exists:siswas,id',
                'subject_id' => 'sometimes|required|uuid|exists:subjects,id',
                'nilai' => 'sometimes|required|integer|min:0|max:100',
            ]);

            if ($validator->fails()) {
                return $this->error(new Error(Code::VALIDATION_ERROR, Message::errorUpdate, $validator->errors()->first()), false);
            }

            $score = Nilai::findOrFail($id);
            $score->update($request->all());
            if (!$score) {
                throw new Error($score['code'], $score['message'], $score['error']);
            }

            DB::commit();
            return $this->success(Code::SUCCESS, $score, Message::successUpdate);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorUpdate, $e));
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $score = Nilai::findOrFail($id);
            $score->delete();
            if (!$score) {
                throw new Error($score['code'], $score['message'], $score['error']);
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
                'ids.*' => 'exists:nilais,id',
            ]);
            if ($validator->fails()) {
                return $this->error(new Error(Code::VALIDATION_ERROR, Message::errorDelete, $validator->errors()->first()), false);
            }

            $ids = $request->input('ids');
            Nilai::destroy($ids);

            DB::commit();
            return $this->success(Code::SUCCESS, null, Message::successDelete);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorDelete, $e));
        }
    }
}
