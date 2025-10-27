@extends('backend.master')
@section('content')

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-sm-0">Category</h4>
                <ol class="breadcrumb m-0 small text-muted">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Features</a></li>
                    <li class="breadcrumb-item active">Category</li>
                </ol>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('backend.feature.category.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="mdi mdi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>
<!-- End Page Title -->

<div class="row justify-content-center mt-4">
    <div class="col-lg-7 col-md-10">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 text-white">{{ @$category ? 'Edit Category' : 'Create Category' }}</h5>
            </div>

            <div class="card-body p-4">
                <form method="post"
                      action="{{ @$category ? route('backend.feature.category.update', @$category->id) : route('backend.feature.category.store') }}"
                      enctype="multipart/form-data">
                    @csrf
                    @if (@$category)
                        @method('PATCH')
                    @endif
                    <div class=" row">
                        <!-- Name -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-semibold">Name</label>
                            <input type="text"
                                id="name"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Enter category name"
                                value="{{ old('name', @$category->name) }}">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label fw-semibold">Type</label>
                            <select id="type"
                                    name="type"
                                    class="form-select @error('type') is-invalid @enderror">
                                <option value="">Select Type</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ old('type', @$category->type) == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Parent Category -->
                        <div class="col-md-6 mb-3">
                            <label for="parent_id" class="form-label fw-semibold">Parent Category</label>
                            <select id="parent_id"
                                    name="parent_id"
                                    class="form-select @error('parent_id') is-invalid @enderror">
                                <option value="">Select Parent Category</option>
                                @foreach($parents as $item)
                                    <option value="{{ $item->id }}" {{ old('parent_id', @$category->parent_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select 
                                    name="status"
                                    class="form-select @error('status') is-invalid @enderror">
                                <option value="">Select Status</option>
                                @foreach($statuses as $key => $status)
                                    <option value="{{ $status }}" {{ old('status', @$category->status) == $status ? 'selected' : '' }}>
                                        {{ ucfirst($key) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
 

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="mdi mdi-content-save-outline me-1"></i>
                            {{ @$category ? 'Update' : 'Submit' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
