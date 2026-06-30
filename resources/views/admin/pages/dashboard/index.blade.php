@extends('admin.layouts.app')
@section('style')
    <style>
        .h2{
            font-size: 1.6rem;
        }
        .card-link {
            text-decoration: none;
            color: #000000;
            display: block;
        }

        .card-link:hover .card {
            transform: translateY(-4px);
            transition: 0.2s;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }
        .card-link .fe{
            padding: 0.7rem;
            border-radius: 15px;
            font-size: 26px !important;
        }
        .card-link .text-primary{
            background: var(--primary-color) !important;
            color: #ffffff !important;
        }
        .card-link .text-danger{
            background: #dc3545 !important;
            color: #ffffff !important;
        }
        .card-link .text-success{
            background: #3ad29f !important;
            color: #ffffff !important;
        }
        .card-link .text-info{
            background: #17a2b8 !important;
            color: #ffffff !important;

        }
        .card-link .text-primary-blue{
            background: #1b68ff !important;
            color: #ffffff !important;

        }
    </style>
@endsection
@section('content')

    <!-- Striped rows -->
    <div class="col-md-12 my-4">
        <div class="card shadow">
            <div class="card-body">
                <div class="toolbar row mb-3">

                </div>
                <div class="row">

                    <div class="col-md-3 mb-4">
                        <a href="{{ route('admin.users.index') }}" class="card-link">
                            <div class="card shadow">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <span class="h2 mb-0">{{ $usersCount }}</span>
                                            <p class="mb-0">{{ __('dashboard.users') }}</p>
                                        </div>
                                        <div class="col-auto">
                                            <span class="fe fe-users text-primary-blue"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 mb-4">
                        <a href="{{ route('admin.vendors.index') }}" class="card-link">
                            <div class="card shadow">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <span class="h2 mb-0">{{ $vendorsCount }}</span>
                                            <p class="mb-0">{{ __('dashboard.vendors') }}</p>
                                        </div>
                                        <div class="col-auto">
                                            <span class="fe fe-shopping-bag text-success"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 mb-4">
                        <a href="{{ route('admin.products.index') }}" class="card-link">
                            <div class="card shadow">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <span class="h2 mb-0">{{ $productsCount }}</span>
                                            <p class="mb-0">{{ __('dashboard.products') }}</p>
                                        </div>
                                        <div class="col-auto">
                                            <span class="fe fe-box text-danger"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>
                <hr class="my-3">

            </div> <!-- simple table -->
        </div>
    </div>




@endsection


<script>
    // search
    document.addEventListener('DOMContentLoaded', function() {
        function searchTable(input) {
            var filter = input.toLowerCase(); // Convert input to lowercase for case-insensitive search
            var rows = document.querySelectorAll('#adminTableBody tr'); // Get all rows in the table body

            rows.forEach(function(row) {
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
        searchInput.addEventListener('keyup', function() {
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
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire("{{__('dashboard.Done!')}}", "{{__('dashboard.User status has been changed.')}}", "success").then(() => {
                                // location.reload(); // Reload page to reflect changes
                            });
                        } else {
                            Swal.fire("{{__('dashboard.Error!')}}", "{{__('dashboard.Something went wrong.')}}", "error");
                        }
                    },
                    error: function() {
                        Swal.fire("{{__('dashboard.Error!')}}", "{{__('dashboard.Could not activate the user.')}}", "error");
                    }
                });
            }
        });
    }
</script>
