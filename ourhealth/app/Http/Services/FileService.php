<?php

namespace App\Http\Services;

use App\Models\File;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
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

    public function getFromPatient(Patient $patient)
    {
        return $patient->files;
    }

    public function getFromVisit(Visit $visit)
    {
        return $visit->files;
    }

    public function get($id, $fail = false)
    {
        return $fail ? File::findOrFail($id) : File::find($id);
    }

    public function store($data)
    {
        $validator = Validator::make($data, [
            'patient' => 'required|exists:patients,id',
            'visit' => 'sometimes|nullable|exists:visits,id',
            'file_name' => 'sometimes|nullable|string|max:255',
            'file' => 'required|file'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $patient = Patient::findOrFail($data['patient']);
        if (isset($data['visit'])) {
            $visit = Visit::findOrFail($data['visit']);
            if ($visit->patient_id !== $patient->id) {
                abort(403, 'Invalid visit or patient');
            }
        }

        $file = new File;
        $file->patient_id = $patient->id;
        $file->visit_id = isset($visit) ? $visit->id : null;
        $file->file_name = isset($data['file_name']) ? $data['file_name'] : $data['file']->getClientOriginalName();
        $file->s3_key = $this->upload($data['file']);
        $file->uploaded_by_id = auth()->user()->id;
        $file->save();

        return $file;
    }

    public function destroy($id)
    {
        $file = File::findOrFail($id);
        Gate::authorize('delete', $file);
        $file->delete();
    }
}
