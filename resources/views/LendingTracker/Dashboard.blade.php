@extends('Layout.layout_lendingtracker')

@section('title', 'Dashboard — Brgy. San Antonio')
@section('page-title', 'Dashboard')

@section('content')

{{-- ===================== STATS CARDS ===================== --}}
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-primary fs-2">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Residents</h6>
                    <h3 class="fw-bold mb-0">{{ $total_residents ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-success fs-2">
                    <i class="fas fa-box-open"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Items Borrowed</h6>
                    <h3 class="fw-bold mb-0">{{ $items_borrowed ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-danger fs-2">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Overdue</h6>
                    <h3 class="fw-bold mb-0">{{ $overdue ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ===================== CHART ===================== --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        Monthly Borrowing Activity
    </div>
    <div class="card-body">
        <div style="height:320px;">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>
</div>

{{-- ===================== QUICK ACTIONS ===================== --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        Quick Actions
    </div>
    <div class="card-body">
        <div class="row g-3 text-center">

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('borrowing.create') }}"
                   class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-plus-circle fs-2 text-primary mb-2"></i>
                            <h6 class="fw-semibold">Add Borrowing</h6>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('borrowing.index') }}"
                   class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-file-alt fs-2 text-success mb-2"></i>
                            <h6 class="fw-semibold">Borrow List</h6>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('residents.index') }}"
                   class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-user-friends fs-2 text-warning mb-2"></i>
                            <h6 class="fw-semibold">Residents</h6>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('items.index') }}"
                   class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-boxes fs-2 text-danger mb-2"></i>
                            <h6 class="fw-semibold">Inventory</h6>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>

{{-- ===================== RECENT TRANSACTIONS ===================== --}}
<div class="card shadow-sm">
    <div class="card-header bg-white fw-semibold">
        Recent Transactions
    </div>

    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Resident</th>
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
                    <td>
                        {{ $t->resident->last_name ?? '' }},
                        {{ $t->resident->first_name ?? '' }}
                    </td>
                    <td>{{ $t->item->name ?? '-' }}</td>
                    <td>{{ $t->quantity }}</td>
                    <td>{{ $t->date_borrowed }}</td>
                    <td>
                        {{ $t->returned_at
                            ? \Carbon\Carbon::parse($t->returned_at)->format('Y-m-d')
                            : '—' }}
                    </td>
                    <td>
                        <span class="badge
                            {{ $t->status === 'Returned' ? 'bg-success' :
                               ($t->status === 'Overdue' ? 'bg-danger' : 'bg-warning') }}">
                            {{ $t->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No recent transactions.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

{{-- ===================== CHART SCRIPT ===================== --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const ctx = document.getElementById('monthlyChart');
    if (!ctx) return;

    const rawData = @json($monthly_stats ?? []);
    const labels = rawData.map(d => d.month);
    const data = rawData.map(d => d.total);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Items Borrowed',
                data: data,
                backgroundColor: 'rgba(13,110,253,.6)',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

});
</script>
@endpush
