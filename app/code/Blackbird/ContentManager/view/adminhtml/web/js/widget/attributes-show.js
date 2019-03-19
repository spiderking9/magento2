/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category        Blackbird
 * @package         Blackbird_ContentManager
 * @copyright       Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author          Blackbird Team
 * @license         https://www.advancedcontentmanager.com/license/
 */
/*jshint browser:true*/
/*global alert:true*/
define([
    'jquery',
    'mage/template',
    'Magento_Ui/js/modal/confirm',
    'jquery/ui',
    'useDefault',
    'collapsable',
    'mage/translate',
    'mage/backend/validation',
    'Magento_Ui/js/modal/modal'
], function ($, mageTemplate, confirm) {
    'use strict';

    $.widget('mage.attributesShow', {
        options: {
            nextId: 1
        },

        _create: function () {
            var self = this;

            // HTML Template of fieldset
            this.baseTmpl = '#attribute-to-show-field-base-template';

            // Add button
            this.addBtn = $('#add_attribute_to_show');
            this.addBtn.click(function () {
                self.addAttributeToShow();
            });

            this._initDragAndDropLayout();
        },

        /**
         * Handle drag and drop
         */
        _initDragAndDropLayout: function () {
            var widget = this;
            // Drag and drop system
            $('#attributes_to_show_container_top').sortable({
                items: '> .column-draggable',
                handle: '> .column-handler',
                connectWith: '.column-dropable',
                placeholder: 'sortable-placeholder',
                scroll: true,
                start: function (event, ui) {
                    $(event.target).addClass('currently-dragging');
                },
                stop: function (event, ui) {
                    $(event.target).removeClass('currently-dragging');

                    // Update Drag & Drop and positions
                    widget._updateFieldPositions();
                }
            });
        },

        /**
         * Update custom fieldset position
         */
        _updateFieldPositions: function () {

        },

        /**
         * Set loaded values from previous widget
         */
        initialize: function (value) {
            var maxAttributeIdAdded = 0;
            value = value.replace(/`/g, "\"");
            value = value.replace(/\\"/g, "'");

            try {
                $('.control-value', $('#contenttype_fieldsets_container_top').parent()).remove();

                var valueJson = JSON.parse(value);

                for (var i = 0; i < valueJson.length; i++) {
                    var attribute = valueJson[i];

                    if (attribute.name.indexOf('[id]') !== -1) {
                        this.options.nextId = attribute.value;
                        this.addAttributeToShow();

                        if (attribute.value > maxAttributeIdAdded) {
                            maxAttributeIdAdded = attribute.value;
                        }
                    }

                    var elem = $('[name="' + attribute.name + '"]');//

                    if ((elem.is('input[type="checkbox"]') || elem.is('input[type="radio"]')) && elem.val() == attribute.value) {
                        elem.attr('checked', 'checked');
                    } else {
                        elem.val(attribute.value);
                    }
                }

                this.options.nextId = maxAttributeIdAdded + 1;

                $('#contenttype_fieldsets_container_top .admin__collapsible-title').trigger('click');
                $('.layout_field_custom_field_id').trigger('change');

            } catch (e) {
                // Silence Is Golden
            }
        },

        /**
         * Encode attribute to show configurations
         */
        encodeAttributeToShow: function () {
            var encodedValue = $('#attributes_to_show_container_top input, #attributes_to_show_container_top select').serializeArray();
            encodedValue = JSON.stringify(encodedValue);
            encodedValue = encodedValue.replace(/'/g, "\\\'");
            encodedValue = encodedValue.replace(/"/g, "`");

            $('#attributes_to_show_encoded').val(encodedValue);
        },

        /**
         * Add attribute to show
         */
        addAttributeToShow: function (event) {
            var data = {};
            data.id = this.options.nextId;
            data.prefix = 'as_';
            this.options.nextId++;

            var tmpl = mageTemplate(this.baseTmpl, {data: data});

            $('#attributes_to_show_container_top').append(tmpl);

            //rebind delete events
            this.bindDeleteAttributeToShow();

            //rebind refresh field title
            this.bindRefreshFieldTitle();

            //refresh drag and drop
            this._initDragAndDropLayout();
        },

        /**
         * Handle deletion
         */
        bindDeleteAttributeToShow: function () {
            $('#attributes_to_show_container_top .action-delete').each(function () {
                $(this).unbind('click');
                $(this).bind('click', function () {
                    $(this).parents('.grid-layout-item').remove();
                });
            });
        },

        /**
         * Update title in main bar when changing option
         */
        bindRefreshFieldTitle: function () {
            $('.layout_field_custom_field_id').each(function () {
                $(this).unbind('change');
                $(this).bind('change', function () {
                    var new_title = $('option[value=' + $(this).val() + ']', $(this)).html();
                    $('.grid-layout-item-title .field-title', $(this).parents('.grid-layout-item')).html(new_title);
                });
            });
        }
    });

});
