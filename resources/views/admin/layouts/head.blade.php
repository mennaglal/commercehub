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
    <style>

        <style>
            /* ============================================
               PREMIUM GOLD DARK THEME
               ============================================ */

        html:has(#darkTheme:not([disabled])) {
            color-scheme: dark;
        }

        html:has(#darkTheme:not([disabled])) body {
            --primary-color: #D4AF62;
            --light-primary-color: #F1D69A;
            --secondary-color: #243247;

            --dark-bg: #111827;
            --dark-sidebar: #172235;
            --dark-card: #1E293B;
            --dark-card-header: #253449;
            --dark-input: #263449;

            --dark-border: #344257;
            --dark-text: #F1F5F9;
            --dark-muted: #A0AEC0;

            background: var(--dark-bg) !important;
            color: var(--dark-text) !important;
        }

        /* ============================================
           BODY & MAIN CONTENT
        ============================================ */

        html:has(#darkTheme:not([disabled])) body,
        html:has(#darkTheme:not([disabled])) .wrapper,
        html:has(#darkTheme:not([disabled])) .main-content,
        html:has(#darkTheme:not([disabled])) main {
            background-color: var(--dark-bg) !important;
            color: var(--dark-text) !important;
        }

        /* ============================================
           SIDEBAR
        ============================================ */

        html:has(#darkTheme:not([disabled])) .sidebar-left {
            background: var(--dark-sidebar) !important;
            border-right: 1px solid var(--dark-border) !important;
            box-shadow: 4px 0 20px rgba(0,0,0,.15);
        }

        html:has(#darkTheme:not([disabled])) .sidebar-left .nav-link,
        html:has(#darkTheme:not([disabled])) .sidebar-left a,
        html:has(#darkTheme:not([disabled])) .sidebar-left .item-text {
            color: #CBD5E1 !important;
            transition: all .2s ease;
        }

        html:has(#darkTheme:not([disabled])) .sidebar-left .nav-link:hover,
        html:has(#darkTheme:not([disabled])) .sidebar-left .active > .nav-link,
        html:has(#darkTheme:not([disabled])) .sidebar-left .show > .nav-link {
            color: var(--primary-color) !important;
            background: rgba(212,175,98,.10) !important;
        }

        html:has(#darkTheme:not([disabled])) .sidebar-left i,
        html:has(#darkTheme:not([disabled])) .sidebar-left .feather {
            color: var(--primary-color) !important;
        }

        html:has(#darkTheme:not([disabled])) .sidebar-count {
            background: var(--primary-color) !important;
            color: #172235 !important;
            box-shadow: 0 2px 8px rgba(212,175,98,.2);
        }

        /* ============================================
           NAVBAR
        ============================================ */

        html:has(#darkTheme:not([disabled])) .navbar,
        html:has(#darkTheme:not([disabled])) .topnav,
        html:has(#darkTheme:not([disabled])) .navbar-light {
            background: var(--dark-sidebar) !important;
            border-color: var(--dark-border) !important;
        }

        html:has(#darkTheme:not([disabled])) .navbar .nav-link,
        html:has(#darkTheme:not([disabled])) .navbar-light .navbar-nav .nav-link {
            color: var(--dark-text) !important;
        }

        /* ============================================
           CARDS
        ============================================ */

        html:has(#darkTheme:not([disabled])) .card,
        html:has(#darkTheme:not([disabled])) .dashboard-card {
            background: var(--dark-card) !important;
            color: var(--dark-text) !important;
            border: 1px solid var(--dark-border) !important;
            border-radius: 14px !important;
            box-shadow: 0 5px 20px rgba(0,0,0,.15);
            overflow: hidden;
        }

        html:has(#darkTheme:not([disabled])) .card-header {
            background: var(--dark-card-header) !important;
            color: var(--dark-text) !important;
            border-bottom: 1px solid var(--dark-border) !important;
        }

        html:has(#darkTheme:not([disabled])) .card-body {
            color: var(--dark-text) !important;
        }

        html:has(#darkTheme:not([disabled])) .card-title {
            color: #F8FAFC !important;
            font-weight: 600;
        }

        /* ============================================
           TEXT & HEADINGS
        ============================================ */

        html:has(#darkTheme:not([disabled])) h1,
        html:has(#darkTheme:not([disabled])) h2,
        html:has(#darkTheme:not([disabled])) h3,
        html:has(#darkTheme:not([disabled])) h4,
        html:has(#darkTheme:not([disabled])) h5,
        html:has(#darkTheme:not([disabled])) h6 {
            color: #F1F5F9 !important;
        }

        html:has(#darkTheme:not([disabled])) p,
        html:has(#darkTheme:not([disabled])) label,
        html:has(#darkTheme:not([disabled])) .form-label {
            color: #CBD5E1;
        }

        html:has(#darkTheme:not([disabled])) .text-muted,
        html:has(#darkTheme:not([disabled])) small,
        html:has(#darkTheme:not([disabled])) .text-secondary {
            color: var(--dark-muted) !important;
        }

        html:has(#darkTheme:not([disabled])) .text-dark {
            color: var(--dark-text) !important;
        }

        /* ============================================
           TABLES
        ============================================ */

        html:has(#darkTheme:not([disabled])) .table {
            background: var(--dark-card) !important;
            color: var(--dark-text) !important;
            border-color: var(--dark-border) !important;
        }

        html:has(#darkTheme:not([disabled])) .table thead th {
            background: var(--dark-card-header) !important;
            color: #E5C47D !important;
            border-color: var(--dark-border) !important;
        }

        html:has(#darkTheme:not([disabled])) .table tbody td {
            background: transparent !important;
            color: #D5DEEA !important;
            border-color: var(--dark-border) !important;
        }

        html:has(#darkTheme:not([disabled])) .table tbody tr:hover {
            background: rgba(212,175,98,.05) !important;
        }

        html:has(#darkTheme:not([disabled])) .table-striped tbody tr:nth-of-type(odd) {
            background: rgba(255,255,255,.025) !important;
        }

        /* ============================================
           INPUTS & SELECTS
        ============================================ */

        html:has(#darkTheme:not([disabled])) .form-control,
        html:has(#darkTheme:not([disabled])) .custom-select,
        html:has(#darkTheme:not([disabled])) select,
        html:has(#darkTheme:not([disabled])) textarea {
            background-color: var(--dark-input) !important;
            color: #F1F5F9 !important;
            border: 1px solid var(--dark-border) !important;
            border-radius: 7px;
        }

        html:has(#darkTheme:not([disabled])) .form-control::placeholder {
            color: #94A3B8 !important;
        }

        html:has(#darkTheme:not([disabled])) .form-control:focus,
        html:has(#darkTheme:not([disabled])) select:focus,
        html:has(#darkTheme:not([disabled])) textarea:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px rgba(212,175,98,.12) !important;
            outline: none;
        }

        html:has(#darkTheme:not([disabled])) option {
            background: var(--dark-card);
            color: var(--dark-text);
        }

        /* ============================================
           BUTTONS
        ============================================ */

        html:has(#darkTheme:not([disabled])) .btn-primary {
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #172235 !important;
            font-weight: 600;
            border-radius: 7px;
        }

        html:has(#darkTheme:not([disabled])) .btn-primary:hover {
            background: var(--light-primary-color) !important;
            border-color: var(--light-primary-color) !important;
        }

        html:has(#darkTheme:not([disabled])) .btn-secondary {
            background: #344257 !important;
            border-color: #344257 !important;
            color: #F1F5F9 !important;
        }

        html:has(#darkTheme:not([disabled])) .btn-light {
            background: #344257 !important;
            border-color: #344257 !important;
            color: #F1F5F9 !important;
        }

        /* ============================================
           DROPDOWN
        ============================================ */

        html:has(#darkTheme:not([disabled])) .dropdown-menu {
            background: var(--dark-card-header) !important;
            border: 1px solid var(--dark-border) !important;
            box-shadow: 0 8px 25px rgba(0,0,0,.3);
            border-radius: 9px;
        }

        html:has(#darkTheme:not([disabled])) .dropdown-item {
            color: var(--dark-text) !important;
        }

        html:has(#darkTheme:not([disabled])) .dropdown-item:hover {
            background: rgba(212,175,98,.10) !important;
            color: var(--primary-color) !important;
        }

        /* ============================================
           BADGES & STATUS
        ============================================ */

        html:has(#darkTheme:not([disabled])) .badge-primary {
            background: var(--primary-color) !important;
            color: #172235 !important;
        }

        html:has(#darkTheme:not([disabled])) .badge-secondary {
            background: #475569 !important;
            color: #F1F5F9 !important;
        }

        /* ============================================
           MODALS
        ============================================ */

        html:has(#darkTheme:not([disabled])) .modal-content {
            background: var(--dark-card) !important;
            color: var(--dark-text) !important;
            border: 1px solid var(--dark-border) !important;
            border-radius: 12px;
        }

        html:has(#darkTheme:not([disabled])) .modal-header,
        html:has(#darkTheme:not([disabled])) .modal-footer {
            background: var(--dark-card-header) !important;
            border-color: var(--dark-border) !important;
        }

        html:has(#darkTheme:not([disabled])) .modal-title {
            color: #F1F5F9 !important;
        }

        /* ============================================
           PAGINATION
        ============================================ */

        html:has(#darkTheme:not([disabled])) .page-link {
            background: var(--dark-card) !important;
            color: var(--dark-text) !important;
            border-color: var(--dark-border) !important;
        }

        html:has(#darkTheme:not([disabled])) .page-item.active .page-link {
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #172235 !important;
        }

        /* ============================================
           SELECT2
        ============================================ */

        html:has(#darkTheme:not([disabled])) .select2-container--bootstrap4 .select2-selection {
            background: var(--dark-input) !important;
            border-color: var(--dark-border) !important;
            color: var(--dark-text) !important;
        }

        html:has(#darkTheme:not([disabled])) .select2-container--bootstrap4 .select2-selection__rendered {
            color: var(--dark-text) !important;
        }

        html:has(#darkTheme:not([disabled])) .select2-dropdown {
            background: var(--dark-card) !important;
            border-color: var(--dark-border) !important;
        }

        html:has(#darkTheme:not([disabled])) .select2-results__option {
            background: var(--dark-card) !important;
            color: var(--dark-text) !important;
        }

        html:has(#darkTheme:not([disabled])) .select2-results__option--highlighted {
            background: var(--primary-color) !important;
            color: #172235 !important;
        }

        /* ============================================
           BORDERS & LINKS
        ============================================ */

        html:has(#darkTheme:not([disabled])) hr,
        html:has(#darkTheme:not([disabled])) .border,
        html:has(#darkTheme:not([disabled])) .dropdown-divider {
            border-color: var(--dark-border) !important;
        }

        html:has(#darkTheme:not([disabled])) a:not(.btn):not(.dropdown-item) {
            color: var(--primary-color);
        }

        /* ============================================
           CHECKBOX & RADIO
        ============================================ */

        html:has(#darkTheme:not([disabled])) .form-check-input {
            background-color: var(--dark-input);
            border-color: #64748B;
        }

        html:has(#darkTheme:not([disabled])) .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* ============================================
           SCROLLBAR
        ============================================ */

        html:has(#darkTheme:not([disabled])) * {
            scrollbar-color: #475569 var(--dark-bg);
        }

        html:has(#darkTheme:not([disabled])) ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        html:has(#darkTheme:not([disabled])) ::-webkit-scrollbar-track {
            background: var(--dark-bg);
        }

        html:has(#darkTheme:not([disabled])) ::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 10px;
        }

        html:has(#darkTheme:not([disabled])) ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }

        /* ============================================
           RTL
        ============================================ */

        html[dir="rtl"]:has(#darkTheme:not([disabled])) body {
            font-family: 'Cairo', sans-serif !important;
        }

        /* Statistics */
        html:has(#darkTheme:not([disabled])) .stat-value,
        html:has(#darkTheme:not([disabled])) .stat-label {
            color: #F1F5F9 !important;
        }

        html:has(#darkTheme:not([disabled])) .stat-note {
            color: #A7B4C8 !important;
        }

        /* Blue */
        html:has(#darkTheme:not([disabled])) .stat-blue {
            background: rgba(96, 145, 230, 0.14) !important;
            color: #8DB5FF !important;
            border: 1px solid rgba(96, 145, 230, 0.22);

        }

        /* Purple */
        html:has(#darkTheme:not([disabled])) .stat-purple {
            background: rgba(155, 125, 225, 0.14) !important;
            color: #C0A7FF !important;
            border: 1px solid rgba(155, 125, 225, 0.22);

        }

        /* Cyan */
        html:has(#darkTheme:not([disabled])) .stat-cyan {
            background: rgba(65, 190, 205, 0.13) !important;
            color: #75DCE8 !important;
            border: 1px solid rgba(65, 190, 205, 0.22);

        }

        /* Gold - إضافي */
        html:has(#darkTheme:not([disabled])) .stat-gold {
            background: rgba(212, 175, 98, 0.14) !important;
            color: #EAC779 !important;
            border: 1px solid rgba(212, 175, 98, 0.22);
        }

        /* =====================================
           PREMIUM DARK DASHBOARD BADGES
        ===================================== */

        /* ==============================
    DARK MODE - ALERT INFO
 ============================== */

        html:has(#darkTheme:not([disabled])) .alert-info {
            color: #6ED6E5 !important;
            background-color: rgba(23, 162, 184, 0.15) !important;
            border-color: rgba(23, 162, 184, 0.25) !important;
        }

        html:has(#darkTheme:not([disabled])) .alert-info a {
            color: #A5EDF5 !important;
        }

        /* ==============================
           DARK MODE - DASHBOARD BADGES
        ============================== */

        /* Primary - Blue */
        html:has(#darkTheme:not([disabled])) .dashboard-badge.primary {
            color: #8DBBFF !important;
            background: rgba(71, 120, 216, 0.16) !important;
            border: 1px solid rgba(71, 120, 216, 0.25);
        }

        /* Info - Cyan / Teal */
        html:has(#darkTheme:not([disabled])) .dashboard-badge.info {
            color: #6ED6E5 !important;
            background: rgba(23, 162, 184, 0.15) !important;
            border: 1px solid rgba(23, 162, 184, 0.25);
        }

        /* Danger - Red */
        html:has(#darkTheme:not([disabled])) .dashboard-badge.danger {
            color: #FF9292 !important;
            background: rgba(220, 75, 75, 0.15) !important;
            border: 1px solid rgba(220, 75, 75, 0.25);
        }

        /* Success - Green */
        html:has(#darkTheme:not([disabled])) .dashboard-badge.success {
            color: #65D6A5 !important;
            background: rgba(22, 163, 109, 0.15) !important;
            border: 1px solid rgba(22, 163, 109, 0.25);
        }

        /* Warning - Gold */
        html:has(#darkTheme:not([disabled])) .dashboard-badge.warning {
            color: #F1D18A !important;
            background: rgba(212, 175, 98, 0.15) !important;
            border: 1px solid rgba(212, 175, 98, 0.25);
        }

        /* General Badge Styling */
        html:has(#darkTheme:not([disabled])) .dashboard-badge {
            border-radius: 20px;
            padding: 5px 11px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        html:has(#darkTheme:not([disabled])) .stat-red {
            background: rgba(220, 75, 75, 0.15) !important;
            color: #FF9292 !important;
            border: 1px solid rgba(220, 75, 75, 0.22);
        }

        html:has(#darkTheme:not([disabled])) .stat-green {
            background: rgba(32, 164, 119, 0.15) !important;
            color: #65D6A5 !important;
            border: 1px solid rgba(32, 164, 119, 0.22);
        }
        html:has(#darkTheme:not([disabled])) .status-track {
            background: #344257 !important;
            height: 8px;
            border-radius: 20px;
            overflow: hidden;
        }
        html:has(#darkTheme:not([disabled])) .bg-light {
            background-color: #344257 !important;
            color: #F1F5F9 !important;
        }
        .general-counts .dashboard-stat,
        .general-counts .dashboard-stat .card-body {
            height: auto !important;
            min-height: 0 !important;
        }

        .general-counts .dashboard-stat .card-body {
            padding: 10px 14px !important;
        }

        .general-counts .dashboard-stat .stat-value {
            line-height: 1 !important;
            margin: 4px 0 !important;
        }

        .general-counts .dashboard-stat .stat-label,
        .general-counts .dashboard-stat .stat-note {
            line-height: 1.2 !important;
            margin: 0 !important;
        }
        /* Buttons Spacing */
        .btn + .btn {
            margin-inline-start: 8px;
        }
        /* ==============================
           DARK MODE - BADGES
        ============================== */
        /* Background colors */
        html:has(#darkTheme:not([disabled])) .badge.bg-warning {
            background-color: rgba(238, 163, 3, 0.18) !important;
            color: #FFC857 !important;
            border-color: rgba(238, 163, 3, 0.25);
        }

        html:has(#darkTheme:not([disabled])) .badge.bg-success {
            background-color: rgba(32, 164, 119, 0.15) !important;
            color: #65D6A5 !important;
            border-color: rgba(32, 164, 119, 0.22);
        }

        html:has(#darkTheme:not([disabled])) .badge.bg-danger {
            background-color: rgba(220, 75, 75, 0.15) !important;
            color: #FF9292 !important;
            border-color: rgba(220, 75, 75, 0.22);
        }

        html:has(#darkTheme:not([disabled])) .badge.bg-info {
            background-color: rgba(59, 130, 246, 0.15) !important;
            color: #93C5FD !important;
            border-color: rgba(59, 130, 246, 0.22);
        }

        html:has(#darkTheme:not([disabled])) .badge.bg-primary {
            background-color: rgba(99, 102, 241, 0.15) !important;
            color: #A5B4FC !important;
            border-color: rgba(99, 102, 241, 0.22);
        }

        html:has(#darkTheme:not([disabled])) .badge.bg-secondary {
            background-color: rgba(148, 163, 184, 0.15) !important;
            color: #CBD5E1 !important;
            border-color: rgba(148, 163, 184, 0.22);
        }

        html:has(#darkTheme:not([disabled])) .badge.bg-light {
            background-color: #344257 !important;
            color: #F1F5F9 !important;
            border-color: #475569;
        }

        html:has(#darkTheme:not([disabled])) .badge.bg-dark {
            background-color: #475569 !important;
            color: #F1F5F9 !important;
            border-color: #64748B;
        }

        /* ==============================
           CUSTOM DASHBOARD BADGES
        ============================== */


        html:has(#darkTheme:not([disabled])) .stat-red {
            background: rgba(220, 75, 75, 0.15) !important;
            color: #FF9292 !important;
            border: 1px solid rgba(220, 75, 75, 0.22);
        }

        html:has(#darkTheme:not([disabled])) .stat-green {
            background: rgba(32, 164, 119, 0.15) !important;
            color: #65D6A5 !important;
            border: 1px solid rgba(32, 164, 119, 0.22);
        }
        /* ==============================
   DARK MODE - BUTTONS
============================== */

        /* Outline Danger */
        html:has(#darkTheme:not([disabled])) .btn-outline-danger {
            color: #FF9292 !important;
            border-color: rgba(220, 53, 69, 0.65) !important;
        }

        html:has(#darkTheme:not([disabled])) .btn-outline-danger:hover {
            background-color: rgba(220, 53, 69, 0.15) !important;
            color: #FFAAAA !important;
            border-color: #FF9292 !important;
        }

        /* Outline Secondary */
        html:has(#darkTheme:not([disabled])) .btn-outline-secondary {
            color: #CBD5E1 !important;
            border-color: #64748B !important;
        }

        html:has(#darkTheme:not([disabled])) .btn-outline-secondary:hover {
            background-color: rgba(148, 163, 184, 0.15) !important;
            color: #F1F5F9 !important;
            border-color: #94A3B8 !important;
        }

        /* Outline Primary */
        html:has(#darkTheme:not([disabled])) .btn-outline-primary {
            color: #93B4FF !important;
            border-color: rgba(59, 130, 246, 0.65) !important;
        }

        html:has(#darkTheme:not([disabled])) .btn-outline-primary:hover {
            background-color: rgba(59, 130, 246, 0.15) !important;
            color: #B4CCFF !important;
            border-color: #93B4FF !important;
        }

        /* Solid Danger */
        html:has(#darkTheme:not([disabled])) .btn-danger {
            color: #FFAAAA !important;
            background-color: rgba(220, 53, 69, 0.18) !important;
            border-color: rgba(220, 53, 69, 0.35) !important;
        }

        html:has(#darkTheme:not([disabled])) .btn-danger:hover {
            background-color: rgba(220, 53, 69, 0.3) !important;
            border-color: rgba(220, 53, 69, 0.55) !important;
            color: #FFFFFF !important;
        }

        /* Focus */
        html:has(#darkTheme:not([disabled])) .btn-outline-danger:focus,
        html:has(#darkTheme:not([disabled])) .btn-outline-secondary:focus,
        html:has(#darkTheme:not([disabled])) .btn-outline-primary:focus,
        html:has(#darkTheme:not([disabled])) .btn-danger:focus {
            box-shadow: 0 0 0 0.2rem rgba(148, 163, 184, 0.15) !important;
        }
        /* ==============================
   DARK MODE - SWEETALERT2
============================== */

        html:has(#darkTheme:not([disabled])) div:where(.swal2-container) div:where(.swal2-popup) {
            background: #1E293B !important;
            color: #F1F5F9 !important;
            border: 1px solid #344257 !important;
            border-radius: 14px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.35);
        }

        /* Title */
        html:has(#darkTheme:not([disabled])) .swal2-title {
            color: #F1F5F9 !important;
        }

        /* Content */
        html:has(#darkTheme:not([disabled])) .swal2-html-container {
            color: #CBD5E1 !important;
        }

        /* Input */
        html:has(#darkTheme:not([disabled])) .swal2-input,
        html:has(#darkTheme:not([disabled])) .swal2-textarea,
        html:has(#darkTheme:not([disabled])) .swal2-select {
            background: #344257 !important;
            color: #F1F5F9 !important;
            border: 1px solid #475569 !important;
        }

        html:has(#darkTheme:not([disabled])) .swal2-input::placeholder,
        html:has(#darkTheme:not([disabled])) .swal2-textarea::placeholder {
            color: #94A3B8 !important;
        }

        /* Validation Message */
        html:has(#darkTheme:not([disabled])) .swal2-validation-message {
            background: #344257 !important;
            color: #FF9292 !important;
        }

        /* Footer */
        html:has(#darkTheme:not([disabled])) .swal2-footer {
            border-top-color: #344257 !important;
            color: #CBD5E1 !important;
        }

        /* Close Button */
        html:has(#darkTheme:not([disabled])) .swal2-close {
            color: #94A3B8 !important;
        }

        html:has(#darkTheme:not([disabled])) .swal2-close:hover {
            color: #FFFFFF !important;
        }

        /* Backdrop */
        html:has(#darkTheme:not([disabled])) .swal2-container.swal2-backdrop-show {
            background: rgba(0, 0, 0, 0.7) !important;
        }
        /* ==============================
   DARK MODE - ALERT SUCCESS
============================== */

        html:has(#darkTheme:not([disabled])) .alert-success {
            color: #65D6A5 !important;
            background-color: rgba(32, 164, 119, 0.15) !important;
            border-color: rgba(32, 164, 119, 0.25) !important;
        }

        /* Alert Link */
        html:has(#darkTheme:not([disabled])) .alert-success a {
            color: #86E5BA !important;
        }

        /* Alert Close Button */
        html:has(#darkTheme:not([disabled])) .alert-success .btn-close {
            filter: invert(1) grayscale(100%) brightness(1.5);
        }
        /* ==============================
   DARK MODE - ALERT DANGER
============================== */

        html:has(#darkTheme:not([disabled])) .alert-danger {
            color: #FF9292 !important;
            background-color: rgba(220, 75, 75, 0.15) !important;
            border-color: rgba(220, 75, 75, 0.25) !important;
        }

        /* Alert Link */
        html:has(#darkTheme:not([disabled])) .alert-danger a {
            color: #FFAAAA !important;
        }

        /* Alert Close Button */
        html:has(#darkTheme:not([disabled])) .alert-danger .btn-close {
            filter: invert(1) grayscale(100%) brightness(1.5);
        }

        /* ==============================
   DARK MODE - ALERT INFO
============================== */

        html:has(#darkTheme:not([disabled])) .alert-info {
            color: #93C5FD !important;
            background-color: rgba(59, 130, 246, 0.15) !important;
            border-color: rgba(59, 130, 246, 0.25) !important;
        }

        /* Alert Link */
        html:has(#darkTheme:not([disabled])) .alert-info a {
            color: #BFDBFE !important;
        }

        /* Alert Close Button */
        html:has(#darkTheme:not([disabled])) .alert-info .btn-close {
            filter: invert(1) grayscale(100%) brightness(1.5);
        }
        /* DARK MODE - BADGE SUCCESS */

        html:has(#darkTheme:not([disabled])) .badge-success {
            color: #65D6A5 !important;
            background-color: rgba(32, 164, 119, 0.15) !important;
            border: 1px solid rgba(32, 164, 119, 0.25) !important;
        }
        /* DARK MODE - BUTTON INFO */

        html:has(#darkTheme:not([disabled])) .btn-info {
            color: #6ED6E5 !important;
            background-color: rgba(23, 162, 184, 0.15) !important;
            border-color: rgba(23, 162, 184, 0.35) !important;
        }

        html:has(#darkTheme:not([disabled])) .btn-info:hover {
            color: #A5EDF5 !important;
            background-color: rgba(23, 162, 184, 0.25) !important;
            border-color: rgba(23, 162, 184, 0.55) !important;
        }

        html:has(#darkTheme:not([disabled])) .btn-info:focus {
            box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.2) !important;
        }
        html:has(#darkTheme:not([disabled])) .badge-info {
            color: #6ED6E5 !important;
            background-color: rgba(23, 162, 184, 0.15) !important;
            border: 1px solid rgba(23, 162, 184, 0.25) !important;
        }
        .btn-group,
        .d-flex {
            gap: 8px;
        }
        /* Spacing between adjacent buttons - All Themes */
        .btn:not(.btn-group .btn) + .btn:not(.btn-group .btn) {
            margin-inline-start: 6px;
        }
        /* Products Table */
        .table thead th,.table tbody td {
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
        }
        /* Fix Arabic text clipping inside select boxes */
        select,
        select.form-control,
        .form-select {
            height: 40px;
            min-height: 40px;
            line-height: 1.8 !important;
            padding-top: 6px !important;
            padding-bottom: 6px !important;
            font-size: 14px;
            box-sizing: border-box;
        }

        /* ========================================
           Admin Tables - Shared Style
        ======================================== */

        .admin-table {
            width: 100%;
            margin-bottom: 0;
            vertical-align: middle;
            color: #111;
        }

        .admin-table thead th {
            background-color: #fff;
            color: #111;
            font-weight: 600;
            white-space: nowrap;
            padding: 14px 12px;
            border-top: 0;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .admin-table tbody td {
            padding: 14px 12px;
            vertical-align: middle;
            border-top: 1px solid #e9ecef;
        }

        .admin-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .admin-table.table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Table Card */
        .admin-table-card {
            border: 1px solid #e3e8ee;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: none;
        }
        /* Hide sidebar counts when sidebar is collapsed */
        .vertical.collapsed .sidebar-count {
            display: none !important;
        }

        /* Keep counts visible when sidebar is expanded */
        .vertical:not(.collapsed) .sidebar-count {
            display: inline-flex !important;
        }
    </style>

    </style>
</head>
