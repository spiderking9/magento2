/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'ko',
    'underscore',
    'sidebar',
    'mage/translate',
    "mage/cookies"
], function (Component, customerData, $, ko, _) {
    'use strict';

    var sidebarInitialized = false,
        addToCartCalls = 0,
        openCart = false,
        miniCart;

    miniCart = $('[data-block=\'minicart\']');
    miniCart.on('dropdowndialogopen', function () {
        initSidebar();
    });

    /**
     * @return {Boolean}
     */
    function initSidebar() {

        if (miniCart.data('mageSidebar')) {
            miniCart.sidebar('update');
        }

        miniCart.trigger('contentUpdated');

        if (sidebarInitialized) {
            return false;
        }
        sidebarInitialized = true;
        miniCart.sidebar({
            'targetElement': 'div.block.block-minicart',
            'url': {
                'checkout': window.checkout.checkoutUrl,
                'shoppingCart': window.checkout.checkoutUrl+'cart',
                'update': window.checkout.updateItemQtyUrl,
                'remove': window.checkout.removeItemUrl,
                'moveToWishlist': window.checkout.moveToWishlist,
                'loginUrl': window.checkout.customerLoginUrl,
                'isRedirectRequired': window.checkout.isRedirectRequired
            },
            'button': {
                'shoppingCart': '#top-cart-btn-shopping-cart',
                'checkout': '#top-cart-btn-checkout',
                'remove': '#mini-cart a.action.delete',
                'addToWishlist': '#mini-cart a.action.wishlist',
                'close': 'button.action.close'
            },
            'showcart': {
                'parent': 'span.counter',
                'qty': 'span.counter-number',
                'label': 'span.counter-label'
            },
            'minicart': {
                'list': '#mini-cart',
                'content': '#minicart-content-wrapper',
                'qty': 'div.items-total',
                'subtotal': 'div.subtotal span.price',
                'maxItemsVisible': window.checkout.minicartMaxItemsVisible
            },
            'item': {
                'qty': ':input.cart-item-qty',
                'button': ':button.update-cart-item'
            },
            'confirmMessage': $.mage.__(
                'Are you sure you would like to remove this item from the shopping cart?'
            ),
            'confirmMessageWishlist': $.mage.__(
                'Are you sure you would like to move this item from the shopping cart to wishlist?'
            )
        });
    }

    return Component.extend({
        shoppingCartUrl: window.checkout.shoppingCartUrl,
        maxItemsToDisplay: window.checkout.maxItemsToDisplay,
        cart: {},
        summaryCookie: $.mage.cookies.get('cartSummaryCookie') || 0,
        /**
         * @override
         */
        initialize: function () {

            var self = this,
                cartData = customerData.get('cart'),
                minicart = $('[data-block="minicart"]');

            this.update(cartData());

            cartData.subscribe(function (updatedCart) {
                addToCartCalls--;
                this.isLoading(addToCartCalls > 0);
                sidebarInitialized = false;
                this.update(updatedCart);
                initSidebar();
            }, this);

            minicart.on('contentLoading', function (event) {
                addToCartCalls++;
                self.isLoading(true);
            });

            minicart.on('contentUpdated', function ()  {

                if(openCart){

                    minicart.find('[data-role="dropdownDialog"]').dropdownDialog("open");

                    minicart.on('mouseleave', function() {
                        setTimeout(function() {
                            minicart.find('[data-role="dropdownDialog"]').dropdownDialog("close");

                            minicart.off('mouseleave');

                            openCart = false;
                        }, 2000);
                    });
                }
            });

            if (cartData().website_id !== window.checkout.websiteId) {
                customerData.reload(['cart'], false);
            }

            return this._super();
        },
        isLoading: ko.observable(false),
        initSidebar: initSidebar,

        /**
         * @return {Boolean}
         */
        closeSidebar: function () {
            var minicart = $('[data-block="minicart"]');
            var showLink = $('.showcart');
            minicart.on('click', '[data-action="close"]', function (event) {
                event.stopPropagation();
                minicart.removeClass('active');
                showLink.removeClass('active');
            });

            return true;
        },

        /**
         * @param {String} productType
         * @return {*|String}
         */
        getItemRenderer: function (productType) {
            return this.itemRenderer[productType] || 'defaultRenderer';
        },

        /**
         * Update mini shopping cart content.
         *
         * @param {Object} updatedCart
         * @returns void
         */
        update: function (updatedCart) {

            var cookieSummaryCount = parseInt(this.summaryCookie, 10);

            openCart = (updatedCart.summary_count !== cookieSummaryCount) && (updatedCart.summary_count > 0);

            if(updatedCart.summary_count === 0) {
                $.mage.cookies.clear('cartSummaryCookie');
            }

            _.each(updatedCart, function (value, key) {
                if (!this.cart.hasOwnProperty(key)) {
                    this.cart[key] = ko.observable();
                }
                this.cart[key](value);
            }, this);

            $.mage.cookies.set('cartSummaryCookie', updatedCart.summary_count);

        },

        /**
         * Get cart param by name.
         *
         * @param {String} name
         * @returns {*}
         */
        getCartParam: function (name) {
            if (!_.isUndefined(name)) {
                if (!this.cart.hasOwnProperty(name)) {
                    this.cart[name] = ko.observable();
                }
            }

            return this.cart[name]();
        },

        /**
         * Returns array of cart items, limited by 'maxItemsToDisplay' setting.
         *
         * @returns []
         */
        getCartItems: function () {
            var items = this.getCartParam('items') || [];
            items = items.slice(parseInt(-this.maxItemsToDisplay, 10));

            return items;
        },

        /**
         * Returns count of cart line items.
         *
         * @returns {Number}
         */
        getCartLineItemsCount: function () {
            var items = this.getCartParam('items') || [];

            return parseInt(items.length, 10);
        }
    });
});
