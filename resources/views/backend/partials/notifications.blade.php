
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let html = document.documentElement; // <html>
        // Load saved mode
        let savedMode = localStorage.getItem("layout-mode");
        if (savedMode) {
            html.setAttribute("data-layout-mode", savedMode);
        }

        // Toggle on click
        let toggleBtn = document.querySelector(".light-dark-mode");
        if (toggleBtn) {
            toggleBtn.addEventListener("click", function () {

                let current = html.getAttribute("data-layout-mode");
                console.log(current)
                let newMode = current === "dark" ? "dark" : "light";
                console.log(current)
                html.setAttribute("data-layout-mode", newMode);
                localStorage.setItem("layout-mode", newMode);
            });
        }
    });
</script>