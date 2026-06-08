<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    // GET /api/employees
    public function index()
    {
        $employees = DB::table('employee')->orderBy('emp_id')->get();
        return response()->json($employees);
    }

    // POST /api/employees
    public function store(Request $request)
    {
        $id = DB::table('employee')->insertGetId([
            'emp_name' => $request->emp_name,
            'position' => $request->position ?? null,
            'phone'    => $request->phone    ?? null,
        ]);
        return response()->json(DB::table('employee')->where('emp_id', $id)->first(), 201);
    }

    // PUT /api/employees/{id}
    public function update(Request $request, $id)
    {
        DB::table('employee')->where('emp_id', $id)->update([
            'emp_name' => $request->emp_name,
            'position' => $request->position,
            'phone'    => $request->phone,
        ]);
        return response()->json(DB::table('employee')->where('emp_id', $id)->first());
    }

    // DELETE /api/employees/{id}
    public function destroy($id)
    {
        DB::table('employee')->where('emp_id', $id)->delete();
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }
}