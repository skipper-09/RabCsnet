<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AtpProject;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

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
            'file' => 'required|file|mimes:zip,rar|max:20240' // Only .zip and .rar, 10MB max
        ]);
    
        // Find active ATP Project for the given project
        $atpProject = AtpProject::where('project_id', $project->id)
            ->where('active', true)
            ->first();
    
        if (!$atpProject) {
            abort(403, 'ATP Upload is not enabled for this project');
        }
    
        $filename = '';
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = 'atpproject_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
    
            $path = Storage::disk('s3')->putFileAs('atpprojects', $file, $filename, 'public'); // Using 'public' visibility

            Storage::disk('s3')->put($path, fopen($file, 'r'), [
                'ACL' => 'public-read'  // Explicitly set the ACL to 'public-read'
            ]);
            $fileUrl = Storage::disk('s3')->url($path);
        }

    
        $atpProject->update([
            'vendor_id' => $project->vendor_id,
            'file' => $fileUrl,
        ]);
    
        // Redirect with success message
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

    $fileUrl = $atpProject->file;

    return response()->stream(function () use ($fileUrl) {
        $fileContent = file_get_contents($fileUrl);
        echo $fileContent;
    }, 200, [
        'Content-Type' => 'application/octet-stream', // Change this depending on file type
        'Content-Disposition' => 'attachment; filename="' . basename($fileUrl) . '"',
        'Content-Length' => strlen(file_get_contents($fileUrl)),
    ]);
}

}
