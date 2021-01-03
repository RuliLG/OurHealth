<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'id', 'doctor_id');
    }

    public function vitals()
    {
        return $this->hasMany(Measurement::class)
            ->where('patient_id', $this->patient_id)
            ->where('batch_id', $this->vitals_batch_id);
    }

    public function triage()
    {
        return $this->hasMany(Measurement::class)
            ->where('patient_id', $this->patient_id)
            ->where('batch_id', $this->triage_batch_id);
    }

    public function conditions()
    {
        return $this->hasManyThrough(Condition::class, VisitDiagnosis::class);
    }

    public function allergies()
    {
        return $this->hasManyThrough(Allergy::class, VisitDiagnosis::class);
    }

    public function symptoms()
    {
        return $this->hasManyThrough(Symptom::class, VisitSymptom::class);
    }

    public function files()
    {
        return $this->hasMany(File::class)
            ->orderBy('created_at', 'DESC');
    }
}
