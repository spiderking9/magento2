define([
    'jquery',
    'menuManagerTree',
    'menuManagerEditorInit'
], function ($) {
    return function(options, element) {
        var editorBlock = $(element);
        editorBlock.addClass('ignore-validate').hide();
        var input = editorBlock.find('#contentlist_id');
        var treeContainer = $('#menumanager_tree_container');
        var tree = treeContainer.jstree(true);
        treeContainer.on("changed.jstree", function (e, data) {
            if (data.node.data && data.node.data.type == options.type) {
                editorBlock.removeClass('ignore-validate').show();
                input.val(data.node.data.entity_id);
            } else {
                editorBlock.addClass('ignore-validate').hide();
                input.val(null);
            }
        });
        input.change(function () {
            var node = tree.get_selected();
            var selected = tree.get_node(node);
            selected.data.entity_id = $(this).val();
        });
    }
});