@extends('backend.master')
@section('title', 'Dashboard | project form')

@section('content')
        
        <!-- start page title -->
        <div class="row">
                <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">Create Project</h4>

                                <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                                <li class="breadcrumb-item"><a href="javascript: void(0);">Project</a></li>
                                                <li class="breadcrumb-item active">Create Project</li>
                                        </ol>
                                </div>
                        </div>
                </div>
        </div>
        <!-- end page title -->

        <form method="post" action="{{ @$project ? route('backend.project.update',['id' => @$project->id]) : route('backend.project.store')}}"
        class="row">
                @csrf
                <div class="col-lg-8">
                        <div class="card">
                                <div class="card-body">
                                        <div class="row">
                                                <div class="col-lg-6">
                                                        <div class="mb-3">
                                                                <label class="form-label" for="project-title-input">Project Title</label>
                                                                <input type="text" class="form-control" id="project-title-input" placeholder="Enter project title">
                                                        </div>
                                                </div>
                                                <div class="col-lg-6">
                                                        <div class="mb-3 mb-lg-0">
                                                        <label for="choices-priority-input" class="form-label">Priority</label>
                                                        <input type="number" class="form-control">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        <!-- end card body -->
                        </div>
                        <!-- end card -->

                        <div class="card">
                                <div class="card-header">
                                        <h5 class="card-title mb-0">Thumbnail</h5>
                                </div>
                                <div class="card-body">
                                        <p class="text-muted">Add an image file here.</p>
                                        <input type="file" name="image" class="dropify" data-height="200" />
                                </div>
                        </div>
                        <!-- end card -->
                        <div class="text-end mb-4">
                        <button type="submit" class="btn btn-danger w-sm">Delete</button>
                        {{-- <button type="submit" class="btn btn-secondary w-sm">Draft</button> --}}
                        <button type="submit" class="btn btn-success w-sm">Create</button>
                        </div>
                </div>
                <!-- end col -->
                <div class="col-lg-4">
                        <div class="card">
                                <div class="card-header">
                                        <h5 class="card-title mb-0">Privacy</h5>
                                </div>
                                <div class="card-body">
                                        <div>
                                        <label for="choices-privacy-status-input" class="form-label">Status</label>
                                        <select class="form-select" data-choices data-choices-search-false id="choices-privacy-status-input">
                                                <option value="Private" selected>Private</option>
                                                <option value="Team">Team</option>
                                                <option value="Public">Public</option>
                                        </select>
                                        </div>
                                </div>
                                <!-- end card body -->
                        </div>
                        <!-- end card -->
                </div>
                <!-- end col -->
        </form>
        <!-- end row -->

@endsection