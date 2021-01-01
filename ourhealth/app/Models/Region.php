<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'iso_code', 'country_iso_code');
    }

    public function hospitals()
    {
        return $this->hasMany(Hospital::class);
    }
}
