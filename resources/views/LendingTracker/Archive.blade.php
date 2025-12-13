@extends('Layout.layout_lendingtracker')

@section('title', 'Archive - Lending Tracker')
@section('page-title', 'Archive')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-archive me-1"></i> Archives</h4>

        <form method="GET" action="{{ route('archive.index') }}" class="d-flex gap-2 align-items-center">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search..." class="form-control form-control-sm" aria-label="Search archived records">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Search</button>
            <a href="{{ route('archive.index') }}" class="btn btn-secondary btn-sm" title="Reset"><i class="fas fa-sync"></i></a>
        </form>
    </div>

    <div class="card-body">

        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center"><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Archived Borrowings --}}
        <h5 class="mb-3"><i class="fas fa-history me-1"></i> Borrowing History</h5>
        @if($archives->isEmpty())
            <div class="text-muted">No archived borrowing records found.</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Borrower</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Borrowed At</th>
                            <th>Returned</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archives as $index => $record)
                        <tr>
                            <td>{{ $archives->firstItem() + $index }}</td>
                            <td>{{ $record->resident ? $record->resident->last_name.', '.$record->resident->first_name : 'Unknown Resident' }}</td>
                            <td>{{ $record->item->name ?? 'Unknown Item' }}</td>
                            <td>{{ $record->quantity }}</td>
                            <td>{{ \Carbon\Carbon::parse($record->date_borrowed)->format('Y-m-d') }}</td>
                            <td>
                                @if($record->returned_at)
                                    <span class="badge bg-success" title="Returned at {{ \Carbon\Carbon::parse($record->returned_at)->format('Y-m-d H:i') }}">Returned</span>
                                @elseif($record->is_lost)
                                    <span class="badge bg-danger" title="Lost">Lost</span>
                                @else
                                    <span class="badge bg-warning text-dark" title="Pending">Pending</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <form method="POST" action="{{ route('archive.restore', $record->id) }}" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-success" title="Restore to Active" onclick="return confirm('Restore this record to active borrowings? This will deduct stock again if it was returned.')">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('archive.destroy', $record->id) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete permanently" onclick="return confirm('Permanently delete this archived record? This cannot be undone.')">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $archives->appends(['residents_page' => $archivedResidents->currentPage()])->links() }}
            </div>
        @endif

        {{-- Archived Residents --}}
        <h5 class="mt-5 mb-3"><i class="fas fa-user-times me-1"></i> Archived Residents (Inactive)</h5>
        @if($archivedResidents->isEmpty())
            <div class="text-muted">No inactive residents found.</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Purok</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archivedResidents as $index => $res)
                        <tr>
                            <td>{{ $archivedResidents->firstItem() + $index }}</td>
                            <td>{{ $res->last_name }}, {{ $res->first_name }} {{ $res->middle_name }}</td>
                            <td>{{ $res->purok ?? $res->sitio ?? '-' }}</td>
                            <td>{{ $res->contact ?? '-' }}</td>
                            <td><span class="badge bg-secondary">Inactive</span></td>
                            <td class="text-nowrap">
                                <form method="POST" action="{{ route('archive.resident.restore', $res->id) }}" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-success" title="Restore to Active" onclick="return confirm('Restore resident to Active status?')">
                                        <i class="fas fa-user-check"></i> Restore
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('archive.resident.destroy', $res->id) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete permanently" onclick="return confirm('Permanently delete this resident? Only allowed if no borrowing history exists.')">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $archivedResidents->appends(['borrowings_page' => $archives->currentPage()])->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
