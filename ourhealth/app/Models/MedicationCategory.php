<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationCategory extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }
}
