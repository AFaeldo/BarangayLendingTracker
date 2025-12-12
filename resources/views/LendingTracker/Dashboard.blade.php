{{-- resources/views/LendingTracker/Dashboard.blade.php --}}
@extends('Layout.layout_lendingtracker')

@section('title', 'Dashboard — Brgy. San Antonio')
@section('page-title', 'Dashboard')

@section('content')


    {{-- STATS --}}
    <div class="stats" aria-hidden="false">
        <div class="card">
            <div class="label">Total Residents</div>
            <div class="value">{{ $total_residents ?? 0 }}</div>
        </div>
        <div class="card">
            <div class="label">Items Borrowed</div>
            <div class="value">{{ $items_borrowed ?? 0 }}</div>
        </div>
        <div class="card">
            <div class="label">Overdue</div>
            <div class="value">{{ $overdue ?? 0 }}</div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <section class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="actions-grid">
            <a href="{{ route('borrowing.create') }}" class="action-button text-decoration-none">
                <i class="fas fa-plus-circle"></i>
                <span>Add Borrowing</span>
            </a>
            <a href="{{ route('borrowing.index') }}" class="action-button text-decoration-none">
                <i class="fas fa-file-alt"></i>
                <span>Borrow List</span>
            </a>
            <a href="{{ route('residents.index') }}" class="action-button text-decoration-none">
                <i class="fas fa-user-friends"></i>
                <span>Residents</span>
            </a>
            <a href="{{ route('items.index') }}" class="action-button text-decoration-none">
                <i class="fas fa-boxes"></i>
                <span>Inventory</span>
            </a>
        </div>
    </section>

    {{-- RECENT TRANSACTIONS --}}
    <div>
        <h3 class="muted mb-10">Recent Transactions</h3>

        <table class="table" aria-label="Recent transactions">
            <thead>
            <tr>
                <th>ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Borrow Date</th>
                <th>Return Date</th>
                <th>Status</th>
            </tr>
            </thead>

            <tbody>
            @forelse ($recent_transactions as $t)
                <tr>
                    <td>{{ $t->id }}</td>
                    <td>{{ $t->resident->last_name ?? '—' }}</td>
                    <td>{{ $t->resident->first_name ?? '—' }}</td>
                    <td>{{ $t->item->name ?? '—' }}</td>
                    <td>{{ $t->quantity }}</td>
                    <td>{{ $t->date_borrowed }}</td>
                    <td>{{ $t->returned_at ? \Carbon\Carbon::parse($t->returned_at)->format('Y-m-d') : '—' }}</td>
                    <td>
                        <span class="status-badge {{ strtolower($t->status) }}">
                            {{ $t->status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center p-20 text-gray">
                        No recent transactions.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

@endsection
