<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'iso_code',
    ];
    protected $appends = [
        'flag_icon_url',
    ];
    protected $hidden = [
        'flag_icon_s3_key',
    ];

    public $timestamps = false;

    public function getFlagIconUrlAttribute()
    {
        return $this->flag_icon_s3_key ? Storage::url('flag_icon_s3_key') : null;
    }

    public function regions()
    {
        return $this->hasMany(Region::class, 'country_iso_code', 'iso_code')
            ->orderBy('name', 'ASC');
    }
}
