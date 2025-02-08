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
            $dudis = Dudi::query()->with('siswas');

            if ($request->has('search')) {
                $searchTerm = $request->input('search');
                $dudis->where('tempat', 'like', "%{$searchTerm}%");
            }

            $perPage = $request->input('limit');
            $page = $request->input('page', 1);
            $totalData = $dudis->count();
            $totalPages = $perPage ? (int) ceil($totalData / $perPage) : 1;
            $dudis = $dudis->forPage($page, $perPage ? $perPage : $totalData)->get();

            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');

            if ($latitude && $longitude) {
                $latitude = (float) $latitude;
                $longitude = (float) $longitude;

                $dudis = $dudis->map(function ($dudi) use ($latitude, $longitude) {
                    $distance = $dudi->calculateDistance($latitude, $longitude);
                    return [
                        'id' => $dudi->id,
                        'tempat' => $dudi->tempat,
                        // 'jumlah' => $dudi->jumlah,
                        'latitude' => $dudi->latitude,
                        'longitude' => $dudi->longitude,
                        'distance' => $distance,
                        'siswa' => $dudi->siswas->map(function ($siswa) {
                            return [
                                'id' => $siswa->id,
                                'nama_siswa' => $siswa->nama_siswa,
                            ];
                        }),
                    ];
                });
            }

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Berhasil mendapatkan data',
                'error' => null,
                'data' => $dudis->toArray(),
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
            $validator = Validator::make($request->all(), [
                'tempat' => 'required|string|max:255',
                // 'jumlah' => 'required|integer|min:1|max:14',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'siswa_ids' => 'nullable|array',
                'siswa_ids.*' => 'uuid|exists:siswas,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $dudi = Dudi::create([
                'tempat' => $request->input('tempat'),
                // 'jumlah' => $request->input('jumlah'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
            ]);
            if ($request->has('siswa_ids') && is_array($request->siswa_ids)) {
                $dudi->siswas()->sync($request->siswa_ids);
            }

            DB::commit();
            return $this->success(Code::POST_SUCCESS, $dudi, Message::successCreate);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorCreate, $e->getMessage()), false);
        }
    }


    public function show($id, Request $request)
    {
        try {
            $dudi = Dudi::with('siswas')->find($id);

            if (!$dudi) {
                throw new Error(404, 'Dudi not found');
            }

            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');

            $distance = ($latitude && $longitude)
                ? $dudi->calculateDistance($latitude, $longitude)
                : null;

            $siswaData = $dudi->siswas->map(function ($siswa, $index) {
                return [
                    'siswa' . ($index + 1) => $siswa->nama_siswa
                ];
            })->collapse()->all();

            $result = array_merge([
                'id' => $dudi->id,
                'tempat' => $dudi->tempat,
                // 'jumlah' => $dudi->jumlah,
                'latitude' => $dudi->latitude,
                'longitude' => $dudi->longitude,
                'distance' => $distance,
            ], $siswaData);

            return $this->success(Code::SUCCESS, $result, Message::successGet);
        } catch (Error | \Exception $e) {
            return $this->error(new Error(Code::NOT_FOUND, Message::notFound, $e->getMessage()), false);
        }
    }


    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'tempat' => 'sometimes|required|string|max:255',
                // 'jumlah' => 'sometimes|required|integer|min:1|max:14',
                'latitude' => 'sometimes|numeric',
                'longitude' => 'sometimes|numeric',
                'siswa_ids' => 'nullable|array',
                'siswa_ids.*' => 'uuid|exists:siswas,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $dudi = Dudi::findOrFail($id);
            if (!$dudi) {
                throw new Error($dudi['code'], $dudi['message'], $dudi['error']);
            }

            $dudi->update($request->only([
                'tempat',
                // 'jumlah',
                'latitude',
                'longitude'
            ]));

            if ($request->has('siswa_ids')) {
                $dudi->siswas()->sync($request->input('siswa_ids'));
            }

            DB::commit();
            return $this->success(Code::SUCCESS, $dudi->load('siswas'), Message::successUpdate);
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
            $dudi->siswas()->detach();

            $dudi->delete();
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

            Dudi::whereIn('id', $request->ids)->each(function ($dudi) {
                $dudi->siswas()->detach();
            });

            dudi::whereIn('id', $request->ids)->delete();
            DB::commit();
            return $this->success(Code::SUCCESS, null, Message::successDelete);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorDelete, $e->getMessage()), false);
        }
    }
}
