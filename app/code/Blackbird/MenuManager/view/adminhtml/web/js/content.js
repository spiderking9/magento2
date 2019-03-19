/**
 * Blackbird MenuManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category            Blackbird
 * @package		Blackbird_MenuManager
 * @copyright           Copyright (c) 2016 Blackbird (http://black.bird.eu)
 * @author		Blackbird Team
 */
/*jshint browser:true*/
/*global alert:true*/
define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($) {
    'use strict';
    $.widget('mage.contentEdit', {
        options: {
            elem: '',
            container: '.control .chooser-container'
        },


        _create: function () {
            this.options.elem = $('#' + this.options.fieldId);
            this.options.field = this.element.find('input#' + this.options.fieldId);
            this.options.btn_apply = this.element.find('button#apply');
            this.options.btn_chooser = this.element.find('button#open_chooser');
            this._initFieldRelation();
            this._initAction();
        },

        _initFieldRelation: function () {
            var elem = this.element;
            var field = this.options.field;
            var type = this.options.fieldType;
            var urlSource = this.options.urlSource;
            var formKey = this.options.formKey;
            var fieldId = this.options.fieldId;
            var container = this.options.container;
            /**
             * Open the chooser for the node type
             */
            var openChooser = function () {
                var params = {};
                var selectedItems = field.val().split(',');

                //Empty previous selected values
                window.category_id_chooser.chooserSelectedItems = $H({});
                window.content_id.chooserSelectedItems = $H({});
                window.contentlist_id.chooserSelectedItems = $H({});
                window.product_id.chooserSelectedItems = $H({});
                window.cms_page_identifier.chooserSelectedItems = $H({});
                window.cms_block_identifier.chooserSelectedItems = $H({});

                // Dispatch action
                switch (type) {
                    case 'category':
                        params = {
                            form_key: formKey,
                            use_massaction: true,
                            selected: selectedItems
                        };
                        break;
                    case 'product':
                        params = {
                            form_key: formKey,
                            selected: selectedItems

                        };
                        break;
                    case 'content':
                        params = {
                            form_key: formKey,
                            use_massaction: true,
                            selected: selectedItems
                        };
                        break;
                    case 'contentlist':
                        params = {
                            form_key: formKey,
                            use_massaction: true,
                            selected: selectedItems
                        };
                        break;
                    case 'cms_page':
                        params = {
                            form_key: formKey,
                            selected: selectedItems
                        };
                        break;
                    case 'cms_block':
                        params = {
                            form_key: formKey,
                            selected: selectedItems
                        };
                        break;
                    default:
                    // Do nothing
                }

                // Prepare container for displaying the chooser
                if (!(elem.find(container).length > 0)) {
                    $('<div>', {
                        class: 'chooser-container'
                    }).appendTo(elem.find('.control'));

                    // Retrieve the chooser form
                    $.ajax({
                        url: urlSource,
                        type: 'POST',
                        data: params,
                        dataType: 'html',
                        context: $('body'),
                        showLoader: true
                    }).done(function(data) {
                        elem.find(container).html(data);
                    });

                } else {
                    elem.find(container).remove();
                }
            };
            /**
             * Add selected item to the field
             */
            var applyChoice = function () {
                elem.find(container).remove();
            };

            this._on({
                // Open chooser
                'click button#open_chooser': openChooser,
                // Apply
                'click button#apply': applyChoice
            });
        },

        _initAction: function () {
            var container = this.options.container,
                elem = this.element;

            $('#save-split-button-button').click(function () {
                elem.find(container).remove();
            });
            $('#save-split-button-new-button').click(function () {
                elem.find(container).remove();
            });
            $('#save-split-button-duplicate-button').click(function () {
                elem.find(container).remove();
            });
            $('#save-split-button-close-button').click(function () {
                elem.find(container).remove();
            });
        },

    });
});
