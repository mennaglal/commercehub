@extends('web.nexus.page')
@section('title', 'Order details')
@section('page', 'order')
@section('content')
<div id="nx-app" data-page="order" data-order-number="{{ $orderNumber }}"><div class="nx-empty"><div class="spinner-border"></div></div></div>
@endsection
