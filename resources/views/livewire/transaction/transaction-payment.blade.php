<?php

use App\Models\Transaction;
use Livewire\Volt\Component;

new class extends Component {
    public $transactionId;
    public Transaction $transaction;

    public function mount($transactionId)
    {
        $this->transactionId = decrypt($transactionId);
        $this->transaction = Transaction::with('payments')->findOrFail($this->transactionId);
    }
}; ?>

<div>
    {{  dd($transaction) }}
</div>
