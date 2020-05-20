let Login = {

    box: '',
    needMagic: '.need-magic',
    toggleEye: '.toggle-eye',
    passGen: '.pass-gen',

    init: function() {
        let self = this;
        self.box = $('.login-box').length > 0 ? '.login-box' : '.register-box';
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' /* optional */
        });
        self.createPasswordCubes();
        self.bindEvents();
        self.resize();
    },
    bindEvents: function () {
        let self = this;
        $(window).resize(function(){
            self.resize();
        });
        $(document)
            .on('click', self.toggleEye, function () {
                self.togglePassword($(this));
            })
            .on('click', self.passGen, function () {
                self.passwordGenerate($(this));
            });

    },
    resize: function() {
        let self = this;
        let mt = ($(window).height() - $(self.box).height()) / 2.5;
        mt = mt > 0 ? (mt + 'px') : 0;
        $(self.box).stop().animate({'margin-top': mt}, 700);
    },
    createPasswordCubes: function() {
        // MG: нет времени делать красивый js, доделаю его после php
        let self = this;
        if ($(self.needMagic).length > 0) {
            $(self.needMagic).wrap($('<div />').addClass('input-group'));
            $(self.needMagic).before('<span class="input-group-btn">\n' +
                '<button type="button" class="btn btn-flat toggle-eye"><i class="fa fa-eye"></i></button>\n' +
                '</span>');
            $(self.needMagic).before('<span class="input-group-btn">\n' +
                '<button type="button" class="btn btn-flat pass-gen"><i class="fa fa-magic"></i></button>\n' +
                '</span>');
        }
    },
    togglePassword: function($elem) {
        let self = this;
        let id = $elem.parent().parent().find('input').attr('id');
        if ($elem.find('i').hasClass('fa-eye-slash')) {
            $elem.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
            document.getElementById(id).setAttribute('type', 'password');
        } else {
            $elem.find('i').removeClass('fa-eye').addClass('fa-eye-slash');
            document.getElementById(id).setAttribute('type', 'text');
        }
    },
    passwordGenerate: function($elem) {
        let self = this;
        let id = $elem.parent().parent().find('input').attr('id');
        let result       = '';
        let words        = '0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
        let max_position = words.length;
        let position;
        let max = Math.floor(Math.random() * (11 - 7)) + 7;
        for(let i = 0; i < max; ++i ) {
            position = Math.floor ( Math.random() * max_position );
            result = result + words.substring(position, position + 1);
        }
        $elem.parent().parent().find(self.toggleEye + ' i').removeClass('fa-eye').addClass('fa-eye-slash');
        document.getElementById(id).setAttribute('type', 'text');
        $('#' + id).val(result);

    }
};

$(function () {
    Login.init();
});