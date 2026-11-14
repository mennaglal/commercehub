<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" @if(app()->getLocale()=='ar') dir="rtl" @endif>

<head>

    @include('admin.layouts.head')

</head>
<style>

    body{

        margin:0;

        font-family:'Cairo',sans-serif;

        background:#f5f7fb;

    }

    .login-page{

        display:flex;

        min-height:100vh;

    }

    .login-left{

        width:45%;

        background:linear-gradient(135deg,#1d3557,#355c8a);

        color:white;

        position:relative;

        display:flex;

        align-items:center;

        justify-content:center;

    }

    .overlay{

        position:absolute;

        inset:0;

        background:rgba(0,0,0,.15);

    }

    .login-content{

        position:relative;

        z-index:2;

        max-width:420px;

        padding:50px;

    }

    .login-content h1{

        font-size:42px;

        font-weight:700;

        margin-top:20px;

    }

    .login-content p{

        font-size:18px;

        opacity:.9;

        margin-bottom:40px;

    }

    .login-content ul{

        list-style:none;

        padding:0;

    }

    .login-content li{

        margin-bottom:18px;

        font-size:18px;

    }

    .logo{

        width:90px;

    }

    .login-right{

        width:55%;

        display:flex;

        align-items:center;

        justify-content:center;

        padding:40px;

    }

    .login-card{

        width:100%;

        max-width:450px;

        background:white;

        border-radius:20px;

        padding:45px;

        box-shadow:0 20px 45px rgba(0,0,0,.08);

    }

    .login-card h3{

        font-weight:700;

        margin-bottom:10px;

    }

    .login-card p{

        color:#777;

        margin-bottom:30px;

    }

    .form-control{

        height:50px;

        border-radius:12px;

    }

    .form-control:focus{

        border-color:#d3a54b;

        box-shadow:none;

    }

    .btn-login{

        width:100%;

        height:50px;

        border:none;

        border-radius:12px;

        background:#d3a54b;

        color:#fff;

        font-weight:600;

        transition:.3s;

    }

    .btn-login:hover{

        background:#927337;

    }

    @media(max-width:991px){

        .login-left{

            display:none;

        }

        .login-right{

            width:100%;

        }

    }
    .login-header{

        text-align:center;

        margin-bottom:35px;

    }

    .login-header h2{

        margin:0;

        font-size:32px;

        font-weight:700;

        color:#1d3557;

    }

    .login-header p{

        margin-top:10px;

        color:#777;

        font-size:15px;

    }

    .input-group{

        display:flex;

        flex-direction:column;

        margin-bottom:22px;

    }

    .input-group label{

        margin-bottom:8px;

        font-size:15px;

        font-weight:600;

        color:#555;

    }

    .input-group input{

        width:100%;

        height:52px;

        border:1px solid #ddd;

        border-radius:12px;

        padding:0 18px;

        font-size:15px;

        transition:.3s;

        outline:none;

        background:#fff;

    }

    .input-group input:focus{

        border-color:#d3a54b;

        box-shadow:0 0 0 4px rgba(211,165,75,.15);

    }

    .remember{

        display:flex;

        align-items:center;

        margin-bottom:25px;

    }

    .remember label{

        display:flex;

        align-items:center;

        gap:8px;

        color:#555;

        cursor:pointer;

    }

    .btn-login{

        width:100%;

        height:55px;

        border:none;

        border-radius:12px;

        background:#d3a54b;

        color:#fff;

        font-size:17px;

        font-weight:700;

        cursor:pointer;

        transition:.3s;

    }

    .btn-login:hover{

        background:#b88a2f;

        transform:translateY(-2px);

    }

    .text-error{

        color:#dc3545;

        margin-top:6px;

        font-size:13px;

    }

</style>
<body>

<div class="login-page">

    <div class="login-left">

        <div class="overlay"></div>

        <div class="login-content">

            <img
                src="{{ asset('assets/images/logo.png') }}"
                class="logo">

            <h1>

                CommerceHub

            </h1>

            <p>

                Multi Vendor SaaS Platform

            </p>

            <ul>

                <li>✔ Multi Vendors</li>

                <li>✔ Products Management</li>

                <li>✔ Orders & Payments</li>

                <li>✔ Analytics Dashboard</li>

            </ul>

        </div>

    </div>

    <div class="login-right">

        @yield('content')

    </div>

</div>

</body>

</html>
