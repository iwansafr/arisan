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
    <flux:button icon="folder-arrow-down" wire:naviget href="{{ route('member.import') }}"></flux:button>
    <flux:button icon="plus" wire:navigate href="{{ route('member.create') }}"></flux:button>
    <div class="flex items-start max-md:flex-col">
        <div class="mt-2">
            <flux:input wire:model.live="search" placeholder="Cari Nama"></flux:input>
        </div>
    </div>
    <x-action-message class="me-3 text-green-500" on="deleted-member">
        {{ __('Berhasil menghapus anggota.') }}
    </x-action-message>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-2">
        <table class="w-full text-sm text-left rtl:text-right text-zinc-500 dark:text-zinc-400">
            <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-700 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Order
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Nama
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Gender
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Hp
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->members as $member)
                    <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-zinc-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 border-zinc-200">
                        <th scope="row" class="px-6 py-4 font-medium text-zinc-900 whitespace-nowrap dark:text-white">
                            {{ $member->order }}
                        </th>
                        <td class="px-6 py-4">
                            @if (!$loop->first)   
                                <flux:button icon="arrow-up" size="xs" wire:key="order-up-{{ $member->id }}" wire:click="orderUp({{ $member->id }})"></flux:button>
                            @endif
                            @if (!$loop->last)   
                                <flux:button icon="arrow-down" size="xs" wire:key="order-down-{{ $member->id }}" wire:click="orderDown({{ $member->id }})"></flux:button>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {{ $member->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $member->gender() }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $member->phone }}
                        </td>
                        <td class="px-6 py-4">
                            <flux:button icon="pencil" href="{{ route('member.edit',['memberId'=>$member->id]) }}" variant="primary" size="xs"></flux:button>
                            <flux:button icon="trash" variant="danger" size="xs" wire:click="deleteMember({{ $member->id }})" wire:confirm="apakah anda yakin ingin menghapus {{ $member->name }} ?"></flux:button>
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
