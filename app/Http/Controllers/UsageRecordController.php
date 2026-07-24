<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsageRecordController extends Controller
{
    public function index()
    {
        return response()->json(
            DB::table('usage_records')
                ->join('resource', 'usage_records.res_id', '=', 'resource.res_id')
                ->joi<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsageRecordController extends Controller
{
    public function index()
    {
        return response()->json(
            DB::table('usage_records')
                ->join('resources', 'usage_records.res_id', '=', 'resources.res_id')
                ->join('employee', 'usage_records.emp_id', '=', 'employee.emp_id')
                ->select('usage_records.*', 'resources.res_name', 'employee.emp_name')
                ->orderByDesc('usage_records.usage_id')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $resource = DB::table('resources')->where('res_id', $request->res_id)->first();
        if (!$resource || $resource->stock_qty < $request->qty_used) {
            return response()->json(['message' => 'ស្តុកមិនគ្រប់គ្រាន់'], 422);
        }
        DB::table('resources')->where('res_id', $request->res_id)
            ->decrement('stock_qty', $request->qty_used);

        $id = DB::table('usage_records')->insertGetId([
            'res_id'     => $request->res_id,
            'emp_id'     => $request->emp_id,
            'qty_used'   => $request->qty_used,
            'usage_date' => $request->usage_date,
        ], 'usage_id');

        return response()->json(DB::table('usage_records')->where('usage_id', $id)->first(), 201);
    }

    public function destroy($id)
    {
        $record = DB::table('usage_records')->where('usage_id', $id)->first();
        if ($record) {
            DB::table('resources')->where('res_id', $record->res_id)
                ->increment('stock_qty', $record->qty_used);
            DB::table('usage_records')->where('usage_id', $id)->delete();
        }
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }
}n('employee', 'usage_records.emp_id', '=', 'employee.emp_id')
                ->select('usage_records.*', 'resource.res_name', 'employee.emp_name')
                ->orderByDesc('usage_records.usage_id')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $resource = DB::table('resource')->where('res_id', $request->res_id)->first();
        if (!$resource || $resource->stock_qty < $request->qty_used) {
            return response()->json(['message' => 'ស្តុកមិនគ្រប់គ្រាន់'], 422);
        }
        DB::table('resource')->where('res_id', $request->res_id)
            ->decrement('stock_qty', $request->qty_used);

        $id = DB::table('usage_records')->insertGetId([
            'res_id'     => $request->res_id,
            'emp_id'     => $request->emp_id,
            'qty_used'   => $request->qty_used,
            'usage_date' => $request->usage_date,
        ]);
        return response()->json(DB::table('usage_records')->where('usage_id', $id)->first(), 201);
    }

    public function destroy($id)
    {
        $record = DB::table('usage_records')->where('usage_id', $id)->first();
        if ($record) {
            DB::table('resource')->where('res_id', $record->res_id)
                ->increment('stock_qty', $record->qty_used);
            DB::table('usage_records')->where('usage_id', $id)->delete();
        }
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }
}