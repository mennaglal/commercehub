@extends('web.nexus.page')
@section('title', 'Information')
@section('page', 'page')
@php($slug = $slug)
@section('content')
<div id="nx-app" data-page="page" data-slug="{{ $slug }}"><div class="nx-empty"><div class="spinner-border"></div></div></div>
@endsection
