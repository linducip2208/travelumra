@props(['type' => 'success', 'message' => null])
@if($message ?? $slot->isNotEmpty())
<div {{ $attributes->merge(['class' => $type === 'success'
    ? 'rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700'
    : 'rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600']) }} role="alert">
    {{ $message ?? $slot }}
</div>
@endif
