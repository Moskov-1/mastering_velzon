@extends('backend.master')
@section('content')

    {{-- PAGE-HEADER --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Create Form</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dynamic page</li>
            </ol>
        </div>
    </div>
    {{-- PAGE-HEADER --}}


    <div class="row">
        <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
            <div class="card box-shadow-0">
                <div class="card-body">
                    <form action="{{ @$page ? route('backend.page.update', @$page->id) : route('backend.page.store')}}" method="POST">
                        @csrf
                        @if (@$page)
                            @method('PATCH')
                        @endif

                        <div class="mb-3">
                            <label for="page_title" class="form-label">Page Title</label>
                            <input type="text" name="page_title" id="page_title" class="form-control"
                                value="{{ old('page_title') }}" placeholder="page_title">
                            @error('page_title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="page_content" class="form-label">Page Content</label>
                            <textarea name="page_content" id="page_content" class="form-control ck-editor" rows="5"
                                placeholder="page_content">{{ old('page_content') }}</textarea>
                            @error('page_content')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection