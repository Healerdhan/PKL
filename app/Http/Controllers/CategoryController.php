<?php

namespace App\Http\Controllers;

use App\Exceptions\Error;
use App\Helpers\Code;
use App\Helpers\Message;
use App\Models\Category;
use App\Traits\PaginationResponse;
use App\Traits\RequestFilter;
use App\Traits\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use ResponseFormatter, RequestFilter, PaginationResponse;

    public function index(Request $request)
    {
        try {
            $query = Category::query();
            $filters = $request->except(['limit', 'page']);
            $query = $this->filter($query, $filters);


            $categories = $query->get();
            $totalData = $categories->count();
            $perPage = 10;

            $totalPages = (int) ceil($totalData / $perPage);

            $page = $request->input('page', 1);
            $paginatedData = $categories->slice(($page - 1) * $perPage, $perPage)->values();

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Berhasil mendapatkan data',
                'error' => null,
                'data' => $paginatedData->toArray(),
                'per_page' => $perPage,
                'total_data' => $totalData,
                'total_pages' => $totalPages,
                'current_page' => $page,
            ]);


            // return $this->success(Code::SUCCESS, $response, Message::successGet);
        } catch (Error | \Exception $e) {
            return $this->error(new Error(Code::SERVER_ERROR, Message::internalServerError, $e->getMessage()), false);
        }
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'jurusan' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                throw new Error($validator['code'], $validator['message'], $validator['error']);
                // return $this->error(new Error(Code::VALIDATION_ERROR, Message::errorCreate, $validator->errors()->first()), false);
            }

            $category = Category::create([
                'jurusan' => $request->jurusan,
            ]);
            if (!$category) {
                throw new Error(422, 'Failed To Add Data');
            }

            DB::commit();
            return $this->success(Code::POST_SUCCESS, $category, Message::successCreate);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorCreate, $e->getMessage()), false);
        }
    }


    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            if (!$category) {
                throw new Error($category['code'], $category['message'], $category['error']);
            }
            return $this->success(Code::SUCCESS, $category, Message::successGet);
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
                'jurusan' => 'required|string|unique:categories,jurusan,' . $id,
            ]);

            if ($validator->fails()) {
                return $this->error(new Error(Code::VALIDATION_ERROR, Message::errorUpdate, $validator->errors()->first()), false);
            }

            $category = Category::findOrFail($id);
            $category->update([
                'jurusan' => $request->jurusan,
            ]);
            if (!$category) {
                throw new Error($category['code'], $category['message'], $category['error']);
            }

            DB::commit();
            return $this->success(Code::SUCCESS, $category, Message::successUpdate);
        } catch (Error | \Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorUpdate, $e));
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            if (!$category) {
                throw new Error($category['code'], $category['message'], $category['error']);
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
            // Validasi data
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'exists:categories,id',
            ]);

            // Jika validasi gagal, kembalikan error
            if ($validator->fails()) {
                return $this->error(new Error(Code::VALIDATION_ERROR, Message::errorDelete, $validator->errors()->first()), false);
            }

            // Mendapatkan array id dari request
            $ids = $request->input('ids');

            // Menghapus kategori berdasarkan id
            Category::destroy($ids);

            DB::commit();
            return $this->success(Code::SUCCESS, null, Message::successDelete);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error(new Error(Code::SERVER_ERROR, Message::errorDelete, $e->getMessage()), false);
        }
    }
}
