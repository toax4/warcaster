@extends('admin.layouts.layout')

@section('title', $unit->name)

@section("toolbar")
<!--begin::Toolbar-->
<div class="toolbar py-5 pb-lg-15" id="kt_toolbar">
	<!--begin::Container-->
	<div id="kt_toolbar_container" class="container-xxl d-flex flex-stack flex-wrap">
		<!--begin::Page title-->
		<div class="page-title d-flex flex-column me-3">
			<!--begin::Title-->
			<h1 class="d-flex text-white fw-bold my-1 fs-3">{{ $unit->name }}</h1>
			<!--end::Title-->
			<!--begin::Breadcrumb-->
			<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
				<!--begin::Item-->
				<li class="breadcrumb-item text-white opacity-75">
					<a href="/" class="text-white text-hover-primary">Accueil</a>
				</li>
				<!--end::Item-->
				<!--begin::Item-->
				<li class="breadcrumb-item">
					<span class="bullet bg-white opacity-75 w-5px h-2px"></span>
				</li>
				<!--end::Item-->
				<!--begin::Item-->
				<li class="breadcrumb-item text-white opacity-75">
					<a href="{{ route('units.index') }}" class="text-white text-hover-primary">
						Unités
					</a>
				</li>
				<!--end::Item-->
				<!--begin::Item-->
				<li class="breadcrumb-item">
					<span class="bullet bg-white opacity-75 w-5px h-2px"></span>
				</li>
				<!--end::Item-->
				<!--begin::Item-->
				<li class="breadcrumb-item text-white opacity-75">{{ $unit->name }}</li>
				<!--end::Item-->
			</ul>
			<!--end::Breadcrumb-->
		</div>
		<!--end::Page title-->
	</div>
	<!--end::Container-->
</div>
<!--end::Toolbar-->
@endsection

@section('content')
<!--begin::Post-->
<div class="content flex-row-fluid" id="kt_content">
	<!--begin::Navbar-->
	<div class="card mb-6 mb-xl-9">
		<div class="card-body pt-9 pb-0">
			<!--begin::Details-->
			<div class="d-flex flex-wrap flex-sm-nowrap mb-6">
				<!--begin::Image-->
				<div class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4"
					style="background-position: center;background-repeat: no-repeat;background-size: cover;background-image:url('{{ $unit->bannerImage }}')">
					{{-- <img class="mw-50px mw-lg-75px" src="" alt="image" /> --}}
				</div>
				<!--end::Image-->
				<!--begin::Wrapper-->
				<div class="flex-grow-1">
					<!--begin::Head-->
					<div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
						<!--begin::Details-->
						<div class="d-flex flex-column">
							<!--begin::Status-->
							<div class="d-flex align-items-center mb-1">
								<a href="#" class="text-gray-800 text-hover-primary fs-2 fw-bold me-3">{{ $unit->name
									}}</a>
							</div>
							<!--end::Status-->
							<!--begin::Description-->
							<div class="d-flex flex-wrap fw-semibold fs-5 text-gray-500">{{ implode(", ",
								$unit->factions->map(function($element) { return $element->withTranslation()->name;
								})->toArray() ) }}</div>
							<div class="d-flex flex-wrap fw-semibold fs-5 text-gray-500 fst-italic">{{ implode(", ",
								$unit->keywords->map(function($element) { return $element->withTranslation()->label;
								})->toArray() ) }}</div>
							<!--end::Description-->
						</div>
						<!--end::Details-->
						<!--begin::Actions-->
						<div class="d-flex mb-4">

						</div>
						<!--end::Actions-->
					</div>
					<!--end::Head-->
					<!--begin::Info-->
					<div class="d-flex flex-wrap justify-content-start">

					</div>
					<!--end::Info-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Details-->
			<div class="separator"></div>
			<!--begin::Nav-->
			<ul class="nav nav-tabs nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold"
				id="unitTab" role="tablist">
				<!--begin::Nav item-->
				<li class="nav-item">
					<a class="nav-link text-active-primary py-5 me-6 active" id="weapon-abilities-tab"
						data-bs-toggle="tab" data-bs-target="#weapon-abilities-tab-pane" role="tab"
						aria-controls="weapon-abilities-tab-pane" aria-selected="true">Armes / Aptitudes</a>
				</li>
				<!--end::Nav item-->
				<!--begin::Nav item-->
				<li class="nav-item">
					<a class="nav-link text-active-primary py-5 me-6" id="profile-tab" data-bs-toggle="tab"
						data-bs-target="#profile-tab-pane" role="tab" aria-controls="profile-tab-pane"
						aria-selected="true">Targets</a>
				</li>
				<!--end::Nav item-->
			</ul>
			<!--end::Nav-->
		</div>
	</div>
	<!--end::Navbar-->
	<!--begin::Row-->
	<div class="tab-content" id="unitTabContent">
		<div class="tab-pane fade show active" id="weapon-abilities-tab-pane" role="tabpanel"
			aria-labelledby="weapon-abilities-tab" tabindex="0">
			<div class="row gx-6 gx-xl-9">
				<div class="col-xl-12">
					<div class="card mb-5 mb-xxl-8">
						<div class="card-header border-0 pt-5">
							<div class="card-title align-items-start flex-column">
								<div class="card-label fw-bold fs-3 mb-1">
									Armes
								</div>
							</div>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-12">
									<table
										class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold dataTable"
										id="weapons_dt"></table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-12">
					<div class="card mb-5 mb-xxl-8">
						<div class="card-header border-0 pt-5">
							<div class="card-title align-items-start flex-column">
								<div class="card-label fw-bold fs-3 mb-1">
									Aptitudes
								</div>
							</div>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-12">
									<table
										class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold dataTable"
										id="abilites_dt"></table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
			<div class="row gx-6 gx-xl-9">
			</div>
		</div>
	</div>


	<!--end::Row-->
</div>
<!--end::Post-->
@endsection

@section("styles")
@include("admin.layouts.styles.default")
@include("admin.layouts.styles.global")
@endsection
@section("scripts")
@include("admin.layouts.scripts.default")
@include("admin.layouts.scripts.datatable")

<script>
	$(function() {
        var weapons_dt = $("#weapons_dt").DataTable({
			dom:"<'row'<'col-12'tr>>",
			info: false,
			searching: false,
			paging: false,
			buttons: [],
            ajax: {
                type: "GET",
                url: "{{ route('api.units.weapons', ['unit' => $unit->id]) }}",
                data: function(d) {},
                dataSrc: function(data) {
                    return data.data;
                }
            },
            columns: [
                {
                    data: "name",
                    title: "Nom",
                    className: "clickable"
                },
                {
                    data: "range",
                    title: "Portée",
					width: "5%",
                    className: "clickable text-center"
                },
                {
                    data: "attack",
                    title: "Attaque",
					width: "5%",
                    className: "clickable text-center"
                },
                {
                    data: "hit",
                    title: "Touche",
					width: "5%",
                    className: "clickable text-center"
                },
                {
                    data: "wound",
                    title: "Blessure",
					width: "5%",
                    className: "clickable text-center"
                },
                {
                    data: "rend",
                    title: "Perforation",
					width: "5%",
                    className: "clickable text-center"
                },
                {
                    data: "damage",
                    title: "Dégâts",
					width: "5%",
                    className: "clickable text-center"
                },
                {
                    data: "abilities",
                    title: "Aptitude(s)",
					width: "25%",
                    className: "clickable",
					createdCell: function(td, cellData, rowData) {
                        $(td).html(cellData.map((ability) => ability.name).join(", "));
					}
                },
            ],
            order: [
                [0, "asc"]
            ],
            drawCallback: function(settings, json) {
                $('[data-toggle="tooltip"]').tooltip();

                $('#weapons_dt tbody').on('click', '.clickable', function() {
                    var data = $('#weapons_dt').DataTable().row( $(this).parents("tr") ).data();
                    // window.location.href = "{{ route('units.index') }}/" + data.id;
                });
            }
        });
        var abilites_dt = $("#abilites_dt").DataTable({
			dom:"<'row'<'col-12'tr>>",
			info: false,
			searching: false,
			paging: false,
			buttons: [],
            ajax: {
                type: "GET",
                url: "{{ route('api.units.abilities', ['unit' => $unit->id]) }}",
                data: function(d) {},
                dataSrc: function(data) {
                    return data.data;
                }
            },
            columns: [
                {
                    data: "name",
                    title: "Nom",
                    className: "clickable"
                },
                {
                    data: "phase.displayOrder",
                    title: "Phase",
					width: "25%",
                    className: "clickable",
					createdCell: function(td, cellData, rowData) {
						$(td).html(rowData.phase.name);

						$(td).prepend($("<i>").addClass("fa-solid fa-square me-2").css("color", rowData.phase.hexcolor))
					}
                },
                {
                    data: "lore",
                    title: "Lore",
					width: "25%",
                    className: "clickable fst-italic"
                },
                {
                    data: "declare",
                    title: "Annonce",
					width: "25%",
                    className: "clickable"
                },
                {
                    data: "effect",
                    title: "Effet",
					width: "25%",
                    className: "clickable"
                },
            ],
            order: [
                [1, "asc"],
                [0, "asc"]
            ],
            drawCallback: function(settings, json) {
                $('[data-toggle="tooltip"]').tooltip();

                $('#abilites_dt tbody').on('click', '.clickable', function() {
                    var data = $('#abilites_dt').DataTable().row( $(this).parents("tr") ).data();
                    // window.location.href = "{{ route('units.index') }}/" + data.id;
                });
            }
        });
    });

</script>
@endsection