<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrowing;
use App\Models\Resident;
use Carbon\Carbon;

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

        return view('LendingTracker.Dashboard', compact(
            'total_residents',
            'items_borrowed',
            'overdue',
            'recent_transactions'
        ));
    }
}
