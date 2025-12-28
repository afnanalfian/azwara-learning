@extends('layouts.landing')

@section('title', 'Bimbel Online Matematika dan SKD')

@section('content')
    @include('landing.partials.hero')
    @include('landing.partials.about')
    @include('landing.partials.courses')
    @include('landing.partials.faq')
@endsection
