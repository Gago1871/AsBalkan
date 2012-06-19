
// jquery
$(function () {

    //sticky scroll
    var a = function () {
        var b = $(window).scrollTop();
        var d = $("#scroller-anchor").offset().top;
        var c = $("#scroller");
        var h = $("#header-container").height();

        var space = h + 20;

        console.log('b=' + b + ' d=' + d + ' h=' + h);
        if (b > d - space) {
            c.css({position:"fixed",top:space + "px"})
        } else {
            if (b <= d + space) {
                c.css({position:"relative",top:""})
            }
        }
    };
    
    $(window).scroll(a);a();

    // init timeago
    $("time.timeago").timeago();

    // set up navigation buttons
    $('#nav-show-upload-form').click(function () {
        // $('#upload-form-layer').show();
        // return false;
    });

    $('#nav-hide-upload-form').click(function () {
        // $('#upload-form-layer').hide();
        // return false;
    });
    
    function scrollToPosition(element) {
        if (element !== undefined) {
            var y = $(element).position().top - 20;
            $('html, body').animate({scrollTop : y}, 200);
        }
    }

    //Create an Array of posts
    var posts = $('.post-element');
    var position = 0; //Start Position

    // bind keyboard shortcuts
    $(window).bind('keydown', 'j', function () {
        scrollToPosition(posts[++position]);
    });

    $(window).bind('keydown', 'k', function () {
        if (position > 0) {
            scrollToPosition(posts[--position]);    
        };
    });

    $(window).bind('keydown', 'r', function () {
        console.log('"r" pressed - rate');
    });

    $(window).bind('keydown', 'c', function () {
        console.log('"c" pressed - comment');
    });

    $(window).bind('keydown', 'h', function () {
        console.log('"h" pressed - hate');
    });

    $(window).bind('keydown', 'l', function () {
        console.log('"l" pressed - like');
    });

// <input type="hidden" name="uploadfromfile" value="0" id="uploadfromfile">
// <tr><td id="file-label"><label for="file" class="required">url pliku <span>(<a id="nav-upload-form-switch-source" href="?uploadfromfile=1">lub dodaj z dysku</a>)</span></label></td>
// <td class="element">
// <input type="text" name="file" id="file" value="" placeholder="http://www" class="poebao">
// <p class="hidden">A to jest opis pola WWW</p></td></tr>



// source
// <tr><td id="source-label"><label for="source" class="optional">Źródło <span>(opcjonalnie)</span></label></td>
// <td class="element">
// <input type="text" name="source" id="source" value="" class="poebao">
// <p class="hidden">A to jest opis pola Źródło</p></td></tr>
    

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

    

    // init form validation
    // $("input").blur(function () {
        
    //     var forElementId = $(this).parent().prev().find("label").attr("for");

    //     doValidation(forElementId);
    // });

    // function doValidation(id) {

    //     var url = "/async/validate/post";
    //     var data = {};
    //     $("input").each(function () {
    //         data[$(this).attr("name")] = $(this).val();
    //     });

    //     $.post(url, data, function (resp) {
            
    //         $("#"+id).parent().find(".errors").remove();
    //         $("#"+id).parent().prepend(getErrorHtml(resp[id], id));
    //     }, "json")
    // }

    // function getErrorHtml(formErrors, id) {
    //     var o = '<ul id="errors-' + id + '" class="errors">';
    //     for (errorKey in formErrors) {
    //         o += "<li>" + formErrors[errorKey] + "</li>";
    //     };

    //     o += "</ul>";
    //     return o;
    // }

    // function uploadSuccess(msg) {
    //     window.location.href = msg.data;
    // }

    // function uploadFailure(msg) {
    //     // $("#"+id).parent().find(".errors").remove();
    //     // $("#"+id).parent().prepend(getErrorHtml(resp[id], id));
    //     console.log(msg.data);

    //     for (error in msg.data) {
    //         console.log(error);
    //     };
    // }

    // // init upload form
    // $('#uploadform').submit(function () {
        
    //     var request = $.ajax({
    //             url: "/dodaj",
    //             type: "POST",
    //             data: $(this).serialize(),
    //             dataType: "json"
    //         });

    //     request.done(function (msg) {
    //         if (msg.status == "success") {
    //             uploadSuccess(msg);
    //         } else {
    //             uploadFailure(msg);
    //         }
    //     });

    //     request.fail(function(jqXHR, textStatus) {
    //         alert( "Request failed: " + textStatus );
    //     });

    //     return false;
    // });

    // infinite scroll

    // infinitescroll() is called on the element that surrounds 
    // the items you will be loading more of
    $('#posts').infinitescroll({

        loading: {
            finished: undefined,
            finishedMsg: "<em>Congratulations, you've reached the end of the internet.</em>",
            img: "/img/loader.gif",
            msgText: "",
            msg: null,
            selector: null,
            speed: 'fast',
            start: undefined
        },
        state: {
            isDuringAjax: false,
            isInvalidPage: false,
            isDestroyed: false,
            isDone: false, // For when it goes all the way through the archive.
            isPaused: false,
            currPage: 1
        },
        callback: undefined,
        debug: false,
        behavior: undefined,
        binder: $(window), // used to cache the selector
        navSelector  : "#paginator", // selector for the paged navigation (it will be hidden)           
        nextSelector : "#nav-post-list-next-page", // selector for the NEXT link (to page 2)
        
        extraScrollPx: 150,
        itemSelector : "ul.post-list", // selector for all items you'll retrieve
        animate: false,
        pathParse: undefined,
        dataType: 'html',
        appendCallback: true,
        bufferPx: 40,
        errorCallback: function () { },
        infid: 0, //Instance ID
        pixelsFromNavToBottom: 100,
        path: undefined
    });
});