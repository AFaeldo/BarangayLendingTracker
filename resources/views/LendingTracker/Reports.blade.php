@extends('Layout.layout_lendingtracker')

@section('title', 'Reports — Brgy. San Antonio')
@section('page-title', 'Inventory & Borrowing Reports')

@section('content')

{{-- ===================== --}}
{{-- TOP STAT CARDS --}}
{{-- ===================== --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Total Items in Inventory</h6>
                <h3>{{ $total_items ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Total Borrowed Items</h6>
                <h3>{{ $total_borrowed ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Overdue Returns</h6>
                <h3>{{ $overdue ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Most Borrowed Item</h6>
                <h5>{{ $most_borrowed_item ?? 'N/A' }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- ===================== --}}
{{-- BORROWED ITEMS SUMMARY --}}
{{-- ===================== --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Borrowed Items Summary</h5>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
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
                    <tr>
                        <td colspan="5" class="text-center text-muted">No borrowed items.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===================== --}}
{{-- DAMAGED / LOST ITEMS SUMMARY --}}
{{-- ===================== --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Damaged / Lost Items Summary</h5>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
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
                    <tr>
                        <td colspan="4" class="text-center text-muted">No damaged or lost items.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===================== --}}
{{-- BORROWING TREND CHART --}}
{{-- ===================== --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Borrowing Trend Overview (Monthly)</h5>
    </div>
    <div class="card-body">
        <canvas id="trendChart" class="w-100" style="height:300px;"></canvas>
    </div>
</div>

<div class="text-end text-muted mb-4">
    Generated on: {{ now()->format('F j, Y g:i A') }}
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('trendChart');
    if(ctx) {
        const rawData = @json($trend_data ?? []);
        const labels = rawData.map(item => item.month);
        const data = rawData.map(item => item.total);
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Borrowings',
                    data: data,
                    borderColor: '#C66B38',
                    backgroundColor: 'rgba(198, 107, 56, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.stats .card {
    padding: 1rem;
    border-radius: 8px;
}
.stats .label { font-weight: 500; color: #6c757d; }
.stats .value { font-size: 1.75rem; font-weight: 600; margin-top: 0.25rem; }

@media print {
    .sidebar, .header, .btn { display: none !important; }
    .card { box-shadow: none; border: 1px solid #ddd; }
    .print-footer { display: block; font-size: 12px; color: #555; text-align: right; }
}
</style>
@endpush
