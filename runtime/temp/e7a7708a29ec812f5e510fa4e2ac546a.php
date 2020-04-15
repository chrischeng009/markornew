<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:41:"./application/admin/view/config/edit.html";i:1562150322;s:61:"/www/wwwroot/nouser/application/admin/view/common/header.html";i:1568000924;s:59:"/www/wwwroot/nouser/application/admin/view/common/menu.html";i:1562208198;s:61:"/www/wwwroot/nouser/application/admin/view/common/footer.html";i:1561019635;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title><?php echo atitle();?></title>
<link href="/css/root.css" rel="stylesheet">
</head>
<body>
<div class="loading"><img src="/img/loading.gif" alt="loading-img"></div>
<div id="top" class="clearfix" style="background:<?php echo $_SESSION['acolor']; ?>;">
    <div class="applogo">
        <a href="/home.php/admin/admin/index.html" class="logo">管控台</a>
    </div>
    <a href="javascript:;" class="sidebar-open-button"><i class="fa fa-bars"></i></a>
    <a href="javascript:;" class="sidebar-open-button-mobile"><i class="fa fa-bars"></i></a>
    <ul class="topmenu" style="padding-left:20px;">
        <li><a href="/home.php/admin/admin/index.html">首页</a></li>
        <?php if($_SESSION['role_id']==1){?>
        <li><a href="/merchant.php" target="_blank">商家端</a></li>
        <!--<li><a href="/home.php/user/user/index.html" target="_blank">粉丝端</a></li>-->
        <?php }?>
    </ul>
    <ul class="top-right">
        <?php if($_SESSION['role_id']==2){?>
        <li style="float: left">红包使用金额：<?php  echo kefuhongbao($_SESSION['admin_id']);?></li>
        <li style="float: left">余额：<?php  echo kefumoney($_SESSION['admin_id']);?></li>
        <?php }?>
        <li class="dropdown link">
            <a href="javascript:;" data-toggle="dropdown" class="dropdown-toggle profilebox">
                <img src="/img/profileimg.png" alt="img"><b style="color:#fff;"><?php echo $_SESSION['admin_name']; ?></b><span class="caret"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-list dropdown-menu-right">
                <li><a href="/home.php/admin/admin/edit_password.html"><i class="fa falist fa-lock"></i>修改密码</a></li>
                <li><a href="/home.php/admin/admin/edit_self.html"><i class="fa falist fa-user"></i>修改资料</a></li>
                <li><a href="/home.php/admin/admin/logout.html"><i class="fa falist fa-power-off"></i> 退出</a></li>
            </ul>
        </li>
    </ul>
</div>
<div class="sidebar clearfix">
    <ul class="sidebar-panel nav">
        <!--li class="sidetitle">MENU</li-->
        <?php
        use app\common\model\MenuModel;
        use app\common\model\AdminModel;
        use think\Db;
        $module = request()->module();
        $control = strtolower(request()->controller());
        $action = request()->action();
        $find_menu = MenuModel::find("and control='".$control."' and action='".$action."'");
        $menu_name = isset($find_menu['name'])?$find_menu['name']:"菜单未配置";
        $findAdmin = AdminModel::find("and id='".$_SESSION['admin_id']."'");
        $authArr = !empty($findAdmin['auths'])?json_decode($findAdmin['auths'],true):[];
        $where='';
        /*
        <!--$day = date("Y-m-d");-->
        <!--$where .= " and uptime >= '" . strtotime($day . " 00:00:00") . "'";-->
        <!--$where .= " and uptime <= '" . strtotime($day . " 23:59:59") . "'"; -->*/
        //非超级管理员
        if($_SESSION['role_id']==5){
        $tid=$_SESSION['admin_id'];
        $mcarr = Db::table('merchant')->where("aid=".$tid)->select();
        if(empty($mcarr)){
        }else{
        $str = "";
        foreach($mcarr as $v){
        $str .= $v['id'].',';
        }
        $strId =substr($str, 0, -1);
        $where .= " and merchant_id in(".$strId.")";
        }
        }
        if($_SESSION['role_id']==2){
        $where .= " and aid='".$_SESSION['admin_id']."'";
        }

        $tkorder=Db::table('order')->where(" type=2 and status2=1 and exceptioninfo !='申请下架' $where")->count();
        $xjorder=Db::table('order')->where(" type=2 and status2=1 and exceptioninfo ='申请下架' $where")->count();
        $ycorder=Db::table('order')->where(" type=3 and status3=1 and exceptioninfo !='申请下架' $where")->count();
        $comorder=Db::table('order')->where(" type in (1,3) and iscomment=2 $where")->count();

        $menu_lists = MenuModel::select("and pid=0");
        foreach($menu_lists as $v){
        $auth_name = $v['control']."-".$v['action'];
        $findMenu = MenuModel::find("and control='".$control."' and action='".$action."'");
        $active = '';
        $display = '';
        if($v['id']==$findMenu['pid']){ $active='active'; $display='display:block';}
        $child = MenuModel::select("and isshow=1 and pid=".$v['id']);
        if(in_array($auth_name,$authArr) && $v['isshow']==1){
        ?>
        <li>
            <a href="javascript:;" class="<?php echo $active; ?>">
                <span class="icon color7"><i class="fa <?php echo $v['icon']; ?>"></i></span><?php echo $v['name']; ?><span class="caret"></span>
            </a>
            <ul style="<?php echo $display; ?>">
                <?php
                if($child){
                foreach($child as $vv){
                $auth2_name = $vv['control']."-".$vv['action'];
                if(in_array($auth2_name,$authArr)){
                if($vv['control']=='user' && $vv['action']=='lists2'){?>
                <li>
                    <a href="/home.php/admin/<?php echo $vv['control']; ?>/<?php echo $vv['action']; ?>.html?status=2"><?php echo $vv['name']; ?></a>
                </li>
                <?php }elseif($vv['control']=='order' && $vv['action']=='lists2'){ ?>
                <li>
                    <a href="/home.php/admin/<?php echo $vv['control']; ?>/<?php echo $vv['action']; ?>.html?status2=1"><?php echo $vv['name']; ?><i style="
                    position: absolute;right: 6px;top: 14px;height: 20px;width: 20px;line-height: 20px;background-color: #fc6d26;z-index: 99;
                    text-align: center;
                    border-radius: 6px;
                    cursor: pointer;
                    font-family: arial;
                    font-size: 14px;
                    font-weight: bold;"><?php echo $tkorder; ?></i></a>
                </li>
                <?php }elseif($vv['control']=='order' && $vv['action']=='offlists2'){ ?>
                <li>
                    <a href="/home.php/admin/<?php echo $vv['control']; ?>/<?php echo $vv['action']; ?>.html?status2=1"><?php echo $vv['name']; ?><i style="
                    position: absolute;right: 6px;top: 14px;height: 20px;width: 20px;line-height: 20px;background-color: #fc6d26;z-index: 99;
                    text-align: center;
                    border-radius: 6px;
                    cursor: pointer;
                    font-family: arial;
                    font-size: 14px;
                    font-weight: bold;"><?php echo $xjorder; ?></i></a>
                </li>
                <?php }elseif($vv['control']=='order' && $vv['action']=='lists3'){ ?>
                <li>
                    <a href="/home.php/admin/<?php echo $vv['control']; ?>/<?php echo $vv['action']; ?>.html?status3=1"><?php echo $vv['name']; ?><i style="
                    position: absolute;right: 6px;top: 14px;height: 20px;width: 20px;line-height: 20px;background-color: #fc6d26;z-index: 99;
                    text-align: center;
                    border-radius: 6px;
                    cursor: pointer;
                    font-family: arial;
                    font-size: 14px;
                    font-weight: bold;"><?php echo $ycorder; ?></i></a>
                </li>
                <?php }elseif($vv['control']=='order' && $vv['action']=='comment'){ ?>
                <li>
                    <a href="/home.php/admin/<?php echo $vv['control']; ?>/<?php echo $vv['action']; ?>.html"><?php echo $vv['name']; ?><i style="
                    position: absolute;right: 6px;top: 14px;height: 20px;width: 20px;line-height: 20px;background-color: #fc6d26;z-index: 99;
                    text-align: center;
                    border-radius: 6px;
                    cursor: pointer;
                    font-family: arial;
                    font-size: 14px;
                    font-weight: bold;"><?php echo $comorder; ?></i></a>
                </li>
                <?php }else{ ?>
                <li>
                    <a href="/home.php/admin/<?php echo $vv['control']; ?>/<?php echo $vv['action']; ?>.html"><?php echo $vv['name']; ?></a>
                </li>
                <?php } } } }?>
            </ul>
        </li>
        <?php } }?>
    </ul>
</div>


<div class="content">
    <div class="page-header">
        <h1 class="title">系统配置</h1>
    </div>
    <div class="container-padding">
        <div class="row">
            <form action="/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_edit.html" class="form-horizontal" method="post" id="dx_form" autocomplete="off" enctype="multipart/form-data">
            <div class="col-md-12">
                <ul class="nav nav-tabs" b="nav-justified">
                    <li class="active" onclick="javascript:swc(1);" swc="1"><a href="javascript:;">网站参数</a></li>
                    <li onclick="javascript:swc(2);" swc="2"><a href="javascript:;">收款配置</a></li>
                    <li onclick="javascript:swc(3);" swc="3"><a href="javascript:;">短信配置</a></li>
                    <li onclick="javascript:swc(4);" swc="4"><a href="javascript:;">邮箱发送配置</a></li>
                </ul>
                <div class="panel panel-default" style="padding-top:15px;">
                    <div class="panel-body" config="1">
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">工作时间</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="worktime" value="<?php echo $find['worktime']; ?>" placeholder="8:00-22:00">
                            </div>
                            <label class="col-sm-2 control-label form-label">每天任务数</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="worknum" value="<?php echo $find['worknum']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">同步设置</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="synchro">
                                    <option value="">不同步</option>
                                    <?php foreach($enum_synchro_arr as $k=>$v){?>
                                    <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">域名</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="website" value="<?php echo $find['website']; ?>">
                            </div>
                            <label class="col-sm-2 control-label form-label">色调</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="acolor">
                                    <?php foreach($enum_acolor_arr as $k=>$v){?>
                                    <option value="<?php echo $k; ?>" <?php if($k==$_SESSION['acolor']){ echo 'selected';}?>><?php echo $v; ?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">后台标题</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="atitle" value="<?php echo $find['atitle']; ?>">
                            </div>
                            <label class="col-sm-2 control-label form-label">标题</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="title" value="<?php echo $find['title']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">关键字</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="keywords" value="<?php echo $find['keywords']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">描述</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="description" value="<?php echo $find['description']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">商家入驻码</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="regcode" value="<?php echo $find['regcode']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="panel-body" config="2" style="display:none;">
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">收款姓名</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="realname" value="<?php echo $find['realname']; ?>">
                            </div>
                            <label class="col-sm-2 control-label form-label">收款银行</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="bankname" value="<?php echo $find['bankname']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">收款卡号</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="bankcode" value="<?php echo $find['bankcode']; ?>">
                            </div>
                            <label class="col-sm-2 control-label form-label">支付宝收款码</label>
                            <div class="col-sm-3">
                                <input type="file" id="dx_file" name="dx_file" onchange="sc('zfbmoneycode');" style="display:none"/>
                                <img src="<?php echo $find['zfbmoneycode']; ?>" id="imgView" style="height:100px;">
                                <input type="hidden" name="zfbmoneycode" value="<?php echo $find['zfbmoneycode']; ?>"/>
                                <button type="button" class="btn btn-danger" onclick="dx_file.click()">点击上传</button>
                            </div>
                        </div>
                        <!--div class="form-group">
                            <label class="col-sm-2 control-label form-label">Appid</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="wx_appid" value="<?php echo $find['wx_appid']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">Appsecret</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="wx_appsecret" value="<?php echo $find['wx_appsecret']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">商户Id</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="wx_mchid" value="<?php echo $find['wx_mchid']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">商户秘钥</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="wx_key" value="<?php echo $find['wx_key']; ?>">
                            </div>
                        </div-->
                    </div>
                    <div class="panel-body" config="3" style="display:none;">
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">Appid</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="sms_appid" value="<?php echo $find['sms_appid']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">Appsecret</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="sms_appsecret" value="<?php echo $find['sms_appsecret']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">签名</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="sms_signname" value="<?php echo $find['sms_signname']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">模版code</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="sms_code" value="<?php echo $find['sms_code']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="panel-body" config="4" style="display:none;">
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">接收邮箱地址：</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="rece_email" value="<?php echo $find['rece_email']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">发件邮箱地址：</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="email_from" value="<?php echo $find['email_from']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">发件人名称：</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="email_name" value="<?php echo $find['email_name']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">STMP服务器:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="smtp_host" value="<?php echo $find['smtp_host']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">服务器端口：</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="smtp_port" value="<?php echo $find['smtp_port']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">邮箱账号：</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="email_user" value="<?php echo $find['email_user']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">邮箱密码：</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="email_password" value="<?php echo $find['email_password']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-3">
                            <input type="hidden" class="form-control" name="id" value="<?php echo $find['id']; ?>">
                            <button type="button" class="btn btn-primary" id="sub">提交</button>
                            <button type="button" class="btn btn-default" onclick="javascript:history.back(-1)">返回</button>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="/js/jquery.min.js"></script>
<script src="/js/bootstrap/bootstrap.min.js"></script>
<script type="text/javascript" src="/js/plugins.js"></script>
<!--<script type="text/javascript">edit2</script>-->
<script type="text/javascript">
    //html data-toggle="tooltip" title="必填项"
    //$('[name="name"]').tooltip('show');
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
            $("#tipBtn").click();$("#tipText").html("文件类型错误,请上传图片类型");
            return false;
        }
        if(parseInt(fileSize) >= parseInt(maxSize)){
            $("#tipBtn").click();$("#tipText").html("上传的文件不能超过5MB");
            return false;
        }
        var data = new FormData($('#dx_form')[0]);
        $.ajax({
            url: "/home.php/<?php echo $module; ?>/config/uploadify.html",
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
                }else{
                    $("#tipBtn").click();$("#tipText").html("上传失败");
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
            $("#tipBtn").click();$("#tipText").html("文件类型错误,请上传图片类型");
            return false;
        }
        if(parseInt(fileSize) >= parseInt(maxSize)){
            $("#tipBtn").click();$("#tipText").html("上传的文件不能超过5MB");
            return false;
        }
        var data = new FormData($('#dx_form')[0]);
        $.ajax({
            url: "/home.php/<?php echo $module; ?>/config/uploadify2.html",
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
                }else{
                    $("#tipBtn").click();$("#tipText").html("上传失败");
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
            $("#tipBtn").click();$("#tipText").html("文件类型错误,请上传图片类型");
            return false;
        }
        if(parseInt(fileSize) >= parseInt(maxSize)){
            $("#tipBtn").click();$("#tipText").html("上传的文件不能超过5MB");
            return false;
        }
        var data = new FormData($('#dx_form')[0]);
        $.ajax({
            url: "/home.php/<?php echo $module; ?>/config/uploadify3.html",
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
                    $("#tipBtn").click();$("#tipText").html("上传失败");
                }
            }
        });
    }
</script>
<!--
$("#tipBtn").click();
$("#tipText").html("");
-->
<button class="btn btn-default" style="display:none;" id="tipBtn" data-toggle="modal" data-target="#tipModal">文字弹窗按钮</button>
<div class="modal fade" id="tipModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">提示信息</h4>
            </div>
            <div class="modal-body">
                <div class="modal-body" id="tipText">在这里添加一些文本</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
                </div>
            </div>
        </div>
    </div>
</div>

<button class="btn btn-default" data-toggle="modal" id="tiperBtn" data-target="#tiperModal" style="display:none;">图片弹窗按钮</button>
<div class="modal fade" id="tiperModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">提示信息</h4>
            </div>
            <div class="modal-body" id="tipZi" style="text-align:center;">文字提示信息内容</div>
            <div class="modal-footer" style="text-align:center;">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function imger(msg){
        var msg = decodeURIComponent(msg);
        $("#tipZi").html("<img style='max-width:400px;' src='"+msg+"' />");
        $("#tiperBtn").click();
    }
</script>

<script type="text/javascript">
    $("#sub").on('click',function(){
        $.ajax({
            type:'POST',
            cache:false,
            url:'/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_edit.html',
            dataType:'text',
            data:$("#dx_form").serialize(),
            success:function(data)
            {
                if(data=='success'){
                    $("#tipBtn").click();
                    $("#tipText").html("操作成功");
                    setTimeout(function(){
                        window.location.reload();
                    },2000);
                }else{
                    $("#tipBtn").click();
                    $("#tipText").html(data);
                }
            }
        });
    });
    
    function swc(id){
        if(id==1){
            $('[config=1]').show();
            $('[config=2]').hide();
            $('[config=3]').hide();
            $('[config=4]').hide();
            $('[swc=1]').addClass('active');
            $('[swc=2]').removeClass('active');
            $('[swc=3]').removeClass('active');
            $('[swc=4]').removeClass('active');
        }
        if(id==2){
            $('[config=2]').show();
            $('[config=1]').hide();
            $('[config=3]').hide();
            $('[config=4]').hide();
            $('[swc=2]').addClass('active');
            $('[swc=1]').removeClass('active');
            $('[swc=3]').removeClass('active');
            $('[swc=4]').removeClass('active');
        }
        if(id==3){
            $('[config=3]').show();
            $('[config=1]').hide();
            $('[config=2]').hide();
            $('[config=4]').hide();
            $('[swc=3]').addClass('active');
            $('[swc=1]').removeClass('active');
            $('[swc=2]').removeClass('active');
            $('[swc=4]').removeClass('active');
        }
        if(id==4){
            $('[config=4]').show();
            $('[config=1]').hide();
            $('[config=2]').hide();
            $('[config=3]').hide();
            $('[swc=4]').addClass('active');
            $('[swc=1]').removeClass('active');
            $('[swc=2]').removeClass('active');
            $('[swc=3]').removeClass('active');
        }
    }
</script>

</body>
</html>