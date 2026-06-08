<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransactionController extends Controller
{
    // ប្រវត្តិ stock in/out
    public function index(Request $request)
    {
        $query = DB::table('stock_transactions')
            ->join('resources', 'stock_transactions.res_id', '=', 'resources.res_id')
            ->leftJoin('suppliers', 'stock_transactions.sup_id', '=', 'suppliers.sup_id')
            ->select(
                'stock_transactions.*',
                'resources.res_name as pro_name',
                'resources.unit',
                'suppliers.sup_name'
            );

        if ($request->res_id)    $query->where('stock_transactions.res_id', $request->res_id);
        if ($request->type)      $query->where('type', $request->type);
        if ($request->date_from) $query->whereDate('txn_date', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('txn_date', '<=', $request->date_to);

        return response()->json($query->orderByDesc('txn_date')->get());
    }

    // Stock In — ទទួលទំនិញ
    public function stockIn(Request $request)
    {
        $request->validate([
            'pro_id' => 'required|exists:resources,res_id',
            'qty'    => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            DB::table('resources')
                ->where('res_id', $request->pro_id)
                ->increment('stock_qty', $request->qty);

            $txnId = DB::table('stock_transactions')->insertGetId([
                'res_id'     => $request->pro_id,
                'sup_id'     => $request->sup_id     ?? null,
                'type'       => 'in',
                'qty'        => $request->qty,
                'unit_price' => $request->unit_price ?? 0,
                'note'       => $request->note       ?? null,
                'txn_date'   => now('Asia/Phnom_Penh'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            $txn = DB::table('stock_transactions')
                ->join('resources', 'stock_transactions.res_id', '=', 'resources.res_id')
                ->leftJoin('suppliers', 'stock_transactions.sup_id', '=', 'suppliers.sup_id')
                ->select('stock_transactions.*', 'resources.res_name as pro_name', 'resources.unit', 'resources.stock_qty', 'suppliers.sup_name')
                ->where('stock_transactions.txn_id', $txnId)
                ->first();

            return response()->json([
                'message' => '✅ ទទួលទំនិញជោគជ័យ!',
                'record'  => $txn,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'មានបញ្ហា: ' . $e->getMessage()], 500);
        }
    }

    // Stock Out — លក់ / ប្រើប្រាស់
    public function stockOut(Request $request)
    {
        $request->validate([
            'pro_id' => 'required|exists:resources,res_id',
            'qty'    => 'required|integer|min:1',
        ]);

        $product = DB::table('resources')->where('res_id', $request->pro_id)->first();

        if ($product->stock_qty < $request->qty) {
            return response()->json([
                'message' => "⚠️ stock មិនគ្រប់គ្រាន់! មាន {$product->stock_qty} {$product->unit} តែប៉ុណ្ណោះ"
            ], 422);
        }

        DB::beginTransaction();
        try {
            DB::table('resources')
                ->where('res_id', $request->pro_id)
                ->decrement('stock_qty', $request->qty);

            $txnId = DB::table('stock_transactions')->insertGetId([
                'res_id'     => $request->pro_id,
                'sup_id'     => null,
                'type'       => 'out',
                'qty'        => $request->qty,
                'unit_price' => $request->unit_price ?? $product->price,
                'note'       => $request->note       ?? null,
                'txn_date'   => now('Asia/Phnom_Penh'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            $txn = DB::table('stock_transactions')
                ->join('resources', 'stock_transactions.res_id', '=', 'resources.res_id')
                ->leftJoin('suppliers', 'stock_transactions.sup_id', '=', 'suppliers.sup_id')
                ->select('stock_transactions.*', 'resources.res_name as pro_name', 'resources.unit', 'resources.stock_qty', 'suppliers.sup_name')
                ->where('stock_transactions.txn_id', $txnId)
                ->first();

            $newQty  = $product->stock_qty - $request->qty;
            $warning = $newQty <= ($product->low_stock_alert ?? 10)
                ? "⚠️ ទំនិញ {$product->res_name} នៅសល់ {$newQty} {$product->unit} ហើយ!"
                : null;

            return response()->json([
                'message' => '✅ បញ្ចេញទំនិញជោគជ័យ!',
                'warning' => $warning,
                'record'  => $txn,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'មានបញ្ហា: ' . $e->getMessage()], 500);
        }
    }

    // Stock Report
    public function report()
    {
        $products = DB::table('resources')
            ->leftJoin('categories', 'resources.cat_id', '=', 'categories.cat_id')
            ->select('resources.*', 'categories.cat_name as category')
            ->orderBy('stock_qty')
            ->get()
            ->map(function ($p) {
                $p->is_low_stock = $p->stock_qty <= ($p->low_stock_alert ?? 10);
                return $p;
            });

        $lowStock = $products->filter(fn($p) => $p->is_low_stock)->values();

        $today    = now('Asia/Phnom_Penh')->toDateString();
        $todayIn  = DB::table('stock_transactions')->where('type', 'in') ->whereDate('txn_date', $today)->sum('qty');
        $todayOut = DB::table('stock_transactions')->where('type', 'out')->whereDate('txn_date', $today)->sum('qty');

        return response()->json([
            'products'    => $products,
            'low_stock'   => $lowStock,
            'today_in'    => $todayIn,
            'today_out'   => $todayOut,
            'total_items' => $products->count(),
        ]);
    }
}