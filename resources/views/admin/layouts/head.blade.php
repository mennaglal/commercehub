<!doctype html>
<html lang="{{app()->getLocale()}}" @if(app()->getLocale() == "ar") dir="rtl" @endif>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets/images/logo.png')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title',__('dashboard.dashboard'))</title>
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="{{asset('dashboard')}}/css/simplebar.css">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="{{asset('dashboard')}}/css/feather.css">
    <link rel="stylesheet" href="{{asset('dashboard')}}/css/quill.snow.css">
    <link rel="stylesheet" href="{{asset('dashboard')}}/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="{{asset('dashboard')}}/css/daterangepicker.css">


    <!-- App CSS -->
    <link rel="stylesheet" href="{{asset('dashboard')}}/css/app-light.css" id="lightTheme">
    <link rel="stylesheet" href="{{asset('dashboard')}}/css/app-rtl.css" id="lightTheme">
    <link rel="stylesheet" href="{{asset('dashboard')}}/css/app-dark.css" id="darkTheme" disabled>
    <link rel="stylesheet" href="{{asset('dashboard')}}/css/select2.css">
    <link rel="stylesheet" href="{{asset('dashboard')}}/css/select2-bootstrap4.css">
    <link href="https://fonts.googleapis.com/css2?family=Alkatra&family=Cairo:wght@200;300;400;500;600&family=Nunito+Sans:opsz,wght@6..12,200;6..12,300&family=Shantell+Sans:wght@300&display=swap" rel="stylesheet">

    @if(app()->getLocale()=="ar")
        <style>
            body{
                font-family: 'Cairo', sans-serif !important;
            }
        </style>
    @endif

    <style>
        :root{
            --light-primary-color: #ffd176;
            --primary-color:#d3a54b;
            --secondary-color: #1D3557FF;
        }
        body{
            color: #000000;
        }
        .editButton{
            text-decoration: none;
            border: navajowhite;
            background: transparent;
        }
    </style>
    <style>
        body {
            font-family: "Inter", sans-serif;
            background: #f8f9fa;
        }

        .chef-profile {
            max-width: 950px;
            margin: 50px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        /* ====== Cover Image ====== */
        .chef-cover {
            position: relative;
            width: 100%;
            height: 250px;
            background-color: #eee;
            background-size: cover;
            background-position: center;
        }

        /* ====== Avatar Over Cover ====== */
        .chef-avatar {
            position: absolute;
            bottom: -60px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }

        .chef-avatar img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }

        .chef-info-section {
            padding: 80px 50px 40px; /* extra top padding for avatar overlap */
        }

        .chef-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .chef-header h2 {
            font-weight: 600;
            margin: 10px 0 5px;
            color: #333;
        }

        .chef-header p {
            margin: 2px 0;
            color: #666;
            font-size: 15px;
        }

        .chef-section {
            margin-top: 35px;
        }

        .chef-section h4 {
            color: #222;
            font-weight: 600;
            margin-bottom: 15px;
            border-left: 4px solid #007bff;
            padding-left: 10px;
        }

        .chef-info {
            display: flex;
            flex-wrap: wrap;
            gap: 20px 60px;
        }

        .chef-info .field {
            width: 40%;
        }

        .chef-info label {
            display: block;
            font-weight: 500;
            color: #555;
            font-size: 14px;
        }

        .chef-info span {
            color: #222;
            font-size: 15px;
        }

        .chef-bio {
            margin-top: 10px;
            line-height: 1.7;
            color: #444;
        }

        .chef-social {
            margin-top: 25px;
        }

        .chef-social a {
            display: inline-block;
            margin-right: 15px;
            text-decoration: none;
            color: #007bff;
            font-weight: 500;
        }

        .chef-social a:hover {
            text-decoration: underline;
        }

        .back-btn {
            display: inline-block;
            margin-top: 40px;
            background: #343a40;
            color: white;
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
        }

        .back-btn:hover {
            background: #23272b;
            color: #fff;
        }

        @media (max-width: 768px) {
            .chef-info .field {
                width: 100%;
            }
        }
    </style>
    <style>
        .ingredient-order-label,
        .instruction-step-label {
            cursor: move;
            user-select: none;
        }
        .table thead th {
            text-align: center;
        }
        .table th, .table td {
            text-align: center;
        }
        .table {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
        }
        .table thead,.table tbody, .table tr{
            width: 100%;
            max-width: 100%;
        }
        .table,.table thead th {
            color: #000000;
        }
        .navbar-light .navbar-nav .nav-link {
            color: #000000;
        }
        .btn-primary,.btn-primary:hover,.btn-primary:focus,.btn-primary:checked {
            color: #ffffff;
            background-color: var(--primary-color);
            border-color:var(--primary-color);
        }
        .text-primary{
            color: var(--primary-color)!important;
        }
        .nav-link:hover{
            color: var(--primary-color)!important;
        }
        .badge{
            color: #ffffff;
        }

        .category-checkbox-card {
            cursor: pointer;
            background-color: #fff;
            transition: border-color 0.2s ease, background-color 0.2s ease;
        }

        .category-checkbox-card:hover {
            border-color: var(--primary-color) !important;
        }

        .category-checkbox-card:has(.category-checkbox:checked) {
            border-color: var(--primary-color) !important;
            background-color: rgba(var(--primary-color), 0.06);
        }

        .category-checkbox-card .form-check-input {
            flex-shrink: 0;
            cursor: pointer;
        }

        .category-checkbox-card span {
            overflow-wrap: anywhere;
        }
        .sidebar-count {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;

            width: 24px;
            height: 24px;
            min-width: 24px;

            padding: 0 !important;
            margin-left: auto;
            margin-right: 8px;

            border-radius: 50%;
            background-color: var(--primary-color);
            color: #fff !important;

            font-size: 11px;
            font-weight: 700;
            line-height: 24px;
            text-align: center;

            flex-shrink: 0;
            vertical-align: middle;
        }
        .sidebar-left .dropdown-toggle {
            display: flex !important;
            align-items: center !important;
            width: 100%;
        }

        .sidebar-left .dropdown-toggle .item-text {
            flex: 1 1 auto;
            min-width: 0;
        }

        .sidebar-left .sidebar-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            min-width: 24px;
            margin: 0 8px 0 0;
            padding: 0;
            border-radius: 50%;
            background: var(--primary-color);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            line-height: 1;
            flex-shrink: 0;
        }
    </style>
    @yield('style')
</head>
