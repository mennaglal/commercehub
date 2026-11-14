@extends('web.nexus.page')
@section('title', 'Return details')
@section('page', 'return')
@section('content')
<div id="nx-app" data-page="return" data-return-number="{{ $returnNumber }}"><div class="nx-empty"><div class="spinner-border"></div></div></div>
@endsection
