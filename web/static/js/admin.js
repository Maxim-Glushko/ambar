let Admin = {

    contentImgDel: '.for-content-img span',
    contentImg :'.content-img',
    contentInputImg :'.content-input-img',
    contentUploadForm: '#contentUploadForm',
    contentFileInput: '#contentUploadForm #content-file',
    articleFileInput: '#contentUploadForm #article-file',
    newsFileInput: '#contentUploadForm #news-file',

    productGallery: '.admin-product-imgs',
    productUploadForm: '#productUploadForm',
    productFileInput: '#productUploadForm #product-file',
    productPicturePlus: '.admin-product-imgs .plus',
    productPictureRight: '.admin-product-imgs .right',
    productPictureLeft: '.admin-product-imgs .left',

    contentSelect: '#content-select',
    productSelect: '#product-select',
    adminProducts: '.admin-order-products',
    productPlus: '.admin-order-product .plus',
    productMinus: '.admin-order-product .minus',
    productInput: '.admin-order-product .product-input',
    productDel: '.admin-order-product .product-delete',

    init: function() {
        let self = this;
        self.bindEvents();
        self.calcGalleryWidth();
        setInterval(self.countOrders, 7000);
    },
    bindEvents: function () {
        let self = this;
        $(document)
            .on('click', self.contentImg, function () {
                $(self.contentFileInput).trigger('click');
                $(self.articleFileInput).trigger('click');
                $(self.newsFileInput).trigger('click');
            })
            .on('change', self.contentFileInput + ', ' + self.articleFileInput + ', ' + self.newsFileInput, function() {
                self.contentFileUpload(this.files);
            })
            .on('click', self.contentImgDel, function() {
                $(self.contentInputImg).val('');
                $(self.contentImgDel).css('display', 'none');
                $(self.contentImg).attr('src', $(self.contentImg).data('src'));
            })
            .on('click', self.productGallery + ' li>span', function() {
                $(this).closest('li').remove();
                self.calcGalleryWidth();
            })
            .on('click', self.productPicturePlus, function() {
                $(self.productFileInput).trigger('click');
            })
            .on('change', self.productFileInput, function() {
                self.productFileUpload(this.files);
            })
            .on('click', self.productPictureLeft, function() {
                let papa = $(this).closest('li');
                let prev = papa.prev('li');
                if (prev.hasClass('one-picture')) {
                    papa.insertBefore(prev);
                }
            })
            .on('click', self.productPictureRight, function() {
                let papa = $(this).closest('li');
                let next = papa.next('li');
                if (next.hasClass('one-picture')) {
                    papa.insertAfter(next);
                }
            })
            .on('change', self.contentSelect, function() {
                self.loadProductSelect();
            })
            .on('change', self.productSelect, function() {
                self.changeOrderProduct($(this).val(), 1, $(this).data('url'));
            })
            .on('click', self.productPlus, function() {
                self.changeOrderProduct($(this).data('id'), 1, $(this).data('url'));
            })
            .on('click', self.productMinus, function() {
                self.changeOrderProduct($(this).data('id'), -1, $(this).data('url'));
            })
            .on('click', self.productDel, function() {
                self.changeOrderProduct($(this).data('id'), 0, $(this).data('url'));
            })
            .on('blur', self.productInput, function() {
                self.changeOrderProduct($(this).data('id'), $(this).val(), $(this).data('url'));
            })
            .on('input', self.productInput, function() {
                let self = this;
                let val = parseInt($(this).val());
                if (isNaN(val) || (val < 1)) val = 1;
                $(this).val(val);
            });
    },
    contentFileUpload: function(files) {
        let self = this;
        if ((typeof files == 'undefined') || !files || !files.length)
            return false;
        let data = new FormData();
        $.each(files, function(key, value){
            data.append(key, value);
        });
        data.append('uploading_files', 1);
        $.ajax({
            url: $(self.contentUploadForm).attr('action'),
            type: 'post',
            dataType: 'json',
            data: data,
            cache: false,
            processData: false, // Не обрабатываем файлы (Don't process the files)
            contentType: false, // Так jQuery скажет серверу что это строковой запрос
            beforeSend: function() {},
            success: function (data) {
                if (typeof data.error !== 'undefined') {
                    Main.message(data.error, 'error');
                } else if (typeof data.src !== 'undefined') {
                    $(self.contentImg).attr('src', data.src);
                    $(self.contentInputImg).val(data.src);
                    $(self.contentImgDel).css('display', 'block');
                }
            },
            error: function(request) {
                Main.message('', 'error');
            }
        });
    },
    productFileUpload: function(files) {
        let self = this;
        if ((typeof files == 'undefined') || !files || !files.length)
            return false;
        let data = new FormData();
        $.each(files, function(key, value){
            data.append(key, value);
        });
        data.append('uploading_files', 1);
        let index = parseInt($(self.productPicturePlus).attr('data-index'));
        data.append('index', index);
        $.ajax({
            url: $(self.productUploadForm).attr('action'),
            type: 'post',
            dataType: 'json',
            data: data,
            cache: false,
            processData: false, // Не обрабатываем файлы (Don't process the files)
            contentType: false, // Так jQuery скажет серверу что это строковой запрос
            beforeSend: function() {},
            success: function (data) {
                if (typeof data.error !== 'undefined') {
                    Main.message(data.error, 'error');
                } else if (typeof data.html !== 'undefined') {
                    $(self.productPicturePlus).attr('data-index', index + 1);
                    $(self.productPicturePlus).closest('li').before(data.html);
                    self.calcGalleryWidth();
                }
            },
            error: function(request) {
                Main.message('', 'error');
            }
        });
    },
    calcGalleryWidth: function() {
        let self = this;
        let width = $(self.productGallery + ' ul li').length * 265 + 1;
        $(self.productGallery + ' ul').css({'width' : width + 'px'});
    },
    loadProductSelect: function() {
        let self = this;
        let id = $(self.contentSelect).val();
        if ($(self.productSelect).length) {
            $(self.productSelect).closest('.papa-select').remove();
        }
        if (!id) return;
        $.ajax({
            url: $(self.contentSelect).data('url'),
            type: 'post',
            dataType: 'json',
            data: {id: id},
            beforeSend: function() {

            },
            success: function (data) {
                if (typeof data.error !== 'undefined') {
                    Main.message(data.error, 'error');
                } else if (typeof data.html !== 'undefined') {
                    $(self.contentSelect).closest('.gpapa-select').append(data.html);
                    //$(self.productSelect).trigger('change.select2');
                }
            },
            error: function(request) {
                Main.message('', 'error');
            }
        });
    },
    changeOrderProduct: function(product_id, num, url) {
        let self = this;
        let order_id = $(self.adminProducts).data('id');

        if (!order_id || !product_id || !url)
            return false;

        let data = {
            product_id: product_id,
            order_id: order_id,
            num: num
        };

        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: data,
            beforeSend: function() {
                $(self.adminProducts).empty();
            },
            success: function (data) {
                if (typeof data.error !== 'undefined') {
                    Main.message(data.error, 'error');
                }
                if (typeof data.message !== 'undefined') {
                    Main.message(data.message);
                }
                if (typeof data.html !== 'undefined') {
                    $(self.adminProducts).append(data.html);
                }
            },
            error: function(request) {
                Main.message('', 'error');
            }
        });
    },
    countOrders: function() {
        let self = this;
        let $ordersCount = $('#ordersCount');
        $.ajax({
            url: $ordersCount.data('url'),
            type: 'post',
            //dataType: 'json',
            //data: data,
            beforeSend: function() {},
            success: function (data) {
                if (typeof data.counter !== 'undefined') {
                    let counter = parseInt(data.counter);
                    let num = parseInt($ordersCount.text());
                    if (counter > num) {
                        let audio = new Audio();
                        audio.preload = 'auto';
                        //audio.src = '/static/sounds/siren.mp3';
                        audio.src = '/static/sounds/tututu.mp3';
                        audio.play();
                    }
                    $ordersCount.text(counter);
                    $('.labelOrderCounter').css({'display': (counter > 0) ? 'block' : 'none'}).text(counter);
                }
            },
            error: function(request) {
                Main.message('', 'error');
            }
        });

    }
};

$(function () {
    Admin.init();
});