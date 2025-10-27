@extends('backend.master')
@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Category</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">something</a></li>
                    <li class="breadcrumb-item active">Category</li>
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
                    <div class="d-flex justify-content-center mt-3 mt-lg-0">
                        <div class="col-lg-8"> <!-- you can change 8 to 6 or 10 depending on desired width -->
                            <form method="post" action="{{ @$category ? route('backend.feature.category.update') : route('backend.feature.category.store')}}"
                                enctype="multipart/form-data" class="card p-4 shadow-sm">
                                @csrf
                                @if (@$category)
                                @method('PATCH')
                                @endif

                                <div class="row mb-4">
                                    <label for="name" class="col-md-3 form-label">Name</label>
                                    <div class="col-md-5">
                                        <input class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" placeholder="Enter your mail mailer"
                                            type="text" value="{{ old('name', @$category->name) }}">
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="type" class="col-md-3 form-label">Type</label>
                                    <div class="col-md-5">
                                        <select class="form-control @error('type') is-invalid @enderror" id="type" name="type">
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
                                </div>

                                <div class="row mb-4">
                                    <label for="parent_id" class="col-md-3 form-label">Parent Category</label>
                                    <div class="col-md-5">
                                        <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
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
                                </div>

                                <div class="row mb-4">
                                    <label for="status" class="col-md-3 form-label">Status</label>
                                    <div class="col-md-5">
                                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                            <option value="">Select Status</option>
                                            @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ old('status', @$category->status) == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>



                                <div class="row justify-content-end">
                                    <div class="col-sm-9">
                                        <button class="btn btn-primary" type="submit">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
                <!--end col-->
            </div>


        </div>
        <!-- end .h-100-->

    </div>
    <!-- end col -->

</div>

@endsection