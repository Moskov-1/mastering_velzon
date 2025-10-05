<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">


<!-- Mirrored from themesbrand.com/velzon/html/default/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 25 Aug 2022 17:02:18 GMT -->
<head>

    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    @stack('styles-top')
    @include('backend.partials.style')
    @stack('styles-bottom')

</head>

<body>
    <div id="layout-wrapper">
        <!-- topbar -->
        @include('backend.partials.topbar')
        <!-- ========== App Menu ========== -->
        @include('backend.partials.sidebar')
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>
        <div class="main-content">
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
     
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                    
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            @include('backend.partials.footer')
        </div>
        <!-- end main content-->

    </div>
    @stack('scripts-top')
    <!-- END layout-wrapper -->
    @include('backend.partials.script')
    @stack('scripts-bottom')
</body>


<!-- Mirrored from themesbrand.com/velzon/html/default/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 25 Aug 2022 17:03:13 GMT -->
</html>