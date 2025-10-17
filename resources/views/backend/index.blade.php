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
@push('scripts-bottom')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Sample full dataset (you would get this from your backend)
        const fullDataset = {
            orders: @json($orders),
            earnings: @json($earnings),
            refunds: @json($refunds),
            months: @json($months)
        };

        // Generate larger sample data for demonstration
        const generateSampleData = () => {
            const allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const years = ['2022', '2023', '2024'];
            
            let allData = {
                orders: [],
                earnings: [],
                refunds: [],
                months: [],
                dates: []
            };

            years.forEach(year => {
                allMonths.forEach((month, index) => {
                    allData.months.push(`${month} ${year}`);
                    allData.dates.push(new Date(parseInt(year), index, 1));
                    allData.orders.push(Math.floor(Math.random() * 1000) + 500);
                    allData.earnings.push(Math.floor(Math.random() * 3000) + 1000);
                    allData.refunds.push(Math.floor(Math.random() * 50) + 10);
                });
            });

            return allData;
        };

        // Use provided data or generate sample data
        const completeData = fullDataset.months && fullDataset.months.length > 0 ? fullDataset : generateSampleData();
        
        let currentChart = null;
        let currentChartType = 'area';
        let currentFilter = 'all';
        let filteredData = { ...completeData };

        // Chart configurations
        const chartConfigs = {
            area: {
                chart: {
                    height: 370,
                    type: "area",
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                series: [
                    { name: "Orders", data: [] },
                    { name: "Earnings", data: [] },
                    { name: "Refunds", data: [] }
                ],
                colors: ["#405189", "#0ab39c", "#f06548"],
                xaxis: { categories: [] },
                legend: { position: 'bottom' },
                fill: { opacity: 0.1 },
                markers: {
                    size: 0,
                    hover: { size: 0 }
                }
            },
            mixed: {
                chart: {
                    height: 370,
                    type: "line",
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                dataLabels: { enabled: false },
                stroke: { 
                    width: [0, 2, 2],
                    curve: 'smooth',
                    dashArray: [0, 0, 5]
                },
                series: [
                    { name: "Orders", type: "column", data: [] },
                    { name: "Earnings", type: "area", data: [] },
                    { name: "Refunds", type: "area", data: [] }
                ],
                colors: ["#2ebdd6ff", "#0ab39c", "#f06548"],
                xaxis: { categories: [] },
                legend: { position: 'bottom' },
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
                    size: [0, 5, 4],
                    colors: ['#405189', '#0ab39c', '#f06548'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: { size: 6 }
                }
            }
        };

        // Filter data based on time range
        function filterData(timeRange, customStart = null, customEnd = null) {
            let filtered = {
                orders: [],
                earnings: [],
                refunds: [],
                months: []
            };

            const now = new Date();
            let startDate;

            switch(timeRange) {
                case '1M':
                    startDate = new Date(now.getFullYear(), now.getMonth() - 1, now.getDate());
                    break;
                case '6M':
                    startDate = new Date(now.getFullYear(), now.getMonth() - 6, now.getDate());
                    break;
                case '1Y':
                    startDate = new Date(now.getFullYear() - 1, now.getMonth(), now.getDate());
                    break;
                case 'custom':
                    if (customStart && customEnd) {
                        startDate = new Date(customStart);
                        const endDate = new Date(customEnd);
                        // Filter based on custom date range
                        completeData.dates.forEach((date, index) => {
                            if (date >= startDate && date <= endDate) {
                                filtered.orders.push(completeData.orders[index]);
                                filtered.earnings.push(completeData.earnings[index]);
                                filtered.refunds.push(completeData.refunds[index]);
                                filtered.months.push(completeData.months[index]);
                            }
                        });
                        return filtered;
                    }
                    // Fall through to 'all' if no custom dates provided
                case 'all':
                default:
                    return { ...completeData };
            }

            // Filter data for predefined ranges
            completeData.dates.forEach((date, index) => {
                if (date >= startDate) {
                    filtered.orders.push(completeData.orders[index]);
                    filtered.earnings.push(completeData.earnings[index]);
                    filtered.refunds.push(completeData.refunds[index]);
                    filtered.months.push(completeData.months[index]);
                }
            });

            return filtered;
        }

        // Initialize or update chart
        function initializeChart(type, data) {
            const chartElement = document.querySelector("#customer_impression_charts");
            if (!chartElement) {
                console.error("Chart container not found!");
                return;
            }

            // Destroy existing chart
            if (currentChart) {
                currentChart.destroy();
            }

            // Update chart configuration with current data
            const options = JSON.parse(JSON.stringify(chartConfigs[type]));
            options.series[0].data = data.orders;
            options.series[1].data = data.earnings;
            options.series[2].data = data.refunds;
            options.xaxis.categories = data.months;

            // Create new chart
            currentChart = new ApexCharts(chartElement, options);
            currentChart.render();
            currentChartType = type;

            // Update button states
            updateButtonStates(type, currentFilter);
        }

        // Update button active states
        function updateButtonStates(chartType, filterType) {
            // Update chart type buttons
            document.querySelectorAll('.chart-switcher').forEach(button => {
                if (button.dataset.chartType === chartType) {
                    button.classList.remove('btn-soft-secondary');
                    button.classList.add('btn-soft-primary');
                } else {
                    button.classList.remove('btn-soft-primary');
                    button.classList.add('btn-soft-secondary');
                }
            });

            // Update time filter buttons
            document.querySelectorAll('.time-filter').forEach(button => {
                if (button.dataset.filter === filterType) {
                    button.classList.remove('btn-soft-secondary');
                    button.classList.add('btn-soft-primary');
                } else {
                    button.classList.remove('btn-soft-primary');
                    button.classList.add('btn-soft-secondary');
                }
            });
        }

        // Update counters
        function updateCounters(data) {
            setTimeout(function() {
                if (data.orders && data.orders.length > 0) {
                    const ordersTotal = data.orders.reduce((a, b) => a + b, 0);
                    document.querySelector('[data-target="7585"]').innerText = ordersTotal.toLocaleString();
                }
                if (data.earnings && data.earnings.length > 0) {
                    const earningsTotal = data.earnings.reduce((a, b) => a + b, 0);
                    document.querySelector('[data-target="22.89"]').innerText = (earningsTotal / 1000).toFixed(2);
                }
                if (data.refunds && data.refunds.length > 0) {
                    const refundsTotal = data.refunds.reduce((a, b) => a + b, 0);
                    document.querySelector('[data-target="367"]').innerText = refundsTotal;
                }
                
                // Update conversation ratio (you can modify this calculation)
                if (data.orders && data.orders.length > 0 && data.earnings && data.earnings.length > 0) {
                    const avgOrderValue = data.earnings.reduce((a, b) => a + b, 0) / data.orders.reduce((a, b) => a + b, 0);
                    const conversationRatio = (avgOrderValue * 0.1).toFixed(2); // Example calculation
                    document.querySelector('[data-target="18.92"]').innerText = conversationRatio;
                }
            }, 100);
        }

        // Apply time filter
        function applyTimeFilter(filterType, customStart = null, customEnd = null) {
            filteredData = filterData(filterType, customStart, customEnd);
            currentFilter = filterType;
            initializeChart(currentChartType, filteredData);
            updateCounters(filteredData);
        }

        // Event Listeners
        document.querySelectorAll('.chart-switcher').forEach(button => {
            button.addEventListener('click', function() {
                const chartType = this.dataset.chartType;
                currentChartType = chartType;
                initializeChart(chartType, filteredData);
            });
        });

        document.querySelectorAll('.time-filter').forEach(button => {
            button.addEventListener('click', function() {
                const filterType = this.dataset.filter;
                if (filterType === 'custom') {
                    // Show custom date modal
                    const modal = new bootstrap.Modal(document.getElementById('customDateModal'));
                    modal.show();
                } else {
                    applyTimeFilter(filterType);
                }
            });
        });

        // Custom date range application
        document.getElementById('applyCustomDate').addEventListener('click', function() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            if (!startDate || !endDate) {
                alert('Please select both start and end dates');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date cannot be after end date');
                return;
            }

            applyTimeFilter('custom', startDate, endDate);
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('customDateModal'));
            modal.hide();
        });

        // Set default dates in custom modal
        document.getElementById('customFilterBtn').addEventListener('click', function() {
            const now = new Date();
            const oneMonthAgo = new Date(now.getFullYear(), now.getMonth() - 1, now.getDate());
            
            document.getElementById('startDate').value = oneMonthAgo.toISOString().split('T')[0];
            document.getElementById('endDate').value = now.toISOString().split('T')[0];
        });

        // Initialize with default chart and filter
        applyTimeFilter(currentFilter);

        // Debug logs
        console.log("Complete Data:", completeData);
        console.log("Filtered Data:", filteredData);
    });
</script>
@endpush
