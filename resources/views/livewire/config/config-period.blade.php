<?php

use App\Models\Config;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    
    public $config;
    public $value;
    public $title;

    public function mount()
    {
        $this->config = Config::where('key', 'period')->first();
        if(empty($this->config)){
            $this->config = new Config();
            $this->config->key = 'period';
            $this->config->value = null;
        } else {
            $period = json_decode($this->config->value);
            $this->value = $period->id;
            $this->title = $period->title;
        }
    }

    #[Computed]
    public function periods()
    {
        return \App\Models\Period::latest()->get();
    }

    public function save()
    {
        $this->validate([
            'value' => 'required',
        ]);

        $this->config->value = $this->periods->where('id', $this->value)->first()->toJson();
        $this->config->key = 'period';
        $this->config->save();

        $this->dispatch('saved-config-period');
    }
}; ?>
<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:button size="xs" class="mb-2" icon="arrow-left" href="{{ route('member') }}" wire:navigate>back</flux:button>
        <flux:subheading size="lg" class="mb-6">{{ __('Config Period') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>
    <div class="flex items-start max-md:flex-col">
        <flux:separator class="md:hidden" />
        <div class="flex-1 self-stretch max-md:pt-6">
            <div class="w-full max-w-lg">
                @if(!empty($config)) Periode Aktif: {{ $title }} @else Config Periode belum di atur @endif
                <form wire:submit="save" class="my-6 w-full space-y-6">
                    <x-action-message class="me-3 text-green-500" on="saved-config-period">
                        {{ __('Berhasil mengubah data anggota.') }}
                    </x-action-message>
                    <flux:select wire:model="value">
                        <flux:select.option>Pilih Periode</flux:select.option>
                        @foreach ($this->periods as $period)
                            <flux:select.option value="{{ $period->id }}">{{ $period->title }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:button icon="arrow-up" size="sm" variant="primary" type="submit" >Simpan</flux:button>
                </form>
            </div>
        </div>
    </div>
</section>