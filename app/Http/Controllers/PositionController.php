<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('positions')
            ->leftJoin('departments', 'positions.dept_id', '=', 'departments.dept_id')
            ->select('positions.*', 'departments.dept_name');
        if ($request->dept_id) $query->where('positions.dept_id', $request->dept_id);
        return response()->json($query->orderBy('pos_name')->get());
    }

    public function store(Request $request)
    {
        $request->validate(['pos_name' => 'required|string|max:255']);
        DB::table('positions')->insert([
            'pos_name'   => $request->pos_name,
            'dept_id'    => $request->dept_id ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $record = DB::table('positions')
            ->leftJoin('departments', 'positions.dept_id', '=', 'departments.dept_id')
            ->select('positions.*', 'departments.dept_name')
            ->orderByDesc('positions.pos_id')
            ->first();
        return response()->json($record, 201);
    }

    public function update(Request $request, $id)
    {
        DB::table('positions')->where('pos_id', $id)->update([
            'pos_name' => $request->pos_name,
            'dept_id' => $request->dept_id ?? null,
            'updated_at' => now(),
        ]);
        $record = DB::table('positions')
            ->leftJoin('departments', 'positions.dept_id', '=', 'departments.dept_id')
            ->select('positions.*', 'departments.dept_name')
            ->where('positions.pos_id', $id)->first();
        return response()->json($record);
    }

    public function destroy($id)
    {
        DB::table('positions')->where('pos_id', $id)->delete();
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }
}