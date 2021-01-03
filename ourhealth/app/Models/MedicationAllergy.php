<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationAllergy extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_id', 'allergy_id',
    ];

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    public function allergy()
    {
        return $this->belongsTo(Allergy::class);
    }
}
