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
    'Magento_Ui/js/modal/modal'
], function ($) {
    'use strict';

    $.widget('mage.contenttype', {

        _create: function () {
            window.isExport = false;
            this._initEditAction();

            $('#export-button').on('click', function () {
                window.isExport = true;

                $('#contentmanager_contenttype_edit_tabs_custom_fields').trigger('click');

                if (window.isFieldSet)
                    $("#edit_form").submit();
            });
        },

        _initEditAction: function () {

            var syncContentTypeTitle = function (event) {
                var value = $(event.target).val().trim(),
                    inputIdentifier = $('#contenttype_identifier');

                value = value.trim().toLowerCase().replace(/[^a-z0-9]+/g, '_');
                inputIdentifier.val(value);
            };

            this._on({
                // Sync content type title
                'change #contenttype_title': syncContentTypeTitle,
                'keyup #contenttype_title': syncContentTypeTitle,
                'paste #contenttype_title': syncContentTypeTitle
            });
        }
    });
});

