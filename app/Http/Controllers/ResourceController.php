<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('resources')
            ->leftJoin('categories', 'resources.cat_id', '=', 'categories.cat_id')
            ->leftJoin('suppliers',  'resources.sup_id', '=', 'suppliers.sup_id')
            ->select('resources.*', 'categories.cat_name as category', 'suppliers.sup_name');

        if ($request->cat_id) $query->where('resources.cat_id', $request->cat_id);
        if ($request->search) $query->where('res_name', 'like', "%{$request->search}%");

        return response()->json(
            $query->orderBy('res_name')->get()->map(function ($p) {
                $p->is_low_stock = $p->stock_qty <= ($p->low_stock_alert ?? 10);
                return $p;
            })
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'res_name' => 'required|string|max:255',
        ]);

        $id = DB::table('resources')->insertGetId([
            'res_name'        => $request->res_name,
            'cat_id'          => $request->cat_id          ?? null,
            'sup_id'          => $request->sup_id          ?? null,
            'price'           => $request->price           ?? 0,
            'stock_qty'       => $request->stock_qty       ?? 0,
            'low_stock_alert' => $request->low_stock_alert ?? 10,
            'unit'            => $request->unit            ?? 'pcs',
            'created_at'      => now(),
            'updated_at'      => now(),
        ], 'res_id'); // ✅ fix: specify primary key

        // បើមាន stock_qty ពេល add → កត់ transaction ដំបូង
        if (($request->stock_qty ?? 0) > 0) {
            DB::table('stock_transactions')->insert([
                'res_id'     => $id,
                'sup_id'     => $request->sup_id ?? null,
                'type'       => 'in',
                'qty'        => $request->stock_qty,
                'unit_price' => $request->price ?? 0,
                'note'       => 'Stock ដំបូង (Initial Stock)',
                'txn_date'   => now('Asia/Phnom_Penh'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json($this->getOne($id), 201);
    }

    public function update(Request $request, $id)
    {
        DB::table('resources')->where('res_id', $id)->update([
            'res_name'        => $request->res_name,
            'cat_id'          => $request->cat_id          ?? null,
            'sup_id'          => $request->sup_id          ?? null,
            'price'           => $request->price           ?? 0,
            'low_stock_alert' => $request->low_stock_alert ?? 10,
            'unit'            => $request->unit            ?? 'pcs',
            'updated_at'      => now(),
        ]);
        return response()->json($this->getOne($id));
    }

    public function destroy($id)
    {
        DB::table('resources')->where('res_id', $id)->delete();
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }

    private function getOne($id)
    {
        return DB::table('resources')
            ->leftJoin('categories', 'resources.cat_id', '=', 'categories.cat_id')
            ->leftJoin('suppliers',  'resources.sup_id', '=', 'suppliers.sup_id')
            ->select('resources.*', 'categories.cat_name as category', 'suppliers.sup_name')
            ->where('resources.res_id', $id)
            ->first();
    }
}