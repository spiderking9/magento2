/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            menuManagerTree: "Blackbird_MenuManager/js/jstree",
            menuManagerEditorInit: "Blackbird_MenuManager/js/init",
            menuManagerEditorSerialize: "Blackbird_MenuManager/js/serialize",
            menuManagerEditorRename: "Blackbird_MenuManager/js/rename",
            chooser: "Blackbird_MenuManager/js/chooser",
            content: "Blackbird_MenuManager/js/content",
            menuManagerEditorAdd: "Blackbird_MenuManager/js/add",
            menuManagerCategory: "Blackbird_MenuManager/js/category",
            menuManagerCmsPage: "Blackbird_MenuManager/js/cmspage",
            menuManagerCmsBlock: "Blackbird_MenuManager/js/cmsblock",
            menuManagerProduct: "Blackbird_MenuManager/js/product",
            menuManagerContent: "Blackbird_MenuManager/js/node_content",
            menuManagerCustomField: "Blackbird_MenuManager/js/customfield",
            menuManagerGroup: "Blackbird_MenuManager/js/group",
            menuManagerContentList: "Blackbird_MenuManager/js/contentlist"
        }
    },
    deps: [
        'Blackbird_MenuManager/js/init'
    ]
};
