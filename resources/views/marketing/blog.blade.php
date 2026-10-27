@extends('layouts.marketing')

@section('title', 'Blog - ' . $settings['site_name'])

@section('content')
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900">Blog & Artikel</h1>
            <p class="mt-3 text-gray-500 max-w-xl mx-auto">Tips umrah, panduan ibadah, informasi terbaru seputar perjalanan ke Tanah Suci</p>
        </div>

        @if($posts->isEmpty())
        <p class="text-center text-gray-500 py-10">Belum ada artikel.</p>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($posts as $post)
            <a href="{{ route('blog.show', $post->slug) }}" class="group bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all flex flex-col">
                @if($post->featured_image)
                <div class="h-48 overflow-hidden">
                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" loading="lazy" width="600" height="400" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                @endif
                <div class="p-5 flex-1 flex flex-col">
                    @if($post->category)
                    <span class="text-xs font-semibold text-brand-600 uppercase tracking-wider mb-2">{{ $post->category->name }}</span>
                    @endif
                    <h2 class="font-bold text-gray-900 group-hover:text-brand-700 transition line-clamp-2 mb-2">{{ $post->title }}</h2>
                    @if($post->excerpt)
                    <p class="text-sm text-gray-500 line-clamp-3 mb-4">{{ $post->excerpt }}</p>
                    @endif
                    <div class="mt-auto flex items-center justify-between text-xs text-gray-400">
                        <span>{{ $post->author->name ?? 'Admin' }}</span>
                        <span>{{ $post->published_at?->format('d M Y') }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>
        @endif
    </div>
</section>
@endsection
