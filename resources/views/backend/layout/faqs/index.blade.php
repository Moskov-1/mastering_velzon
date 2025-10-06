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
                        <table class="table align-middle table-nowrap data-table table-striped mb-0" id="faq-table">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="wd-15p border-bottom-0">ID</th>
                                    <th class="wd-15p border-bottom-0">Question</th>
                                    <th class="wd-15p border-bottom-0">Question</th>
                                    <th class="wd-15p border-bottom-0">Priority</th>
                                    <th class="wd-15p border-bottom-0">Status</th>
                                    <th class="wd-15p border-bottom-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                             
                            </tbody>
                        </table>
                        <!--end table-->
                        <div class="noresult" style="display: none">
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" 
                                style="width:75px;height:75px"></lord-icon>
                                <h5 class="mt-2">Sorry! No Result Found</h5>
                                <p class="text-muted mb-0">We've searched more than 200k+ tasks We did not find any tasks for you search.</p>
                            </div>
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

@push('scripts-top')

@endpush
@push('scripts-bottom')

    <script>
    (function($){
        $(function () {
            $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('backend.faq.index') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'question', name: 'question'},
                    {data: 'answer', name: 'answer'},
                    {data: 'priority', name: 'priority'},
                    {data: 'status', name: 'status', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        });
    })(jQuery);


    function editFaq(id) {
        window.location.href = "/admin/faq/" + id + "/edit";
    }

    function deleteData(url) {
        if(confirm("Are you sure you want to delete this FAQ?")) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {_token: "{{ csrf_token() }}"},
                success: function () {
                    $('.data-table').DataTable().ajax.reload();
                }
            });
        }
    }
    </script>
@endpush