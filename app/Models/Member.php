<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'order',
        'name',
        'gender',
        'phone',
    ];

    public function generateOrder()
    {
        $latestMember = Member::latest()->first();
        if (!empty($latestMember)) {
            return $latestMember->order + 1;
        }else{
            return 1;
        }
    }

    public static function genderOptions()
    {
        return [
            '1' => 'Laki-laki',
            '2' => 'Perempuan',
        ];
    }

    public function gender()
    {
        return $this->genderOptions()[$this->gender];
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
