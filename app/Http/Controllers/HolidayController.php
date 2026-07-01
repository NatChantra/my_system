<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HolidayController extends Controller
{
    public function index()
    {
        return response()->json(
            DB::table('holiday_schedule')->orderBy('holiday_date')->get()
        );
    }

    public function store(Request $request)
    {
        $id = DB::table('holiday_schedule')->insertGetId([
            'holiday_name' => $request->holiday_name,
            'holiday_date' => $request->holiday_date,
            'description'  => $request->description,
            'created_at'   => now(),
        ]);
        return response()->json(DB::table('holiday_schedule')->where('holiday_id', $id)->first(), 201);
    }

    public function destroy($id)
    {
        DB::table('holiday_schedule')->where('holiday_id', $id)->delete();
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }
}