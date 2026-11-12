@include('admin.layouts.head')
<body class="vertical light @if(app()->getLocale() == 'ar') rtl @endif">
<div class="wrapper">
    @include('vendor-dashboard.layouts.header')
    @include('vendor-dashboard.layouts.sidebar')
    <main role="main" class="main-content">
        <div class="container-fluid py-3">
            @if(session('success')) <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div> @endif
            @if(session('error')) <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div> @endif
            @if($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
            @yield('content')
        </div>
    </main>
    @include('admin.layouts.footer')
