<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrowing;
use App\Models\Resident;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Residents
        $total_residents = Resident::where('status', 'Active')->count();

        // Items Borrowed (Active)
        $items_borrowed = Borrowing::where('status', 'Borrowed')->sum('quantity');

        // Overdue
        $overdue = Borrowing::where('status', 'Borrowed')
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::now())
            ->count();

        // Recent Transactions (Last 5)
        $recent_transactions = Borrowing::with(['resident', 'item'])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        // Monthly Trend for Chart (Last 6 months)
        // Group by Y-m. Compatible with MySQL. 
        // If using SQLite locally, might need strftime('%Y-%m', date_borrowed)
        $monthly_stats = Borrowing::select(
                DB::raw("DATE_FORMAT(date_borrowed, '%Y-%m') as month"),
                DB::raw("COUNT(*) as total")
            )
            ->where('date_borrowed', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();

        return view('LendingTracker.Dashboard', compact(
            'total_residents',
            'items_borrowed',
            'overdue',
            'recent_transactions',
            'monthly_stats'
        ));
    }
}
