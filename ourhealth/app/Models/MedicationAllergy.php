<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationAllergy extends Model
{
    use HasFactory;

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    public function allergy()
    {
        return $this->belongsTo(Allergy::class);
    }
}
