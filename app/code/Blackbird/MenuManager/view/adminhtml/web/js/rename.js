define([
    'jquery',
    'menuManagerTree',
    'menuManagerEditorInit'
], function ($) {
    return function(options, element) {
        var editorBlock = $(element);
        var nodeInput = $(element);
        var treeContainer = $('#menumanager_tree_container');
        var tree = treeContainer.jstree(true);
        var divContainerNodeData = $('div#div-data-container-node');

        //Hide the block nodes data
        editorBlock.addClass('ignore-validate').hide();

        treeContainer.on("changed.jstree", function (e, data) {
            //Show the block with node data
             editorBlock.removeClass('ignore-validate').show();
            divContainerNodeData.show();

            if(options.type == 'name') {
                nodeInput.val(data.instance.get_text(data.selected));
            } else if (options.type == 'classes') {
                var node = data.instance.get_node(data.selected);
                if(node.data) {
                    nodeInput.val(node.data.classes);
                }
            } else if (options.type == 'status') {
                var node = data.instance.get_node(data.selected);
                if(node.data) {
                    nodeInput.val(node.data.status);
                }
            } else if (options.type == 'target') {
                var node = data.instance.get_node(data.selected);
                if(node.data) {
                    nodeInput.val(node.data.target);
                }
            } else if (options.type == 'type') {
                var node = data.instance.get_node(data.selected);
                if(node.data) {
                    nodeInput.val(node.data.type);
                }
            } else if (options.type == 'entity_id') {
                var node = data.instance.get_node(data.selected);
                if(node.data) {
                    nodeInput.val(node.data.entity_id);
                }
            } else if (options.type == 'url_path') {
                var node = data.instance.get_node(data.selected);
                if(node.data) {
                    nodeInput.val(node.data.url_path);
                }
            } else if (options.type == 'canonical') {
                var node = data.instance.get_node(data.selected);
                if(node.data) {
                    nodeInput.val(node.data.canonical);
                }
            } else if (options.type == 'link_first_child') {
                var node = data.instance.get_node(data.selected);
                if(node.data) {
                    nodeInput.val(node.data.link_first_child);
                }
            }

        });
        nodeInput.change(function () {
            tree = treeContainer.jstree(true);
            if(options.type == 'name') {
                tree.rename_node(tree.get_selected(), $(this).val());
            } else if (options.type == 'classes') {
                tree.get_node(tree.get_selected()).data.classes = $(this).val();
            } else if (options.type == 'status') {
                tree.get_node(tree.get_selected()).data.status = $(this).val();
            } else if (options.type == 'target') {
                tree.get_node(tree.get_selected()).data.target = $(this).val();
            } else if (options.type == 'type') {
                tree.get_node(tree.get_selected()).data.type = $(this).val();
                //to dynamically change the block when a node type changes
                var node1 = tree.get_selected();
                tree.deselect_node(node1);
                tree.select_node(node1);
            } else if (options.type == 'entity_id') {
                tree.get_node(tree.get_selected()).data.entity_id = $(this).val();
            } else if (options.type == 'url_path') {
                tree.get_node(tree.get_selected()).data.url_path = $(this).val();
            } else if (options.type == 'canonical') {
                tree.get_node(tree.get_selected()).data.canonical = $(this).val();
            } else if (options.type == 'link_first_child') {
                tree.get_node(tree.get_selected()).data.link_first_child = $(this).val();
            }
        });
    }
});