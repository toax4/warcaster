<div id="toast-container" class="toast-container position-fixed  bottom-0 start-50 translate-middle-x p-5"
    style="z-index: 11">
    @foreach (["primary", "success", "danger"] as $theme)
    <div id="toast-{{$theme}}" class="toast align-items-center text-white bg-{{$theme}}" role="alert"
        aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <img src="/assets/media/logos/favicon.ico" class="rounded me-2" alt="...">
            <strong class="me-auto toast-title">Bootstrap</strong>
            {{-- <small>11 mins ago</small> --}}
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="d-flex">
            <div class="toast-body"></div>
        </div>
    </div>
    <div id="toast-lite-{{$theme}}" class="toast align-items-center text-white bg-{{$theme}}" role="alert"
        aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
    @endforeach
</div>
