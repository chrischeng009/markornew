/***
 * 弹窗
 * 用法
 * <script src="js/libs/modal.js"></script>
 * 阻止关闭弹窗时 返回  false
Modal.confirm('这是一段提示文字', {
    ok: function(){
        console.log('你点击了ok, 此时不关闭按钮');
        return false;
    },
    cancel: function(){
        console.log('你点击了cancel, 此时关闭按钮');
    }
});
或者
Modal.alert();
  
 */

function isMobile(value){
    if(/^13\d{9}$/g.test(value)||(/^14[0-35-9]\d{8}$/g.test(value))||(/^15[0-35-9]\d{8}$/g.test(value))||(/^16[0-35-9]\d{8}$/g.test(value))||(/^17[0-35-9]\d{8}$/g.test(value))||(/^18[01-9]\d{8}$/g.test(value))||(/^19[01-9]\d{8}$/g.test(value))){     
        return true;   
    }else{
        return false;
    }
}
function sc(name){
    var animateimg = $("#dx_file").val(); //获取上传的图片名 带//  
    var imgarr=animateimg.split('\\'); //分割  
    var myimg=imgarr[imgarr.length-1]; //去掉 // 获取图片名  
    var houzui = myimg.lastIndexOf('.'); //获取 . 出现的位置  
    var ext = myimg.substring(houzui, myimg.length).toUpperCase();  //切割 . 获取文件后缀  
    var file = $('#dx_file').get(0).files[0]; //获取上传的文件
    var fileSize = file.size;           //获取上传的文件大小
    var maxSize = 5348576;              //最大5MB 1MB=1048576
    if(ext !='.PNG' && ext !='.GIF' && ext !='.JPG' && ext !='.JPEG' && ext !='.BMP'){
        Modal.alert('文件类型错误,请上传图片类型');return false;
    }
    if(parseInt(fileSize) >= parseInt(maxSize)){
        Modal.alert('上传的文件不能超过5MB');return false;
    }
    var data = new FormData($('#dx_form')[0]);
    $('#tishi').show();
    $.ajax({
        url: "/home.php/user/user/uploadify.html",   
        type: 'POST',
        data: data,
        dataType: 'text',
        processData: false,
        contentType: false,
        success:function(res)
        {
            var jsonobj=JSON.parse(res);
            if(jsonobj.code=='success'){
                var imgUrl = jsonobj.msg;
                $('#imgView').attr('src',imgUrl);
                $('[name="'+name+'"]').val(imgUrl);
                $('#tishi').hide();
            }else{
                Modal.alert('上传失败');
            }
        }
    });
}
function sc2(name){
    var animateimg = $("#dx_file2").val(); //获取上传的图片名 带//  
    var imgarr=animateimg.split('\\'); //分割  
    var myimg=imgarr[imgarr.length-1]; //去掉 // 获取图片名  
    var houzui = myimg.lastIndexOf('.'); //获取 . 出现的位置  
    var ext = myimg.substring(houzui, myimg.length).toUpperCase();  //切割 . 获取文件后缀  
    var file = $('#dx_file2').get(0).files[0]; //获取上传的文件
    var fileSize = file.size;           //获取上传的文件大小
    var maxSize = 5348576;              //最大5MB 1MB=1048576
    if(ext !='.PNG' && ext !='.GIF' && ext !='.JPG' && ext !='.JPEG' && ext !='.BMP'){
        Modal.alert('文件类型错误,请上传图片类型');return false;
    }
    if(parseInt(fileSize) >= parseInt(maxSize)){
        Modal.alert('上传的文件不能超过5MB');return false;
    }
    var data = new FormData($('#dx_form')[0]);
    $('#tishi').show();
    $.ajax({
        url: "/home.php/user/user/uploadify2.html",   
        type: 'POST',
        data: data,
        dataType: 'text',
        processData: false,
        contentType: false,
        success:function(res)
        {
            var jsonobj=JSON.parse(res);
            if(jsonobj.code=='success'){
                var imgUrl = jsonobj.msg;
                $('#imgView2').attr('src',imgUrl);
                $('[name="'+name+'"]').val(imgUrl);
                $('#tishi').hide();
            }else{
                Modal.alert('上传失败');
            }
        }
    });
}
function sc3(name){
    var animateimg = $("#dx_file3").val(); //获取上传的图片名 带//  
    var imgarr=animateimg.split('\\'); //分割  
    var myimg=imgarr[imgarr.length-1]; //去掉 // 获取图片名  
    var houzui = myimg.lastIndexOf('.'); //获取 . 出现的位置  
    var ext = myimg.substring(houzui, myimg.length).toUpperCase();  //切割 . 获取文件后缀  
    var file = $('#dx_file3').get(0).files[0]; //获取上传的文件
    var fileSize = file.size;           //获取上传的文件大小
    var maxSize = 5348576;              //最大5MB 1MB=1048576
    if(ext !='.PNG' && ext !='.GIF' && ext !='.JPG' && ext !='.JPEG' && ext !='.BMP'){
        Modal.alert('文件类型错误,请上传图片类型');return false;
    }
    if(parseInt(fileSize) >= parseInt(maxSize)){
        Modal.alert('上传的文件不能超过5MB');return false;
    }
    var data = new FormData($('#dx_form')[0]);
    $.ajax({
        url: "/home.php/user/user/uploadify3.html",   
        type: 'POST',
        data: data,
        dataType: 'text',
        processData: false,
        contentType: false,
        success:function(res)
        {
            var jsonobj=JSON.parse(res);
            if(jsonobj.code=='success'){
                var imgUrl = jsonobj.msg;
                $('#imgView3').attr('src',imgUrl);
                $('[name="'+name+'"]').val(imgUrl);
            }else{
                Modal.alert('上传失败');
            }
        }
    });
}

var getType = function(obj){ return Object.prototype.toString.call(obj).slice(8, -1).toLowerCase()};
var isArray = function(obj){ return getType(obj) === "array"};
var isObject = function(obj){ return getType(obj) === "object"};
var isFunction = function(obj){ return getType(obj) === "function"};

var Modal = {
    el: null,
    opts: {
        title: '提示'
    },
    msg: '输入错误',
    alertHtml:'<div class="dialog vh-h-center"><div class="dialog_body"><div class="title vh-between"><span class="title_text">提示</span><i class="icon_close icon_close_red" id="aclose"></i></div><div class="dialog_section dialog_alert text_center vh-h-center" id="atext">输入错误</div><div class="dialog_footer vh-center"><a id="jump"><div class="btn alertok">确定</div></a></div></div></div>',
    confirmHtml:'<div class="dialog vh-h-center"><div class="dialog_body"><div class="title vh-between"><span class="title_text">提示</span><i class="icon_close icon_close_red" id="cclose"></i></div><div class="dialog_section dialog_alert text_center vh-h-center" id="ctext">输入错误</div><div class="dialog_footer vh-center"><div class="btn cancle" >取消</div><div class="btn ok">确定</div></div></div></div>',
    openHtml:'<div class="dialog vh-h-center"><div class="dialog_body"><div class="title vh-between"><span class="title_text">提示</span><i class="icon_close"></i></div><div class="dialog_section text_center vh-h-center">输入错误</div></div></div>',
    init: function(msg, opts, html){
        this.opts = opts || this.opts;
        this.msg = msg;
        this.el = $(html);
        this.show();
    },
    alert: function(msg, opts){
        this.init(msg, opts, this.alertHtml);
    },
    confirm: function(msg, opts){
        this.init(msg, opts, this.confirmHtml);
    },
    ok: function(){
        var _this = this;
        if(_this.opts['ok'] && isFunction(_this.opts['ok'])){
            if(_this.opts.ok() !== false){
                _this.hide();
            }
        }else{
            _this.hide();
        }
    },
    cancle: function(){
        var _this = this;
        if(_this.opts['cancel'] && isFunction(_this.opts['cancel'])) {
            if(_this.opts.cancel() !== false){
                _this.hide();
            }
        }else{
            _this.hide();
        }
    },
    hide: function(){
        this.el.remove();
        $('body').removeClass('hidden');
    },
    open: function(html, opts){
        this.init(html, opts, this.openHtml);
    },
    show: function(){
        $('body').append(this.el).addClass('hidden');
        if(this.opts.title) this.el.find('.title_text').html(this.opts.title);
        if(this.msg) this.el.find('.dialog_section').html(this.msg);
        this.el.find('.ok').unbind('click').bind('click', this.ok.bind(this));
        this.el.find('.alertok').unbind('click').bind('click', this.cancle.bind(this));
        this.el.find('.icon_close').unbind('click').bind('click', this.cancle.bind(this));
        if(this.el.find('.cancle').length > 0) this.el.find('.cancle').unbind('click').bind('click', this.cancle.bind(this));
    }
    
}

