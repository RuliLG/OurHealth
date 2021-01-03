<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'role',
        'profile_picture_s3_key',
    ];

    protected $appends = [
        'is_superadmin',
        'is_hospital_admin',
        'is_doctor',
        'profile_picture_url',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getProfilePictureUrlAttribute()
    {
        return $this->profile_picture_s3_key ? Storage::url($this->profile_picture_s3_key) : null;
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function hospital_department()
    {
        return $this->belongsTo(HospitalDepartment::class);
    }

    public function getIsSuperadminAttribute()
    {
        return $this->role === 'superadmin';
    }

    public function getIsHospitalAdminAttribute()
    {
        return $this->role === 'hospital_admin';
    }

    public function getIsDoctorAttribute()
    {
        return $this->role === 'doctor';
    }
}
