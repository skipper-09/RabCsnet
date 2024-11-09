//sweet alert delete button
"use strict";

$("#datatable").on("click", ".action", function () {
    //  let route = $(this).data("route");
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
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (res) {
                        $("#datatable").DataTable().ajax.reload();

                        if (res.status === "success") {
                            Swal.fire("Deleted!", res.message, "success");
                        } else {
                            Swal.fire("Error!", res.message, "error");
                        }
                    },
                    error: function () {
                        Swal.fire(
                            "Error!",
                            "Terjadi kesalahan pada server.",
                            "error"
                        );
                    },
                });
            }
        });
    }
});
