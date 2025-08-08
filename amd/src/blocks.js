define(["jquery"], function ($) {
    var blocks = {
        create: function (cmid, thumb) {
            let $module = $(`body.format-weeks:not(.editing) #module-${cmid}, body.format-topics:not(.editing) #module-${cmid}`);
            $module.addClass("theme-block");
            $module.find(".activity-item")
                .css({"background-image": `url('${thumb}')`})
                .click(function () {
                    if (event.target === this) {
                        location.href = $module.find("a.aalink").attr("href");
                    }
                });
            $module.append($module.find(".activity-completion"));
        },

        icons: function (cmid, thumb) {
            $(`#course-index-cm-${cmid}`).addClass("personal-icon");

            $("<style>")
                .prop("type", "text/css")
                .html(`
                    #course-index-cm-${cmid} .activity-icon img,
                    #module-${cmid} .courseicon img,
                    .cmid-${cmid} #page-header .activityiconcontainer img {
                        content : url('${thumb}');
                        filter  : none;
                    }`)
                .appendTo("head");
        },

        color: function (cmid, color) {
            $("<style>")
                .prop("type", "text/css")
                .html(`
                    #module-${cmid} .courseicon {
                        background       : ${color} !important;
                        background-color : ${color} !important;
                    }`)
                .appendTo("head");
        },
    };

    return blocks;
});
