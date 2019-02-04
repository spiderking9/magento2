/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    'use strict';

    return {
        modalWindow: null,

        /**
         * Create popUp window for provided element
         *
         * @param {HTMLElement} element
         */
        createPopUp: function (element) {
            var trigger = '';

            if($('.panel.header .authorization-link').hasClass('no-logged')) {
                trigger = '.panel.header .authorization-link';

                $(document).on('click', '.panel.header .authorization-link.no-logged', function(e){
                    e.preventDefault();
                });
            }


            var options = {
                'type': 'slide',
                'modalClass': 'popup-authentication-quicklogin',
                'focus': '[name=username]',
                'clickableOverlay': true,
                'closeOnEscape': true,
                'responsive': true,
                'innerScroll': true,
                'trigger': trigger,
                'buttons': []
            };

            this.modalWindow = element;
            modal(options, $(this.modalWindow));
        },

        /** Show login popup window */
        showModal: function () {
            $(this.modalWindow).modal('openModal');
        }
    };
});
