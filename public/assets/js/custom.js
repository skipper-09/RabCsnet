//sweet alert delete button
"use strict";
$("#dataTable").on("click", ".action", function () {
    let data = $(this).data();
    let id = data.id;
    let type = data.type;
    var route = data.route;

    if (type === "delete") {
        Swal.fire({
            title: "Apakah Kamu Yakin?",
            text: "Menghapus data ini bersifat permanen",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal",
            dangerMode: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: route,
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (res) {
                        // Reload table
                        $("#dataTable").DataTable().ajax.reload();

                        if (res.status === "success") {
                            Swal.fire("Deleted!", res.message, "success");
                        } else {
                            Swal.fire("Error!", res.message, "error");
                        }
                    },
                    error: function () {
                        Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
                    },
                });
            }
        });
    } else if (type === "print") {
        // Set modal content and display modal
        $("#showmodal").find(".modal-content").html(`
            <div class="modal-header">
                <h5 class="modal-title">Print Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex px-3">
                    <button class="btn btn-sm btn-success mr-2" id="print-standart">Standart Printer</button>
                    <button class="btn btn-sm btn-primary" id="print-thermal">Thermal Printer</button>
                </div>
            </div>
        `);
        $("#showmodal").modal("show");
    }
});



