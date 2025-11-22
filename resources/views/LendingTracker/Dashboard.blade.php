{{-- resources/views/Account/LendingTracker/Dashboard.blade.php --}}
@extends('Layout.layout_lendingtracker')

@section('title', 'Dashboard — Brgy. San Antonio')
@section('page-title', 'Dashboard')

@section('content')


    {{-- STATS --}}
    <div class="stats" aria-hidden="false">
        <div class="card">
            <div class="label">Total Residents</div>
            <div class="value">0</div>
        </div>
        <div class="card">
            <div class="label">Items Borrowed</div>
            <div class="value">0</div>
        </div>
        <div class="card">
            <div class="label">Overdue</div>
            <div class="value">0</div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <section class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="actions-grid">
            <button class="action-button">
                <i class="fas fa-plus-circle"></i>
                Borrowed Record
            </button>
            <button class="action-button">
                <i class="fas fa-arrow-left"></i>
                Return Item
            </button>
            <button class="action-button">
                <i class="fas fa-user-friends"></i>
                Residents
            </button>
            <button class="action-button">
                <i class="fas fa-boxes"></i>
                Items Available
            </button>
        </div>
    </section>

    {{-- RECENT TRANSACTIONS --}}
    <div>
        <h3 class="muted" style="margin-bottom:10px">Recent Transactions</h3>

        <table class="table" aria-label="Recent transactions">
            <thead>
            <tr>
                <th>Transaction ID</th>
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
            {{-- EMPTY STATE --}}
            <tr>
                <td colspan="8" style="text-align:center; padding:20px; color:gray;">
                    No recent transactions.
                </td>
            </tr>
            </tbody>
        </table>
    </div>

@endsection
