@extends('layouts.marketing')

@section('title', 'FAQ - ' . $settings['site_name'])

@section('content')
<section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900">Pertanyaan Umum (FAQ)</h1>
            <p class="mt-3 text-gray-500">Jawaban untuk pertanyaan yang sering diajukan seputar perjalanan umrah dan haji</p>
        </div>

        @if($faqs->isEmpty())
        <p class="text-center text-gray-500 py-10">Belum ada pertanyaan.</p>
        @else
        <div class="space-y-8">
            @foreach($faqs as $category => $items)
            <div>
                @if($category)
                <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $category }}</h2>
                @endif
                <div class="space-y-3">
                    @foreach($items as $faq)
                    <details class="bg-white rounded-xl border border-gray-200 group">
                        <summary class="px-6 py-4 cursor-pointer font-semibold text-gray-900 hover:text-brand-700 transition flex items-center justify-between">
                            {{ $faq->question }}
                            <svg class="w-5 h-5 group-open:rotate-180 transition-transform text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="px-6 pb-4 text-gray-600 leading-relaxed">
                            {!! $faq->answer !!}
                        </div>
                    </details>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endsection
