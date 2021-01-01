<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitSymptom extends Model
{
    use HasFactory;

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function symptom()
    {
        return $this->belongsTo(Symptom::class);
    }
}
