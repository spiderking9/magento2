define([
    'jquery',
    'menuManagerTree',
    'menuManagerEditorInit'
], function ($) {
    return function(options, element) {
        var buttonContainer = $(element);
        var treeContainer = $('#menumanager_tree_container');
        var tree = treeContainer.jstree(true);
        buttonContainer.children('button').click(function(e) {
            e.preventDefault();
            var selected = tree.get_selected();
            var $basetype = 'category';
            var nodedata = {
                    data:{
                        status: 1,
                        target: '_self',
                        type: $basetype
                    }};
            var divFieldsContainer = $('div#div-data-container-node');

           // var type = nodedata.data.type;
            if(selected.length == 0) {
                selected = '#';
            }

           if ($(this).data('remove') && selected != '#') {
                tree.delete_node(selected);
               divFieldsContainer.hide();
           } else if($(this).data('createRoot')){
                //create a new root menu
               var newRootNodeId = tree.create_node('#', nodedata, 'last');

               var newRootNode = $('#'+newRootNodeId);
               //change the attribute data-type
               newRootNode.attr('data-type', $basetype);
               //persist the attribute data-type
               treeContainer.jstree('get_node', newRootNode).li_attr['data-type'] = $basetype;
           } else if($(this).data('createChild')){
               //create a new child menu
               var newNodeId = tree.create_node(selected, nodedata);
               tree.open_node(selected);
               var newChildNode = $('#'+newNodeId);

               //change the attribute data-type
               newChildNode.attr('data-type', $basetype);
               //persist the attribute data-type
               treeContainer.jstree('get_node', newChildNode).li_attr['data-type'] = $basetype;
           }
        });

    }
});