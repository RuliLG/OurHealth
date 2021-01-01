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

    public function getPhotoUrlAttribute()
    {
        return $this->photo_s3_key ? Storage::url($this->photo_s3_key) : null;
    }

    public function getAgeAttribute()
    {
        return $this->birth_date->age;
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'iso_code', 'nationality');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function preferred_hospital()
    {
        return $this->belongsTo(Hospital::class, 'id', 'preferred_hospital_id');
    }

    public function third_party_insurance()
    {
        return $this->belongsTo(ThirdPartyInsurance::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'id', 'doctor_id');
    }

    public function biological_father()
    {
        return $this->belongsTo(Patient::class, 'id', 'biological_father_id');
    }

    public function biological_mother()
    {
        return $this->belongsTo(Patient::class, 'id', 'biological_mother_id');
    }
}
