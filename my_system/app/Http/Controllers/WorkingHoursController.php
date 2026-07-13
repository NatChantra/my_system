<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkingHoursController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('working_hours')
            ->leftJoin('departments', 'working_hours.dept_id', '=', 'departments.dept_id')
            ->select('working_hours.*', 'departments.dept_name');
        if ($request->dept_id) $query->where('working_hours.dept_id', $request->dept_id);
        return response()->json($query->orderBy('departments.dept_name')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'dept_id' => 'required|exists:departments,dept_id',
            'start_time' => 'required', 'end_time' => 'required',
        ]);
        $id = DB::table('working_hours')->insertGetId([
            'dept_id' => $request->dept_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'days' => $request->days ?? 'Mon-Fri',
            'note' => $request->note ?? null,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $record = DB::table('working_hours')
            ->leftJoin('departments', 'working_hours.dept_id', '=', 'departments.dept_id')
            ->select('working_hours.*', 'departments.dept_name')
            ->where('working_hours.wh_id', $id)->first();
        return response()->json($record, 201);
    }

    public function update(Request $request, $id)
    {
        DB::table('working_hours')->where('wh_id', $id)->update([
            'dept_id' => $request->dept_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'days' => $request->days ?? 'Mon-Fri',
            'note' => $request->note ?? null,
            'updated_at' => now(),
        ]);
        $record = DB::table('working_hours')
            ->leftJoin('departments', 'working_hours.dept_id', '=', 'departments.dept_id')
            ->select('working_hours.*', 'departments.dept_name')
            ->where('working_hours.wh_id', $id)->first();
        return response()->json($record);
    }

    public function destroy($id)
    {
        DB::table('working_hours')->where('wh_id', $id)->delete();
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }
}