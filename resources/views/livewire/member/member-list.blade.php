<?php

use App\Models\Member;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;

new class extends Component {

    public $search;
    
    #[Computed]
    public function members()
    {
        return Member::where('name','like','%'.$this->search.'%')->orderBy('order','asc')->paginate(12);
    }

    #[Computed]
    public function latestMember()
    {
        return Member::latest()->first();
    }

    public function orderUp($memberId)
    {
        $currentMember = Member::findOrFail($memberId);
        $aboveMember = Member::where('order', $currentMember->order - 1)->first();
        $currentMember->order = $currentMember->order - 1;
        $aboveMember->order = $aboveMember->order + 1;
        $currentMember->save();
        $aboveMember->save();
    }

    public function orderDown($memberId)
    {
        $currentMember = Member::findOrFail($memberId);
        $belowMember = Member::where('order', $currentMember->order + 1)->first();
        $currentMember->order = $currentMember->order + 1;
        $belowMember->order = $belowMember->order - 1;
        $currentMember->save();
        $belowMember->save();
    }

    public function deleteMember($memberId)
    {
        Member::find($memberId)->delete();
        $this->dispatch('deleted-member');
    }
}; ?>

<div>
    <flux:button icon="plus" wire:navigate href="{{ route('member.create') }}"></flux:button>
    <div class="flex items-start max-md:flex-col">
        <flux:input wire:model.live="search" label="Nama" placeholder="Cari Nama"></flux:input>
    </div>
    <x-action-message class="me-3 text-green-500" on="deleted-member">
        {{ __('Berhasil menghapus anggota.') }}
    </x-action-message>
    @forelse ($this->members as $item)
    <div class="space-y-2">
        <flux:text class="mt-2 ">
            {{ $item->order }} {{ $item->name }} {{ $item->gender() }} {{ $item->phone }}
            @if (!$loop->first)   
                <flux:button icon="arrow-up" size="xs" wire:click="orderUp({{ $item->id }})"></flux:button>
            @endif
            @if (!$loop->last)   
                <flux:button icon="arrow-down" size="xs" wire:click="orderDown({{ $item->id }})"></flux:button>
            @endif
            <flux:button icon="pencil" href="{{ route('member.edit',['memberId'=>$item->id]) }}" variant="primary" size="xs"></flux:button>
            <flux:button icon="trash" variant="danger" size="xs" wire:click="deleteMember({{ $item->id }})" wire:confirm="apakah anda yakin ingin menghapus {{ $item->name }} ?"></flux:button>
        </flux:text>
        <flux:separator variant="subtle" />
    </div>
    @empty
        
    @endforelse
</div>
