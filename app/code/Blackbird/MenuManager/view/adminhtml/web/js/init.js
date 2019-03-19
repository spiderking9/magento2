define([
    'jquery',
    'menuManagerTree'
], function ($) {
    return function (options, element) {
        var treeContainer = $(element);
        treeContainer.jstree({
            "core": {
                "check_callback": true,
                "multiple": false,
                "themes": {
                    "icons": true,
                    "dots": true
                }
            },
            "plugins": ["dnd"]
        });
    }
});