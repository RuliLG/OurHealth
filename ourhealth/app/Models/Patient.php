<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Patient extends Model
{
    use HasFactory;

    protected $appends = [
        'photo_url', 'age',
    ];

    protected $hidden = [
        'photo_s3_key',
    ];

    protected $casts = [
        'date_of_birth' => 'datetime',
    ];

    public function getPhotoUrlAttribute()
    {
        return $this->photo_s3_key ? Storage::url($this->photo_s3_key) : null;
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'nationality', 'iso_code');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function preferred_hospital()
    {
        return $this->belongsTo(Hospital::class, 'preferred_hospital_id', 'id');
    }

    public function third_party_insurance()
    {
        return $this->belongsTo(ThirdPartyInsurance::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id', 'id');
    }

    public function biological_father()
    {
        return $this->belongsTo(Patient::class, 'biological_father_id', 'id');
    }

    public function biological_mother()
    {
        return $this->belongsTo(Patient::class, 'biological_mother_id', 'id');
    }

    public function conditions()
    {
        return $this->belongsToMany(Condition::class, 'patient_conditions');
    }

    public function allergies()
    {
        return $this->belongsToMany(Allergy::class, 'patient_allergies');
    }

    public function medications()
    {
        return $this->belongsToMany(Medication::class, 'patient_medications');
    }

    public function measurements()
    {
        return $this->hasMany(Measurement::class)
            ->orderBy('created_at', 'DESC');
    }

    public function files()
    {
        return $this->hasMany(File::class)
            ->orderBy('created_at', 'DESC');
    }
}
