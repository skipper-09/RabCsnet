<?php

namespace App\Http\Controllers\PaymentVendor;

use App\Http\Controllers\Controller;
use App\Models\PaymentVendor;
use App\Models\User;
use App\Models\Project;
use App\Models\Vendor;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PaymentVendorController extends Controller
{
    public function index()
    {
        $data = [
            'tittle' => 'Payment Vendor', // Corrected 'tittle' to 'title'
            'vendor' => Vendor::all(),
            'project' => Project::all(),
        ];

        return view('pages.payment.index', $data);
    }

    public function getData(Request $request)
    {
        $currentUser = Auth::user();
        $currentUserRole = $currentUser->roles->first()->name;
        $vendor = $currentUserRole === 'Vendor'
            ? Vendor::where('user_id', $currentUser->id)->firstOrFail()
            : null;

        // Initialize base query with eager loading
        $query = PaymentVendor::with(['project', 'vendor']);

        // Apply project ID filter if provided
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->query('project_id'));
        }

        if ($currentUserRole === 'Vendor') {
            return $query->where('vendor_id', $vendor->id);
        }

        // Apply filters for non-vendor roles
        if ($request->filled('vendor_filter')) {
            $query->where('vendor_id', $request->input('vendor_filter'));
        }

        if ($request->filled('project_filter')) {
            $query->where('project_id', $request->input('project_filter'));
        }

        // Apply common filters and ordering
        $query->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('project', fn($item) => $item->project?->name ?? '-')
            ->addColumn('vendor', fn($item) => $item->vendor?->name ?? '-')
            ->editColumn('amount', fn($data) => 'Rp ' . number_format($data->amount, 0, ',', '.'))
            ->editColumn('payment_date', fn($data) => Carbon::parse($data->payment_date)->format('Y-m-d'))
            ->addColumn('action', function ($data) {
                $userAuth = User::with('roles')->find(Auth::id());
                $buttons = [];

                if ($userAuth->can('update-paymentvendors')) {
                    $buttons[] = sprintf(
                        '<a href="%s" class="btn btn-sm btn-success action mr-1" 
                data-id="%d" 
                data-type="edit" 
                data-toggle="tooltip" 
                data-placement="bottom" 
                title="Edit Data">
                <i class="fas fa-pencil-alt"></i>
            </a>',
                        route('payment.edit', $data->id),
                        $data->id
                    );
                }

                if ($userAuth->can('delete-paymentvendors')) {
                    $buttons[] = sprintf(
                        '<button class="btn btn-sm btn-danger action" 
                data-id="%d" 
                data-type="delete" 
                data-route="%s" 
                data-toggle="tooltip" 
                data-placement="bottom" 
                title="Delete Data">
                <i class="fas fa-trash-alt"></i>
            </button>',
                        $data->id,
                        route('payment.delete', $data->id)
                    );
                }

                return '<div class="d-flex gap-2">' . implode('', $buttons) . '</div>';
            })
            ->rawColumns(['action', 'project', 'vendor', 'amount', 'payment_date'])
            ->make(true);
    }

    public function create()
    {
        $data = [
            'tittle' => 'Payment Vendor',
            'projects' => Project::all(),
            'vendors' => Vendor::all(),
        ];

        return view('pages.payment.add', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'vendor_id' => 'required|exists:vendors,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'note' => 'required|string',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5048',
        ], [
            'project_id.required' => 'Project wajib diisi.',
            'project_id.exists' => 'Project tidak valid.',
            'vendor_id.required' => 'Vendor wajib diisi.',
            'vendor_id.exists' => 'Vendor tidak valid.',
            'amount.required' => 'Jumlah pembayaran wajib diisi.',
            'amount.numeric' => 'Jumlah pembayaran harus berupa angka.',
            'amount.min' => 'Jumlah pembayaran tidak boleh negatif.',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
            'payment_date.date' => 'Format tanggal pembayaran tidak valid.',
            'note.required' => 'Catatan wajib diisi.',
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diisi.',
            'bukti_pembayaran.image' => 'File harus berupa gambar.',
            'bukti_pembayaran.mimes' => 'Format gambar tidak valid.',
            'bukti_pembayaran.max' => 'Ukuran gambar tidak boleh lebih dari 5MB.',
        ]);

        $filename = '';
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = 'payment_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/images/payment/'), $filename);
        }

        $paymentVendor = PaymentVendor::create([
            'project_id' => $request->project_id,
            'vendor_id' => $request->vendor_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'note' => $request->note,
            'bukti_pembayaran' => $filename,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($paymentVendor)
            ->event('created')
            ->log('Pembayaran Vendor dibuat dengan jumlah ' . number_format($paymentVendor->amount, 2));

        return redirect()->route('payment')->with(['status' => 'Success', 'message' => 'Berhasil Menambahkan Pembayaran!']);
    }

    public function show($id)
    {
        $data = [
            'tittle' => 'Payment Vendor',
            'payment' => PaymentVendor::find($id),
            'projects' => Project::all(),
            'vendors' => Vendor::all(),
        ];

        return view('pages.payment.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'vendor_id' => 'required|exists:vendors,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'note' => 'required|string',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5048',
        ], [
            'project_id.required' => 'Project wajib diisi.',
            'project_id.exists' => 'Project tidak valid.',
            'vendor_id.required' => 'Vendor wajib diisi.',
            'vendor_id.exists' => 'Vendor tidak valid.',
            'amount.required' => 'Jumlah pembayaran wajib diisi.',
            'amount.numeric' => 'Jumlah pembayaran harus berupa angka.',
            'amount.min' => 'Jumlah pembayaran tidak boleh negatif.',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
            'payment_date.date' => 'Format tanggal pembayaran tidak valid.',
            'note.required' => 'Catatan wajib diisi.',
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diisi.',
            'bukti_pembayaran.image' => 'File harus berupa gambar.',
            'bukti_pembayaran.mimes' => 'Format gambar tidak valid.',
            'bukti_pembayaran.max' => 'Ukuran gambar tidak boleh lebih dari 5MB.',
        ]);

        $paymentVendor = PaymentVendor::findOrFail($id); // Added error handling

        $filename = $paymentVendor->bukti_pembayaran;

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = 'payment_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/images/payment/'), $filename);
            if ($paymentVendor->bukti_pembayaran !== 'default.png' && file_exists(public_path('storage/images/payment/' . $paymentVendor->bukti_pembayaran))) {
                File::delete(public_path('storage/images/payment/' . $paymentVendor->bukti_pembayaran));
            }
        }

        $paymentVendor->update([
            'project_id' => $request->project_id,
            'vendor_id' => $request->vendor_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'note' => $request->note,
            'bukti_pembayaran' => $filename,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($paymentVendor)
            ->event('updated')
            ->log('Pembayaran Vendor diubah dengan jumlah ' . number_format($paymentVendor->amount, 2));

        return redirect()->route('payment')->with([
            'status' => 'Success',
            'message' => 'Berhasil Mengubah Pembayaran!'
        ]);
    }

    public function destroy($id)
    {
        try {
            $paymentVendor = PaymentVendor::findOrFail($id);
            $paymentVendorData = $paymentVendor->toArray(); // Capture the data before deletion

            $paymentVendor->delete();

            // Log activity for d$data deletion
            activity()
                ->causedBy(Auth::user()) // Logs who performed the action
                ->performedOn($paymentVendor) // The entity being changed
                ->event('deleted') // Event of the action
                ->withProperties([
                    'attributes' => $paymentVendorData // The data before deletion
                ])
                ->log('Pembayaran Vendor dihapus dengan jumlah ' . number_format($paymentVendor->amount, 2));

            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data !',
                'trace' => $e->getTrace()
            ]);
        }
    }
}
