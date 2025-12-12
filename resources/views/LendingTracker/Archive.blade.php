@extends('Layout.layout_lendingtracker')

@section('title', 'Archive - Lending Tracker')

@section('page-title', 'Archive')

@push('styles')
<style>
    /* Small adjustments to match layout */
    .archive-table { width: 100%; border-collapse: collapse; }
    .archive-table th, .archive-table td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
    .actions button { margin-right: 6px; }
    .search-row { display:flex; gap:8px; margin-bottom:12px; align-items:center; }
    .no-data { padding: 20px; text-align: center; color: #666; }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-header">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Archived Borrowed Records</h3>

            <form method="GET" action="{{ route('archive.index') }}" style="display:flex; gap:8px; align-items:center;">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name, item, or transaction..." aria-label="Search archived records" />
                <button class="btn" type="submit"><i class="fas fa-search"></i> Search</button>
                <a href="{{ route('archive.index') }}" class="btn" title="Reset"><i class="fas fa-sync"></i></a>
            </form>
        </div>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif

        @if($archives->isEmpty())
            <div class="no-data">No archived records found.</div>
        @else
            <table class="archive-table" role="table" aria-label="Archived borrowings">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Borrower</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Borrowed At</th>
                        <th>Due Date</th>
                        <th>Returned</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($archives as $index => $record)
                    <tr>
                        <td>{{ $archives->firstItem() + $index }}</td>
                        <td>
                            @if($record->resident)
                                {{ $record->resident->last_name }}, {{ $record->resident->first_name }}
                            @else
                                <span class="text-muted">Unknown Resident</span>
                            @endif
                        </td>
                        <td>{{ $record->item->name ?? 'Unknown Item' }}</td>
                        <td>{{ $record->quantity }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->date_borrowed)->format('Y-m-d') }}</td>
                        <td>{{ $record->due_date ? \Carbon\Carbon::parse($record->due_date)->format('Y-m-d') : '—' }}</td>
                        <td>
                            @if($record->returned_at)
                                <span title="Returned at {{ \Carbon\Carbon::parse($record->returned_at)->format('Y-m-d H:i') }}"><i class="fas fa-check-circle" style="color:green"></i> {{ \Carbon\Carbon::parse($record->returned_at)->format('Y-m-d') }}</span>
                            @elseif($record->is_lost)
                                <span title="Lost"><i class="fas fa-times-circle" style="color:red"></i> Lost</span>
                            @else
                                <span title="Not returned"><i class="fas fa-clock" style="color:orange"></i> Pending</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($record->remarks, 40) }}</td>
                        <td class="actions" style="white-space:nowrap;">
                            <form method="POST" action="{{ route('archive.restore', $record->id) }}" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn" title="Restore to Active" onclick="return confirm('Restore this record to active borrowings? This will deduct stock again if it was returned.');">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </form>

                            <form method="POST" action="{{ route('archive.destroy', $record->id) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" title="Delete permanently" onclick="return confirm('Permanently delete this archived record? This cannot be undone.');">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div style="margin-top:12px;">
                {{ $archives->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Accessibility: close native dropdown if Escape pressed when focus inside
    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape'){
            const dropdown = document.getElementById('dropdown-menu');
            if(dropdown && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
                document.getElementById('profile-menu')?.setAttribute('aria-expanded','false');
            }
        }
    });
</script>
@endpush
