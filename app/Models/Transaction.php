<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'period_id',
        'member_id',
        'date',
        'amount',
        'alias',
        'description',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
