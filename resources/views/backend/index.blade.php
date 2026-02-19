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
               
                @include('backend.partials.charts.sales-months')
           

                
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
    // 1. Get REAL data from backend
    const chartData = @json($chart_data);
    
    // 2. Add dates array for filtering (parsed from month labels)
    chartData.dates = chartData.months.map(label => {
        const [month, year] = label.split(' ');
        return new Date(`${month} 1, ${year}`);
    });

    // 3. Chart configuration with dual Y-axes
    const getChartOptions = (type, data) => {
        return {
            series: [
                { name: "Orders", data: data.orders },
                { name: "Earnings", data: data.earnings },
                { name: "Refunds", data: data.refunds }
            ],
            chart: {
                height: 370,
                type: type === 'area' ? 'area' : 'line',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            dataLabels: { enabled: false },
            stroke: type === 'area' 
                ? { curve: 'smooth', width: 2 }
                : { 
                    width: [0, 2, 2],
                    curve: 'smooth',
                    dashArray: [0, 0, 5]
                },
            colors: ["#405189", "#0ab39c", "#f06548"],
            xaxis: { categories: data.months },
            yaxis: [
                {
                    seriesName: 'Orders',
                    show: true,
                    labels: { formatter: val => Math.round(val) }
                },
                {
                    seriesName: 'Earnings',
                    show: true,
                    opposite: true,
                    labels: { 
                        formatter: val => val >= 1000 
                            ? (val/1000).toFixed(1) + 'K' 
                            : Math.round(val)
                    }
                }
            ],
            legend: { position: 'bottom' },
            fill: type === 'area'
                ? { opacity: 0.1 }
                : {
                    type: ['solid', 'gradient', 'solid'],
                    opacity: [0.8, 0.1, 0],
                    gradient: {
                        shade: 'light',
                        type: "vertical",
                        shadeIntensity: 0.5,
                        gradientToColors: ['#0ab39c'],
                        stops: [0, 90, 100]
                    }
                },
            markers: type === 'area'
                ? { size: 0 }
                : {
                    size: [0, 5, 4],
                    colors: ['#405189', '#0ab39c', '#f06548'],
                    strokeColors: '#fff',
                    strokeWidth: 2
                },
            plotOptions: type === 'mixed' ? {
                bar: { columnWidth: '40%', borderRadius: 5 }
            } : {}
        };
    };

    // 4. Filtering function
    const filterData = (range) => {
        const now = new Date();
        let startDate;
        
        switch(range) {
            case '1M': startDate = new Date(now.getFullYear(), now.getMonth() - 1, 1); break;
            case '6M': startDate = new Date(now.getFullYear(), now.getMonth() - 6, 1); break;
            case '1Y': startDate = new Date(now.getFullYear() - 1, now.getMonth(), 1); break;
            case 'custom': 
                const start = new Date(document.getElementById('start-date').value);
                const end = new Date(document.getElementById('end-date').value);
                return {
                    months: chartData.months.filter((_, i) => chartData.dates[i] >= start && chartData.dates[i] <= end),
                    orders: chartData.orders.filter((_, i) => chartData.dates[i] >= start && chartData.dates[i] <= end),
                    earnings: chartData.earnings.filter((_, i) => chartData.dates[i] >= start && chartData.dates[i] <= end),
                    refunds: chartData.refunds.filter((_, i) => chartData.dates[i] >= start && chartData.dates[i] <= end)
                };
            default: return chartData;
        }
        
        return {
            months: chartData.months.filter((_, i) => chartData.dates[i] >= startDate),
            orders: chartData.orders.filter((_, i) => chartData.dates[i] >= startDate),
            earnings: chartData.earnings.filter((_, i) => chartData.dates[i] >= startDate),
            refunds: chartData.refunds.filter((_, i) => chartData.dates[i] >= startDate)
        };
    };

    // 5. Initialize chart
    let chart = null;
    let currentType = 'area';
    
    const renderChart = (type, data) => {
        if(chart) chart.destroy();
        
        const options = getChartOptions(type, data);
        chart = new ApexCharts(document.querySelector("#sales-analytics-chart"), options);
        chart.render();
    };

    // 6. Event listeners
    document.querySelectorAll('.chart-switcher').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.chart-switcher').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentType = btn.dataset.chartType;
            renderChart(currentType, filterData(document.querySelector('.time-filter.active').dataset.filter));
        });
    });

    document.querySelectorAll('.time-filter').forEach(btn => {
        btn.addEventListener('click', () => {
            if(btn.dataset.filter === 'custom') return;
            
            document.querySelectorAll('.time-filter').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            renderChart(currentType, filterData(btn.dataset.filter));
        });
    });

    document.getElementById('apply-custom-dates').addEventListener('click', () => {
        const start = document.getElementById('start-date').value;
        const end = document.getElementById('end-date').value;
        
        if(!start || !end) {
            alert('Please select both dates');
            return;
        }
        
        if(new Date(start) > new Date(end)) {
            alert('Start date cannot be after end date');
            return;
        }
        
        document.querySelectorAll('.time-filter').forEach(b => b.classList.remove('active'));
        document.querySelector(`[data-filter="custom"]`).classList.add('active');
        
        renderChart(currentType, filterData('custom'));
        bootstrap.Modal.getInstance(document.getElementById('customDateModal')).hide();
    });

    // 7. Set default dates in modal
    document.querySelector('[data-filter="custom"]').addEventListener('click', () => {
        const now = new Date();
        const oneMonthAgo = new Date(now.getFullYear(), now.getMonth() - 1, 1);
        
        document.getElementById('start-date').valueAsDate = oneMonthAgo;
        document.getElementById('end-date').valueAsDate = now;
    });

    // 8. Initialize with default view
    renderChart(currentType, chartData);
});
</script>
@endpush