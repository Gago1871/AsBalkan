
// jquery
$(function () {
    // set up navigation buttons
    // $('#nav-show-upload-form').click(function () {
    //     $('#upload-form-layer').show();
    //     return false;
    // });

    // $('#nav-hide-upload-form').click(function () {
    //     $('#upload-form-layer').hide();
    //     return false;
    // });

    $("time.timeago").timeago();

    // infinite scroll

    // infinitescroll() is called on the element that surrounds 
    // the items you will be loading more of
    $('ul#posts').infinitescroll({

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
        debug: true,
        behavior: undefined,
        binder: $(window), // used to cache the selector
        navSelector  : "div#paginator", // selector for the paged navigation (it will be hidden)           
        nextSelector : "#nav-post-list-next-page", // selector for the NEXT link (to page 2)
        
        extraScrollPx: 150,
        itemSelector : "ul#posts", // selector for all items you'll retrieve
        animate: false,
        pathParse: undefined,
        dataType: 'html',
        appendCallback: true,
        bufferPx: 40,
        errorCallback: function () { },
        infid: 0, //Instance ID
        pixelsFromNavToBottom: undefined,
        path: undefined
    });
});