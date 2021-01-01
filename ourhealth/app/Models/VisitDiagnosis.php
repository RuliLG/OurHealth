<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitDiagnosis extends Model
{
    use HasFactory;

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function allergy()
    {
        return $this->belongsTo(Allergy::class);
    }
}
