@push('css')
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
@endpush

<div id="ReportProjectModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="myModalLabel">Report Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="{{ route('report.project') }}" method="GET">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="project_id" class="form-label">
                                    Pilih Project
                                </label>
                                <select name="project_id" id="select2modal"
                                    class="form-control @error('project_id') is-invalid @enderror">
                                    <option value="">Pilih Project</option>
                                    @foreach ($projects as $dt)
                                        <option value="{{ $dt->id }}">{{ $dt->name }}</option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Lihat Report</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@push('js')
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    {{-- custom swetaert --}}
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Menambahkan event listener untuk ketika form disubmit
            $('form').on('submit', function(event) {
                var projectId = $('#select2modal').val(); // Mengambil nilai project_id

                // Validasi apakah project_id dipilih
                if (projectId === "") {
                    event.preventDefault(); // Mencegah form disubmit
                    // Menampilkan pesan error di dalam modal
                    Swal.fire({
                        title: "Error",
                        text: "Harap Pilih Project Terlebih dahulu.",
                        icon: "error",
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return false; // Menghentikan proses submit form
                }
            });
        });
        $('#select2modal').select2({
            dropdownParent: $('#ReportProjectModal')
        });
    </script>
@endpush
