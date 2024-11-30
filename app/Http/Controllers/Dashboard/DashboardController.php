<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Dapatkan pengguna saat ini
        $currentUser = Auth::user();

        // Dapatkan role pengguna saat ini
        $currentUserRole = $currentUser->roles->first()?->name; // Menggunakan null coalescing operator

        // Hitung jumlah proyek yang belum direview
        $totalProjectsNotReviewed = Project::where('status_pengajuan', 'pending')->count();

        // Hitung jumlah proyek yang sudah selesai
        $totalCompletedProjects = Project::where('status', 'finish')->count();

        // Tentukan proyek yang perlu direview berdasarkan role
        $projectsToReview = 0; // Default 0
        switch ($currentUserRole) {
            case 'Accounting':
                $projectsToReview = Project::where('status_pengajuan', 'pending')
                    ->whereDoesntHave('ProjectReview', function ($query) {
                        $query->whereHas('reviewer.roles', function ($roleQuery) {
                            $roleQuery->where('name', 'Accounting');
                        });
                    })->count();
                break;

            case 'Owner':
                $projectsToReview = Project::where('status_pengajuan', 'pending')
                    ->whereDoesntHave('ProjectReview', function ($query) {
                        $query->whereHas('reviewer.roles', function ($roleQuery) {
                            $roleQuery->where('name', 'Owner');
                        });
                    })->count();
                break;

            case 'Developer':
                $projectsToReview = Project::whereIn('status_pengajuan', ['pending', 'in_review'])
                    ->whereHas('Projectfile') 
                    ->count(); // Tambahkan count() untuk menghitung jumlah proyek
                break;

            default:
                // Role lain tidak memiliki akses
                break;
        }

        // Data yang akan diteruskan ke view
        $data = [
            'tittle' => 'Dashboard', 
            'totalProjectsNotReviewed' => $totalProjectsNotReviewed,
            'totalCompletedProjects' => $totalCompletedProjects,
            'projectsToReview' => $projectsToReview,
        ];

        return view('pages.dashboard.index', $data);
    }
}