@extends('backend.master')
@section('title', 'Dashboard | faq form')

@section('content')

        <!-- start page title -->
        <div class="row">
                <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                        <h4 class="mb-sm-0">Create FAQ</h4>
                                        <a href="{{ route('backend.feature.faq.index') }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="mdi mdi-arrow-left"></i> Back
                                        </a>
                                </div>

                                <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                                <li class="breadcrumb-item"><a href="javascript:void(0);">FAQ</a></li>
                                                <li class="breadcrumb-item active">Create FAQ</li>
                                        </ol>
                                </div>
                        </div>
                </div>
        </div>
        <!-- end page title -->

        <form method="post" action="{{ @$faq ? route('backend.feature.faq.update', @$faq->id) : route('backend.feature.faq.store')}}"
                class="row">
                @csrf
                @if (@$faq)
                        @method('PATCH')
                @endif
                <div class="col-lg-8">
                        <div class="card">
                                <div class="card-body">
                                        <div class="row">
                                                <div class="col-lg-6">
                                                        <div class="mb-3">
                                                                <label class="form-label" for="project-title-input">FAQ
                                                                        Question</label>
                                                                <input type="text" name="question"
                                                                        value="{{old('question', @$faq->question)}}"
                                                                        class="form-control @error('title') is-invalid @enderror"
                                                                        name="title" id="project-title-input"
                                                                        placeholder="Enter project title">
                                                                @error('title')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                        </div>
                                                </div>
                                                <div class="col-lg-6">
                                                        <div class="mb-3 mb-lg-0">
                                                                <label for="choices-priority-input"
                                                                        class="form-label">Priority</label>
                                                                <input type="number" name="priority"
                                                                        value="{{old('priority', @$faq->priority)}}"
                                                                        class="form-control @error('priority') is-invalid @enderror">
                                                                @error('priority')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="row">
                                                <div class="mb-3">
                                                        <label class="form-label">FAQ Answer</label>
                                                        <textarea name='answer' id="ckeditor-classic">{{old('answer', @$aq->answer)}}
                                                                </textarea>
                                                        @error('answer')
                                                                <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                </div>
                                        </div>
                                </div>
                                <!-- end card body -->
                        </div>
                        <!-- end card -->


                        <!-- end card -->
                        <div class="text-end mb-4">
                                <a href="{{route('backend.feature.faq.index')}}" class="btn btn-danger w-sm">Cancel</a>
                                {{-- <button type="submit" class="btn btn-secondary w-sm">Draft</button> --}}
                                <button type="submit" class="btn btn-success w-sm">{{@$faq ? 'Update' : 'Create'}}</button>
                        </div>
                </div>
                <!-- end col -->
                <div class="col-lg-4">
                        <div class="card">
                                <div class="card-header">
                                        <h5 class="card-title mb-0">Visibility</h5>
                                </div>
                                <div class="card-body">
                                        <div>
                                                <label for="choices-privacy-status-input" class="form-label">Status</label>
                                                <select name="status" class="form-select" data-choices data-choices-search-false
                                                        id="choices-privacy-status-input">
                                                        <option value="" disabled selected>Select Option</option>
                                                        @foreach ($status as $key => $item)
                                                                <option value="{{$item}}" {{old('status', @$faq->status) ? 'selected' : ''}}>{{$key}}</option>
                                                        @endforeach
                                                </select>
                                        </div>
                                </div>
                                <!-- end card body -->
                        </div>
                        <!-- end card -->
                        {{-- <div class="card">
                                <div class="card-header">
                                        <h5 class="card-title mb-0">Thumbnail</h5>
                                </div>
                                <div class="card-body">
                                        <p class="text-muted">Add an image file here.</p>
                                        <input type="file" name="image" class="dropify" data-height="200" />
                                </div>
                        </div> --}}
                </div>
                <!-- end col -->
        </form>
        <!-- end row -->

@endsection
@push('style-bottom')
        <style>
                .dropify-wrapper .dropify-message p {
                        line-height: 2;
                        /* increase spacing */
                        font-size: 16px;
                        /* adjust font size if needed */
                        color: #555;
                        /* custom text color */
                }
        </style>
@endpush

@push('scripts-top')

        <!-- ckeditor -->
        <script src="{{asset('assets/libs/%40ckeditor/ckeditor5-build-classic/build/ckeditor.js')}}"></script>

        <!-- project-create init -->
        <script src="{{asset('')}}assets/js/pages/project-create.init.js"></script>
@endpush