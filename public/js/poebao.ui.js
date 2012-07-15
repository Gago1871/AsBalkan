var poebao = poebao || {};

poebao.ui = {

    init: function () {
        $(window).scroll(poebao.ui.evntScrollWindow);
        
        poebao.ui.evntScrollWindow();
        poebao.ui.cropPostAgoPictures();

        // init timeago
        $("time.timeago").timeago();
    },

    infiniteScrollLoaded: function () {
        $("time.timeago").timeago();
        // render Facebook buttons
        FB.XFBML.parse();

        // render G+ buttons
        gapi.plusone.go();
    },

    // Make additional column stick to header
    stickyAdditional: function (scrollTop) {
        
        var d = $("#scroller-anchor").offset().top;
        var c = $("#scroller");
        var h = $("#header-container").height();

        var space = h + 20;

        if (scrollTop > d - space) {
            c.css({position:"fixed",top:space + "px"})
        } else {
            if (scrollTop <= d + space) {
                c.css({position:"relative",top:""})
            }
        }
    },

    cropPostAgoPictures: function () {
        poebao.ui.cropPostAgoPicture($("#posts-ago-0 img"), {width: 160, height: 210});
        poebao.ui.cropPostAgoPicture($("#posts-ago-1 img"), {width: 120, height: 100});
        poebao.ui.cropPostAgoPicture($("#posts-ago-2 img"), {width: 120, height: 100});
    },

    cropPostAgoPicture: function (img, destSize) {
    
        $("<img/>") // Make in memory copy of image to avoid css issues
            .attr("src", img.attr("src"))
            .load(function() {
                width = this.width;   // Note: $(this).width() will not
                height = this.height; // work for in memory images.

                if (width > height) {
                    // horizontal photo
                    img.css('height', destSize.height);
                    var x = (img.width() - destSize.width) / -2;
                    img.css('margin-left', x);
                } else {
                    // vertical photo
                    img.css('width', destSize.width);
                    var y = (img.height() - destSize.height) / -2;
                    img.css('margin-top', y);
                };
            });
    },

    // Minimize header when scrolled down a little
    minimizingHeader: function (scrollTop) {
        if (scrollTop > 50) {
            poebao.ui.minimizeHeader();
        } else {
            poebao.ui.maximizeHeader();
        }
    },

    minimizeHeader: function () {
        var duration = 100;
        // $("#header img").attr("src", "/img/poebao-smaller.png");
        $("#header img").animate({width: "120px"}, { duration: duration, queue: false });
        $("#header").animate({height: "52px"}, { duration: duration, queue: false });
        $("#header").animate({"padding-top": "0px"}, { duration: duration, queue: false });
        $("#nav").animate({"margin-top": "12px"}, { duration: duration, queue: false });
    },

    maximizeHeader: function () {
        var duration = 100;
        $("#header").animate({height: "85px"}, { duration: duration, queue: false });
        $("#header").animate({"padding-top": "15px"}, { duration: duration, queue: false });
        $("#nav").animate({"margin-top": "23px"}, { duration: duration, queue: false });
        $("#header img").animate({width: "179px"}, { duration: duration, queue: false });
        // $("#header img").attr("src", "/img/poebao.png");
    },

    // call when window is scrolled
    evntScrollWindow: function () {
        var scrollTop = $(window).scrollTop();

        poebao.ui.stickyAdditional(scrollTop);
        poebao.ui.minimizingHeader(scrollTop);
    }
};