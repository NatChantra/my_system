<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'week');

        [$startDate, $endDate] = match($period) {
            'last_week' => [
                Carbon::now()->subWeek()->startOfWeek(),
                Carbon::now()->subWeek()->endOfWeek(),
            ],
            'month' => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ],
            default => [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ],
        };

        // ── Attendance Stats ──────────────────────────────────
        $attendances  = DB::table('attendances')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalDays    = max($attendances->count(), 1);
        $presentCount = $attendances->whereIn('status', ['On Time', 'Late'])->count();
        $lateCount    = $attendances->where('status', 'Late')->count();
        $avgAtt       = round(($presentCount / $totalDays) * 100, 1);

        // Trend Mon–Sun
        $trendData = [];
        for ($i = 0; $i < 7; $i++) {
            $day   = Carbon::now()->startOfWeek()->addDays($i)->toDateString();
            $count = DB::table('attendances')
                ->where('date', $day)
                ->whereIn('status', ['On Time', 'Late'])
                ->count();
            $trendData[] = ['actual' => $count];
        }

        // Today's exceptions
        $today      = Carbon::today()->toDateString();
        $exceptions = DB::table('attendances')
            ->join('employee', 'attendances.emp_id', '=', 'employee.emp_id')
            ->join('departments', 'employee.dept_id', '=', 'departments.dept_id')
            ->whereDate('attendances.date', $today)
            ->whereIn('attendances.status', ['Absent', 'Late'])
            ->select(
                'employee.emp_id',
                'employee.emp_name',
                'departments.dept_name as department',
                'attendances.status',
                'attendances.time_in'
            )
            ->get()
            ->map(fn($e) => [
                'emp_id'     => $e->emp_id,
                'emp_name'   => $e->emp_name,
                'department' => $e->department,
                'status'     => $e->status === 'Late'
                                ? 'Late (' . ($e->time_in ?? '') . ')'
                                : 'Absent',
            ]);

        // ── Inventory Stats ───────────────────────────────────
        // ✅ FIX: table is "resources" (plural), join categories to get cat_name,
        // and use the real column names: price, low_stock_alert (not unit_price/sku/reorder_point)
        $resources = DB::table('resources')
            ->leftJoin('categories', 'resources.cat_id', '=', 'categories.cat_id')
            ->select('resources.*', 'categories.cat_name as category')
            ->get();

        $totalStockValue = $resources->sum(fn($r) => $r->stock_qty * ($r->price ?? 0));
        $lowStockCount   = $resources->filter(
            fn($r) => $r->stock_qty <= ($r->low_stock_alert ?? 10)
        )->count();

        // Stock by category
        $totalQty  = max($resources->sum('stock_qty'), 1);
        $colors    = ['#1a3a8f', '#f4a261', '#e63946', '#9ca3af', '#2cb67d', '#4895ef', '#9d4edd'];
        $stockCats = $resources->groupBy('category')->map(function ($items, $cat) use ($totalQty, &$colors) {
            return [
                'name'  => $cat ?? 'Other',
                'pct'   => round($items->sum('stock_qty') / $totalQty * 100),
                'color' => array_shift($colors) ?? '#9ca3af',
            ];
        })->values();

        // Reorder list (below low_stock_alert threshold)
        $reorderList = $resources
            ->filter(fn($r) => $r->stock_qty <= ($r->low_stock_alert ?? 10))
            ->map(fn($r) => [
                'name'    => $r->res_name,
                'sku'     => 'RES-' . str_pad($r->res_id, 4, '0', STR_PAD_LEFT),
                'current' => $r->stock_qty,
                'reorder' => $r->low_stock_alert ?? 10,
                'status'  => match(true) {
                    $r->stock_qty <= 0                                    => 'Critical',
                    $r->stock_qty <= (($r->low_stock_alert ?? 10) / 2)     => 'Low Stock',
                    default                                                => 'Approaching',
                },
            ])
            ->values();

        // ── Response ─────────────────────────────────────────
        return response()->json([
            'total_stock_value' => round($totalStockValue, 2),
            'low_stock_count'   => $lowStockCount,
            'avg_attendance'    => $avgAtt,
            'late_count'        => $lateCount,
            'attendance_trend'  => $trendData,
            'exceptions'        => $exceptions,
            'stock_categories'  => $stockCats,
            'reorder_list'      => $reorderList,
        ]);
    }
}