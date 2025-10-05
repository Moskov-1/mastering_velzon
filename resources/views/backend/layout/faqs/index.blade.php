@extends('backend.master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="tasksList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">All FAQs</h5>
                        <div class="flex-shrink-0">
                            <button class="btn btn-danger add-btn" data-bs-toggle="modal" data-bs-target="#showModal">
                            <i class="ri-add-line align-bottom me-1"></i> Create FAQ</button>
                            <button class="btn btn-soft-danger" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
                        </div>
                    </div>
                </div>
               
                <!--end card-body-->
                <div class="card-body">
                    <div class="table-responsive table-card mb-4">
                        <table class="table align-middle table-nowrap mb-0" id="tasksTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th scope="col" style="width: 40px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th class="sort" data-sort="id">ID</th>
                                    <th class="sort" data-sort="project_name">Project</th>
                                    <th class="sort" data-sort="tasks_name">Task</th>
                                    <th class="sort" data-sort="client_name">Client Name</th>
                                    <th class="sort" data-sort="assignedto">Assigned To</th>
                                    <th class="sort" data-sort="due_date">Due Date</th>
                                    <th class="sort" data-sort="status">Status</th>
                                    <th class="sort" data-sort="priority">Priority</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                             
                            </tbody>
                        </table>
                        <!--end table-->
                        <div class="noresult" style="display: none">
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                <h5 class="mt-2">Sorry! No Result Found</h5>
                                <p class="text-muted mb-0">We've searched more than 200k+ tasks We did not find any tasks for you search.</p>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <div class="pagination-wrap hstack gap-2">
                            <a class="page-item pagination-prev disabled" href="#">
                                Previous
                            </a>
                            <ul class="pagination listjs-pagination mb-0"></ul>
                            <a class="page-item pagination-next" href="#">
                                Next
                            </a>
                        </div>
                    </div>
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection
