@extends('admin.layouts.layout')

@section('title', 'Warcaster')

@section("toolbar")
@include("admin.layouts.includes.breadcrump")
@endsection

@section('content')
<!--begin::Post-->
<div class="content flex-row-fluid" id="kt_content">
    <div class="row gx-6 gx-xl-9">
        <div class="col-12">
            <div class="card card-flush h-lg-100">
                <div class="card-body p-9 pt-5">
                    <table class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold dataTable"
                        id="factions_dt"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Post-->
@endsection

@section("styles")
@include("admin.layouts.styles.default")
@include("admin.layouts.styles.datatable")
@include("admin.layouts.styles.global")
@endsection
@section("scripts")
@include("admin.layouts.scripts.default")
@include("admin.layouts.scripts.datatable")

<script>
    $(function() {
        var factions_dt = $("#factions_dt").DataTable({
            ajax: {
                type: "GET",
                url: "{{ route('api.factions.index') }}",
                data: function(d) {},
                dataSrc: function(data) {
                    return data.data;
                }
            },
             rowCallback: function (row, data) {
                $(row)
                    .addClass("h-150px")
                    .css("background-image", "url('"+data.rosterHeaderImage+"')")
                    .css("background-repeat", "no-repeat")
                    .css("background-position", "center top")
                    .css("background-size", "cover");
            },
            columns: [
                // {
                //     data: "rowImage",
                //     title: "",
                //     width: "3%",
                //     className: "clickable",
                //     createdCell: function(td, cellData, rowData) {
                //         $(td).html("<div class='symbol w-100'><div class='symbol-label w-100 h-150px' style='aspect-ratio:16/9;background-size:contain;background-image:url(\""+cellData+"\")'></div></div>");
                //     }
                // },
                {
                    data: "name",
                    title: "Nom",
                    className: "clickable",
                    createdCell: function(td, cellData, rowData) {
                        $(td).html("<h2 class='ms-5'>"+cellData+"</h2>");
                    }
                },
                // {
                //     data: null,
                //     title: "Actions",
                //     className: "text-end",
                //     width: "10%",
                //     orderable: false,
                //     createdCell: function(td, cellData, rowData) {
                //         var dropdown = $("<div>").addClass("dropdown").html(
                //             $("<button>").addClass(
                //                 "btn btn-primary dropdown-toggle no-caret px-4 py-1")
                //             .attr('type', 'button').attr('data-bs-toggle', 'dropdown')
                //             .html(
                //                 $("<i>").addClass("fas fa-caret-down"))
                //         );

                //         var ul = $("<ul>").addClass('dropdown-menu dropwdown-menu-end');

                //             $(ul).append(
                //                 $('<li>').append(
                //                     $("<a>").addClass('dropdown-item cursor-pointer').attr(
                //                         "onclick",
                //                         "DeleteEmployee(" + cellData.id +
                //                         ", this)"
                //                     ).html(
                //                         "<i class='fas fa-trash me-2'></i>Supprimer"
                //                     )
                //                 )
                //             );

                //         $(ul).appendTo(dropdown);
                //         if ($(ul).children().length > 0) {
                //             $(td).html(dropdown);
                //         } else {
                //             $(td).html("");
                //         }
                //     }
                // }
            ],
            // autoWidth: false,
            order: [
                [0, "asc"]
            ],
            drawCallback: function(settings, json) {
                $('[data-toggle="tooltip"]').tooltip();

                $('#factions_dt tbody').on('click', '.clickable', function() {
                    var data = $('#factions_dt').DataTable().row( $(this).parents("tr") ).data();
                    window.location.href = "{{ route('factions.index') }}/" + data.id;
                });
            }
        });
    });

</script>
@endsection