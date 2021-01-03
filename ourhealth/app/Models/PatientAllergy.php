<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientAllergy extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'allergy_id',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function allergy()
    {
        return $this->belongsTo(Allergy::class);
    }
}
