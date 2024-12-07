<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectLisence;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
class PerijinanProjectController extends Controller
{
    public function create($id)
    {
        $data = [
            'tittle' => 'Perijinan Project',
            'project' => Project::find($id),
        ];

        return view('pages.project.perijinan.add', $data);
    }



    public function store(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'file' => 'nullable|mimes:pdf,doc,docx|max:5048',
            'note' => 'required|string|max:1000',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'price.required' => 'Nominal wajib diisi.',
            'price.numeric' => 'Nominal harus berupa angka.',
            'price.min' => 'Nominal tidak boleh kurang dari 0.',
            'file.mimes' => 'File harus berupa PDF atau Word (doc, docx).',
            'file.max' => 'Ukuran file maksimal adalah 5MB.',
            'note.required' => 'Catatan wajib diisi.',
            'note.string' => 'Catatan harus berupa teks.',
            'note.max' => 'Catatan maksimal 1000 karakter.',
        ]);
        try {


            $fileperijinan = '';
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileperijinan = 'perijinan_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/files/perijinan/'), $fileperijinan);
            }
            ProjectLisence::create([
                'project_id' => $id,
                'name' => $request->name,
                'note' => $request->note,
                'price' => $request->price,
                'perijinan_file' => $fileperijinan
            ]);
            return redirect()->route('project.detail', ['id' => $id])->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Perijinan Project!']);

        } catch (Exception $e) {

            return redirect()->back()->with(['status' => 'Error', 'message' => 'Gagal Menambahkan Perijinan Project']);
        }

    }


    


    public function show($id, $idperijinan)
    {
        $detail = ProjectLisence::find($idperijinan);
        $data = [
            'tittle' => 'Perijinan Project',
            'project' => Project::find($id),
            'perijinan' => $detail,
        ];

        return view('pages.project.perijinan.edit', $data);
    }


    public function update(Request $request, $idperijinan,$id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'file' => 'nullable|mimes:pdf,doc,docx|max:5048',
            'note' => 'required|string|max:1000',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'price.required' => 'Nominal wajib diisi.',
            'price.numeric' => 'Nominal harus berupa angka.',
            'price.min' => 'Nominal tidak boleh kurang dari 0.',
            'file.mimes' => 'File harus berupa PDF atau Word (doc, docx).',
            'file.max' => 'Ukuran file maksimal adalah 5MB.',
            'note.required' => 'Catatan wajib diisi.',
            'note.string' => 'Catatan harus berupa teks.',
            'note.max' => 'Catatan maksimal 1000 karakter.',
        ]);
        try {
            $perijinan = ProjectLisence::find($id);
        

            $fileperijinan = $perijinan->perijinan_file;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileperijinan = 'perijinan_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/files/perijinan/'), $fileperijinan);

                if (file_exists(public_path('storage/files/perijinan/' . $perijinan->perijinan_file))) {
                    File::delete(public_path('storage/files/perijinan/' . $perijinan->perijinan_file));
                }
            }
            $perijinan->update([
                'project_id' => $idperijinan,
                'name' => $request->name,
                'note' => $request->note,
                'price' => $request->price,
                'perijinan_file' => $fileperijinan
            ]);
            return redirect()->route('project.detail', ['id' => $idperijinan])->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Perijinan Project!']);

        } catch (Exception $e) {


            return redirect()->back()->with(['status' => 'Error', 'message' => 'Gagal Menambahkan Perijinan Project']);
        }

    }




    public function getData(Request $request)
    {
        $projectid = $request->id;
        $dataType = ProjectLisence::with(['project'])->where('project_id', $projectid)
            ->orderByDesc('id')
            ->get();


        return DataTables::of($dataType)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $button = '';
                $button .= '<a href="' . route('projectlisence.edit', ['idperijinan' => $data->id, 'id' => $data->project_id]) . '" class="btn btn-sm btn-success action mr-1" data-id="' . $data->id . '" data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data">
                <i class="fas fa-pencil-alt"></i>
            </a>';
                $button .= '<button class="btn btn-sm btn-danger action" data-id="' . $data->id . '" data-type="delete" data-route="' . route('projectlisence.delete', ['idperijinan' => $data->id, 'id' => $data->project_id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data">
                <i class="fas fa-trash-alt"></i>
            </button>';
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->editColumn('file', function ($data) {
                return $data->perijinan_file == null ? '<span class="badge bg-secondary">Tidak Ada File</span>' : '<a href="' . asset('storage/files/perijinan/' . $data->perijinan_file) . '" target="_blank" rel="noopener noreferrer"><span class="badge bg-primary">Download</span></a>';
            })->rawColumns(['action', 'file'])
            ->make(true);
    }



    public function destroy(string $perijinan,string $id)
    {
        try {
            $data = ProjectLisence::find($id);
            $data->delete();

            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Perijinan Projek Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Gagal Menghapus Data Data Perijinan Projek!",
                'trace' => $e->getTrace()
            ]);
        }
    }
}
