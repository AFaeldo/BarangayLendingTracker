@extends('Layout.layout_lendingtracker')

@section('title', 'Archive - Lending Tracker')

@section('page-title', 'Archive')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-between align-center">
            <h3>Archives</h3>

            <form method="GET" action="{{ route('archive.index') }}" class="d-flex flex-gap-8 align-center">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search..." aria-label="Search archived records" class="input" />
                <button class="btn" type="submit"><i class="fas fa-search"></i> Search</button>
                <a href="{{ route('archive.index') }}" class="btn" title="Reset"><i class="fas fa-sync"></i></a>
            </form>
        </div>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ============================ --}}
        {{-- 1. ARCHIVED BORROWING RECORDS --}}
        {{-- ============================ --}}
        <h4 class="section-title"><i class="fas fa-history"></i> Borrowing History</h4>
        
        @if($archives->isEmpty())
            <div class="no-data">No archived borrowing records found.</div>
        @else
            <table class="table" role="table" aria-label="Archived borrowings">
                <thead>
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
                        <td>
                            @if($record->returned_at)
                                <span title="Returned at {{ \Carbon\Carbon::parse($record->returned_at)->format('Y-m-d H:i') }}"><i class="fas fa-check-circle text-success"></i> {{ \Carbon\Carbon::parse($record->returned_at)->format('Y-m-d') }}</span>
                            @elseif($record->is_lost)
                                <span title="Lost"><i class="fas fa-times-circle text-danger"></i> Lost</span>
                            @else
                                <span title="Not returned"><i class="fas fa-clock text-warning"></i> Pending</span>
                            @endif
                        </td>
                        <td class="actions whitespace-nowrap">
                            <form method="POST" action="{{ route('archive.restore', $record->id) }}" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn" title="Restore to Active" onclick="return confirm('Restore this record to active borrowings? This will deduct stock again if it was returned.');">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </form>

                            <form method="POST" action="{{ route('archive.destroy', $record->id) }}" class="d-inline">
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
            <div class="mt-12">
                {{ $archives->appends(['residents_page' => $archivedResidents->currentPage()])->links() }}
            </div>
        @endif


        {{-- ============================ --}}
        {{-- 2. INACTIVE RESIDENTS --}}
        {{-- ============================ --}}
        <h4 class="section-title"><i class="fas fa-user-times"></i> Archived Residents (Inactive)</h4>

        @if($archivedResidents->isEmpty())
            <div class="no-data">No inactive residents found.</div>
        @else
            <table class="table" role="table" aria-label="Archived residents">
                <thead>
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
                        <td><span class="status-badge inactive">Inactive</span></td>
                        <td class="actions">
                            <form method="POST" action="{{ route('archive.resident.restore', $res->id) }}" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn" title="Restore to Active" onclick="return confirm('Restore resident to Active status?');">
                                    <i class="fas fa-user-check"></i> Restore
                                </button>
                            </form>

                            <form method="POST" action="{{ route('archive.resident.destroy', $res->id) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" title="Delete permanently" onclick="return confirm('Permanently delete this resident? Only allowed if no borrowing history exists.');">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="mt-12">
                {{ $archivedResidents->appends(['borrowings_page' => $archives->currentPage()])->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
