function removeFromUrl() {
    $('#upload-from-url').remove();
}

function removeFromFile() {
    $('#upload-from-file').remove();
    $('#upload-from-file-source').remove();
}

function addFromFile() {
    $('#upload-from-url').parent().prepend('<tr id="upload-from-file"><td id="file-label"><label for="file" class="required">Wgraj z dysku <span>(<a id="nav-upload-form-switch-source" href="?uploadfromfile=0">lub z url</a>)</span></label></td><td class="element"><input type="hidden" name="MAX_FILE_SIZE" value="15728640" id="MAX_FILE_SIZE"><input type="file" name="file" id="file" class="file"></td></tr>');
    // add source
}

function addFromUrl() {
    $('#upload-from-file').parent().prepend('<tr id="upload-from-url"><td id="file-label"><label for="file" class="required">url pliku <span>(<a id="nav-upload-form-switch-source" href="?uploadfromfile=1">lub dodaj z dysku</a>)</span></label></td><td class="element"><input type="text" name="file" id="file" value="" placeholder="http://www" class="poebao"><p class="hidden">A to jest opis pola WWW</p></td></tr>');
}


    // function switchSource() {
    //     console.log('yo');
    //     if ($('#upload-from-file').length == 0) {

    //         console.log('leng = 0');

    //         $('#uploadfromfile').val(1);
    //         addFromFile();
    //         removeFromUrl();
    //     } else {

    //         console.log('leng != 0');

    //         $('#uploadfromfile').val(0);
    //         addFromUrl();
    //         removeFromFile();
    //     }

    //     $('#nav-upload-form-switch-source').unbind('click');

    //     $('#nav-upload-form-switch-source').click(function () {
    //         switchSource();
    //         return false;
    //     });

    //     $('#nav-upload-form-switch-source').bind('click');

        
    // }

    // swtich upload form file<->url
    // $('#nav-upload-form-switch-source').click(function () {
    //     switchSource();
    //     return false;
    // });
