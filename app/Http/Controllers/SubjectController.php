<?php

namespace App\Http\Controllers;

use App\Exceptions\Error;
use App\Helpers\Code;
use App\Helpers\Message;
use App\Models\Subject;
use App\Traits\PaginationResponse;
use App\Traits\RequestFilter;
use App\Traits\ResponseFormatter;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class SubjectController extends Controller
{

    use ResponseFormatter, PaginationResponse, RequestFilter;

    public function index(Request $request)
    {
        try {
            $subject = Subject::query();
            $filters = $request->except(['limit', 'page']);
            $query = $this->filter($subject, $filters);

            $perPage = $request->input('limit', 10);
            $page = $request->input('page', 1);


            $totalData = $query->count();
            $totalPages = (int) ceil($totalData / $perPage);
            $subjects = $query->forPage($page, $perPage)->get();
            // if ($subjects->isEmpty()) {
            //     return $this->error(new Error(422, 'Data Not Found'), false);
            // }

            $response = [
                'data' => $subjects->toArray(),
                'per_page' => $perPage,
                'total_data' => $totalData,
                'total_pages' => $totalPages,
                'current_page' => $page
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
                'nama' => 'required|string|max:255',
                'type' => 'required|in:Non-Teknis,Teknis',
            ]);
            if ($validator->fails()) {
                throw new Error($validator['code'], $validator['message'], $validator['error']);
            }

            $subject = Subject::create([
                'nama' => $request->nama,
                'type' => $request->type
            ]);
            if (!$subject) {
                throw new Error(422, 'Failed To Add Data');
            }

            DB::commit();
            return $this->success(Code::POST_SUCCESS, $subject, Message::successCreate);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorCreate, $e->getMessage()), false);
        }
    }


    public function show($id)
    {
        try {
            $subject = Subject::findOrFail($id);
            if (!$subject) {
                // throw new Error($subject['code'], $subject['message'], $subject['error']);
                throw new Error(422, 'Not Found');
            }
            return $this->success(Code::SUCCESS, $subject, Message::successGet);
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
                'nama' => 'sometimes|required|string|max:255',
                'type' => 'sometimes|required|in:Non-Teknis,Teknis',
            ]);

            if ($validator->fails()) {
                return $this->error(new Error(Code::VALIDATION_ERROR, Message::errorUpdate, $validator->errors()->first()), false);
            }

            $subject = Subject::findOrFail($id);
            $subject->update($request->all());
            if (!$subject) {
                throw new Error($subject['code'], $subject['message'], $subject['error']);
            }

            DB::commit();
            return $this->success(Code::SUCCESS, $subject, Message::successUpdate);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorUpdate, $e));
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $subject = Subject::findOrFail($id);
            $subject->delete();

            if (!$subject) {
                throw new Error($subject['code'], $subject['message'], $subject['error']);
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
                'ids.*' => 'exists:subjects,id',
            ]);
            if ($validator->fails()) {
                return $this->error(new Error(Code::VALIDATION_ERROR, Message::errorDelete, $validator->errors()->first()), false);
            }

            $ids = $request->input('ids');
            Subject::destroy($ids);

            DB::commit();
            return $this->success(Code::SUCCESS, null, Message::successDelete);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorDelete, $e->getMessage()), false);
        }
    }
}
