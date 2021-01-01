<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationIncompatibility extends Model
{
    use HasFactory;

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    public function incompatible_with()
    {
        return $this->belongsTo(Medication::class, 'id', 'incompatible_with');
    }
}
