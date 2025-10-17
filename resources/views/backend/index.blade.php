@extends('backend.master')
@section('title', 'Dashboard | Velzon - Admin & Dashboard Template')

@section('content')
<!-- Begin page -->

        

     

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Dashboard</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboards</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1">Good Morning, {{Auth::user()->name}}!</h4>
                                <p class="text-muted mb-0">Here's what's happening with your store today.</p>
                            </div>
                            <div class="mt-3 mt-lg-0">
                                <form action="javascript:void(0);">
                                    <div class="row g-3 mb-0 align-items-center">
                                        <!-- <div class="col-sm-auto">
                                            <div class="input-group">
                                                <input type="text" class="form-control border-0 dash-filter-picker shadow" data-provider="flatpickr" data-range-date="true" data-date-format="d M, Y" data-deafult-date="01 Jan 2022 to 31 Jan 2022">
                                                <div class="input-group-text bg-primary border-primary text-white">
                                                    <i class="ri-calendar-2-line"></i>
                                                </div>
                                            </div>
                                        </div> -->
                                        <!--end col-->
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-soft-success"><i class="ri-add-circle-line align-middle me-1"></i> Add Product</button>
                                        </div>
                                        <!--end col-->
                                        <!-- <div class="col-auto">
                                            <button type="button" class="btn btn-soft-info btn-icon waves-effect waves-light layout-rightside-btn"><i class="ri-pulse-line"></i></button>
                                        </div> -->
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                        </div><!-- end card header -->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->

                <!-- stat 1 -->
                @include('backend.partials.stat-top')
                <!-- end row-->

                {{-- sales - months --}}
                @include('backend.partials.charts.sales-months')
                <!-- chart 2 : best & top sellers-->
                @include('backend.partials.chart-2')
                <!-- end row-->
                

                
            </div>
            <!-- end .h-100-->

        </div>
        <!-- end col -->

    </div>





    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

@endsection

{{-- @push('scripts-bottom')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var chartElement = document.querySelector("#customer_impression_charts");
            if (!chartElement) {
                console.error("Chart container not found!");
                return;
            }

            var ordersData = @json($orders);
            var earningsData = @json($earnings);
            var refundsData = @json($refunds);
            var months = @json($months);
            
            console.log("Orders Data:", ordersData);
            console.log("Earnings Data:", earningsData);
            console.log("Refunds Data:", refundsData);
            console.log("Months:", months);

            var options = {
                chart: {
                    height: 370,
                    type: "area",
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                series: [
                    {
                        name: "Orders",
                        data: ordersData
                    },
                    {
                        name: "Earnings",
                        data: earningsData
                    },
                    {
                        name: "Refunds",
                        data: refundsData
                    }
                ],
                colors: ["#405189", "#0ab39c", "#f06548"],
                xaxis: {
                    categories: months
                },
                legend: { position: 'bottom' },
                fill: { opacity: 0.1 }
            };

            var chart = new ApexCharts(document.querySelector("#customer_impression_charts"), options);
            chart.render();

            // Update the counter values after chart is rendered
            setTimeout(function() {
                if (ordersData && ordersData.length > 0) {
                    var ordersTotal = ordersData.reduce((a, b) => a + b, 0);
                    document.querySelector('[data-target="7585"]').innerText = ordersTotal;
                }
                if (earningsData && earningsData.length > 0) {
                    var earningsTotal = earningsData.reduce((a, b) => a + b, 0);
                    document.querySelector('[data-target="22.89"]').innerText = (earningsTotal / 1000).toFixed(2);
                }
                if (refundsData && refundsData.length > 0) {
                    var refundsTotal = refundsData.reduce((a, b) => a + b, 0);
                    document.querySelector('[data-target="367"]').innerText = refundsTotal;
                }
            }, 100);
        });
    </script>
@endpush --}}

@push('scripts-bottom')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var chartElement = document.querySelector("#customer_impression_charts");
        if (!chartElement) {
            console.error("Chart container not found!");
            return;
        }

        var ordersData = @json($orders);
        var earningsData = @json($earnings);
        var refundsData = @json($refunds);
        var months = @json($months);

        var options = {
            chart: {
                height: 370,
                type: "line",
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            dataLabels: { 
                enabled: false 
            },
            stroke: { 
                width: [0, 2, 2],
                curve: 'smooth',
                dashArray: [0, 0, 5] // Solid for bars and earnings, dotted for refunds
            },
            series: [
                {
                    name: "Orders",
                    type: "column",
                    data: ordersData
                },
                {
                    name: "Earnings",
                    type: "area", // Filled area
                    data: earningsData
                },
                {
                    name: "Refunds",
                    type: "area", // Regular line
                    data: refundsData
                }
            ],
            colors: ["#2ebdd6ff", "#0ab39c", "#f06548"],
            xaxis: {
                categories: months
            },
            legend: { 
                position: 'bottom'
            },
            fill: {
                type: ['solid', 'gradient', 'solid'],
                opacity: [0.8, 0.1, 0],
                gradient: {
                    shade: 'light',
                    type: "vertical",
                    shadeIntensity: 0.5,
                    gradientToColors: ['#0ab39c'],
                    inverseColors: false,
                    opacityFrom: 0.3,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            plotOptions: {
                bar: {
                    columnWidth: '40%',
                    borderRadius: 5
                }
            },
            markers: {
                size: [0, 5, 4], // Only refunds have markers
                colors: ['#405189', '#0ab39c', '#f06548'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: {
                    size: 6
                }
            },
            tooltip: {
                shared: true,
                intersect: false
            }

        };

        var chart = new ApexCharts(document.querySelector("#customer_impression_charts"), options);
        chart.render();

        // Update counters (same as before)
        setTimeout(function() {
            if (ordersData && ordersData.length > 0) {
                var ordersTotal = ordersData.reduce((a, b) => a + b, 0);
                document.querySelector('[data-target="7585"]').innerText = ordersTotal;
            }
            if (earningsData && earningsData.length > 0) {
                var earningsTotal = earningsData.reduce((a, b) => a + b, 0);
                document.querySelector('[data-target="22.89"]').innerText = (earningsTotal / 1000).toFixed(2);
            }
            if (refundsData && refundsData.length > 0) {
                var refundsTotal = refundsData.reduce((a, b) => a + b, 0);
                document.querySelector('[data-target="367"]').innerText = refundsTotal;
            }
        }, 100);
    });
</script>
@endpush

