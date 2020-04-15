<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:47:"./application/merchant/view/merchant/login.html";i:1573120110;}*/ ?>
<!DOCTYPE HTML>
<html lang="zxx">
<head>
    <title><?php echo qtitle();?></title>
    <!-- Meta tag Keywords -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords" content=""/>
    <script>
        addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>
    <!-- Meta tag Keywords -->
    <!-- css files -->
    <link rel="stylesheet" href="/css/sj/style.css" type="text/css" media="all" />
    <!-- Style-CSS -->
    <link rel="stylesheet" href="/css/sj/fontawesome-all.css">
    <!-- Font-Awesome-Icons-CSS -->
    <!-- //css files -->
</head>

<body>
<!-- bg effect -->
<div id="bg">
    <canvas></canvas>
    <canvas></canvas>
    <canvas></canvas>
</div>
<!-- //bg effect -->
<!-- title -->
<h1>平台名称</h1>
<!-- //title -->
<!-- content -->
<div class="sub-main-w3">
    <form action="" name="" autocomplete="off" id="dx_form">
        <h2>Login Now
            <i class="fas fa-level-down-alt"></i>
        </h2>
        <div class="form-style-agile">
            <label>
                <i class="fas fa-user"></i>
                用户名
            </label>
            <input placeholder="请输入手机号" name="mobile" id="mobile" type="text" required="">
        </div>
        <div class="form-style-agile">
            <label>
                <i class="fas fa-unlock-alt"></i>
               密码
            </label>
            <input name="password" id="password" placeholder="请输入密码" type="password" required="">
        </div>
        <!-- checkbox -->
        <div class="wthree-text">
            <ul>
                <li>
                    <label class="anim">
                        <input type="checkbox" class="checkbox"  id="ck_rmbUser">
                        <span>记住登录名</span>
                    </label>
                </li>
                <li ><a href="/home.php/<?php echo $module; ?>/<?php echo $control; ?>/register.html" >还没有账号？立即注册</a></li>
                <li>
                    <a href="/home.php/<?php echo $module; ?>/<?php echo $control; ?>/findpass.html">忘记密码？</a>
                </li>
            </ul>
        </div>
        <!-- //checkbox -->
        <input type="button" value="登录" id="sub">
    </form>
</div>
<!-- //content -->

<!-- copyright -->
<div class="footer">
    <!--<p>Copyright © 2015 - 2019 猫客网络 <a href="http://www.miibeian.gov.cn/" target="_blank"> 粤ICP备15008861号</a></p>-->
</div>
<!-- //copyright -->

<!-- Jquery -->
<script src="/js/jquery-1.9.1.min.js"></script>
<!-- //Jquery -->

<!-- effect js -->
<script src="/js/sj/canva_moving_effect.js"></script>
<!-- //effect js -->
<script src="/js/jquery.cookie.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        if ($.cookie("rmbUser") == "true") {
            $("#ck_rmbUser").prop("checked", true);
            $("#mobile").val($.cookie("mobile"));
            $("#password").val($.cookie("password"));
        }
    });

    //记住用户名密码
    function save(){
        if ($("#ck_rmbUser").prop("checked")){
            var mobile = $("#mobile").val();
            var password = $("#password").val();
            $.cookie("rmbUser", "true", { expires: 300 }); //存储一个带300天期限的cookie
            $.cookie("mobile", mobile, { expires: 300 });
            $.cookie("password", password, { expires: 300 });
        }else{
            $.cookie("rmbUser", "false", { expire: -1 });
            $.cookie("mobile", "", { expires: -1 });
            $.cookie("password", "", { expires: -1 });
        }
    };

    $("[name='password']").keydown(function(e){
        var captcha = e.which;
        if(captcha == 13){
            $("#sub").click();
            return false;
        }
    });
    $("#sub").bind('click',function(){
        var mobile = $("[name='mobile']").val();
        var password = $("[name='password']").val();
        save();
        $.ajax({
            type:'POST',
            cache:false,
            url:'/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_login.html',
            dataType:'text',
            data:$("#dx_form").serialize(),
            success:function(data)
            {
                if(data=='success'){
                    window.location.href='/home.php/<?php echo $module; ?>/<?php echo $control; ?>/index.html';
                }else{
                    alert(data);
                }
            }
        })
    });
</script>

</body>

</html>