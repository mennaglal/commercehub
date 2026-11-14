@extends('web.nexus.page')
@section('title', 'Order confirmation')
@section('page', 'order-success')
@php($orderNumber = $orderNumber)
@section('content')
<div id="nx-app" data-page="order-success" data-order-number="{{ $orderNumber }}"><div class="nx-empty"><div class="spinner-border"></div></div></div>
@endsection
