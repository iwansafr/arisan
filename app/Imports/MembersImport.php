<?php

namespace App\Imports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MembersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Member([
            'order'  => $row['order'],
            'name'   => $row['name'],
            'gender' => $row['gender'],
            'phone'  => $row['phone'],
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'order' => 'required|integer',
            'name' => 'required|string|max:255',
            'gender' => 'required|integer|in:1,2',
            'phone' => 'required|string|max:255',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'order.required' => 'Kolom order wajib diisi.',
            'order.integer' => 'Kolom order harus berupa angka.',
            'name.required' => 'Kolom name wajib diisi.',
            'gender.required' => 'Kolom gender wajib diisi.',
            'gender.in' => 'Kolom gender harus 1 (Laki-laki) atau 2 (Perempuan).',
            'phone.required' => 'Kolom phone wajib diisi.',
        ];
    }
}