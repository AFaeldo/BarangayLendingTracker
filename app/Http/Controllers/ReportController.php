<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Borrowing;
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

        // Total items in inventory (sum of total quantity)
        $total_items = Item::sum('quantity');

        // Total currently borrowed (active only, sum of quantity)
        $total_borrowed = Borrowing::where('status', 'Borrowed')->sum('quantity');

        // Overdue items
        $overdue = Borrowing::where('status', 'Borrowed')
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::now())
            ->count();

        // Most Borrowed Item
        $most_borrowed_item = Borrowing::select('item_id', DB::raw('count(*) as total'))
            ->groupBy('item_id')
            ->orderByDesc('total')
            ->with('item')
            ->first();
        $most_borrowed_item_name = $most_borrowed_item ? $most_borrowed_item->item->name : 'N/A';

        /*
        |--------------------------------------------------------------------------
        | 1. CURRENT INVENTORY LIST
        |--------------------------------------------------------------------------
        */
        $inventory_list = Item::all();


        /*
        |--------------------------------------------------------------------------
        | 2. BORROWED ITEMS SUMMARY (active only)
        |--------------------------------------------------------------------------
        */
        // Transforming to match view expectations: borrower_name, item_name, qty, date_borrowed, expected_return
        $borrowed_summary_raw = Borrowing::with(['resident', 'item'])
            ->where('status', 'Borrowed')
            ->get();
        
        $borrowed_summary = $borrowed_summary_raw->map(function($b) {
            return (object)[
                'borrower_name' => $b->resident ? ($b->resident->last_name . ', ' . $b->resident->first_name) : 'Unknown',
                'item_name' => $b->item ? $b->item->name : 'Unknown',
                'qty' => $b->quantity,
                'date_borrowed' => $b->date_borrowed, // Cast to string if needed in view, but blade handles dates ok usually
                'expected_return' => $b->due_date
            ];
        });


        /*
        |--------------------------------------------------------------------------
        | 3. DAMAGED / LOST ITEMS SUMMARY
        |--------------------------------------------------------------------------
        */
        // We don't have a damage_reports table. We can list items with condition != Good
        // OR Borrowings that were returned Damaged or Lost.
        // Let's go with Borrowings that were Lost or Returned Damaged.
        
        $damage_list_raw = Borrowing::with(['item'])
            ->where(function($q) {
                $q->where('is_lost', true)
                  ->orWhere('condition_returned', 'Damaged'); // Assuming 'Damaged' is the string used
            })
            ->orderByDesc('updated_at')
            ->get();

        $damage_list = $damage_list_raw->map(function($b) {
            return (object)[
                'item_name' => $b->item ? $b->item->name : 'Unknown',
                'type' => $b->is_lost ? 'Lost' : ($b->condition_returned ?? 'Damaged'),
                'description' => $b->remarks ?? 'No remarks',
                'reported_at' => $b->updated_at
            ];
        });


        /*
        |--------------------------------------------------------------------------
        | 5. MONTHLY TREND (chart)
        |--------------------------------------------------------------------------
        */
        // Using SQLite/MySQL compatible date format
        // For MySQL: DATE_FORMAT(date_borrowed, '%Y-%m')
        // For SQLite: strftime('%Y-%m', date_borrowed)
        // Assuming MySQL based on typical setup, but let's try to be generic or use raw
        
        $trend_data = Borrowing::select(
                DB::raw("DATE_FORMAT(date_borrowed, '%Y-%m') as month"),
                DB::raw("COUNT(*) as total")
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();


        return view('LendingTracker.Reports', compact(
            'total_items',
            'total_borrowed',
            'overdue',
            'most_borrowed_item_name', // Renamed variable to match view expectation if I change view
            'inventory_list',
            'borrowed_summary',
            'damage_list',
            'trend_data'
        ))->with('most_borrowed_item', $most_borrowed_item_name); 
    }
}