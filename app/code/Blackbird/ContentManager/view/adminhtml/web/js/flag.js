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

    $.widget('mage.flag', {

        _create: function () {
            if (!this.options.urlbase) {
                this.options.urlbase = '/';
            }

            this._initEditAction();
            this._initPreview();
        },

        _initEditAction: function () {
            var urlbase = this.options.urlbase;

            var syncImageFlag = function (event) {
                var elem = $(event.srcElement);
                var src = urlbase + '/' + elem.val();

                $('img#' + elem.attr('id')).attr('src', src);
            };

            this._on({
                // Sync image flag
                'change .flag_preview': syncImageFlag,
                'keyup .flag_preview': syncImageFlag,
            });
        },

        _initPreview: function () {
            var urlbase = this.options.urlbase;

            $('.flag_preview').each(function () {
                var src = urlbase + '/' + $(this).val();

                $('img#' + $(this).attr('id')).attr('src', src);
            });
        },
    });
});
