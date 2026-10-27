@props(['package' => null, 'dark' => false])
@php $types = ['quad' => 'Quad (4 orang)', 'triple' => 'Triple (3 orang)', 'double' => 'Double (2 orang)', 'single' => 'Single (1 orang)']; @endphp
<div class="grid {{ $dark ? 'grid-cols-2' : 'grid-cols-1 sm:grid-cols-2' }} gap-3">
    @foreach ($types as $key => $label)
        @php $priceField = 'price_' . $key; $price = $package?->$priceField ?? $package?->price_quad ?? 0; @endphp
        @if($price > 0)
        <label class="relative border-2 rounded-xl p-4 cursor-pointer transition {{ $dark ? 'border-white/20 hover:border-white/40 has-[:checked]:border-white has-[:checked]:bg-white/10' : 'border-gray-200 hover:border-indigo-300 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 has-[:checked]:ring-2 has-[:checked]:ring-indigo-200' }}">
            <div class="flex items-start gap-3">
                <input type="radio" name="room_type" value="{{ $key }}" {{ $key === 'quad' ? 'checked' : '' }} class="mt-0.5 w-4 h-4 {{ $dark ? 'text-indigo-600 border-white/30 bg-white/20' : 'text-indigo-600 border-gray-300' }} focus:ring-indigo-500">
                <div>
                    <div class="text-sm font-semibold {{ $dark ? '' : 'text-gray-900' }}">{{ $label }}</div>
                    <div class="text-lg font-bold {{ $dark ? '' : 'text-indigo-600' }} mt-0.5">Rp {{ number_format($price, 0, ',', '.') }}</div>
                    <div class="text-xs {{ $dark ? 'text-indigo-300' : 'text-gray-400' }} mt-0.5">/orang</div>
                </div>
            </div>
        </label>
        @endif
    @endforeach
</div>
@error('room_type') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
