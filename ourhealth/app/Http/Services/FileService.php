<?php

namespace App\Http\Services;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class FileService
{
    public function upload(UploadedFile $file, $folder = 'files')
    {
        while (Str::endsWith($folder, '/')) {
            $folder = substr($folder, 0, -1);
        }

        $uuid = Str::uuid();
        $fileName = $uuid . '.' . $file->extension();
        $s3Key = $folder . '/' . $fileName;
        Storage::put($s3Key, $file->get());
        return $s3Key;
    }
}
