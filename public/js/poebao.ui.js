var poebao = {};

poebao.ui = {

    init: function () {
        $(window).scroll(poebao.ui.evntScrollWindow);
        
        poebao.ui.evntScrollWindow();
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
        $("#header img").animate({width: "127px"}, { duration: duration, queue: false });
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