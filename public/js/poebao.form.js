var poebao = poebao || {};
poebao.ui = poebao.ui || {};

poebao.ui.uploadForm = {

    sourceAnimSpeed: 900,
    sourceAnimDelay: 1000,
    widerField: 440,
    narrowerField: 135,

    init: function () {

        var bigger = 440;
        var smaller = 135;

        $("#url").focus(poebao.ui.uploadForm.widenUrl);
        $("#file").change(poebao.ui.uploadForm.widenFile);
    },

    widenUrl: function() {
        $("#fromFile").prop('checked', false);

        $("#url").animate({
            width: poebao.ui.uploadForm.widerField + 'px',
            delay: poebao.ui.uploadForm.sourceAnimDelay
        }, poebao.ui.uploadForm.sourceAnimSpeed);

        $("#file").animate({
            width: poebao.ui.uploadForm.narrowerField + 'px',
            delay: poebao.ui.uploadForm.sourceAnimDelay
        }, poebao.ui.uploadForm.sourceAnimSpeed);
    },

    widenFile: function() {
        $("#fromFile").prop('checked', true);
        $("#url").animate({
            width: poebao.ui.uploadForm.narrowerField + 'px',
            delay: poebao.ui.uploadForm.sourceAnimDelay
        }, poebao.ui.uploadForm.sourceAnimSpeed);

        $("#file").animate({
            width: poebao.ui.uploadForm.widerField + 'px',
            delay: poebao.ui.uploadForm.sourceAnimDelay
        }, poebao.ui.uploadForm.sourceAnimSpeed);
    }
}