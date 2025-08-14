@extends('admin.layouts.layout')

@section('title', 'Warcaster')

@section("toolbar")
@include("admin.layouts.includes.breadcrump")
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
					<a class="nav-link text-active-primary py-5 me-6 active" id="home-tab" data-bs-toggle="tab"
						data-bs-target="#home-tab-pane" role="tab" aria-controls="home-tab-pane"
						aria-selected="true">Overview</a>
				</li>
				<!--end::Nav item-->
				<!--begin::Nav item-->
				<li class="nav-item">
					<a class="nav-link text-active-primary py-5 me-6" id="profile-tab" data-bs-toggle="tab"
						data-bs-target="#profile-tab-pane" role="tab" aria-controls="profile-tab-pane"
						aria-selected="true">Targets</a>
				</li>
				<!--end::Nav item-->
				<!--begin::Nav item-->
				<li class="nav-item">
					<a class="nav-link text-active-primary py-5 me-6" href="apps/projects/budget.html">Budget</a>
				</li>
				<!--end::Nav item-->
				<!--begin::Nav item-->
				<li class="nav-item">
					<a class="nav-link text-active-primary py-5 me-6" href="apps/projects/users.html">Users</a>
				</li>
				<!--end::Nav item-->
				<!--begin::Nav item-->
				<li class="nav-item">
					<a class="nav-link text-active-primary py-5 me-6" href="apps/projects/files.html">Files</a>
				</li>
				<!--end::Nav item-->
				<!--begin::Nav item-->
				<li class="nav-item">
					<a class="nav-link text-active-primary py-5 me-6" href="apps/projects/activity.html">Activity</a>
				</li>
				<!--end::Nav item-->
				<!--begin::Nav item-->
				<li class="nav-item">
					<a class="nav-link text-active-primary py-5 me-6" href="apps/projects/settings.html">Settings</a>
				</li>
				<!--end::Nav item-->
			</ul>
			<!--end::Nav-->
		</div>
	</div>
	<!--end::Navbar-->
	<!--begin::Row-->
	<div class="tab-content" id="unitTabContent">
		<div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab"
			tabindex="0">
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
@endsection
@section("scripts")
@include("admin.layouts.scripts.default")
@endsection