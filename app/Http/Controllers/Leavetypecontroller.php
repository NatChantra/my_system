<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveTypeController extends Controller
{
    public function index()
    {
        return response()->json(
            DB::table('leave_types')->orderBy('leave_type_id')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate(['type_name' => 'required|string|max:255']);

        $id = DB::table('leave_types')->insertGetId([
            'type_name'  => $request->type_name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(DB::table('leave_types')->find($id), 201);
    }

    public function update(Request $request, $id)
    {
        DB::table('leave_types')->where('leave_type_id', $id)->update([
            'type_name'  => $request->type_name,
            'updated_at' => now(),
        ]);

        return response()->json(DB::table('leave_types')->find($id));
    }

    public function destroy($id)
    {
        DB::table('leave_types')->where('leave_type_id', $id)->delete();
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }
}