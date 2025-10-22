<?php

use Carbon\Carbon;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    
    public $date;
    public $member_id = null;
    public $period_id;

    public function mount()
    {
        //next 2 weeks date
        $this->date = Carbon::now()->addWeeks(2)->format('Y-m-d');
    }

    #[Computed]
    public function members()
    {
        return \App\Models\Member::hwereDoesntHave('transaction',function(Builder $query){
            $query->whereDate('period_id', $this->period_id);
        })->get();
    }

    public function save()
    {
        $this->validate([
            'date' => 'required|date',
            'member_id' => 'required',
        ]);
    }
}; ?>

<div class="w-full">
    <flux:modal.trigger name="add-transaction">
        <flux:button>Tambah Transaksi</flux:button>
    </flux:modal.trigger>

    <flux:modal name="add-transaction" class="w-full">
        <form wire:submit="save">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Add Transaction</flux:heading>
                    <flux:text class="mt-2">dapat arisan.</flux:text>
                </div>
                <x-action-message class="me-3 text-green-500" on="create-member">
                    {{ __('Berhasil menambah data transaksi.') }}
                </x-action-message>

                <flux:select wire:model="member_id" label="Member" placeholder="Anggota Belum Dapat Arisan">
                    <flux:select.option value="">{{ __('Select Member') }}</flux:select.option>
                    @foreach ($this->members as $member)
                        <flux:select.option value="{{ $member->id }}"> {{ $member->name }} </flux:select.option>
                    @endforeach
                </flux:select>
    
                <flux:input label="Tanggal" type="date" wire:model="date" />
    
                <div class="flex">
                    <flux:spacer />
    
                    <flux:button type="submit" variant="primary">Add Transaction</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
