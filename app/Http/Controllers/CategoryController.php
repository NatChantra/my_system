<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(DB::table('categories')->orderBy('cat_name')->get());
    }

    public function store(Request $request)
    {
        $request->validate(['cat_name' => 'required|string|max:255']);

         $id = DB::table('categories')->insertGetId([
        'cat_name'   => $request->cat_name,
        'created_at' => now(),
        'updated_at' => now(),
    ], 'cat_id');

    return response()->json(DB::table('categories')->where('cat_id', $id)->first(), 201);
    }

    public function destroy($id)
    {
        DB::table('categories')->where('cat_id', $id)->delete();
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }
}