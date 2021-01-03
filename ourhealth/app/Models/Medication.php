<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(MedicationCategory::class, 'medication_category_id');
    }

    public function allergies()
    {
        return $this->belongsToMany(Allergy::class, 'medication_allergies');
    }

    public function conditions()
    {
        return $this->belongsToMany(Condition::class, 'medication_conditions');
    }

    public function incompatibilities()
    {
        return $this->belongsToMany(Medication::class, 'medication_incompatibilities', 'incompatible_with', 'medication_id');
    }
}
