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

    $.widget('mage.contentlist', {

        _create: function () {
            this._initEditAction();

            $('#contentlist_pager').trigger('change');
        },

        _initEditAction: function () {
            this._on({
                // Toggle content list pager options form
                'change select#contentlist_pager': function (event) {
                    var value = $(event.target).val().trim(),
                        pagerPosition = $('#contentlist_pager_position'),
                        limitPager = $('#contentlist_limit_per_page');

                    if (value == 1) {
                        pagerPosition.removeClass('ignore-validate');
                        limitPager.removeClass('ignore-validate');
                        pagerPosition.closest('.field').show();
                        limitPager.closest('.field').show();
                    } else {
                        pagerPosition.removeClass('ignore-validate');
                        limitPager.removeClass('ignore-validate');
                        pagerPosition.closest('.field').hide();
                        limitPager.closest('.field').hide();
                    }
                }
            });
        },

    });
});
