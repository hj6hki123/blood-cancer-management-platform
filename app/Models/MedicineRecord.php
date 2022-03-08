<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'date',
        'course',
        'type',
        'dose',
    ];

    public function cases(){
        return $this->belongsTo(CaseModel::class, 'case_id', 'id');
    }
}
