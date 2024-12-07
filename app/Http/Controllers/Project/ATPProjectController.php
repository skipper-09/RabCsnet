<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AtpProject;
use App\Models\Project;

class ATPProjectController extends Controller
{
    public function enableAtpUpload(Project $project)
    {
        // Ensure start_status is 1
        $project->update(['start_status' => 1]);

        // Check if ATP project already exists
        $atpProject = $project->Projectatp;

        if (!$atpProject) {
            // Create ATP project if it doesn't exist
            $atpProject = $project->Projectatp()->create([
                'vendor_id' => $project->vendor_id,
                'file' => null,
                'active' => true
            ]);
        } else {
            // If ATP project exists but is not active, activate it
            if (!$atpProject->active) {
                $atpProject->update(['active' => true]);
            }
        }

        return redirect()->route('project')->with(['status' => 'Success', 'message' => 'Berhasil Mengaktifkan Upload ATP!']);
    }

    public function disableAtpUpload(Project $project)
    {
        // Ensure start_status is 1
        $project->update(['start_status' => 1]);

        // Check if ATP project exists
        $atpProject = $project->Projectatp;

        if ($atpProject) {
            // Deactivate ATP project
            $atpProject->update(['active' => false]);
        }

        return redirect()->route('project')->with(['status' => 'Success', 'message' => 'Berhasil Menonaktifkan Upload ATP!']);
    }

    public function uploadAtpView(Project $project)
    {
        // Check if ATP is active
        $atpProject = $project->Projectatp;

        // Validate ATP project exists and is active
        if (!$atpProject || !$atpProject->active) {
            abort(403, 'ATP Upload is not enabled for this project');
        }

        $data = [
            'tittle' => 'Upload ATP', // Corrected 'tittle' typo
            'project' => $project,
            'atpProject' => $atpProject
        ];

        return view('pages.project.upload-atp', $data);
    }

    public function storeAtpFile(Request $request, Project $project)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240' // 10MB max
        ]);

        $atpProject = AtpProject::where('project_id', $project->id)
            ->where('active', true)
            ->first();

        if (!$atpProject) {
            abort(403, 'ATP Upload is not enabled for this project');
        }

        // Store file
        $filename = '';
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = 'atpproject_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/files/atpproject/'), $filename);
        }

        // Update ATP Project
        $atpProject->update([
            'vendor_id' => $project->vendor_id, // Assuming vendor user has vendor_id
            'file' => $filename,
            // 'active' => false // Disable further uploads after successful upload
        ]);

        return redirect()->route('project')->with(['status' => 'Success', 'message' => 'Berhasil Mengupload ATP!']);
    }

    public function downloadAtpFile(Project $project)
    {
        $atpProject = AtpProject::where('project_id', $project->id)
            ->where('active', true)
            ->first();

        if (!$atpProject || !$atpProject->file) {
            abort(404, 'ATP File not found');
        }

        $filePath = public_path('storage/files/atpproject/' . $atpProject->file);

        if (!file_exists($filePath)) {
            abort(404, 'File does not exist');
        }

        return response()->download($filePath);
    }
}
