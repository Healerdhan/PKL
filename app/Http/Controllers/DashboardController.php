<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $data = null;

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Berhasil mendapatkan data',
                'error' => null,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Terjadi kesalahan saat mendapatkan data',
                'error' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
