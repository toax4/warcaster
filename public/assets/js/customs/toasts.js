// showToast
function showToast(toastTemplate, content = {}) {
    $(toastTemplate).find(".toast-title").html(content.title);
    $(toastTemplate).find(".toast-body").html(content.body);

    var toast = new bootstrap.Toast($(toastTemplate));

    toast.show();
}

function showToastPrimary(content = {}) {
    showToast(getToastPrimary(), content);
}
function showToastPrimaryLite(content = {}) {
    showToast(getToastPrimaryLite(), content);
}
function showToastSuccess(content = {}) {
    showToast(getToastSuccess(), content);
}
function showToastSuccessLite(content = {}) {
    showToast(getToastSuccessLite(), content);
}
function showToastDanger(content = {}) {
    showToast(getToastDanger(), content);
}
function showToastDangerLite(content = {}) {
    showToast(getToastDangerLite(), content);
}

// getToast
function getToast(toastTemplate) {
    // Create new toast element
    const newToast = $(toastTemplate).clone(true);
    $(newToast)
        .removeAttr("id")
        .addClass("toast-custom")
        .on("hidden.bs.toast", function () {
            // $(this).remove();
        });

    $("#toast-container").append(newToast);

    return $(newToast);
}
function getToastPrimary() {
    return getToast($("#toast-primary"));
}
function getToastPrimaryLite() {
    return getToast($("#toast-lite-primary"));
}
function getToastSuccess() {
    return getToast($("#toast-success"));
}
function getToastSuccessLite() {
    return getToast($("#toast-lite-success"));
}
function getToastDanger() {
    return getToast($("#toast-danger"));
}
function getToastDangerLite() {
    return getToast($("#toast-lite-danger"));
}

// Dev
function showToastPrimaryDev(content = {}) {
    showToast(getToastPrimary(), { title: "Furet" });
}
function showToastPrimaryLiteDev(content = {}) {
    showToast(getToastPrimaryLite(), {
        body: "<h1>showToastPrimaryLiteDev</h1>",
    });
}
function showToastSuccessDev(content = {}) {
    showToast(getToastSuccess(), {
        title: "success",
        body: "<br><br><br>showToastSuccessDev",
    });
}
function showToastSuccessLiteDev(content = {}) {
    showToast(getToastSuccessLite(), { body: "showToastSuccessLiteDev" });
}
function showToastDangerDev(content = {}) {
    showToast(getToastDanger(), { body: "showToastDangerDev" });
}
function showToastDangerLiteDev(content = {}) {
    showToast(getToastDangerLite(), content);
}
