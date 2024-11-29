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


$("#datatabledistribusi").on("click", ".action", function () {
    //  let route = $(this).data("route");
    let data = $(this).data();
    let id = data.id;
    let type = data.type;
    var route = data.route;

    if (type === "view") {
        // Tampilkan modal
        $(".exampleModalFullscreen").modal("show");

        // if ($.fn.DataTable.isDataTable('#datatabledistribusi')) {
        //     $('#datatabledistribusi').DataTable().clear().destroy();
        // }
        $.ajax({
            url: route, 
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ),
            },
            success: function (res) {
                $("#datatablemodal").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: route,
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            class: 'text-center',
                        },
                        {
                            data: 'item_code',
                            name: 'item_code'
                        },
                        {
                            data: 'item_name',
                            name: 'item_name'
                        },
                        {
                            data: 'material_price',
                            name: 'material_price'
                        },
                        {
                            data: 'service_price',
                            name: 'service_price'
                        },
                        
                    ],
                });
                $(".dataTables_length select").addClass("form-select form-select-sm");
            },
        });
    }
   
});