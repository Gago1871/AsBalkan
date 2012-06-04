
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

    // init timeago
    $("time.timeago").timeago();

    // init upload form
    $('#uploadform').submit(function() {
        
        var request = $.ajax({
                url: "/dodaj",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json"
            });

            request.done(function(msg) {
                
                alert( "Request success: " + msg );
            });

            request.fail(function(jqXHR, textStatus) {
                alert( "Request failed: " + textStatus );
            });

        return false;
    });

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