/**
 * @todo refactor with content.js, use the native and generic chooser.js
 */
define([
    "jquery",
    'Magento_Ui/js/modal/alert',
    "mage/translate",
    "prototype"
], function (jQuery) {

    var RelationChooserForm = new Class.create();

    RelationChooserForm.prototype = {
        initialize: function (elem) {
            this.updateElement = $(elem);
            this.chooserSelectedItems = $H({});
            //todo
            var values = this.updateElement.value.split(','), s = '';
            for (i = 0; i < values.length; i++) {
                s = values[i].strip();
                if (s != '') {
                    this.chooserSelectedItems.set(s, 1);
                }
            }
        },

        showChooserElement: function (chooser) {
            if (chooser.hasClassName('no-split')) {
                this.chooserSelectedItems.set(this.updateElement.value, 1);
            } else {
                var values = this.updateElement.value.split(','), s = '';
                for (i = 0; i < values.length; i++) {
                    s = values[i].strip();
                    if (s != '') {
                        this.chooserSelectedItems.set(s, 1);
                    }
                }
            }
            new Ajax.Request(chooser.getAttribute('url'), {
                evalScripts: true,
                parameters: {'form_key': FORM_KEY, 'selected[]': this.chooserSelectedItems.keys()},
                onSuccess: function (transport) {
                    if (this._processSuccess(transport)) {
                        $(chooser).update(transport.responseText);
                        this.showChooserLoaded(chooser, transport);
                        jQuery(chooser).trigger('contentUpdated');
                    }
                }.bind(this),
                onFailure: this._processFailure.bind(this)
            });
        },

        showChooserLoaded: function (chooser, transport) {
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
            if (chooser.style.display == 'block') {
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

        _processSuccess: function (transport) {
            if (transport.responseText.isJSON()) {
                var response = transport.responseText.evalJSON();
                if (response.error) {
                    alert(response.message);
                }
                if (response.ajaxExpired && response.ajaxRedirect) {
                    setLocation(response.ajaxRedirect);
                }
                return false;
            }
            return true;
        },

        _processFailure: function (transport) {
            location.href = BASE_URL;
        },

        chooserGridInit: function (grid) {
            grid.reloadParams = {'selected[]': this.chooserSelectedItems.keys()};
        },

        chooserGridRowInit: function (grid, row) {
            if (!grid.reloadParams) {
                grid.reloadParams = {'selected[]': this.chooserSelectedItems.keys()};
            }
        },

        chooserGridRowClick: function (grid, event) {
            var trElement = Event.findElement(event, 'tr');
            var isInput = Event.element(event).tagName == 'INPUT';
            if (trElement) {
                var checkbox = Element.select(trElement, 'input');
                if (checkbox[0]) {
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
        }
    };

    return RelationChooserForm;
});
