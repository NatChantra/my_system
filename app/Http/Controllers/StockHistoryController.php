<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockHistoryController extends Controller
{
    // GET /api/stock-history?res_id=1
    public function index(Request $request)
    {
        $query = DB::table('stock_history')
            ->join('resources', 'stock_history.res_id', '=', 'resources.res_id')
            ->select('stock_history.*', 'resources.res_name');

        if ($request->res_id) {
            $query->where('stock_history.res_id', $request->res_id);
        }

        return response()->json(
            $query->orderByDesc('stock_history.created_at')->get()
        );
    }

    // POST /api/stock-history
    public function store(Request $request)
    {
        $id = DB::table('stock_history')->insertGetId([
            'res_id'     => $request->res_id,
            'type'       => $request->type,
            'qty_change' => $request->qty_change,
            'qty_before' => $request->qty_before,
            'qty_after'  => $request->qty_after,
            'note'       => $request->note,
            'emp_id'     => $request->emp_id,
            'created_at' => now('Asia/Phnom_Penh'),
            'updated_at' => now('Asia/Phnom_Penh'),
        ], 'history_id');

        return response()->json(
            DB::table('stock_history')->where('history_id', $id)->first(),
            201
        );
    }
}