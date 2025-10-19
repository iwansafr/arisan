<?php

use App\Models\Member;
use Livewire\Volt\Component;

new class extends Component {

    public $name, $phone, $gender = 1;

    public function createMember()
    {
        $this->validate([
            'name' => 'required',
            'phone' => 'required',
        ]);

        $member = new Member();
        $member->name = $this->name;
        $member->phone = $this->phone;
        $member->gender = $this->gender;
        $member->order = $member->generateOrder();
        $member->save();
        $this->reset(['name','phone','gender']);

        $this->dispatch('create-member');
    }
}; ?>
<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:button size="xs" icon="arrow-left" href="{{ route('member') }}" wire:navigate>back</flux:button>
        <flux:heading size="xl" level="1">{{ __('Tambah Anggota') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Anggota Arisan') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>
    <div class="flex items-start max-md:flex-col">
        <flux:separator class="md:hidden" />
        <div class="flex-1 self-stretch max-md:pt-6">
            <div class="w-full max-w-lg">
                <form wire:submit="createMember" class="my-6 w-full space-y-6">
                    <x-action-message class="me-3 text-green-500" on="create-member">
                        {{ __('Berhasil menambah anggota.') }}
                    </x-action-message>
                    <flux:input label="Nama" wire:model="name" placeholder="Nama Anggota"></flux:input>
                    <flux:input label="Phone" wire:model="phone" placeholder="Nomor Hp"></flux:input>
                    <flux:select wire:model="gender" label="Gender">
                        <flux:select.option value="1">{{ __('Laki-laki') }}</flux:select.option>
                        <flux:select.option value="2">{{ __('Perempuan') }}</flux:select.option>
                    </flux:select>
                    <flux:button type="submit">Simpan</flux:button>
                </form>
            </div>
        </div>
    </div>
</section>

