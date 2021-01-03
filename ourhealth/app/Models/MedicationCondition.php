<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_id', 'condition_id',
    ];

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }
}
