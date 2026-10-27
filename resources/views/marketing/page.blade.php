@extends('layouts.marketing')

@section('title', $page->meta_title ?: $page->title . ' - ' . $settings['site_name'])
@section('meta_description', $page->meta_description)

@section('content')
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-8">{{ $page->title }}</h1>
        <div class="prose prose-lg max-w-none">
            {!! $page->content !!}
        </div>
    </div>
</section>
@endsection
