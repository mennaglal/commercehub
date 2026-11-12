@extends('admin.layouts.app')
@section('title', __('dashboard.Admins'))

@section('content')
    @include('includes.error')
    @include('includes.success')

    <!-- Striped rows -->
    <div class="col-md-12 my-4">
        <div class="card shadow">
            <div class="card-body">
                <div class="toolbar row mb-3">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.admins.index') }}" class="w-100"
                              style="max-width: 400px;">
                            <div class="form-group">
                                <input
                                    type="text"
                                    name="search"
                                    class="form-control mb-2"
                                    placeholder="{{ __('dashboard.Search by name or email') }}"
                                    value="{{ request('search') }}">

                                <div class="d-flex ">
                                    <button class="btn btn-primary  mr-1" type="submit">
                                        <i class="fe fe-search"></i> {{ __('dashboard.Search') }}
                                    </button>
                                    <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary ml-1">
                                        <i class="fe fe-rotate-ccw"></i> {{ __('dashboard.Reset') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-6 text-right">
                        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                            {{ __('dashboard.Add-Admin') }}+
                        </a>
                    </div>
                </div>

                <!-- table -->

                <form id="bulkDeleteForm" method="POST" action="{{ route('admin.bulk.delete') }}">
                    @csrf

                    <input type="hidden" name="model" value="admins">
                    <input type="hidden" name="ids" id="selectedIds">

                    <button type="button" onclick="confirmBulkDelete()" class="btn btn-danger mb-3">
                        @lang('dashboard.delete_all')
                    </button>
                </form>

                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>#</th>
                        <th>{{ __('dashboard.id') }}</th>
                        <th>{{ __('dashboard.Name') }}</th>
                        <th>{{ __('dashboard.Email') }}</th>
                        <th>{{ __('dashboard.Role') }}</th>
                        <th>{{ __('dashboard.Image') }}</th>
                        <th>{{ __('dashboard.Actions') }}</th>
                    </tr>
                    </thead>

                    <tbody id="adminTableBody">
                    @foreach($admins as $admin)
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" value="{{ $admin->id }}">
                            </td>

                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $admin->id }}</td>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>

                            <td>
                                @foreach($admin->roles as $role)
                                    <span class="badge badge-success">
                            {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                        </span>
                                @endforeach
                            </td>

                            <td>
                                @if($admin->avatar)
                                    <img src="{{ asset($admin->avatar) }}"
                                         alt="Avatar"
                                         width="50"
                                         height="50">
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('admin.admins.edit', $admin->id) }}"
                                   style="text-decoration: none;">
                                    <i class="fe fe-edit text-warning"></i>
                                </a>

                                <form action="{{ route('admin.admins.destroy', $admin) }}"
                                      method="POST"
                                      id="btnDelete{{ $admin->id }}"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="button"
                                            onclick="confirmDelete({{ $admin->id }})"
                                            class="btn btn-link p-0 border-0">
                                        <i class="fe fe-trash text-danger"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>


            </div> <!-- simple table -->
        </div>
    </div>


    <div class="d-flex justify-content-center mt-3">
        {{ $admins->links('vendor.pagination.medium') }}
    </div>

@endsection


<script>
    // search
    document.addEventListener('DOMContentLoaded', function () {
        function searchTable(input) {
            var filter = input.toLowerCase(); // Convert input to lowercase for case-insensitive search
            var rows = document.querySelectorAll('#adminTableBody tr'); // Get all rows in the table body

            rows.forEach(function (row) {
                var rowText = row.textContent
                    .toLowerCase(); // Get all text in the row and convert to lowercase
                if (rowText.indexOf(filter) > -1) {
                    row.style.display = ''; // Show row if text matches search
                } else {
                    row.style.display = 'none'; // Hide row if text doesn't match
                }
            });
        }

        // Event listener for the search input field
        var searchInput = document.getElementById('search');
        searchInput.addEventListener('keyup', function () {
            var searchValue = searchInput.value; // Get the value of the input field
            searchTable(searchValue); // Call the search function

        });


    });

    function confirmDelete(id) {
        Swal.fire({
            title: "{{__('dashboard.Are you sure?')}}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "{{__('dashboard.Yes, delete it!')}}",
            cancelButtonText: "{{__('dashboard.Cancelbtn')}}"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("btnDelete" + id).submit();
            }
        });
    }


    function activate(element) {
        let userId = $(element).data("id"); // Get user ID from button
        Swal.fire({
            title: "{{__('dashboard.Would you like to change this user status ?')}}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#d33",
            confirmButtonText: "{{__('dashboard.Yes')}}",
            cancelButtonText: "{{__('dashboard.Cancelbtn')}}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "admins/activate/" + userId,
                    type: "POST",
                    data: {_token: "{{ csrf_token() }}"},
                    success: function (response) {
                        if (response.success) {
                            Swal.fire("{{__('dashboard.Done!')}}", "{{__('dashboard.User status has been changed.')}}", "success").then(() => {
                                // location.reload(); // Reload page to reflect changes
                            });
                        } else {
                            Swal.fire("{{__('dashboard.Error!')}}", "{{__('dashboard.Something went wrong.')}}", "error");
                        }
                    },
                    error: function () {
                        Swal.fire("{{__('dashboard.Error!')}}", "{{__('dashboard.Could not activate the user.')}}", "error");
                    }
                });
            }
        });
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        /* =========================
           SELECT ALL
        ==========================*/

        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');

        if (selectAll) {
            // لما نضغط Select All
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
            });
        }

        // لو شلنا علامة من عنصر واحد
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                const total = checkboxes.length;
                const checked = document.querySelectorAll('.row-checkbox:checked').length;

                if (selectAll) {
                    selectAll.checked = (total === checked);
                }
            });
        });

    });


    /* =========================
       BULK DELETE
    ==========================*/

    function confirmBulkDelete() {

        let selected = [];

        document.querySelectorAll('.row-checkbox:checked').forEach(cb => {
            selected.push(cb.value);
        });

        if (selected.length === 0) {
            Swal.fire(
                "{{__('dashboard.Warning')}}",
                "{{__('dashboard.No items selected')}}",
                "warning"
            );
            return;
        }

        Swal.fire({
            title: "{{__('dashboard.Are you sure?')}}",
            text: "{{__('dashboard.Selected items will be deleted!')}}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "{{__('dashboard.Yes, delete them!')}}",
            cancelButtonText: "{{__('dashboard.Cancelbtn')}}"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('selectedIds').value = selected.join(',');
                document.getElementById('bulkDeleteForm').submit();
            }
        });
    }
</script>
