@extends('backend.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-sync-alt fa-3x text-primary"></i>
                    </div>
                    <h4 class="card-title mb-3">Clear Application Cache</h4>
                    <p class="text-muted mb-4">
                        This will clear all Laravel cache including config, routes, views, and application cache.
                    </p>
                    <button id="clearCacheBtn" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-broom me-2"></i>Clear Cache
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts-bottom')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('clearCacheBtn').addEventListener('click', function() {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will clear all application caches!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, clear it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("{{ route('clear.cache') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cache Cleared!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Operation Failed',
                        text: data.message || 'Something went wrong!'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Unable to connect to server'
                });
            });
        }
    });
});
</script>
@endpush