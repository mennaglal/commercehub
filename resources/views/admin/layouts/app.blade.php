@include('admin.layouts.head')
@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<main role="main" class="main-content">
    <div class="container-fluid">
        @yield('content')
    </div>
</main>

@include('admin.layouts.footer')
