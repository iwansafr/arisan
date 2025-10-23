<?php

use App\Models\Config;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    
    public $config;

    public function mount()
    {
        $this->config = Config::where('key', 'period')->first();
    }

    #[Computed]
    public function periods()
    {
        return \App\Models\Period::latest()->get();
    }
}; ?>
<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:button size="xs" class="mb-2" icon="arrow-left" href="{{ route('member') }}" wire:navigate>back</flux:button>
        <flux:subheading size="lg" class="mb-6">{{ __('Anggota Arisan') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>
    <div class="flex items-start max-md:flex-col">
        <flux:separator class="md:hidden" />
        <div class="flex-1 self-stretch max-md:pt-6">
            <div class="w-full max-w-lg">
                <form wire:submit="saveMember" class="my-6 w-full space-y-6">
                    <x-action-message class="me-3 text-green-500" on="saved-member">
                        {{ __('Berhasil mengubah data anggota.') }}
                    </x-action-message>
                    <flux:select>
                        @foreach ($this->periods as $period)
                            <flux:select.option>{{ $period->title }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:button icon="arrow-up" size="sm" variant="primary" type="submit" >Simpan</flux:button>
                </form>
            </div>
        </div>
    </div>
</section>