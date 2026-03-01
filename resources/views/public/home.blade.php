@extends('layouts.public')

@section('title', $page->meta_description ?: $page->title . ' - ' . config('app.name'))

@section('content')
    @include('components.public.hero-slider')

    @include('components.public.announcement-ticker')

    @include('components.public.welcome-section', ['page' => $page])

    @include('components.public.stat-counters')

    @include('components.public.course-cards')

    @include('components.public.certifications')

    @include('components.public.testimonials')

    @include('components.public.employers-carousel')
@endsection
