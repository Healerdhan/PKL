<?php

namespace App\Http\Controllers\FrontEnd;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('category.index', compact('categories'));
    }

    // public function show($id)
    // {
    //     $category = Category::findOrFail($id);
    //     return view('category.show', compact('category'));
    // }
}
