<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(MedicationCategory::class);
    }

    public function allergies()
    {
        return $this->hasManyThrough(Allergy::class, MedicationAllergy::class);
    }

    public function conditions()
    {
        return $this->hasManyThrough(Condition::class, MedicationCondition::class);
    }

    public function incompatibilities()
    {
        return $this->hasManyThrough(Medication::class, MedicationCondition::class);
    }
}
