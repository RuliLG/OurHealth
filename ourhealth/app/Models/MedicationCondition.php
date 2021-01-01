<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationCondition extends Model
{
    use HasFactory;

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }
}
