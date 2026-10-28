@extends('layouts.marketing')

@section('title', $post->meta_title ?: $post->title . ' - Blog ' . $settings['site_name'])
@section('meta_description', $post->meta_description ?: $post->excerpt)

@section('content')
<article class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($post->featured_image)
        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" loading="lazy" width="1200" height="600" class="w-full h-64 sm:h-96 object-cover rounded-2xl mb-8">
        @endif

        @if($post->category)
        <span class="text-xs font-semibold text-brand-600 uppercase tracking-wider">{{ $post->category->name }}</span>
        @endif
        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-2 mb-4">{{ $post->title }}</h1>

        <div class="flex items-center gap-4 text-sm text-gray-500 mb-8 pb-8 border-b">
            <span>{{ $post->author->name ?? 'Admin' }}</span>
            <span>|</span>
            <span>{{ $post->published_at?->format('d M Y') }}</span>
        </div>

        <div class="prose prose-lg max-w-none">
            {!! $post->content !!}
        </div>

        <div class="mt-12 pt-8 border-t">
            <a href="{{ route('blog') }}" class="inline-flex items-center text-brand-600 hover:text-brand-700 font-semibold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Blog
            </a>
        </div>

        @if($relatedPosts->isNotEmpty())
        <div class="mt-12 pt-8 border-t">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Artikel Terkait</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($relatedPosts as $related)
                <a href="{{ route('blog.show', $related->slug) }}" class="group bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition">
                    <h4 class="font-semibold text-gray-900 group-hover:text-brand-700 transition line-clamp-2">{{ $related->title }}</h4>
                    <span class="text-xs text-gray-400 mt-2">{{ $related->published_at?->format('d M Y') }}</span>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</article>
@endsection
