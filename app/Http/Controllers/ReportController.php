<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | TOP STATS
        |--------------------------------------------------------------------------
        */

        // Total items in inventory
        $total_items = DB::table('items')->sum('quantity_total');

        // Total currently borrowed (active only)
        $total_borrowed = DB::table('borrow_items')
            ->whereNull('returned_at')
            ->sum('qty_borrowed');

        // Overdue items
        $overdue = DB::table('borrow_records')
            ->whereNull('date_returned')
            ->where('expected_return', '<', Carbon::now())
            ->count();

        // Most Borrowed Item
        $most_borrowed_item = DB::table('borrow_items')
            ->join('items', 'items.id', '=', 'borrow_items.item_id')
            ->select('items.name', DB::raw('SUM(borrow_items.qty_borrowed) as total'))
            ->groupBy('items.name')
            ->orderByDesc('total')
            ->limit(1)
            ->value('name');

        /*
        |--------------------------------------------------------------------------
        | 1. CURRENT INVENTORY LIST
        |--------------------------------------------------------------------------
        */
        $inventory_list = DB::table('items')->get();


        /*
        |--------------------------------------------------------------------------
        | 2. BORROWED ITEMS SUMMARY (active only)
        |--------------------------------------------------------------------------
        */
        $borrowed_summary = DB::table('borrow_records')
            ->join('borrow_items', 'borrow_items.borrow_record_id', '=', 'borrow_records.id')
            ->join('items', 'items.id', '=', 'borrow_items.item_id')
            ->select(
                'borrow_records.borrower_name',
                'items.name as item_name',
                'borrow_items.qty_borrowed as qty',
                'borrow_records.date_borrowed',
                'borrow_records.expected_return'
            )
            ->whereNull('borrow_records.date_returned')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 3. DAMAGED / LOST ITEMS SUMMARY
        |--------------------------------------------------------------------------
        */
        $damage_list = DB::table('damage_reports')
            ->join('items', 'items.id', '=', 'damage_reports.item_id')
            ->select(
                'items.name as item_name',
                'damage_reports.type',
                'damage_reports.description',
                'damage_reports.created_at as reported_at'
            )
            ->orderBy('damage_reports.created_at', 'DESC')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 4. BORROWING FREQUENCY PER BARANGAY AREA
        |--------------------------------------------------------------------------
        */
        $frequency_area = DB::table('borrow_records')
            ->select(
                'barangay_zone as zone',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('barangay_zone')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 5. MONTHLY TREND (chart)
        |--------------------------------------------------------------------------
        */
        $trend_data = DB::table('borrow_records')
            ->select(
                DB::raw("DATE_FORMAT(date_borrowed, '%Y-%m') as month"),
                DB::raw("COUNT(*) as total")
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();


        return view('reports.index', compact(
            'total_items',
            'total_borrowed',
            'overdue',
            'most_borrowed_item',
            'inventory_list',
            'borrowed_summary',
            'damage_list',
            'frequency_area',
            'trend_data'
        ));
    }
}
