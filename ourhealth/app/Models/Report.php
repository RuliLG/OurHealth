<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function files()
    {
        return $this->hasManyThrough(File::class, ReportFile::class);
    }

    public function measurements()
    {
        return $this->hasManyThrough(Measurement::class, ReportMeasurement::class);
    }
}
