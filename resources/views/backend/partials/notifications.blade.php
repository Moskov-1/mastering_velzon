
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