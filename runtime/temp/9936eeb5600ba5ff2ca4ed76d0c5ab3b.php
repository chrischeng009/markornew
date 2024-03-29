<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:41:"./application/admin/view/admin/login.html";i:1573118507;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="expires" content="0">
    <title><?php echo atitle();?></title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <!--必要样式-->
    <link href="/css/login/styles.css" rel="stylesheet" type="text/css" />
    <link href="/css/login/demo.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class='login'>
    <div class='login_title'>
        <span>管理员登录</span>
    </div>
    <div class='login_fields'>
        <div class='login_fields__user'>
            <div class='icon'>
                <img alt="" src='/img/login/user_icon_copy.png'>
            </div>
            <input name="admin_name" id="admin_name" placeholder='用户名' maxlength="16" type='text' autocomplete="off" />
            <div class='validation'>
                <img alt="" src='/img/login/tick.png'>
            </div>
        </div>
        <div class='login_fields__password'>
            <div class='icon'>
                <img alt="" src='/img/login/lock_icon_copy.png'>
            </div>
            <input name="password" id="password" type="password" placeholder='密码' maxlength="16"  autocomplete="off">
            <div class='validation'>
                <img alt="" src='/img/login/tick.png'>
            </div>
        </div>
        <div class='login_fields__password'style="margin-bottom: 30px;">
            <div class='icon' style="opacity: 1;" for="rememberCheck">
                <input type="checkbox" id="ck_rmbUser"/>记住帐号
            </div>
            <canvas  id="myCanvas"style="display:none" >对不起，您的浏览器不支持canvas，请下载最新版浏览器!</canvas>
        </div>
        <div class='login_fields__submit'>
            <input type='button' value='登录' id="login_btn">
        </div>
    </div>
    <div class='success'>
    </div>
    <div class='disclaimer'>
        <p>欢迎登陆后台管理系统</p>
    </div>
</div>
<div class="OverWindows"></div>
<link href="/js/login/layui/css/layui.css" rel="stylesheet" type="text/css" />
<script src="/js/jquery-1.9.1.min.js"></script>
<script src="/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/js/login/jquery-ui.min.js"></script>
<script type="text/javascript" src='/js/login/stopExecutionOnTimeout.js?t=1'></script>
<script src="/js/login/Particleground.js" type="text/javascript"></script>
<script src="/js/login/Treatment.js" type="text/javascript"></script>
<script src="/js/login/jquery.mockjax.js" type="text/javascript"></script>
<script src="/js/login/layui/layui.js" type="text/javascript"></script>
<script type="text/javascript">
    var canGetCookie = 0;//是否支持存储Cookie 0 不支持 1 支持
    var ajaxmockjax = 1;//是否启用虚拟Ajax的请求响 0 不启用  1 启用
    //默认账号密码

    var truelogin = "kbcxy";
    var truepwd = "mcwjs";

    var CodeVal = 0;
    Code();
    function Code() {
        if(canGetCookie == 1){
            createCode("AdminCode");
            var AdminCode = getCookieValue("AdminCode");
            showCheck(AdminCode);
        }else{
            showCheck(createCode(""));
        }
    }
    function showCheck(a) {
        CodeVal = a;
        var c = document.getElementById("myCanvas");
        var ctx = c.getContext("2d");
        ctx.clearRect(0, 0, 1000, 1000);
        ctx.font = "80px 'Hiragino Sans GB'";
        ctx.fillStyle = "#E8DFE8";
        ctx.fillText(a, 0, 100);
    }
    $(document).keypress(function (e) {
        // 回车键事件
        if (e.which == 13) {
            $('input[type="button"]').click();
        }
    });
    //粒子背景特效
    $('body').particleground({
        dotColor: '#E8DFE8',
        lineColor: '#133b88'
    });
    $('input[name="pwd"]').focus(function () {
        $(this).attr('type', 'password');
    });
    $('input[type="text"]').focus(function () {
        $(this).prev().animate({ 'opacity': '1' }, 200);
    });
    $('input[type="text"],input[type="password"]').blur(function () {
        $(this).prev().animate({ 'opacity': '.5' }, 200);
    });
    $('input[name="login"],input[name="pwd"]').keyup(function () {
        var Len = $(this).val().length;
        if (!$(this).val() == '' && Len >= 5) {
            $(this).next().animate({
                'opacity': '1',
                'right': '30'
            }, 200);
        } else {
            $(this).next().animate({
                'opacity': '0',
                'right': '20'
            }, 200);
        }
    });
    $(document).ready(function(){
        if ($.cookie("rmbUser") == "true") {
            $("#ck_rmbUser").prop("checked", true);
            $("#admin_name").val($.cookie("admin_name"));
            $("#password").val($.cookie("password"));
        }
    });

    //记住用户名密码
    function save(){
        if ($("#ck_rmbUser").prop("checked")){
            var admin_name = $("#admin_name").val();
            var password = $("#password").val();
            $.cookie("rmbUser", "true", { expires: 300 }); //存储一个带300天期限的cookie
            $.cookie("admin_name", admin_name, { expires: 300 });
            $.cookie("password", password, { expires: 300 });
        }else{
            $.cookie("rmbUser", "false", { expire: -1 });
            $.cookie("admin_name", "", { expires: -1 });
            $.cookie("password", "", { expires: -1 });
        }
    };

    $("[name='password']").keydown(function(e){
        var captcha = e.which;
        if(captcha == 13){
            $("#login_btn").click();
            return false;
        }
    });
    layui.use('layer', function () {
        $("#login_btn").bind('click', function () {
            var admin_name = $("[name='admin_name']").val();
            var password = $("[name='password']").val();
            save();
            $.ajax({
                type: 'POST',
                cache: false,
                url: '/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_login.html',
                dataType: 'text',
                data: "admin_name=" + admin_name + "&password=" + password,
                success: function (data) {
                    if (data == 'success') {
                        window.location.href = '/home.php/<?php echo $module; ?>/<?php echo $control; ?>/index.html';
                    } else {
                        ErroAlert(data);
                        setTimeout(function(){
                            window.location.reload();
                        },2000);
                    }
                }
            })
        });
    });
</script>
</body>
</html>
