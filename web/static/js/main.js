let Main = {
    bigPicture: '.big-picture',
    smallPictures: '.small-pictures',

    init: function() {
        let self = this;
        self.bindEvents();

    },
    bindEvents: function() {
        let self = this;
        $(document)
            .on('click', self.smallPictures + ' img', function () {
                self.changePicture($(this));
            });
    },
    message: function(text, type) {
        let $popup = $('#popup-message');
        type = typeof type === 'undefined' ? 'message' : type;
        if (type == 'error' && text == '') {
            text =  'Вибачте, виникла помилка. Спробуйте знову через хвилину.';
        }
        $popup.removeClass('error').addClass(type).empty().text(text).fadeIn(1000);
        setTimeout(function() {$popup.fadeOut(1000);}, 7000);
    },

    changePicture: function($elem) {
        let self = this;
        $(self.bigPicture + ' img').attr('src', $elem.attr('src'));
        $(self.smallPictures + ' img').removeClass('active');
        $elem.addClass('active');
    }
};

$(function () {
    'use strict';
    Main.init();
});