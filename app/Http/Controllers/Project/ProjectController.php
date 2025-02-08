<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\DetailProject;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectReview;
use App\Models\Summary;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProjectController extends Controller
{
    public function index()
    {
        $data = [
            'tittle' => 'Project'
        ];

        return view('pages.project.index', $data);
    }

    public function getData(Request $request)
    {
        $currentUser = Auth::user();
        $currentUserRole = $currentUser->roles->first()?->name;
        $vendor = Vendor::where('user_id', $currentUser->id)->first();


        if ($currentUserRole == 'Vendor') {
            $dataType = Project::with([
                'company',
                'detailproject',
                'Projectfile',
                'ProjectReview',
                'responsibleperson',
                'taskdata'
            ])->where('start_status', true)
                ->where('vendor_id', $vendor->id)
                ->orderByDesc('id')
                ->get();
        } else {
            $dataType = Project::with([
                'company',
                'detailproject',
                'Projectfile',
                'ProjectReview',
            ])->orderByDesc('id')
                ->get();
        }


        return DataTables::of($dataType)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';
                $projectReview = $data->ProjectReview()->latest()->first();

                if ($data->detailproject->isNotEmpty() && !$data->Projectfile) {
                    if ($userauth->can('approval-projects')) {
                        $button .= '<a href="' . route('project.proses', $data->id) . '" class="btn btn-sm btn-success action d-inline-flex align-items-center mb-2 me-2" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Proses Pengajuan">
                    <i class="fas fa-upload me-1"></i> <span class="d-none d-sm-inline">Proses Pengajuan</span>
                </a>';
                    }
                }

                // Check project review status
                if ($projectReview && $projectReview->status_review == 'approved' && !$data->vendor_id) {
                    if ($userauth->can('start-projects')) {
                        $button .= '<a href="' . route('project.start', $data->id) . '" class="btn btn-sm btn-success action d-inline-flex align-items-center mb-2 me-2" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Start Project">
                    <i class="fas fa-upload me-1"></i> <span class="d-none d-sm-inline">Start Project</span>
                </a>';
                    }
                }

                if ($data->start_status == true) {
                    $projectAtp = $data->Projectatp;
                    $buttons = [];

                    // Add finish project button for Developer and Owner
                    if ($userauth->hasRole(['Developer', 'Owner']) && $data->status !== 'finish') {
                        $buttons[] = '<a href="' . route('project.finish', $data->id) . '" 
                    class="btn btn-sm btn-info action d-inline-flex align-items-center mb-2 me-2" 
                    data-id="' . $data->id . '" 
                    data-type="finish-project" 
                    data-toggle="tooltip" 
                    data-placement="bottom" 
                    title="Finish Project">
                    <i class="fas fa-check-circle me-1"></i> <span class="d-none d-sm-inline">Finish</span>
                </a>';
                    }

                    if ((!$projectAtp || !$projectAtp->active) && $userauth->can('enable-atp-upload')) {
                        $buttons[] = '<a href="' . route('project.enable-atp-upload', $data->id) . '" 
                    class="btn btn-sm btn-primary action d-inline-flex align-items-center mb-2 me-2" 
                    data-id="' . $data->id . '" 
                    data-type="enable-atp-upload" 
                    data-toggle="tooltip" 
                    data-placement="bottom" 
                    title="Enable Vendor ATP Upload">
                    <i class="fas fa-toggle-on me-1"></i> <span class="d-none d-sm-inline">Enable ATP</span>
                </a>';
                    }

                    if ($projectAtp && $projectAtp->active) {
                        if ($userauth->can('disable-atp-upload')) {
                            $buttons[] = '<a href="' . route('project.disable-atp-upload', $data->id) . '" 
                        class="btn btn-sm btn-primary action d-inline-flex align-items-center mb-2 me-2" 
                        data-id="' . $data->id . '" 
                        data-type="disable-atp-upload" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title="Disable ATP Upload">
                        <i class="fas fa-toggle-off me-1"></i> <span class="d-none d-sm-inline">Disable ATP</span>
                    </a>';
                        }

                        if ($userauth->can('download-atp') && $projectAtp->file) {
                            $buttons[] = '<a href="' . route('project.download-atp', $data->id) . '" 
                            class="btn btn-sm btn-primary action mr-1" 
                            data-id="' . $data->id . '" 
                            data-type="download-atp" 
                            download
                            data-toggle="tooltip" 
                            data-placement="bottom" 
                            title="Download ATP File">
                            <i class="fas fa-download"></i> Download ATP
                        </a>';
                        } elseif ($userauth->can('upload-atp') && !$projectAtp->file) {
                            $buttons[] = '<a href="' . route('project.upload-atp', $data->id) . '" 
                        class="btn btn-sm btn-success action d-inline-flex align-items-center mb-2 me-2" 
                        data-id="' . $data->id . '" 
                        data-type="upload-atp" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title="Upload ATP File">
                        <i class="fas fa-file-upload me-1"></i> <span class="d-none d-sm-inline">Upload ATP</span>
                    </a>';
                        }
                    }

                    $button .= implode('', $buttons);
                }

                if ($data->start_status == false && !$data->Projectfile) {
                    if ($userauth->can('update-projects')) {
                        $button .= '<a href="' . route('project.edit', $data->id) . '" class="btn btn-sm btn-success action d-inline-flex align-items-center mb-2 me-2" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data">
                    <i class="fas fa-pencil-alt me-1"></i> <span class="d-none d-sm-inline">Edit</span>
                </a>';
                    }
                    if ($userauth->can('read-detail-projects')) {
                        $button .= '<a href="' . route('project.detail', $data->id) . '" class="btn btn-sm btn-warning action d-inline-flex align-items-center mb-2 me-2" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="View Details">
                    <i class="fas fa-eye me-1"></i> <span class="d-none d-sm-inline">View</span>
                </a>';
                    }
                } else if ($userauth->hasRole(['Developer', 'Owner'])) {
                    if ($userauth->can('update-projects')) {
                        $button .= '<a href="' . route('project.edit', $data->id) . '" class="btn btn-sm btn-success action d-inline-flex align-items-center mb-2 me-2" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data">
                    <i class="fas fa-pencil-alt me-1"></i> <span class="d-none d-sm-inline">Edit</span>
                </a>';
                    }
                    if ($userauth->can('read-detail-projects')) {
                        $button .= '<a href="' . route('project.detail', $data->id) . '" class="btn btn-sm btn-warning action d-inline-flex align-items-center mb-2 me-2" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="View Details">
                    <i class="fas fa-eye me-1"></i> <span class="d-none d-sm-inline">View</span>
                </a>';
                    }
                }

                if ($userauth->can('delete-projects')) {
                    $button .= '<button class="btn btn-sm btn-danger action d-inline-flex align-items-center mb-2" data-id="' . $data->id . '" data-type="delete" data-route="' . route('project.delete', $data->id) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data">
                <i class="fas fa-trash-alt me-1"></i> <span class="d-none d-sm-inline">Delete</span>
            </button>';
                }

                return '<div class="d-flex flex-wrap gap-1">' . $button . '</div>';
            })
            ->editColumn('status', function ($data) {
                $status = '';
                switch ($data->status) {
                    case 'pending':
                        $status = '<span class="badge badge-pill badge-soft-primary font-size-13">Pending</span>';
                        break;
                    case 'in_progres':
                        $status = '<span class="badge badge-pill badge-soft-info font-size-13">In Progress</span>';
                        break;
                    case 'canceled':
                        $status = '<span class="badge badge-pill badge-soft-danger font-size-13">Canceled</span>';
                        break;
                    default:
                        $status = '<span class="badge badge-pill badge-soft-success font-size-13">Selesai</span>';
                }
                return $status;
            })
            ->addColumn('status_review', function ($data) {
                $review = $data->ProjectReview()->latest()->first();

                if (!$review) {
                    return '<span class="badge badge-pill badge-soft-secondary font-size-13">No Review</span>';
                }

                $statusClasses = [
                    'pending' => 'primary',
                    'in_review' => 'info',
                    'approved' => 'success',
                    'revision' => 'warning',
                    'rejected' => 'danger'
                ];

                $statusClass = $statusClasses[$review->status_review] ?? 'secondary';
                $statusText = ucfirst(str_replace('_', ' ', $review->status_review));

                return '<span class="badge badge-pill badge-soft-' . $statusClass . ' font-size-13">' . $statusText . '</span>';
            })
            ->editColumn('company', function ($data) {
                return $data->company->name;
            })
            ->editColumn('review', function ($data) {
                $review = $data->ProjectReview()->latest()->first();
                return $review ? $review->review_note : '-';
            })
            ->editColumn('reviewer', function ($data) {
                $review = $data->ProjectReview()->latest()->first();
                return $review && $review->reviewer ? $review->reviewer->name : '-';
            })
            ->editColumn('name', function ($data) {
                return $data->vendor_id
                    ? '<a href="' . route('report.project', ['project_id' => $data->id]) . '" class="text-primary">' . $data->name . '</a>'
                    : $data->name;
            })
            ->rawColumns(['action', 'name', 'company', 'status', 'review', 'reviewer', 'status_review'])
            ->make(true);
    }
    public function detail($id)
    {

        $data = [
            'tittle' => 'Detail Project',
            'project' => Project::find($id),
        ];

        return view('pages.project.detail', $data);
    }

    public function create()
    {
        $data = [
            'tittle' => 'Project',
            'company' => Company::all(),
            'user' => User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Developer')->orwhere('name', 'Vendor');
            })->get(),
            'vendor' => Vendor::all()
        ];

        return view('pages.project.add', $data);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'company_id.required' => 'Perusahaan wajib diisi.',
            'company_id.exists' => 'Perusahaan tidak valid.',
        ]);

        $project = Project::create($request->all());

        activity()
            ->causedBy(Auth::user()) // Logs who performed the action
            ->performedOn($project) // The entity being changed
            ->event('created') // Event of the action
            ->log('Project dibuat dengan nama ' . $project->name);

        return redirect()->route('project.detail', ['id' => $project->id])->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Project!']);
    }

    public function show($id)
    {
        $data = [
            'tittle' => 'Project',
            'project' => Project::find($id),
            'company' => Company::all(),
            'user' => User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Developer')->orwhere('name', 'Vendor');
            })->get(),
            'vendor' => Vendor::all()
        ];

        return view('pages.project.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $project = Project::find($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'company_id.required' => 'Perusahaan wajib diisi.',
            'company_id.exists' => 'Perusahaan tidak valid.',
        ]);
        $project->update($request->all());

        activity()
            ->causedBy(Auth::user()) // Logs who performed the action
            ->performedOn($project) // The entity being changed
            ->event('updated') // Event of the action
            ->withProperties([
                'attributes' => $project->toArray() // The data before deletion
            ])
            ->log('Project di update dengan nama ' . $project->name);
        return redirect()->route('project')->with(['status' => 'Success', 'message' => 'Berhasil Mengubah Project!']);
    }


    public function destroy(string $id)
    {
        try {
            $project = Project::find($id);
            $projectData = $project->toArray();

            $project->delete();

            activity()
                ->causedBy(Auth::user()) // Logs who performed the action
                ->performedOn($project) // The entity being changed
                ->event('deleted') // Event of the action
                ->withProperties([
                    'attributes' => $projectData // The data before deletion
                ])
                ->log('Project dihapus dengan nama ' . $project->name);

            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Projek Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Gagal Menghapus Data Projek !",
                'trace' => $e->getTrace()
            ]);
        }
    }

    public function ProsesProject($id)
    {
        $project = Project::find($id);
        $detailProjects = DetailProject::with(['detailitemporject', 'projecttype'])->where('project_id', $id)->get();
        $ratebackup = Setting('backup') / 100;
        $ppnRate = Setting('ppn') / 100;

        // Olah data untuk menghitung total biaya material dan service
        $detailData = $detailProjects->map(function ($detail) use ($ppnRate, $ratebackup) {
            $totalMaterial = 0;
            $totalService = 0;


            foreach ($detail->detailitemporject as $detailItem) {
                // Ambil biaya material dan service dari item terkait
                $totalMaterial += $detailItem->cost_material;
                $totalService += $detailItem->cost_service;
            }

            $subTotal = $totalMaterial + $totalService;
            $ppn = $subTotal * $ppnRate;
            $backup = $subTotal * $ratebackup;
            $totalWithPpn = $subTotal + $ppn;
            $totalWithbackup = $subTotal + $backup;

            return [
                'distribusi' => $detail->name . ' - ' . $detail->projecttype->name,
                'total_material' => $totalMaterial,
                'total_service' => $totalService,
                'total' => $subTotal,
                'backup' => $backup,
                'ppn' => $ppn,
                'total_with_ppn' => $totalWithPpn,
                'total_with_backup' => $totalWithbackup,
                'total_with_ppn_backup' => $totalWithPpn + $backup,
            ];
        });

        $data = [
            'tittle' => $project->name,
            'project' => $detailProjects,
            'details' => $detailData,
            'ppn_rate' => $ppnRate * 100,
            'backup_rate' => $ratebackup * 100,
            'id_project' => $project->id

        ];

        return view('pages.project.proses', $data);
    }

    public function finishProject(Project $project)
    {
        $project->update(['status' => 'finish']);
        return redirect()->back()->with('success', 'Project status updated to finished');
    }

    public function ProsesProjectStore(Request $request, $id)
    {
        $request->validate([
            'excel' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'kmz' => [
                'required',
                'file',
                'max:10240',
                'mimes:kml,kmz,xml,zip',
                'mimetypes:application/vnd.google-earth.kmz,application/vnd.google-earth.kml+xml,application/zip,application/xml,text/xml'
            ],
            'total_material' => 'required|numeric|min:0',
            'total_service' => 'required|numeric|min:0',
            'ppn' => 'required|numeric|min:0',
            'total_with_ppn' => 'required|numeric|min:0',
        ], [
            'excel.required' => 'File Excel wajib diunggah.',
            'excel.file' => 'Yang diunggah harus berupa file.',
            'excel.mimes' => 'File yang diunggah harus berformat xlsx, xls, atau csv.',
            'excel.max' => 'Ukuran file tidak boleh lebih dari 10 MB.',
            'kmz.required' => 'File KMZ/KML wajib diunggah.',
            'kmz.file' => 'Yang diunggah harus berupa file.',
            'kmz.mimes' => 'File yang diunggah harus berformat KML, KMZ, atau XML.',
            'kmz.max' => 'Ukuran file tidak boleh lebih dari 10 MB.',
            'total_material.required' => 'Total material wajib diisi.',
            'total_material.numeric' => 'Total material harus berupa angka.',
            'total_material.min' => 'Total material tidak boleh kurang dari 0.',
            'total_service.required' => 'Total service wajib diisi.',
            'total_service.numeric' => 'Total service harus berupa angka.',
            'total_service.min' => 'Total service tidak boleh kurang dari 0.',
            'ppn.required' => 'PPN wajib diisi.',
            'ppn.numeric' => 'PPN harus berupa angka.',
            'ppn.min' => 'PPN tidak boleh kurang dari 0.',
            'total_with_ppn.required' => 'Total dengan PPN wajib diisi.',
            'total_with_ppn.numeric' => 'Total dengan PPN harus berupa angka.',
            'total_with_ppn.min' => 'Total dengan PPN tidak boleh kurang dari 0.',
        ]);

        try {
            DB::beginTransaction();

            // Ambil proyek beserta review terbaru
            $project = Project::with([
                'ProjectReview' => function ($query) {
                    $query->latest(); // Mengambil review terbaru berdasarkan waktu
                }
            ])->findOrFail($id);

            // Debugging untuk melihat status proyek dan review
            \Log::info('Current Project Status: ' . $project->status);

            // Cek status proyek sebelum mencoba untuk memperbarui
            if ($project->status === 'pending' || $project->status === 'canceled') {
                $latestReview = $project->ProjectReview->first(); // Mengambil review terbaru

                // Log untuk melihat status review
                \Log::info('Latest Review Status: ' . ($latestReview ? $latestReview->status_review : 'No review'));

                // Jika ada review dan statusnya adalah 'rejected' atau 'revision'
                if ($latestReview && in_array($latestReview->status_review, ['rejected', 'revision'])) {
                    // Update status proyek ke 'pending' jika belum 'pending'
                    if ($project->status !== 'pending') {
                        $project->status = 'pending';
                        $project->save(); // Menyimpan perubahan status proyek
                        \Log::info('Project status updated to pending');
                    }

                    // // Update status review ke 'pending' jika belum 'pending'
                    // if ($latestReview->status_review !== 'pending') {
                    //     $latestReview->status_review = 'pending';
                    //     $latestReview->save(); // Menyimpan perubahan status review
                    //     \Log::info('Review status updated to pending');
                    // }
                }
            }

            $fileexcel = '';
            if ($request->hasFile('excel')) {
                $file = $request->file('excel');
                $fileexcel = 'excel_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/files/excel/'), $fileexcel);
            }

            $filekmz = '';
            if ($request->hasFile('kmz')) {
                $file = $request->file('kmz');
                $filekmz = 'kmz_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/files/kmz/'), $filekmz);
            }

            // Create project file record
            ProjectFile::create([
                'project_id' => $id,
                'excel' => $fileexcel,
                'kmz' => $filekmz,
            ]);

            // Create summary record
            Summary::create([
                'project_id' => $id,
                'total_material_cost' => $request->total_material,
                'total_service_cost' => $request->total_service,
                'total_ppn_cost' => $request->ppn,
                'total_summary' => $request->total_with_ppn
            ]);

            // Update project amount
            $project->update(['amount' => $request->total_with_ppn]);

            DB::commit();
            return redirect()->route('project')->with([
                'status' => 'Success',
                'message' => 'Pengajuan Berhasil Terkirim!'
            ]);

        } catch (Exception $e) {
            \Log::error("Error in ProsesProjectStore: " . $e->getMessage());
            DB::rollBack();
            return redirect()->back()->with([
                'status' => 'Error',
                'message' => 'Gagal Proses Pengajuan!'
            ]);
        }
    }

    public function StartProject($id)
    {
        $data = [
            'tittle' => 'Project',
            'user' => User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Developer')->orwhere('name', 'Vendor');
            })->get(),
            'vendor' => Vendor::all(),
            'project_id' => $id,
        ];

        return view('pages.project.startproject', $data);
    }

    public function ProjectStart(Request $request, $id)
    {

        $request->validate([
            'vendor_id' => 'required',
            'responsible_person' => 'required',
            'start_date' => 'required||date',
            'end_date' => 'required||date',
        ]);
        try {
            $project = Project::find($id);
            $project->update([
                'vendor_id' => $request->vendor_id,
                'responsible_person' => $request->responsible_person,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'in_progres',
                'start_status' => true,
            ]);
            return redirect()->route('project')->with(['status' => 'Success', 'message' => 'Project Berhasil Di Start!']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => 'Error', 'message' => 'Gagal Start Project']);
        }
    }
}
