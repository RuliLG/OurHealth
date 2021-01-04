<?php

namespace App\Http\Services;

use App\Models\File;
use App\Models\Measurement;
use App\Models\Report;
use App\Models\ReportFile;
use App\Models\ReportMeasurement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class ReportService
{
    public function getAll($data = [])
    {
        $validator = Validator::make($data, [
            'patient' => 'required|exists:patients,id',
            'visit' => 'sometimes|nullable|exists:visits,id'
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $reports = Report::where('patient_id', $data['patient']);
        if (isset($data['visit'])) {
            $reports->where('visit_id', $data['visit']);
        }

        return $reports->orderBy('created_at', 'DESC')->get();
    }

    public function get($id, $fail = false)
    {
        return $fail ? Report::with('files', 'measurements')->findOrFail($id) : Report::with('files', 'measurements')->find($id);
    }

    public function store($data)
    {
        $validator = Validator::make($data, [
            'patient' => 'required|exists:patients,id',
            'visit' => 'required|exists:visits,id',
            'name' => 'required|string|max:255',
            'comments' => 'sometimes|nullable|string',
            'measurements' => 'sometimes|nullable|array',
            'measurements.*' => 'sometimes|exists:measurements,id',
            'files' => 'sometimes|nullable|array',
            'files.*' => 'sometimes|exists:files,id',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        DB::beginTransaction();
        $report = new Report;
        $report->patient_id = $data['patient'];
        $report->visit_id = $data['visit'];
        $report->name = $data['name'];
        if (isset($data['comments'])) {
            $report->comments = $data['comments'];
        }

        $report->save();
        if (isset($data['measurements'])) {
            foreach ($data['measurements'] as $measurementId) {
                $this->linkMeasurement($report, $measurementId);
            }
        }

        if (isset($data['files'])) {
            foreach ($data['files'] as $fileId) {
                $this->linkFile($report, $fileId);
            }
        }

        DB::commit();

        return $this->get($report->id);
    }

    public function update($id, $data)
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|nullable|string|max:255',
            'comments' => 'sometimes|nullable|string',
            'measurements' => 'sometimes|nullable|array',
            'measurements.*' => 'sometimes|exists:measurements,id',
            'files' => 'sometimes|nullable|array',
            'files.*' => 'sometimes|exists:files,id',
        ]);

        if ($validator->fails()) {
            throw new InvalidParameterException($validator->errors()->first());
        }

        $report = Report::findOrFail($id);
        DB::beginTransaction();
        if (isset($data['name'])) {
            $report->name = $data['name'];
        }

        if (isset($data['comments'])) {
            $report->comments = $data['comments'];
        }

        if ($report->isDirty()) {
            $report->save();
        }

        if (isset($data['measurements'])) {
            foreach ($data['measurements'] as $measurementId) {
                $this->linkMeasurement($report, $measurementId);
            }
        }

        if (isset($data['files'])) {
            foreach ($data['files'] as $fileId) {
                $this->linkFile($report, $fileId);
            }
        }

        DB::commit();

        return $this->get($report->id);
    }

    public function linkMeasurement(Report $report, $measurementId)
    {
        $measurement = Measurement::where([
            'id' => $measurementId,
            'patient_id' => $report->patient_id
        ])->first();
        if (!$measurement) {
            return;
        }

        $reportMeasurement = new ReportMeasurement;
        $reportMeasurement->report_id = $report->id;
        $reportMeasurement->measurement_id = $measurementId;
        $reportMeasurement->save();
    }

    public function unlinkMeasurement(Report $report, $measurementId)
    {
        ReportMeasurement::where([
            'report_id' => $report->id,
            'measurement_id' => $measurementId
        ])->delete();
    }

    public function linkFile(Report $report, $fileId)
    {
        $file = File::where([
            'id' => $fileId,
            'patient_id' => $report->patient_id
        ])->first();
        if (!$file) {
            return;
        }

        $reportFile = new ReportFile;
        $reportFile->report_id = $report->id;
        $reportFile->file_id = $fileId;
        $reportFile->save();
    }

    public function unlinkFile(Report $report, $fileId)
    {
        ReportFile::where([
            'report_id' => $report->id,
            'file_id' => $fileId
        ])->delete();
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
    }
}
