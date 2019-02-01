define([
    'jquery',
    'menuManagerTree',
    'menuManagerEditorInit'
], function ($) {
    return function(options, element) {
        var serializedInput = $(element);
        var treeContainer = $('#menumanager_tree_container');
        var anyInputText = $('.input-text');
        var anyInputSelect = $('.input-select');
        var buttonsApply = $('.button-apply-chooser');
        var selectNodeType = $('#menumanager-node-type');

        if(treeContainer.jstree(true) === false) {
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
        var tree = treeContainer.jstree(true);

        function serialize() {
            // var data = tree.get_json(null, {flat:true, no_a_attr:true, no_li_attr:true, no_state:true});
            var data = tree.get_json(null, {flat:true, no_a_attr:true, no_li_attr:true, no_state:true});
            data = JSON.stringify(data);
            serializedInput.val(data);
        }

        treeContainer.on("changed.jstree", function () {
            serialize();
            buttonsApply.trigger("click");
        });
        anyInputText.change(function (){
            serialize();
        });
        anyInputSelect.change(function () {
            serialize();
        });
        selectNodeType.change(function(){
            //Change the icon in front of the node when the node type is changed
            var node = tree.get_selected();
            var selected = tree.get_node(node);
            var selectedNode = $('.jstree-clicked').parent("li").eq(0);
            //change the attribute data-type
            selectedNode.attr('data-type', selectNodeType.val());
            //persist the attribute data-type
            treeContainer.jstree('get_node', selectedNode).li_attr['data-type'] = selectNodeType.val();
        });
        $(document).on("dnd_stop.vakata", serialize);
    }
});