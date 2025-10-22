<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    public $title;
    public $description;

    public $search;

    #[Computed]
    public function periods()
    {
        return \App\Models\Period::all();
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|unique:periods,title',
            'description' => 'required',
        ]);

        \App\Models\Period::create([
            'title' => $this->title,
            'description' => $this->description,
        ]);

        $this->reset(['title','description']);

        $this->dispatch('create-period');
    }
}; ?>

<div>
    <flux:modal.trigger name="add-transaction">
        <flux:button>Tambah Periode</flux:button>
    </flux:modal.trigger>

    <flux:modal name="add-transaction" class="w-full">
        <form wire:submit="save">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Add Period</flux:heading>
                </div>
                <x-action-message class="me-3 text-green-500" on="create-period">
                    {{ __('Berhasil Menambah Periode.') }}
                </x-action-message>
    
                <flux:input label="Periode" type="text" wire:model="title" placeholder="Nama Periode" />
                <flux:textarea label="Deskripsi" wire:model="description" placeholder="Deskripsi Periode"></flux:textarea>
    
                <div class="flex">
                    <flux:spacer />
    
                    <flux:button type="submit" variant="primary">Tambah</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    <div class="flex items-start max-md:flex-col">
        <div class="mt-2">
            <flux:input wire:model.live="search" placeholder="Cari Nama"></flux:input>
        </div>
    </div>
    <x-action-message class="me-3 text-green-500" on="deleted-member">
        {{ __('Berhasil menghapus Periode.') }}
    </x-action-message>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-2">
        <table class="w-full text-sm text-left rtl:text-right text-zinc-500 dark:text-zinc-400">
            <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-700 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Periode
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Deskripsi
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->periods as $period)
                    <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-zinc-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 border-zinc-200">
                        <th scope="row" class="px-6 py-4 font-medium text-zinc-900 whitespace-nowrap dark:text-white">
                            {{ $period->id }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $period->title }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $period->description }}
                        </td>
                        <td class="px-6 py-4">
                            {{-- <flux:button icon="pencil" href="{{ route('period.edit',['memberId'=>$period->id]) }}" variant="primary" size="xs"></flux:button> --}}
                            <flux:button icon="trash" variant="danger" size="xs" wire:click="deletePeriod({{ $period->id }})" wire:confirm="apakah anda yakin ingin menghapus {{ $period->title }} ?"></flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center">
                            Tidak ada data anggota.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
