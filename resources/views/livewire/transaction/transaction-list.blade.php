<?php

use Carbon\Carbon;
use App\Models\Transaction;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Illuminate\Database\Eloquent\Builder;

new class extends Component {
    public $date;
    public $member_id = null;
    public $description;
    public $period_id;
    public $activePeriod;
    public $transactionId;

    public function mount()
    {
        //next 2 weeks date
        $this->date = Carbon::now()->addWeeks(2)->format('Y-m-d');
        $this->activePeriod = json_decode(\App\Models\Config::where('key', 'period')->first()->value);
        $this->period_id = $this->activePeriod->id;
    }

    #[Computed]
    public function members()
    {
        return \App\Models\Member::whereDoesntHave('transaction', function (Builder $query) {
            $query->where('period_id', $this->period_id);
        })->get();
    }

    #[Computed]
    public function transactions()
    {
        return Transaction::with('member')->where('period_id', $this->period_id)->latest()->paginate(24);
    }

    public function save()
    {
        $validated = [
            'date' => 'required|date',
            'member_id' => 'required',
            'description' => 'nullable|string',
        ];
        if(!empty($this->transactionId)){
            unset($validated['member_id']);
        }
        $this->validate($validated);

        if (empty($this->transactionId)) {
            try {
                DB::transaction(function () {
                    $transaction = Transaction::create([
                        'period_id' => $this->period_id,
                        'member_id' => $this->member_id,
                        'alias' => $this->members->where('id', $this->member_id)->first()->name,
                        'date' => $this->date,
                        'amount' => 0,
                    ]);
        
                    $paymentMembers = \App\Models\Member::where('id', '!=', $this->member_id)->get();
                    foreach ($paymentMembers as $paymentMember) {
                        \App\Models\TransactionPayment::create([
                            'transaction_id' => $transaction->id,
                            'member_id' => $paymentMember->id,
                            'alias' => $paymentMember->name,
                            'date' => $this->date,
                            'amount' => 0,
                        ]);
                    }
                });
            } catch (\Throwable $th) {
                return $this->addError('transaction_error', 'Gagal menambah transaksi: ' . $th->getMessage());
            }
        }else{
            $transaction = Transaction::findOrFail($this->transactionId);
            $transaction->date = $this->date;
            $transaction->description = $this->description;
            $transaction->save();
        }

        $this->dispatch('created-transaction');
    }

    public function setTransaction($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        $this->member_id = $transaction->member_id;
        $this->date = $transaction->date;
        $this->transactionId = $transactionId;
    }
}; ?>

<div class="w-full">
    <flux:modal.trigger name="add-transaction">
        <flux:button>@if(empty($transactionId)) Tambah @else Update @endif Transaksi</flux:button>
    </flux:modal.trigger>
    <flux:subheading class="mt-2">Periode Aktif: {{ $activePeriod->title }}</flux:subheading>
    <flux:modal name="add-transaction" class="w-full">
        <form wire:submit="save">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Transaction</flux:heading>
                    <flux:text class="mt-2">dapat arisan.</flux:text>
                </div>
                <x-action-message class="me-3 text-green-500" on="created-transaction">
                    {{ __('Berhasil menambah data transaksi.') }}
                </x-action-message>

                @if (empty($transactionId))
                    <flux:select wire:model="member_id" label="Member" placeholder="Anggota Belum Dapat Arisan">
                        <flux:select.option value="">{{ __('Select Member') }}</flux:select.option>
                        @foreach ($this->members as $member)
                            <flux:select.option value="{{ $member->id }}"> {{ $member->name }} </flux:select.option>
                        @endforeach
                    </flux:select>
                @endif

                <flux:input label="Tanggal" type="date" wire:model="date" />
                <flux:textarea label="Keterangan" wire:model="description"></flux:textarea>

                <div class="flex">
                    <flux:spacer />

                    <flux:button type="submit" variant="primary">@if(empty($transactionId)) Add @else Update @endif Transaction</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-2">
        <table class="w-full text-sm text-left rtl:text-right text-zinc-500 dark:text-zinc-400">
            <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-700 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Nama
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Keterangan
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
                @forelse ($this->transactions as $transaction)
                    <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-zinc-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 border-zinc-200">
                        <th scope="row" class="px-6 py-4 font-medium text-zinc-900 whitespace-nowrap dark:text-white">
                            {{ $transaction->created_at->format('d-M-Y') }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $transaction->alias }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $transaction->description }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $transaction->member->phone }}
                        </td>
                        <td class="px-6 py-4">
                            {{-- <flux:button icon="pencil" href="{{ route('transaction.edit',['memberId'=>$transaction->id]) }}" variant="primary" size="xs"></flux:button> --}}
                            <flux:button icon="arrow-right-circle" href="{{ route('transaction.payment',['transactionId'=>encrypt($transaction->id)]) }}" variant="primary" wire:navigate size="xs" class="me-2">Pembayaran</flux:button>
                            <flux:modal.trigger name="add-transaction">
                                <flux:button icon="pencil" variant="primary" size="xs" class="me-2" wire:click="setTransaction({{ $transaction->id }})">
                                </flux:button>
                            </flux:modal.trigger>
                            <flux:button icon="trash" variant="danger" size="xs" wire:click="deleteTransaction({{ $transaction->id }})" wire:confirm="apakah anda yakin ingin menghapus {{ $transaction->name }} ?"></flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center">
                            Tidak ada data transaksi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
