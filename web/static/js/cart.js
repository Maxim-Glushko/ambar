let Cart = {
    productItem: '.one-product',
    productInput: '.product-input',
    plusBtn: '.plus',
    minusBtn: '.minus',
    addBtn: '.add',
    btns: '.product-buttons',
    already: '.product-already',
    unavailable: '.product-unavailable',
    forCart: '#for-cart',
    cartProduct: '.cart-product',
    cartProductDel: '.cart-product-del',
    toOrder: '#to-order',
    forOrder: '.for-order',
    orderForm: '#order-form',

    currentProductId: 0,

    init: function() {
        let self = this;
        self.bindEvents();
    },
    bindEvents: function () {
        let self = this;
        $(document)
            .on('click', self.productItem + ' ' + self.plusBtn, function () {
                self.plusProduct($(this));
            })
            .on('click', self.productItem + ' ' + self.minusBtn, function () {
                self.minusProduct($(this));
            })
            .on('input', self.productItem + ' ' + self.productInput, function() {
                self.checkInput($(this));
            })
            .on('click', self.productItem + ' ' + self.addBtn, function () {
                self.addProduct($(this));
            })
            .on('click', self.cartProduct + ' ' + self.cartProductDel, function () {
                self.delProduct($(this));
            })
            .on('click', self.cartProduct + ' ' + self.plusBtn, function () {
                self.cartPlusMinus($(this), '+');
            })
            .on('click', self.cartProduct + ' ' + self.minusBtn, function () {
                self.cartPlusMinus($(this), '-');
            })
            .on('input', self.cartProduct + ' ' + self.productInput, function () {
                self.checkInput($(this));
            })
            .on('blur', self.cartProduct + ' ' + self.productInput, function () {
                self.cartSet($(this));
            })
            .on('click', self.toOrder, function () {
                self.showOrderForm();
            })
            .on('submit', self.orderForm, function (e) {
                e.preventDefault();
                self.orderSubmit($(this));
                return false;
            });
    },
    checkInput: function($elem) {
        let self = this;
        let val = parseInt($elem.val());
        if (isNaN(val) || (val < 1)) val = 1;
        $elem.val(val);
        return val;
    },
    plusProduct: function($elem) {
        let self = this;
        let input = $elem.closest(self.productItem).find(self.productInput);
        let result = self.checkInput(input) + 1;
        if (result > input.attr('max')) {
            result = input.attr('max');
        }
        input.val(result);
    },
    minusProduct: function($elem) {
        let self = this;
        let input = $elem.closest(self.productItem).find(self.productInput);
        let val = self.checkInput(input);
        input.val((val > 1) ? (val - 1) : 1);
    },
    addProduct: function($elem) {
        let self = this;
        let papa = $elem.closest(self.productItem);
        let input = papa.find(self.productInput);
        let data = {
            product_id: papa.data('product_id'),
            quantity: self.checkInput(input)
        };
        let url = papa.data('url');
        self.currentProductId = papa.data('product_id');
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: data,
            beforeSend: function() {
                $elem.attr('disabled', 'disabled');
            },
            success: function (data) {
                $elem.attr('disabled', false);
                self.checkProductsAndCart(data);
            },
            error: function(request) {
                Main.message('', 'error');
            }
        });
    },
    delProduct: function($elem) {
        let self = this;
        let papa = $elem.closest(self.cartProduct);
        let data = {
            product_id: papa.data('product_id')
        };
        let url = $elem.data('url');
        self.currentProductId = papa.data('product_id');
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: data,
            beforeSend: function() {},
            success: function (data) {
                self.checkProductsAndCart(data);
            },
            error: function(request) {
                Main.message('', 'error');
            }
        });
    },
    cartPlusMinus: function($elem, plusMinus) {
        let self = this;
        let papa = $elem.closest(self.cartProduct);
        if (plusMinus == '+') {
            let input = papa.find('input');
            if (input.attr('max') < input.val() + 1) {
                return;
            }
        }
        let url = $elem.data('url');
        let data = {
            product_id: papa.data('product_id'),
            plus_minus: plusMinus
        };
        self.currentProductId = papa.data('product_id');
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: data,
            beforeSend: function() {},
            success: function (data) {
                self.checkProductsAndCart(data);
            },
            error: function(request) {
                Main.message('', 'error');
            }
        });
    },
    cartSet: function($elem) {
        let self = this;
        let papa = $elem.closest(self.cartProduct);
        let url = $elem.data('url');
        let data = {
            product_id: papa.data('product_id'),
            quantity: $elem.val()
        };
        self.currentProductId = papa.data('product_id');
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: data,
            beforeSend: function() {},
            success: function (data) {
                self.checkProductsAndCart(data);
            },
            error: function(request) {
                Main.message('', 'error');
            }
        });
    },
    checkProductsAndCart: function(data) {
        let self = this;
        if (typeof data.error !== 'undefined') {
            Main.message(data.error, 'error');
        } else {
            if (typeof data.cart !== 'undefined') {
                $(self.forCart).empty().append(data.cart);
            }
            if ((typeof data.productIds !== 'undefined') && $.isArray(data.productIds)) {
                let productId;
                let unProductIds = [];
                if ((typeof data.unProductIds !== 'undefined') && $.isArray(data.unProductIds)) {
                    unProductIds = data.unProductIds;
                }
                $(self.productItem).each(function(i, el) {
                    productId = $(el).data('product_id');
                    if ($.inArray(productId, data.productIds) > -1) {  // если такой товар уже в корзине
                        $(el).find(self.btns).css({'display': 'none'});
                        $(el).find(self.unavailable).css({'display': 'none'});
                        $(el).find(self.already).css({'display': 'block'});
                    } else { // если такого товара нет в корзине
                        if (self.currentProductId == productId) {
                            if ($.inArray(productId, unProductIds) != -1) { // если наличие за время просмотра страницы стало 0
                                $(el).find(self.btns).css({'display': 'none'});
                                $(el).find(self.unavailable).css({'display': 'block'});
                                $(el).find(self.already).css({'display': 'none'});
                                $(el).find('.prices .status').text(data.statusUnavailable);
                            } else {
                                $(el).find(self.btns).css({'display': 'block'});
                                $(el).find(self.unavailable).css({'display': 'none'});
                                $(el).find(self.already).css({'display': 'none'});
                            }
                        }
                    }
                });
            }
            if (typeof data.form !== 'undefined') {
                $(self.forOrder).empty().append(data.form);
                $('.for-to-order-btn, .min-order-sum-text').css({'display': 'none'});
                $(self.orderForm + ' button').attr('disabled', false);
            }
            if (typeof data.message !== 'undefined') {
                Main.message(data.message);
            }
        }
    },
    showOrderForm: function() {
        let self = this;
        $.ajax({
            url: $(self.forOrder).data('url'),
            type: 'post',
            dataType: 'json',
            data: {},
            beforeSend: function() {
                $('.for-to-order-btn').attr('disabled', 'disabled');
            },
            success: function (data) {
                self.checkProductsAndCart(data);
            },
            error: function(request) {
                Main.message('', 'error');
            }
        });
    },
    orderSubmit: function($elem) {
        let self = this;
        $.ajax({
            url: $(self.forOrder).data('url'),
            type: 'post',
            dataType: 'json',
            data: $elem.serialize(),
            beforeSend: function() {
                $(self.orderForm + ' button').attr('disabled', 'disabled');
            },
            success: function (data) {
                self.checkProductsAndCart(data);
            },
            error: function(request) {
                Main.message('', 'error');
            }
        });
    }
};

$(function () {
    Cart.init();
});