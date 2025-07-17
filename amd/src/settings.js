define(["jquery", "theme_boost_training/minicolors"], function($, minicolors) {
    return {
        minicolors: function(elementid) {
            window.$ = $;
            $("#" + elementid).minicolors();
        }
    };
});
