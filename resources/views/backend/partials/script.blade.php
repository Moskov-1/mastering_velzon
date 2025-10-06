<!-- JAVASCRIPT -->
<!-- jQuery (required) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Dropify JS -->
<script src="https://cdn.jsdelivr.net/npm/dropify@0.2.2/dist/js/dropify.min.js"></script>

<script src="{{asset('')}}assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="{{asset('')}}assets/libs/simplebar/simplebar.min.js"></script>
<script src="{{asset('')}}assets/libs/node-waves/waves.min.js"></script>
<script src="{{asset('')}}assets/libs/feather-icons/feather.min.js"></script>
<script src="{{asset('')}}assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
<script src="{{asset('')}}assets/js/plugins.js"></script>


<!-- apexcharts -->
<script src="{{asset('')}}assets/libs/apexcharts/apexcharts.min.js"></script>

<!-- Vector map-->
<script src="{{asset('')}}assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
<script src="{{asset('')}}assets/libs/jsvectormap/maps/world-merc.js"></script>

<!--Swiper slider js--> 
<script src="{{asset('')}}assets/libs/swiper/swiper-bundle.min.js"></script>

<!--Swiper sweet alert 2 js-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Dashboard init -->
<script src="{{asset('')}}assets/js/pages/dashboard-ecommerce.init.js"></script>


<!-- DataTables with Bootstrap 5 -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- App js -->
<script src="{{asset('')}}assets/js/app.js"></script>


<script>
    $(document).ready(function(){
        // Initialize Dropify

        // Optional events
        let drEvent = $('.dropify').dropify({
            messages: {
                'default': 'Drag and drop a file',
                'replace': 'Drag and drop or click to replace',
                'remove':  'Remove file',
                'error':   'Oops, something wrong happened.'
            }
        });

        drEvent.on('dropify.beforeClear', function(event, element){
            return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
        });

        drEvent.on('dropify.afterClear', function(event, element){
            alert('File deleted');
        });
    });
</script>

@if(session('success'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: '{{ session('error') }}',
        showConfirmButton: false,
        timer: 3000
    });
</script>
@endif

@if (@$errors->any())
<script>
    @foreach ($errors->all() as $error)
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ $error }}',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    @endforeach
</script>
@endif