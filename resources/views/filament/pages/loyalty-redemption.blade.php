<x-filament-panels::page>
    <div class="max-w-2xl">
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 p-6">
            <h2 class="text-base font-semibold text-gray-950 mb-4">Tukar Poin Loyalitas Jamaah</h2>
            <form wire:submit.prevent="redeem">
                {{ $this->form }}
                <div class="mt-4">
                    <x-filament::button type="submit" color="success" size="md">
                        <x-filament::icon icon="heroicon-o-check-circle" class="h-5 w-5 mr-2" />
                        Tukar Poin
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>
