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
    {{-- 2: BORROWED ITEMS SUMMARY --}}
    {{-- ===================== --}}
    <div class="card p-18">
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
    <div class="card p-18">
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



   


        {{-- 5: TREND OVERVIEW CHART --}}



   


        {{-- ===================== --}}



   


        <div class="card p-18">



   


            <h3 class="muted">Borrowing Trend Overview (Monthly)</h3>



   


    



   


            {{-- Chart Canvas --}}



   


            <div class="h-300 bg-light rounded p-10" style="position: relative;">



   


                <canvas id="trendChart"></canvas>



   


            </div>



   


        </div>



   


    



   


        {{-- Watermark & Footer for Print --}}



   


        <div class="print-watermark">



   


            <img src="{{ asset('image/logo.png') }}" alt="Watermark">



   


        </div>



   


        



   


        <div class="print-footer">



   


            Generated on: {{ now()->format('F j, Y g:i A') }}



   


        </div>



   


    



   


    @endsection



   


    



   


    @push('styles')



   


    <style>



   


        /* Print Styles */



   


        @media print {



   


            @page { margin: 2cm; }



   


            body { -webkit-print-color-adjust: exact; }



   


            



   


            .sidebar, .header, .btn, .top-bar { display: none !important; }



   


            .card { box-shadow: none; border: 1px solid #ddd; break-inside: avoid; }



   


            .app { display: block; }



   


            .main { margin-left: 0; }



   


            .content { padding: 0; }



   


    



   


            /* Watermark */



   


            .print-watermark {



   


                position: fixed;



   


                top: 50%;



   


                left: 50%;



   


                transform: translate(-50%, -50%);



   


                opacity: 0.1;



   


                z-index: -1;



   


                pointer-events: none;



   


                display: block !important;



   


            }



   


            .print-watermark img {



   


                width: 500px;



   


                height: auto;



   


            }



   


    



   


            /* Footer */



   


            .print-footer {



   


                position: fixed;



   


                bottom: 0;



   


                right: 0;



   


                font-size: 12px;



   


                color: #555;



   


                display: block !important;



   


            }



   


        }



   


    



   


        /* Hide print elements on screen */



   


        .print-watermark, .print-footer { display: none; }



   


    </style>



   


    @endpush



   


    



   


    @push('scripts')



   


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



   


    <script>



   


        document.addEventListener('DOMContentLoaded', function() {



   


            const ctx = document.getElementById('trendChart');



   


            if (ctx) {



   


                // Data from ReportController



   


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



   


    