<?php

use App\Models\Member;
use Livewire\Volt\Component;

new class extends Component {

    public $name, $phone, $gender = 1;

    public Member $member;

    public $memberId;

    public function mount()
    {
        $this->member = Member::findOrFail($this->memberId);
        $this->name = $this->member->name;
        $this->phone = $this->member->phone;
        $this->gender = $this->member->gender;
    }

    public function saveMember()
    {
        $this->validate([
            'name' => 'required',
            'phone' => 'required',
        ]);

        $this->member->name = $this->name;
        $this->member->phone = $this->phone;
        $this->member->gender = $this->gender;
        $this->member->save();

        $this->dispatch('saved-member');
    }
}; ?>
<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:button size="xs" class="mb-2" icon="arrow-left" href="{{ route('member') }}" wire:navigate>back</flux:button>
        <flux:heading size="xl" level="1">{{ __('Edit Anggota') }} {{ $member->name }}</flux:heading>
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
                    <flux:input label="Nama" wire:model="name" placeholder="Nama Anggota"></flux:input>
                    <flux:input label="Phone" wire:model="phone" placeholder="Nomor Hp"></flux:input>
                    <flux:select wire:model="gender" label="Gender">
                        <flux:select.option value="1">{{ __('Laki-laki') }}</flux:select.option>
                        <flux:select.option value="2">{{ __('Perempuan') }}</flux:select.option>
                    </flux:select>
                    <flux:button icon="arrow-up" size="sm" variant="primary" type="submit" >Simpan</flux:button>
                </form>
            </div>
        </div>
    </div>
</section>

