<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    

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

    <flux:modal name="add-transaction">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Update profile</flux:heading>
                <flux:text class="mt-2">Make changes to your personal details.</flux:text>
            </div>

            <flux:select>
                @foreach ($this->members as $member)
                    <flux:select.option value="{{ $member->id }}"> {{ $member->name }} </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input label="Date of birth" type="date" />

            <div class="flex">
                <flux:spacer />

                <flux:button type="submit" variant="primary">Save changes</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
