
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{$settings->icon ? asset($settings->icon) : asset('assets/images/favicon.ico')}}">

    <!-- jsvectormap css -->
    <link href="{{asset('')}}assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />

    <!--Swiper slider css-->
    <link href="{{asset('')}}assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropify@0.2.2/dist/css/dropify.min.css" />
    {{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css"> --}}
    {{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css"> --}}

    <!-- yajra datatable -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
        {{-- Toastr CSS --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <!-- Layout config Js -->
    <script src="{{asset('')}}assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="{{asset('')}}assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{asset('')}}assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{asset('')}}assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{asset('')}}assets/css/custom.min.css" rel="stylesheet" type="text/css" />

    <style>
        .data-table th,
        .data-table td {
            text-align: center !important;
            vertical-align: middle !important;
        }
         .toast {
            background-color: #333 !important;
            color: #fff !important;
            font-weight: 500;
        }
    </style>