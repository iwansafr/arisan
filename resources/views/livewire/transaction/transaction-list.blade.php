<?php

use Carbon\Carbon;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    
    public $date;
    public $member_id;

    public function mount()
    {
        //next 2 weeks date
        $this->date = Carbon::now()->addWeeks(2)->format('Y-m-d');
    }

    #[Computed]
    public function members()
    {
        return \App\Models\Member::doesntHave('transaction')->get();
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
                    <flux:heading size="lg">Update profile</flux:heading>
                    <flux:text class="mt-2">Make changes to your personal details.</flux:text>
                </div>
    
                <flux:select wire:model="member_id" label="Member">
                    @foreach ($this->members as $member)
                        <flux:select.option value="{{ $member->id }}"> {{ $member->name }} </flux:select.option>
                    @endforeach
                </flux:select>
    
                <flux:input label="Tanggal" type="date" wire:model="date" />
    
                <div class="flex">
                    <flux:spacer />
    
                    <flux:button type="submit" variant="primary">Save changes</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
