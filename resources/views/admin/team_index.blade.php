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
                    <div class="row">
                        <div class="col-12 mb-5">
                            <!--begin::Nav-->
                            <ul class="nav nav-tabs nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold"
                                id="unitTab" role="tablist">
                                <!--begin::Nav item-->
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary py-5 me-6 active" id="army-tab"
                                        data-bs-toggle="tab" data-bs-target="#army-tab-pane" role="tab"
                                        aria-controls="army-tab-pane" aria-selected="true">Détail de l'armée</a>
                                </li>
                                <!--end::Nav item-->
                                <!--begin::Nav item-->
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary py-5 me-6" id="abilities-tab"
                                        data-bs-toggle="tab" data-bs-target="#abilities-tab-pane" role="tab"
                                        aria-controls="abilities-tab-pane" aria-selected="true">Aptitudes</a>
                                </li>
                                <!--end::Nav item-->
                            </ul>
                            <!--end::Nav-->
                        </div>
                        <div class="col-12">
                            <div class="tab-content" id="unitTabContent">
                                <div class="tab-pane fade show active" id="army-tab-pane" role="tabpanel"
                                    aria-labelledby="army-tab" tabindex="0">
                                    <div class="row gx-6 gx-xl-9">
                                        <div class="col-xl-12">
                                            <div class="row">
                                                <div class="col-12 mb-5">
                                                    <form id="unit_search">
                                                        @csrf
                                                        <input type="text" name="search" id="unit_search_input"
                                                            class="form-control" placeholder="Rechercher une unité">
                                                    </form>
                                                    <div class="search_result_container d-none card card-flush">
                                                        <div class="card-body p-9 pt-5 d-none search_result_spinner">
                                                            <h1 class="text-center">
                                                                <i class="fa-solid fa-spinner fa-spin"></i>
                                                            </h1>
                                                        </div>
                                                        <div class="card-body p-9 pt-5 d-none search_result_no_result">
                                                            <h1>Aucun résultat</h1>
                                                        </div>
                                                        <div class="card-body p-9 pt-5 d-none search_result_results">

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <table
                                                        class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold dataTable"
                                                        id="units_dt"></table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="abilities-tab-pane" role="tabpanel"
                                    aria-labelledby="abilities-tab" tabindex="0">
                                    <div class="flex-nowrap gx-6 gx-xl-9 overflow-x-scroll row">
                                        @foreach ($phases as $phase)
                                        <div class="card col-4 mx-1 p-0">
                                            <div class="card-header background-phase-{{ $phase->slug }}">
                                                <h3 class="card-title text-white">{{ $phase->name }}</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row" id="phase_container_{{ $phase->slug }}"></div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
@include("admin.layouts.scripts.global")

<script>
    const LS_KEY = 'wc:selected-units:v1';

    function loadLSUnits() {
        try {
            const raw = localStorage.getItem(LS_KEY);
            if (!raw) return [];
            const {items} = JSON.parse(raw);
            return Array.isArray(items) ? items : [];
        } catch (e) {
            console.warn('LS parse error', e);
            return [];
        }
    }

    function saveLSUnits(units) {
        const payload = {
            items: units,
            updatedAt: Date.now()
        };
        localStorage.setItem(LS_KEY, JSON.stringify(payload));
    }

    function addLSUnit(unit) {
        const units = loadLSUnits();
        if (!units.some(u => u === unit.id)) {
            units.push(unit.id);
            saveLSUnits(units);
        }
        return units;
    }

    function removeLSUnit(id) {
        const units = loadLSUnits().filter(u => u !== id);
        saveLSUnits(units);
        return units;
    }

    function clearLSUnits() {
        saveLSUnits([]);
    }

    function fetchDtUnits() {
        const units = loadLSUnits();

        var data = new FormData();
        data.append("ids", units);
        data.append("_token", $("[name='_token']").val());

        var resultat = $.ajax({
            url: "{{ route('team.fetch.units') }}",
            method: 'POST',
            data: new URLSearchParams(data).toString(),
            beforeSend: function () {
            },
            // return the result
            success: function (result) {
                // console.log(result);
                $("#units_dt").DataTable().rows.add(result.data).draw();
                // return result.data;
            },
            error: function (jqXHR, testStatus, error) {
                console.error(error);
                showToastDangerLite({
                    body: "Erreur lors de la récupération des informations du fournisseur",
                });
            },
            complete: function () {
            },
        });
    }

    function RemoveDtUnit(id, el) {
        removeLSUnit(id);
        $("#units_dt").DataTable()
        .row($(el).parents('tr'))
        .remove()
        .draw();
    }

    function drawSearchElement(div, data) {
        $(div).append("<div class='col-4'><div class='symbol w-100'><div class='symbol-label w-100 h-150px' style='aspect-ratio:16/9;background-size:contain;background-image:url(\""+data.rowImage+"\")'></div></div></div>");
        var titles = $("<div class='align-content-center align-items-start col-8 d-flex flex-column justify-content-center'></div>");
        $(titles).append("<h3>"+data.name+"</h3>");
        $(titles).append("<small>"+data.factions.map((el) => el.name).join(" - ")+"</small>");
        $(div).append(titles);
    }

    function fetchAbilities() {
        $("#phase_container_start_of_turn,#phase_container_hero_phase,#phase_container_movement_phase,#phase_container_shooting_phase,#phase_container_charge_phase,#phase_container_combat_phase,#phase_container_end_of_turn").html("");

        var units = $("#units_dt").DataTable().data();
        console.log(units);
        $.each(units, function(k, unit) {
            $.each(unit.abilities, function(k, ability) {
                if(ability.declare) {
                    var declare = `<p><b>Annonce: </b>`+ability.declare+`</p>`;
                } else {
                    var declare = "";
                }

                if(ability.effect) {
                    var effect = `<p><b>Effet: </b>`+ability.effect+`</p>`;
                } else {
                    var effect = "";
                }

                // `++`
                $("#phase_container_"+ability.phase.slug).append(`
                    <div class="card my-1 p-0">
                        <div class="card-header py-2 background-phase-`+ability.phase.slug+`" style='min-height:unset;'>
                            <h5 class="card-title text-white">`+ability.phase_detail.name+`</h5>
                        </div>
                        <p class="text-center mx-3 my-2"><b class="fw-bold me-2">`+ability.name+`</b><small class="fst-italic">`+unit.name+`</small></p>
                        <div class="card-body">
                            `+declare+`
                            `+effect+`
                        </div>
                    </div>
                `);
            });
        });


// #phase_container_start_of_turn
// #phase_container_hero_phase
// #phase_container_movement_phase
// #phase_container_shooting_phase
// #phase_container_charge_phase
// #phase_container_combat_phase
// #phase_container_end_of_turn
    }

</script>
<script>
    $(function() {
        var units_dt = $("#units_dt").DataTable({
            idSrc: 'id',
            dom:"<'row'<'col-12'tr>>",
			info: false,
			searching: false,
			paging: false,
			buttons: [],
            data: null,
            columns: [
                {
                    data: "rowImage",
                    title: "",
                    width: "3%",
                    className: "clickable",
                    createdCell: function(td, cellData, rowData) {
                        $(td).html("<div class='symbol w-100'><div class='symbol-label w-100 h-150px' style='aspect-ratio:16/9;background-size:contain;background-image:url(\""+cellData+"\")'></div></div>");
                    }
                },
                {
                    data: "name",
                    title: "Nom",
                    className: "clickable"
                },
                {
                    data: null,
                    title: "Actions",
                    className: "text-end",
                    width: "10%",
                    orderable: false,
                    createdCell: function(td, cellData, rowData) {
                        var dropdown = $("<div>").addClass("dropdown").html(
                            $("<button>").addClass(
                                "btn btn-primary dropdown-toggle no-caret px-4 py-1")
                            .attr('type', 'button').attr('data-bs-toggle', 'dropdown')
                        );

                        var ul = $("<ul>").addClass('dropdown-menu dropwdown-menu-end');

                            $(ul).append(
                                $('<li>').append(
                                    $("<a>").addClass('dropdown-item cursor-pointer').attr(
                                        "onclick",
                                        "RemoveDtUnit(" + rowData.id +
                                        ", this)"
                                    ).html(
                                        "<i class='fas fa-trash me-2'></i>Supprimer"
                                    )
                                )
                            );

                        $(ul).appendTo(dropdown);
                        if ($(ul).children().length > 0) {
                            $(td).html(dropdown);
                        } else {
                            $(td).html("");
                        }
                    }
                }
            ],
            // autoWidth: false,
            order: [
                [1, "asc"]
            ],
            drawCallback: function(settings, json) {
                $('[data-toggle="tooltip"]').tooltip();

                $('#units_dt tbody').on('click', '.clickable', function() {
                    var data = $('#units_dt').DataTable().row( $(this).parents("tr") ).data();
                    window.location.href = "{{ route('units.index') }}/" + data.id;
                });

                fetchAbilities();
            },
            initComplete: function(settings, json) {
                fetchDtUnits();
            }
        });

        $("#unit_search_input").on("input", delay(function() {
                console.log($("#unit_search_input").val());
                if($("#unit_search_input").val().length > 3) {
                    $('#unit_search').submit();
                }
            }, 500)
        );

        $('#unit_search').on('submit', function (e) {
            e.preventDefault();

            var data = new FormData($(this)[0]);

            $.ajax({
                url: "{{ route('team.search') }}",
                method: 'POST',
                data: new URLSearchParams(data).toString(),
                beforeSend: function () {
                    $(".search_result_container, .search_result_spinner, .search_result_no_result, .search_result_results").addClass("d-none");
                    $(".search_result_results").html("");
                    $(".search_result_container, .search_result_spinner").removeClass("d-none");
                },
                // return the result
                success: function (result) {
                    showToastSuccessLite({
                        body: $("#unit_search_input").val(),
                    });

                    if(result.data.length == 0) {
                        $(".search_result_no_result").removeClass("d-none");
                    } else {
                        $.each(result.data, function(k,v) {
                            var div = $("<div>").addClass("row clickable");
                            drawSearchElement(div, v);

                            $(div).on("click", function() {
                                $("#units_dt").DataTable().row.add(v).draw();
                                $(".search_result_container, .search_result_spinner, .search_result_no_result, .search_result_results").addClass("d-none");
                                $("#unit_search_input").val("");
                                
                                addLSUnit(v);
                            })
                            // console.log(k,v);
                            $(".search_result_results").append(div);
                        });

                        $(".search_result_results").removeClass("d-none");
                    }
                },
                error: function (jqXHR, testStatus, error) {
                    console.error(error);
                    showToastDangerLite({
                        body: "Erreur lors de la récupération des informations du fournisseur",
                    });
                },
                complete: function () {
                    $(".search_result_spinner").addClass("d-none");
                    // $(el).prop("disabled", false).find('i').removeClass("fa-spinner fa-spin");
                },
            });
        });
    });
</script>
@endsection