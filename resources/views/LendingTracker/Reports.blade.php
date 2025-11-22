@extends('Layout.layout_lendingtracker')

@section('title', 'Reports — Brgy. San Antonio')
@section('page-title', 'Inventory & Borrowing Reports')

@section('content')

    {{-- ===================== --}}
    {{-- TOP STAT CARDS --}}
    {{-- ===================== --}}
    <div class="stats">
        <div class="card">
            <div class="label">Total Items in Inventory</div>
            <div class="value">{{ $total_items ?? 0 }}</div>
        </div>

        <div class="card">
            <div class="label">Total Borrowed Items</div>
            <div class="value">{{ $total_borrowed ?? 0 }}</div>
        </div>

        <div class="card">
            <div class="label">Overdue Returns</div>
            <div class="value">{{ $overdue ?? 0 }}</div>
        </div>

        <div class="card">
            <div class="label">Most Borrowed Item</div>
            <div class="value">{{ $most_borrowed_item ?? 'N/A' }}</div>
        </div>
    </div>



    {{-- ===================== --}}
    {{-- 1: CURRENT INVENTORY LIST --}}
    {{-- ===================== --}}
    <div class="card" style="padding:18px">
        <h3 class="muted">Current Inventory List</h3>

        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Total Qty</th>
                    <th>Available</th>
                    <th>Condition</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventory_list ?? [] as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->quantity_total }}</td>
                        <td>{{ $item->quantity_available }}</td>
                        <td>{{ $item->condition }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No items found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>



    {{-- ===================== --}}
    {{-- 2: BORROWED ITEMS SUMMARY --}}
    {{-- ===================== --}}
    <div class="card" style="padding:18px">
        <h3 class="muted">Borrowed Items Summary</h3>

        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Borrower</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Borrowed Date</th>
                    <th>Expected Return</th>
                </tr>
            </thead>
            <tbody>
                @forelse($borrowed_summary ?? [] as $row)
                    <tr>
                        <td>{{ $row->borrower_name }}</td>
                        <td>{{ $row->item_name }}</td>
                        <td>{{ $row->qty }}</td>
                        <td>{{ $row->date_borrowed }}</td>
                        <td>{{ $row->expected_return }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No borrowed items.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>



    {{-- ===================== --}}
    {{-- 3: DAMAGED / LOST ITEMS SUMMARY --}}
    {{-- ===================== --}}
    <div class="card" style="padding:18px">
        <h3 class="muted">Damaged / Lost Items Summary</h3>

        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Reported On</th>
                </tr>
            </thead>
            <tbody>
                @forelse($damage_list ?? [] as $damage)
                    <tr>
                        <td>{{ $damage->item_name }}</td>
                        <td>{{ ucfirst($damage->type) }}</td>
                        <td>{{ $damage->description }}</td>
                        <td>{{ $damage->reported_at }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No damaged or lost items.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>



    {{-- ===================== --}}
    {{-- 4: BORROWING FREQUENCY PER AREA --}}
    {{-- ===================== --}}
    <div class="card" style="padding:18px">
        <h3 class="muted">Borrowing Frequency per Barangay Area</h3>

        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Area / Zone</th>
                    <th>Total Borrowings</th>
                </tr>
            </thead>
            <tbody>
                @forelse($frequency_area ?? [] as $area)
                    <tr>
                        <td>{{ $area->zone }}</td>
                        <td>{{ $area->total }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-center">No records by area.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>



    {{-- ===================== --}}
    {{-- 5: TREND OVERVIEW CHART --}}
    {{-- ===================== --}}
    <div class="card" style="padding:18px">
        <h3 class="muted">Borrowing Trend Overview (Monthly)</h3>

        {{-- Placeholder for your chart.js or Livewire chart --}}
        <div style="height:300px; background:#f9f9f9; border-radius:8px; display:flex; align-items:center; justify-content:center;">
            <span class="muted">Chart Placeholder</span>
        </div>
    </div>

@endsection
