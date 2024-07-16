<?php

namespace App\Http\Controllers;

use App\Exceptions\Error;
use App\Helpers\Code;
use App\Helpers\Message;
use App\Models\dudi;
use App\Traits\PaginationResponse;
use App\Traits\RequestFilter;
use App\Traits\ResponseFormatter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DudiController extends Controller
{
    use ResponseFormatter, PaginationResponse, RequestFilter;

    public function index(Request $request)
    {
        try {
            $dudis = dudi::query();
            $dudis = $this->filter($dudis, $request->all());

            if ($request->has('tempat')) {
                $dudis->where('dudis.tempat', 'like', '%' . $request->tempat . '%');
            }

            $perPage = $this->getLimit();
            $page = $this->getPage();

            if ($perPage) {
                $dudis = $dudis->paginate($perPage, ['*'], 'page', $page);
            } else {
                $dudis = $dudis->get();
            }

            $dudis->load(['siswa1', 'siswa2', 'siswa3', 'siswa4', 'siswa5', 'siswa6', 'siswa7', 'siswa8', 'siswa9', 'siswa10', 'siswa11', 'siswa12', 'siswa13', 'siswa14']);


            if ($dudis->isEmpty()) {
                throw new Error(422, 'Data Not Found');
            }

            $dudis->transform(function ($dudi) {
                return [
                    'id' => $dudi->id,
                    'dudi' => $dudi->dudi,
                    'tempat' => $dudi->tempat,
                    'jumlah' => $dudi->jumlah,
                    'siswa1' => optional($dudi->siswa1)->nama_siswa,
                    'siswa2' => optional($dudi->siswa2)->nama_siswa,
                    'siswa3' => optional($dudi->siswa3)->nama_siswa,
                    'siswa4' => optional($dudi->siswa4)->nama_siswa,
                    'siswa5' => optional($dudi->siswa5)->nama_siswa,
                    'siswa6' => optional($dudi->siswa6)->nama_siswa,
                    'siswa7' => optional($dudi->siswa7)->nama_siswa,
                    'siswa8' => optional($dudi->siswa8)->nama_siswa,
                    'siswa9' => optional($dudi->siswa9)->nama_siswa,
                    'siswa10' => optional($dudi->siswa10)->nama_siswa,
                    'siswa11' => optional($dudi->siswa11)->nama_siswa,
                    'siswa12' => optional($dudi->siswa12)->nama_siswa,
                    'siswa13' => optional($dudi->siswa13)->nama_siswa,
                    'siswa14' => optional($dudi->siswa14)->nama_siswa,
                ];
            });

            return $this->success(Code::SUCCESS, $dudis, Message::successGet);
        } catch (Error | \Exception $e) {
            return $this->error(new Error(Code::SERVER_ERROR, Message::internalServerError, $e->getMessage()), false);
        }
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'tempat' => 'required|string|max:255',
                'jumlah' => 'required|integer|min:1|max:14',
                'siswa_id1' => 'nullable|uuid|exists:siswas,id',
                'siswa_id2' => 'nullable|uuid|exists:siswas,id',
                'siswa_id3' => 'nullable|uuid|exists:siswas,id',
                'siswa_id4' => 'nullable|uuid|exists:siswas,id',
                'siswa_id5' => 'nullable|uuid|exists:siswas,id',
                'siswa_id6' => 'nullable|uuid|exists:siswas,id',
                'siswa_id7' => 'nullable|uuid|exists:siswas,id',
                'siswa_id8' => 'nullable|uuid|exists:siswas,id',
                'siswa_id9' => 'nullable|uuid|exists:siswas,id',
                'siswa_id10' => 'nullable|uuid|exists:siswas,id',
                'siswa_id11' => 'nullable|uuid|exists:siswas,id',
                'siswa_id12' => 'nullable|uuid|exists:siswas,id',
                'siswa_id13' => 'nullable|uuid|exists:siswas,id',
                'siswa_id14' => 'nullable|uuid|exists:siswas,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $dudi = Dudi::create($request->all());
            if (!$dudi) {
                throw new Error(422, 'Data Not Found');
            }

            DB::commit();
            return $this->success(Code::POST_SUCCESS, $dudi, Message::successCreate);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorCreate, $e->getMessage()), false);
        }
    }


    public function show($id)
    {
        try {
            $dudis = dudi::with(
                'siswa1',
                'siswa2',
                'siswa3',
                'siswa4',
                'siswa5',
                'siswa6',
                'siswa7',
                'siswa8',
                'siswa9',
                'siswa10',
                'siswa11',
                'siswa12',
                'siswa13',
                'siswa14'
            )
                ->findOrfail($id);
            if (!$dudis) {
                throw new Error(422, 'Data Not Found');
                // throw new Error($siswa['code'], $siswa['message'], $siswa['error']);
            }

            $transformedDudi = [
                'id' => $dudis->id,
                'tempat' => $dudis->tempat,
                'jumlah' => $dudis->jumlah,
                'siswa1' => optional($dudis->siswa1)->nama_siswa,
                'siswa2' => optional($dudis->siswa2)->nama_siswa,
                'siswa3' => optional($dudis->siswa3)->nama_siswa,
                'siswa4' => optional($dudis->siswa4)->nama_siswa,
                'siswa5' => optional($dudis->siswa5)->nama_siswa,
                'siswa6' => optional($dudis->siswa6)->nama_siswa,
                'siswa7' => optional($dudis->siswa7)->nama_siswa,
                'siswa8' => optional($dudis->siswa8)->nama_siswa,
                'siswa9' => optional($dudis->siswa9)->nama_siswa,
                'siswa10' => optional($dudis->siswa10)->nama_siswa,
                'siswa11' => optional($dudis->siswa11)->nama_siswa,
                'siswa12' => optional($dudis->siswa12)->nama_siswa,
                'siswa13' => optional($dudis->siswa13)->nama_siswa,
                'siswa14' => optional($dudis->siswa14)->nama_siswa,
            ];

            return $this->success(Code::SUCCESS, $transformedDudi, Message::successGet);
        } catch (Error | \Exception $e) {
            return $this->error(new Error(Code::NOT_FOUND, Message::notFound, $e->getMessage()), false);
        }
    }


    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'dudi' => 'sometimes|required|string|max:255',
                'tempat' => 'sometimes|required|string|max:255',
                'jumlah' => 'sometimes|required|integer|min:1|max:14',
                'siswa_id1' => 'nullable|uuid|exists:siswas,id',
                'siswa_id2' => 'nullable|uuid|exists:siswas,id',
                'siswa_id3' => 'nullable|uuid|exists:siswas,id',
                'siswa_id4' => 'nullable|uuid|exists:siswas,id',
                'siswa_id5' => 'nullable|uuid|exists:siswas,id',
                'siswa_id6' => 'nullable|uuid|exists:siswas,id',
                'siswa_id7' => 'nullable|uuid|exists:siswas,id',
                'siswa_id8' => 'nullable|uuid|exists:siswas,id',
                'siswa_id9' => 'nullable|uuid|exists:siswas,id',
                'siswa_id10' => 'nullable|uuid|exists:siswas,id',
                'siswa_id11' => 'nullable|uuid|exists:siswas,id',
                'siswa_id12' => 'nullable|uuid|exists:siswas,id',
                'siswa_id13' => 'nullable|uuid|exists:siswas,id',
                'siswa_id14' => 'nullable|uuid|exists:siswas,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $dudi = Dudi::findOrFail($id);
            $dudi->update($request->all());
            if (!$dudi) {
                throw new Error($dudi['code'], $dudi['message'], $dudi['error']);
            }
            DB::commit();
            return $this->success(Code::SUCCESS, $dudi, Message::successUpdate);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorUpdate, $e->getMessage()), false);
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $dudi = Dudi::findOrFail($id);
            $dudi->delete();
            if (!$dudi) {
                throw new Error($dudi['code'], $dudi['message'], $dudi['error']);
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
                'ids.*' => 'exists:dudis,id',
            ]);

            if ($validator->fails()) {
                return $this->error(new Error(Code::VALIDATION_ERROR, Message::errorDelete, $validator->errors()->first()), false);
            }

            dudi::whereIn('id', $request->ids)->delete();
            DB::commit();
            return $this->success(Code::SUCCESS, null, Message::successDelete);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorDelete, $e->getMessage()), false);
        }
    }
}
