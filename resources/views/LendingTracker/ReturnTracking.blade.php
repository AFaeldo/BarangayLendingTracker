@extends('Layout.layout_lendingtracker')

@section('title', 'Return Tracking — Brgy. San Antonio')
@section('page-title', 'Return Tracking')

@section('content')
    <div class="card p-4">
        <h3>Active Borrowings (To Be Returned)</h3>
        <p class="text-muted">Select an item from the list below to mark it as returned.</p>
        
        <div class="mt-3">
            <a href="{{ route('borrowing.index') }}" class="btn btn-primary">
                <i class="fas fa-list"></i> Go to Borrowing List
            </a>
        </div>
    </div>
@endsection
