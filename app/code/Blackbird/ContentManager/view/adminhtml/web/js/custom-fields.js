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

    $.widget('mage.customFields', {
        fieldsets: {
            fields: {
                itemCount: {},
                rows: {
                    itemCount: {},
                },
                assocIds: {},
            },
            itemCount: 1,
            assocIds: {},
        },

        _create: function () {
            // HTML Template of fieldset
            this.fieldsetBaseTmpl = mageTemplate('#custom-fieldset-base-template');
            this.fieldBaseTmpl = mageTemplate('#custom-field-base-template');
            this.rowBaseTmpl = mageTemplate('#custom-field-select-type-row-template');

            this._initSearchOptions();
            this._initFieldsetBoxes();
            this._addValidation();
        },

        _addValidation: function () {
            // Init validator methods (let it in first)
            var identifiers = [];
            $.validator.addMethod(
                'init-contenttype-custom-fields-validator', function (value) {
                    identifiers = [];
                    return true;
                }, $.mage.__('Error.')
            );

            $.validator.addMethod(
                'required-custom-field-identifier', function (value) {
                    if ($.inArray(value, identifiers) !== -1) {
                        return false;
                    } else {
                        identifiers.push(value);
                        return true;
                    }
                }, $.mage.__('This identifier is already used for another field or is a system identifier.')
            );

            $.validator.addMethod(
                'required-custom-field-select', function (value) {
                    return (value !== '');
                }, $.mage.__('Select type of field.')
            );
        },

        _initSearchOptions: function () {
            $('select#contenttype_search_enabled').on('change', function (event) {
                var element = $(event.target);

                if (element.val() == 1) {
                    $('#contenttype_custom_fields_fieldset .fieldset-alt-search').removeClass('ignore-validate').show();
                } else {
                    $('#contenttype_custom_fields_fieldset .fieldset-alt-search').addClass('ignore-validate').hide();
                }
            });
        },

        _initFieldsetBoxes: function () {
            this.element.sortable({
                axis: 'y',
                handle: '[data-role=draggable-handle]',
                items: '#contenttype_fieldsets_container_top > div',
                update: this._updateFieldsetBoxPositions,
            });

            var syncTitle = function (event, id, title) {
                var currentValue = $(event.target).val().trim(),
                    fieldsetBoxTitle = $(id + ' > .admin__collapsible-title > span', $(event.target).closest('.fieldset-wrapper'));

                fieldsetBoxTitle.text(currentValue === '' ? title : currentValue);
            };

            var syncIdent = function (event, title) {
                var currentValue = $(event.target).val().trim(),
                    inputIdentifier = $('.field-field-identifier > .control > input', $(event.target).closest('.fieldset-wrapper')),
                    ctIdentifier = $('#contenttype_identifier').val(),
                    value = ctIdentifier.substr(0, 12) + '_';

                value += currentValue === '' ? title : currentValue;
                value = value.trim().toLowerCase();
                value = value.replace(/[^a-z0-9]+/g, '_');

                inputIdentifier.val(value);
            };

            var syncFieldsetTitle = function (event) {
                var title = $.mage.__('New Fieldset');
                syncTitle(event, '#custom-fieldset-title', title);
            };

            var syncFieldTitle = function (event) {
                var title = $.mage.__('New Field'),
                    field_id = $(event.target).closest('.fieldset-alt').find('[id^="contenttype_fieldset_"][id$="_field_id"]').val();
                syncTitle(event, '#custom-field-title', title);
                if (field_id == '0') {
                    syncIdent(event, title);
                }
            };

            this._on({
                /**
                 * Remove custom fieldset, field or field row for 'select' type of custom field
                 */
                'click button[id^=contenttype_fieldset_][id$=_delete]': function (event) {
                    var element = $(event.target).closest('#contenttype_fieldsets_container_top > div.fieldset-wrapper,div.fieldset-wrapper,tr');

                    confirm({
                        title: '',
                        content: $.mage.__('Are you sure you want to delete this item?'),
                        actions: {
                            confirm: function () {
                                $('#contenttype_' + element.attr('id').replace('contenttype_', '') + '_is_delete').val(1);
                                element.addClass('ignore-validate').hide();
                            }
                        },
                        always: function () {
                            this.refreshSortableElements();
                        }
                    });
                },

                /**
                 * Add new custom fieldset
                 */
                'click #add_new_custom_fieldset': function (event) {
                    this.addFieldset(event);
                },

                /**
                 * Add new custom field
                 */
                'click button[id^=contenttype_fieldset_][id$=_add_new_custom_field]': function (event) {
                    this.addField(event);
                },

                /**
                 * Add new field row for 'select' type of custom field
                 */
                'click button[id^=contenttype_fieldset_][id$=_add_select_row]': function (event) {
                    this.addSelection(event);
                },

                /**
                 * Change custom field type
                 */
                'change select[id^=contenttype_fieldset_][id$=_type]': function (event, data) {
                    data = data || {};
                    var widget = this,
                        currentElement = $(event.target),
                        parentId = '#' + currentElement.closest('.fieldset-alt').attr('id'),
                        group = currentElement.find('[value="' + currentElement.val() + '"]')
                            .closest('optgroup').attr('data-optgroup-name'),
                        type = currentElement.val(),
                        previousGroup = $(parentId + '_previous_group').val(),
                        previousType = $(parentId + '_previous_type').val(),
                        previousTypeBlock = $(parentId + '_previous_type'),
                        previousBlock = $(parentId + '_type_' + previousGroup),
                        tmpl;

                    if (typeof group !== 'undefined') {
                        group = group.toLowerCase();
                    }

                    if (previousGroup !== group || (group !== 'select' && group !== 'date' && previousType !== type)) {
                        // Delete previous type
                        if (previousBlock.length) {
                            previousBlock.remove();
                        }
                        $(parentId + '_previous_group').val(group);

                        if (typeof group === 'undefined') {
                            return;
                        }

                        if ($.isEmptyObject(data)) {
                            data.id = currentElement.closest('#contenttype_fieldsets_container_top > div')
                                .find('[name^="contenttype[fieldsets]"][name$="[id]"]').val();
                            data.field_id = $(parentId + '_id').val();
                        }
                        data.group = group;
                        data.type = type;

                        // Default values
                        if (data.type == 'image' && !data.file_extension) {
                            data.file_extension = 'png,jpg,jpeg,gif';
                        } else if (data.type == 'file' && !data.file_extension) {
                            data.file_extension = 'doc,docx,pdf,odt,xls,xlsx,csv';
                        }

                        tmpl = widget.element.find('#custom-field-' + group + '-type-template').html();
                        tmpl = mageTemplate(tmpl, {
                            data: data
                        });

                        $(tmpl).appendTo($(parentId).find('[id$="-content"]'));
                        previousTypeBlock.val(type);

                        //Add selections
                        if (data.optionValues) {
                            data.optionValues.each(function (value) {
                                widget.addSelection(value);
                            });
                        }
                    }
                },

                // Sync Fieldset Title
                'change .field-fieldset-title > .control > input[id$="_title"]': syncFieldsetTitle,
                'keyup .field-fieldset-title > .control > input[id$="_title"]': syncFieldsetTitle,
                'paste .field-fieldset-title > .control > input[id$="_title"]': syncFieldsetTitle,
                // Sync Field Title
                'change .field-field-title > .control > input[id$="_title"]': syncFieldTitle,
                'keyup .field-field-title > .control > input[id$="_title"]': syncFieldTitle,
                'paste .field-field-title > .control > input[id$="_title"]': syncFieldTitle
            });
        },

        /**
         * Able to sort custom fields
         */
        _initSortableFields: function () {
            var fieldsBoxes = this.element.find('[id^=contenttype_fields_container_top_]');

            fieldsBoxes.sortable({
                connectWith: '[id^=contenttype_fields_container_top_]',
                axis: 'y',
                handle: '[data-role=draggable-handle]',
                helper: function (event, ui) {
                    ui.children().each(function () {
                        $(this).width($(this).width());
                    });

                    return ui;
                },
            });
            fieldsBoxes.on("sortupdate", this, this._updateFieldBoxPositions);
        },

        /**
         * Init sortable selections 'select' for custom field
         */
        _initSortableSelections: function () {
            this.element.find('[id^=contenttype_fieldset_][id$=_type_select] tbody').sortable({
                axis: 'y',
                handle: '[data-role=draggable-handle]',
                helper: function (event, ui) {
                    ui.children().each(function () {
                        $(this).width($(this).width());
                    });

                    return ui;
                },
                update: this._updateSelectionsPositions,
            });
        },

        /**
         * Update custom fieldset position
         */
        _updateFieldsetBoxPositions: function () {
            $(this).find('div[id^=contenttype_fieldset_] .fieldset-alt > [name^="contenttype[fieldsets]"][name$="[sort_order]"]').each(function (index) {
                $(this).val(index);
            });
        },

        /**
         * Update custom field position
         */
        _updateFieldBoxPositions: function (event, ui) {
            if (event) {
                var element = $(event.srcElement),
                    widget = event.data,
                    fieldsetUid = element.closest('fieldset.fieldset').find('.fieldset-alt [id^=contenttype_fieldset_][id$=_fieldset_id]').val(),
                    prevFieldsetUid = $(this).closest('fieldset.fieldset').find('.fieldset-alt [id^=contenttype_fieldset_][id$=_fieldset_id]').val(),
                    fieldsetId = element.closest('fieldset.fieldset').find('.fieldset-alt [name$="[id]"]').val(),
                    prevFieldsetId = $(this).closest('fieldset.fieldset').find('.fieldset-alt [name$="[id]"]').val(),
                    field = element.closest('[id^=contenttype_fieldset_]'),
                    prevFieldId = field.find('.fieldset-alt [name$="[id]"]').val(),
                    fieldId = widget.fieldsets.fields.itemCount[fieldsetId],
                    fieldUid = field.find('.fieldset-alt [name$="[option_id]"]').val();

                if (!fieldId) {
                    fieldId = 1;
                }

                // If the field has been moved to an another fieldset
                if (prevFieldsetId != fieldsetId) {
                    console.log('passé');
                    var patternName = {};
                    var patternAttr = {};
                    // Init params
                    patternName['[fieldsets][' + prevFieldsetId + ']'] = '[fieldsets][' + fieldsetId + ']';
                    patternName['[fields][' + prevFieldId + ']'] = '[fields][' + fieldId + ']';
                    patternAttr['fieldset_' + prevFieldsetId] = 'fieldset_' + fieldsetId;
                    patternAttr['field_' + prevFieldId] = 'field_' + fieldId;
                    patternAttr['fieldset-' + prevFieldsetId] = 'fieldset-' + fieldsetId;
                    patternAttr['field-' + prevFieldId] = 'field-' + fieldId;

                    // For each elements in this field
                    field.find('*').each(function (index) {
                        var attrName = $(this).attr('name'),
                            attrs = {},
                            currentElement = $(this);

                        // Init attr to update
                        attrs['class'] = $(this).attr('class');
                        attrs['id'] = $(this).attr('id');
                        attrs['for'] = $(this).attr('for');
                        attrs['data-target'] = $(this).data('target');

                        // Update the attr
                        $.each(attrs, function (index, value) {
                            if (value) {
                                $.each(patternAttr, function (pattern, replacement) {
                                    value = value.replace(pattern, replacement);
                                });
                                currentElement.attr(index, value);
                            }
                        });

                        // Special case for attr name
                        if (attrName) {
                            $.each(patternName, function (index, value) {
                                attrName = attrName.replace(index, value);
                            });
                            $(this).attr('name', attrName);
                        }
                    });

                    // Only if this field already exist
                    if (fieldUid) {
                        widget.fieldsets.fields.assocIds[fieldUid] = fieldId;
                    }

                    // Increments temp id for this fieldset field count
                    widget.fieldsets.fields.itemCount[fieldsetId] = parseInt(widget.fieldsets.fields.itemCount[fieldsetId], 10) + 1;
                }
            }

            // Update sort order
            $('div[id^=contenttype_fields_container_top_] .fieldset-alt > [name^="contenttype[fieldsets]"][name$="[sort_order]"]').each(function (index) {
                $(this).val(index);
            });
        },

        /**
         * Update selections positions for 'select' type of custom field
         */
        _updateSelectionsPositions: function () {
            $(this).find('tr:not(.ignore-validate) [name$="[sort_order]"]').each(function (index) {
                $(this).val(index);
            });
        },

        /**
         * Add custom fieldset
         */
        addFieldset: function (event) {
            var data = {},
                element = event.target || event.srcElement || event.currentTarget,
                baseTmpl;

            if (typeof element !== 'undefined') {
                data.id = this.fieldsets.itemCount;
                data.fieldset_id = 0;
            } else {
                data = event;
                data.id = this.fieldsets.itemCount;
                this.fieldsets.assocIds[data.fieldset_id] = data.id;
            }

            baseTmpl = this.fieldsetBaseTmpl({
                data: data
            });

            $(baseTmpl)
                .appendTo(this.element.find('#contenttype_fieldsets_container_top'))
                .find('.collapse').collapsable();

            if (data.fieldset_id != 0) {
                // Set collapsable item
                $('#contenttype_fieldset_' + data.id).find('.admin__collapsible-title').click();
            }

            this.refreshSortableElements();
            this.fieldsets.itemCount++;
            $('#contenttype_fieldset_' + data.id + '_title').trigger('change');
        },

        /**
         * Add custom field
         */
        addField: function (event) {
            var data = {},
                element = event.target || event.srcElement || event.currentTarget,
                baseTmpl;

            if (typeof element !== 'undefined') {
                data.id = $(element).closest('#contenttype_fieldsets_container_top > div')
                    .find('[name^="contenttype[fieldsets]"][name$="[id]"]').val();
                data.field_type_id = -1;
                data.field_uid = 0;
            } else {
                data = event;
                data.id = this.fieldsets.assocIds[data.fieldset_id];
            }

            if (!this.fieldsets.fields.itemCount[data.id]) {
                this.fieldsets.fields.itemCount[data.id] = 1;
            }
            data.field_id = this.fieldsets.fields.itemCount[data.id];

            // Only if this field already exist
            if (data.field_uid) {
                this.fieldsets.fields.assocIds[data.field_uid] = data.field_id;
            }

            baseTmpl = this.fieldBaseTmpl({
                data: data
            });

            $(baseTmpl)
                .appendTo(this.element.find('#contenttype_fields_container_top_' + data.id))
                .find('.collapse').collapsable();

            // Set selected values for <select> and <input type="checkbox">
            if (data.field_uid != 0) {
                // Set collapsable item
                $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id).find('.admin__collapsible-title').click();

                // Set selected type value if set
                $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_type').val(data.type).trigger('change', data);

                // Disable identifier and type
                $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_identifier').attr('readonly', 'readonly');
                $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_type').attr('disabled', 'disabled');

                // Set values for is require select
                $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_is_require').val(data.is_require);

                // Set values for show in grid select
                $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_show_in_grid').val(data.show_in_grid);

                // Set values for wysiwyg
                $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_wysiwyg_editor').val(data.wysiwyg_editor);

                // Set values for is searchable
                $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_is_searchable').val(data.is_searchable);

                // Set value for content field
                $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_content_type').val(data.content_type);

                // Set value for attribute field
                $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_attribute').val(data.attribute);
                $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_attribute_max_characters').val(data.max_characters);

                // Set values for search weight
                $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_search_weight').val(data.search_weight);

                // Set wysiwyg editor check
                if (data.wysiwyg_editor == 1) {
                    $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_wysiwyg_editor_').attr("checked", "checked");
                }

                // Set crop check
                if (data.crop == 1) {
                    $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_crop').attr("checked", "checked");
                }

                // Set keep aspect ratio check
                if (data.keep_aspect_ratio == 1) {
                    $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_keep_aspect_ratio').attr("checked", "checked");
                }

                // Set img alt check
                if (data.img_alt == 1) {
                    $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_img_alt').attr("checked", "checked");
                }

                // Set img alt check
                if (data.img_title == 1) {
                    $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_img_title').attr("checked", "checked");
                }

                // Set img url check
                if (data.img_url == 1) {
                    $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_img_url').attr("checked", "checked");
                }
            }

            this.refreshSortableElements();
            this.fieldsets.fields.itemCount[data.id] = parseInt(this.fieldsets.fields.itemCount[data.id], 10) + 1;
            $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_title').trigger('change');
            // Hide or show the search options
            $('select#contenttype_search_enabled').trigger('change');
        },

        /**
         * Add selection value for 'select' type of custom field
         */
        addSelection: function (event) {
            var data = {},
                element = event.target || event.srcElement || event.currentTarget,
                rowTmpl;

            if (typeof element !== 'undefined') {
                data.id = $(element).closest('#contenttype_fieldsets_container_top > div')
                    .find('[name^="contenttype[fieldsets]"][name$="[id]"]').val();
                data.field_id = $(element).closest('.fieldset-wrapper')
                    .find('[name^="contenttype[fieldsets][' + data.id + '][fields]"][name$="[id]"]').val();
                data.group = $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_previous_group').val();
                data.field_type_id = -1;
                data.select_uid = -1
            } else {
                data = event;
                data.id = this.fieldsets.assocIds[data.fieldset_id];
                data.field_id = this.fieldsets.fields.assocIds[data.field_uid];
                data.group = $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_previous_group').val();
            }

            // Init of data.select_id
            if (!this.fieldsets.fields.rows.itemCount[data.id]) {
                this.fieldsets.fields.rows.itemCount[data.id] = {};
            }
            if (!this.fieldsets.fields.rows.itemCount[data.id][data.field_id]) {
                this.fieldsets.fields.rows.itemCount[data.id][data.field_id] = 1;
            }
            data.select_id = this.fieldsets.fields.rows.itemCount[data.id][data.field_id];

            rowTmpl = this.rowBaseTmpl({
                data: data
            });

            $(rowTmpl).appendTo($('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_type_' + data.group + '_row'));

            if (data.select_uid !== -1) {
                $('#contenttype_fieldset_' + data.id + '_field_' + data.field_id + '_select_' + data.select_id + '_default').val(data.default_val);
            }

            this.refreshSortableElements();
            this.fieldsets.fields.rows.itemCount[data.id][data.field_id] = parseInt(this.fieldsets.fields.rows.itemCount[data.id][data.field_id], 10) + 1;
        },

        refreshSortableElements: function () {
            this.element.sortable('refresh');
            this._updateFieldsetBoxPositions.apply(this.element);

            var fieldsBoxes = this.element.find('[id^=contenttype_fields_container_top_]'),
                selectionBoxes = this.element.find('[id^=contenttype_fieldset_][id$=_type_select] tbody');

            // Initialize or update sortable elements
            if (fieldsBoxes) {
                try {
                    fieldsBoxes.sortable('refresh');
                } catch (e) {
                    this._initSortableFields();
                }
                this._updateFieldBoxPositions.apply(this.element);

                if (selectionBoxes) {
                    try {
                        selectionBoxes.sortable('refresh');
                    } catch (e) {
                        this._initSortableSelections();
                    }
                }
                this._updateSelectionsPositions.apply(this.element);
            }
        }

    });

});
