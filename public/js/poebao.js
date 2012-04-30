
// jquery
$(function () {
    // set up navigation buttons
    $('#nav-show-upload-form').click(function () {
        $('#upload-form-layer').show();
        return false;
    });

    $('#nav-hide-upload-form').click(function () {
        $('#upload-form-layer').hide();
        return false;
    });
});