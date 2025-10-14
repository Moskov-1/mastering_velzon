@extends('backend.master')
@section('content')
<!-- start page title -->
        <div class="row">
                <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                        <h4 class="mb-sm-0">Create FAQ</h4>
                                        <a href="{{ route('backend.system-user.index') }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="mdi mdi-arrow-left"></i> Back
                                        </a>
                                </div>

                                <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                                <li class="breadcrumb-item"><a href="javascript:void(0);">Admin User</a></li>
                                                <li class="breadcrumb-item active">Create Admin User</li>
                                        </ol>
                                </div>
                        </div>
                </div>
        </div>
        <!-- end page title -->

        <form method="post" action="{{ @$system_user ? route('backend.system-user.update', @$system_user->id) : route('backend.system-user.store')}}"
                class="row">
                @csrf
                @if (@$system_user)
                        @method('PATCH')
                @endif
                <div class="col-lg-8">
                        <div class="card">
                                <div class="card-body">
                                        <div class="row">
                                                <div class="col-lg-6">
                                                        <div class="mb-3">
                                                                <label class="form-label" for="project-title-input">Name</label>
                                                                <input type="text"
                                                                 name="name"
                                                                        value="{{old('name', @$system_user->name)}}"
                                                                        class="form-control @error('name') is-invalid @enderror"
                                                                        placeholder="Enter user name">
                                                                @error('name')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                        </div>
                                                </div>
                                                <div class="col-lg-6">
                                                        <div class="mb-3 mb-lg-0">
                                                                <label for="email"
                                                                        class="form-label">Email</label>
                                                                <input type="email" name="email" {{@$system_user ? 'disabled' : ''}}
                                                                        value="{{old('email', @$system_user->email)}}"
                                                                        class="form-control @error('email') is-invalid @enderror">
                                                                @error('email')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                        </div>
                                                </div>
                                        </div>
                                        <input type="text" name="is_admin_user" hidden value="1">
                                        <div class="row">
                                                <div class="col-lg-6">
                                                        <div class="mb-3 mb-lg-0">
                                                                <label for="email"
                                                                        class="form-label">Password</label>
                                                                <input type="text" name="password" 
                                                                        class="form-control @error('password') is-invalid @enderror">
                                                                @error('password')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                                <!-- end card body -->
                        </div>
                        <!-- end card -->


                        <!-- end card -->
                        <div class="text-end mb-4">
                                <a href="{{route('backend.system-user.index')}}" class="btn btn-danger w-sm">Cancel</a>
                                {{-- <button type="submit" class="btn btn-secondary w-sm">Draft</button> --}}
                                <button type="submit" class="btn btn-success w-sm">{{@$system_user ? 'Update' : 'Create'}}</button>
                        </div>
                </div>
                <!-- end col -->
        </form>
        <!-- end row -->
@endsection
@push('styles-top')
{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts-bottom')
{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#role').select2({
            placeholder: "Select roles",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush