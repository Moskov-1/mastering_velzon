@extends('backend.master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="tasksList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">All FAQs</h5>
                        <div class="flex-shrink-0">
                            <a class="btn btn-danger add-btn" href="{{route('backend.feature.faq.create')}}">
                                <i class="ri-add-line align-bottom me-1"></i> Create FAQ
                            </a>

                            {{-- <button class="btn btn-soft-danger" onClick="deleteMultiple()"><i
                                    class="ri-delete-bin-2-line"></i></button> --}}
                        </div>
                    </div>
                </div>

                <!--end card-body-->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">FAQ List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-card mb-4">
                            <table class="table align-middle table-nowrap table-striped mb-0 data-table">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th class="wd-10p border-bottom-0">ID</th>
                                        <th class="wd-30p border-bottom-0">Question</th>
                                        <th class="wd-30p border-bottom-0">Answer</th>
                                        <th class="wd-10p border-bottom-0">Priority</th>
                                        <th class="wd-10p border-bottom-0">Status</th>
                                        <th class="wd-10p border-bottom-0">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all"></tbody>
                            </table>
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
        (function ($) {
            $(function () {
                $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: { details: true },

                    ajax: "{{ route('backend.feature.faq.index') }}",
                    columns: [
                         { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'question', name: 'question' },
                        { data: 'answer', name: 'answer' },
                        { data: 'priority', name: 'priority' },
                        { data: 'status', name: 'status', orderable: false, searchable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });
            });
        })(jQuery);
        $(document).on('shown.bs.collapse shown.bs.tab', function () {
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust()
                .responsive.recalc();
        });
        function statusFaq(id) {
            let url = "{{ route('backend.feature.faq.status', ':id') }}";
            $.ajax({
                type: "POST",
                url: url.replace(':id', id),
                data: {
                    id: id,
                    _token: "{{csrf_token()}}"
                },
                success: function (response) {
                    console.log(response);
                    // Reloade DataTable
                    $('.datatable').DataTable().ajax.reload();
                    if (response.success) {
                        $('.data-table').DataTable().ajax.reload();
                        Swal.fire({
                            toast: true,
                            position: "top-end",   // top-end, top-start, bottom-end, bottom-start
                            icon: "success",
                            title: response.message || "Faq Deleted successfully",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                    else {
                        Swal.fire({
                            toast: true,
                            position: "top-end",
                            icon: "error",
                            title: response.message || "Something went wrong",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                },
                error: function (error) {
                    // location.reload();
                }
            });
        }

        function editFaq(id) {
            let url = "{{ route('backend.feature.faq.edit', ':id') }}";
            url = url.replace(':id', id);

            window.location.href = url;
        }

        function deleteData(url) {
            if (confirm("Are you sure you want to delete this FAQ?")) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        if (response.success) {
                            $('.data-table').DataTable().ajax.reload();
                            Swal.fire({
                                toast: true,
                                position: "top-end",   // top-end, top-start, bottom-end, bottom-start
                                icon: "success",
                                title: response.message || "Faq Deleted successfully",
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                        else {
                            Swal.fire({
                                toast: true,
                                position: "top-end",
                                icon: "error",
                                title: response.message || "Something went wrong",
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                    }
                });
            }
        }
    </script>
@endpush