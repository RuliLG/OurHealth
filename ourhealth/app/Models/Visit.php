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
        return $this->belongsTo(User::class, 'doctor_id', 'id');
    }

    public function vitals()
    {
        return $this->hasMany(Measurement::class);
    }

    public function triage()
    {
        return $this->hasMany(Measurement::class);
    }

    public function conditions()
    {
        return $this->belongsToMany(Condition::class, 'visit_diagnoses');
    }

    public function allergies()
    {
        return $this->belongsToMany(Allergy::class, 'visit_diagnoses');
    }

    public function symptoms()
    {
        return $this->belongsToMany(Symptom::class, 'visit_symptoms');
    }

    public function files()
    {
        return $this->hasMany(File::class)
            ->orderBy('created_at', 'DESC');
    }

    public function reports()
    {
        return $this->hasMany(Report::class)
            ->orderBy('created_at', 'DESC');
    }
}
