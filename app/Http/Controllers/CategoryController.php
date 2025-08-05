<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories,200);
    }

    public function store(Request $request)
    {

        $categories = Category::create($request->all());
        return response()->json($categories, 201);
    }

    public function update(Request $request, $id)
    {
        $categories = Category::find($id);
        $categories ->update($request->all());
        return response()->json($categories,200);
    }

    public function destroy($id)
    {
        Category::destroy($id);
        return response()->json(['message' => 'Category deleted'],204);
    }
}
