<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $appends = [
        'file_path',
    ];
    protected $hidden = [
        's3_key',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function uploaded_by()
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }

    public function getFilePathAttribute()
    {
        return Storage::url($this->s3_key);
    }
}
