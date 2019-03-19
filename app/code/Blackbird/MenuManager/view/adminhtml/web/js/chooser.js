/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    "jquery",
    'Magento_Ui/js/modal/alert',
    "mage/translate",
    "prototype"
], function(jQuery){
    var RelationChooserForm = new Class.create();
    RelationChooserForm.prototype = {
        initialize : function(elem){
            this.updateElement = $(elem);
            this.chooserSelectedItems = $H({});
        },

        showChooserElement: function (chooser) {
            this.chooserSelectedItems = $H({});
            if (chooser.hasClassName('no-split')) {
                this.chooserSelectedItems.set(this.updateElement.value, 1);
            } else {
                var values = this.updateElement.value.split(','), s = '';
                for (i=0; i<values.length; i++) {
                    s = values[i].strip();
                    if (s!='') {
                        this.chooserSelectedItems.set(s,1);
                    }
                }
            }
            new Ajax.Request(chooser.getAttribute('url'), {
                evalScripts: true,
                parameters: {'form_key': FORM_KEY, 'selected[]':this.chooserSelectedItems.keys() },
                onSuccess: function(transport) {
                    if (this._processSuccess(transport)) {
                        $(chooser).update(transport.responseText);
                        this.showChooserLoaded(chooser, transport);
                    }
                }.bind(this),
                onFailure: this._processFailure.bind(this)
            });
        },

        showChooserLoaded: function(chooser, transport) {
            chooser.style.display = 'block';
        },

        showChooser: function (container, event) {
            var chooser = container.up('li');
            if (!chooser) {
                return;
            }
            chooser = chooser.down('.rule-chooser');
            if (!chooser) {
                return;
            }
            this.showChooserElement(chooser);
        },

        hideChooser: function (container, event) {
            var chooser = container.up('li');
            if (!chooser) {
                return;
            }
            chooser = chooser.down('.rule-chooser');
            if (!chooser) {
                return;
            }
            chooser.style.display = 'none';
        },

        toggleChooser: function (container, event) {
            if (this.readOnly) {
                return false;
            }
            var chooser = container.up('li').down('.rule-chooser');
            if (!chooser) {
                return;
            }
            if (chooser.style.display=='block') {
                chooser.style.display = 'none';
                this.cleanChooser(container, event);
            } else {
                this.showChooserElement(chooser);
            }
        },

        cleanChooser: function (container, event) {
            var chooser = container.up('li').down('.rule-chooser');
            if (!chooser) {
                return;
            }
            chooser.innerHTML = '';
        },

        _processSuccess : function(transport) {
            if (transport.responseText.isJSON()) {
                var response = transport.responseText.evalJSON()
                if (response.error) {
                    alert(response.message);
                }
                if(response.ajaxExpired && response.ajaxRedirect) {
                    setLocation(response.ajaxRedirect);
                }
                return false;
            }
            return true;
        },

        _processFailure : function(transport) {
            location.href = BASE_URL;
        },

        chooserGridInit: function (grid) {
            grid.reloadParams = {'selected[]':this.chooserSelectedItems.keys()};
        },

        chooserGridRowInit: function (grid, row) {
            if (!grid.reloadParams) {
                grid.reloadParams = {'selected[]':this.chooserSelectedItems.keys()};
                //To hide the Checkbox that select every items in the grid
                Element.select('th', '.data-grid-actions-cell > input')[0].remove();
            }
        },

        chooserGridRowClick: function (grid, event) {
            var trElement = Event.findElement(event, 'tr');
            var isInput = Event.element(event).tagName == 'INPUT';

            if (trElement) {
                var checkbox = Element.select(trElement, 'input');

                if (checkbox[0]) {
                    var checkboxes = trElement.up(1).select('.data-grid-checkbox-cell-inner > input[type="checkbox"]:checked');
                    for (i=0; i<checkboxes.length; i++) {
                        if(checkbox[0] != checkboxes[i])
                        {
                            grid.setCheckboxChecked(checkboxes[i], false);
                        }
                    }

                    var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    grid.setCheckboxChecked(checkbox[0], checked);

                }
            }
        },

        chooserGridCheckboxCheck: function (grid, element, checked) {
            if (checked) {
                if (!element.up('th')) {
                    this.chooserSelectedItems.set(element.value, 1);
                }
            } else {
                this.chooserSelectedItems.unset(element.value);
            }

            this.chooserGridRowInit(grid);
            this.updateElement.value = this.chooserSelectedItems.keys().join(',');

            //Trigger the right event for the right chooser
            if(this.updateElement.id == 'product_id'){
                jQuery('#product_id').trigger('change');
            } else if(this.updateElement.id == 'content_id'){
                jQuery('#content_id').trigger('change');
            } else if(this.updateElement.id == 'contentlist_id'){
                jQuery('#contentlist_id').trigger('change');
            } else if(this.updateElement.id == 'cms_page_identifier'){
                jQuery('#cms_page_identifier').trigger('change');
            } else if(this.updateElement.id == 'cms_block_identifier'){
                jQuery('#cms_block_identifier').trigger('change');
            } else if(this.updateElement.id == 'category_id_chooser'){
                jQuery('#category_id_chooser').trigger('change');
            }

        }

    };

    return RelationChooserForm;
});
