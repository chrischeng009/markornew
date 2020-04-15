<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:43:"./application/admin/view/order/zuzhang.html";i:1565230822;s:61:"/www/wwwroot/nouser/application/admin/view/common/header.html";i:1568000924;s:59:"/www/wwwroot/nouser/application/admin/view/common/menu.html";i:1562208198;s:61:"/www/wwwroot/nouser/application/admin/view/common/footer.html";i:1561019635;}*/ ?>
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
    <?php if(empty($_GET['abc'])){?>
    <div class="page-header">
        <h1 class="title"><?php echo $menu_name; ?></h1>
    </div>
    <?php }?>
<div class="container-padding">
    <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
                <div class="panel-body">
                    <form action="/home.php/<?php echo $module; ?>/<?php echo $control; ?>/zuzhang.html" method="get" autocomplete="off">
                    <?php if(empty($_GET['abc'])){if($_SESSION['role_id']==1){?>
                    <div class="col-sm-1">
                        <h5>所属业务员</h5>
                        <div class="form-group">
                            <select class="form-control" name="tid">
                                <option value="">请选择</option>
                                <?php foreach($teacher_list as $v){?>
                                <option value="<?php echo $v['id']; ?>" <?php if($v['id']==$_GET['tid']){ echo 'selected';}?>><?php echo $v['name']; ?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                        <?php }?>
                    <div class="col-sm-1">
                        <h5>所属客服</h5>
                        <div class="form-group">
                            <select class="form-control" name="aid">
                                <option value="">请选择</option>
                                <?php foreach($server_list as $v){?>
                                <option value="<?php echo $v['id']; ?>" <?php if($v['id']==$_GET['aid']){ echo 'selected';}?>><?php echo $v['name']; ?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <h5>订单类型</h5>
                        <div class="form-group">
                            <select class="form-control" name="type">
                                <option value="">请选择</option>
                                <?php foreach($enum_type_arr as $k=>$v){?>
                                <option value="<?php echo $k; ?>" <?php if($k==$_GET['type']){ echo 'selected';}?>><?php echo $v; ?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <h5>店铺名</h5>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shop_name" value="<?php echo $_GET['shop_name']; ?>">
                        </div>
                    </div>

                    <div class="col-sm-1">
                        <h5>旺旺号</h5>
                        <div class="form-group">
                            <input type="text" class="form-control" name="wangwang" value="<?php echo $_GET['wangwang']; ?>">
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <h5>任务编码</h5>
                        <div class="form-group">
                            <input type="text" class="form-control" name="verifycode" value="<?php echo $_GET['verifycode']; ?>">
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <h5>开始时间</h5>
                        <div class="form-group">
                            <input type="text" class="form-control" name="starttime" value="<?php echo $_GET['starttime']; ?>" onclick="WdatePicker({el:this,dateFmt:'yyyy-MM-dd',onpicked:null})">
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <h5>结束时间</h5>
                        <div class="form-group">
                            <input type="text" class="form-control" name="lasttime" value="<?php echo $_GET['lasttime']; ?>" onclick="WdatePicker({el:this,dateFmt:'yyyy-MM-dd',onpicked:null})">
                        </div>
                    </div>
                        <div class="col-sm-1">
                            <h5>分页条数</h5>
                            <select class="form-control" name="limit">
                                <option value="" >请选择</option>
                                <option value="30" <?php if($limit==30) echo 'selected="selected"'?> >30</option>
                                <option value="50" <?php if($limit==50)echo 'selected="selected"'?> >50</option>
                                <option value="100" <?php if($limit==100) echo 'selected="selected"'?>>100</option>
                            </select>
                        </div>
                    <?php }?>
                    <div class="col-lg-3">
                        <h5>操作</h5>
                        <div class="form-group">
                            <?php if(empty($_GET['abc'])){?>
                            <button type="submit" class="btn btn-success">搜索</button>
                            <?php }else{?>
                            <button type="button" class="btn btn-success" onclick="javascript:history.back(-1)">返回</button>
                            <?php }?>
                            <a target="_blank" href="/home.php/<?php echo $module; ?>/<?php echo $control; ?>/export.html?<?php echo http_build_query($_GET);?>" class="btn btn-default">导出</a>
                            <!--button type="button" class="btn btn-danger" id="offBtn">下架</button-->
                            合计：<?php echo $total; ?>
                        </div>
                    </div>
                    </form>

                </div>
            </div>
        </div>
      </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr >
                            <td>今日总金额 ：<font> <?php if($_SESSION['role_id']==1 || $_SESSION['role_id']==5){?><?php echo $todypayprice+$todywithout_price; }else{?><?php echo $todypayprice+$todywithin_price; }?></font></td>
                            <td>今日总本金：<font><?php echo $todypayprice; ?></font></td>
                            <td style="<?php if($_SESSION['role_id']==5){ echo 'display:none';}?>">今日总佣金 ：<font><?php echo $todywithin_price; ?></font></td>
                            <?php if($_SESSION['role_id']==1 || $_SESSION['role_id']==5){?>
                            <td>今日总服务费 ：<font><?php echo $todywithout_price; ?></font></td>
                            <?php }?>
                            <td>今日退款总金额 ：<font> <?php if($_SESSION['role_id']==1){?><?php echo $todyrefund+$todyrefundwithout_price; }else{?><?php echo $todyrefund+$todyrefundwithin_price; }?></font></td>
                            <td>今日退款总本金 ：<font><?php echo $todyrefund; ?></font></td>
                            <td style="<?php if($_SESSION['role_id']==5){ echo 'display:none';}?>">今日退款总佣金 ：<font><?php echo $todyrefundwithin_price; ?></font></td>
                            <?php if($_SESSION['role_id']==1){?>
                            <td>今日退款总服务费 ：<font><?php echo $todyrefundwithout_price; ?></font></td>
                            <?php }?>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-body table-responsive">
          <table class="table table-hover">
            <thead>
                <tr>
                    <td><input type="checkbox" name="sel_all" onclick="selectAllid(this)" style="height:15px;"/></td>
                    <td>编码</td>
                    <td>上架时间</td>
                    <td>完成时间</td>
                    <td>店铺</td>
                    <td>主图</td>
                    <td>关键词</td>
                    <td>单价</td>
                    <td>实际下单价</td>
                    <td style="<?php if($_SESSION['role_id']==2){ echo 'display:none';}?>">服务费</td>
                    <td style="<?php if($_SESSION['role_id']==5){ echo 'display:none';}?>">佣金</td>
                    <td>礼品</td>
                    <!--<td>手机号</td>-->
                    <td>旺旺号</td>
                    <td>类型</td>
                    <td>订单状态</td>
                    <td>异常信息</td>
                    <td style="display:none;">所属业务员</td>
                    <td >所属客服</td>
                    <td >评价状态</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody id="tbody">
                <?php
                use app\common\model\ShopModel;
                use app\common\model\TaskModel;
                use app\common\model\TaskdetailModel;
                use app\common\model\OrderModel;
                use app\common\model\MerchantModel;
                foreach($lists as $v){
                $find_task = TaskModel::find("and id=".$v['task_id']);
                $find_taskdetail = TaskdetailModel::find("and id=".$v['taskdetail_id']);
                $find_shop = ShopModel::find("and id=".$v['shop_id']);
                $type_name = OrderModel::enum_type_text($v['type']);
                $task_status_name = TaskModel::enum_status_text($find_task['status']);
                $status_name = '';
                if($v['type']==1){
                    $status_name = OrderModel::enum_status1_text($v['status1']);
                }
                if($v['type']==2){
                    $status_name = OrderModel::enum_status2_text($v['status2']);
                }
                if($v['type']==3){
                    $status_name = OrderModel::enum_status3_text($v['status3']);
                }
                if($v['aid']!=0){
                    $find_server = AdminModel::find("and id=".$v['aid']);
                    $find_server['name']=$find_server['name'];
                }else{
                $find_server['name']='';
                }

                $find_merchant = MerchantModel::find("and id=".$v['merchant_id']);
                if(!empty($find_merchant['aid'])){
                    $find_teacher = AdminModel::find("and id=".$find_merchant['aid']);
                }
                $find_teacher['name'] = isset($find_teacher['name'])?$find_teacher['name']:'';
                 $comment = OrderModel::enum_comment_text($v['iscomment']);
                ?>
                <tr>
                    <td><input name="id" value="<?php echo $v['id']; ?>" type="checkbox" style="height:15px;"></td>
                    <td><?php echo $v['verifycode']; ?></td>
                    <td><?php echo date('Y-m-d H:i:s',$v['intime']);if($_SESSION['role_id']==1 && $v['aidtime']>0){ echo '领取时间('.date('Y-m-d H:i:s',$v['aidtime']).')';}?></td>
                    <td><?php if($v['finishtime']){ echo date('Y-m-d H:i:s',$v['finishtime']);}?></td>
                    <td><?php echo $v['shop_name']; ?><br />(旺旺号：<?php echo $find_shop['wangwang']; ?>)</td>
                    <td><a href="javascript:;" onclick="javascript:imger('<?php echo urlencode($find_task['mainimage']);?>');"><img src="<?php echo $find_task['mainimage']; ?>" style="height:30px;"/></a></td>
                    <td><a href="<?php echo $find_task['goodsurl']; ?>" target="_blank"> <?php echo $find_taskdetail['searchkeywords']; ?></a></td>
                    <td><?php echo $v['price']; ?></td>
                    <td><?php echo $v['payprice']; ?></td>
                    <td style="<?php if($_SESSION['role_id']==2){ echo 'display:none';}?>"><?php echo $v['without_price']; ?></td>
                    <td style="<?php if($_SESSION['role_id']==5){ echo 'display:none';}?>"><?php echo $v['within_price']; ?></td>
                    <td><?php echo $find_task['present']; ?></td>
                    <!--<td><?php echo $v['mobile']; ?></td>-->
                    <!--<td rel="wang">-->
                        <!--<span><?php echo $v['wangwang']; ?></span>-->
                        <!--<input type="text" class="form-control" name="wang" oid="<?php echo $v['id']; ?>" value="<?php echo $v['wangwang']; ?>" style="display:none;">-->
                    <!--</td>-->
                    <td><?php echo $v['wangwang']; ?></td>
                    <td><?php echo $type_name; ?></td>
                    <td><?php echo $task_status_name; ?>-<?php echo $status_name; ?></td>
                    <td><?php echo $v['exceptioninfo']; ?></td>
                    <td style="display:none;"><?php echo $find_teacher['name']; ?></td>
                    <td ><?php echo $find_server['name']; ?></td>
                    <td ><?php echo $comment; ?></td>
                    <td>
                        <?php if($v['type']==1 && in_array($v['status1'],[1,6]) && in_array($find_task['status'],[2,3])){?>
                        <!--a href="javascript:;" onclick="javascript:edit_refundoff(<?php echo $v['id']; ?>);"><span class="label label-danger">下架</span></a-->
                        <?php }if($v['type']==1 && $v['status1']==4){?>
                        <a href="javascript:;" onclick="javascript:edit_yc(<?php echo $v['id']; ?>);"><span class="label label-warning">异常</span></a>
                        <?php }?>
                        <a href="/home.php/<?php echo $module; ?>/<?php echo $control; ?>/view.html?id=<?php echo $v['id']; ?>"><span class="label label-info">详情</span></a>
                    </td>
                </tr>
                <?php }?>
            </tbody>
          </table>
        </div>
          <?php echo $page_show; ?>
      </div>
    </div>
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


<script src="/js/dateCalendar/WdatePicker.js"></script>
<script type="text/javascript">
    function checkallid(){
        var str="";
        $("[name='id']:checked").each(function(){
            str= str + $(this).val()+',';
        });
        return str.substring(0,str.length-1);
    }
    function selectAllid(a){
        $("[name='id']").each(function(){
            return this.checked = a.checked ? "checked": "";
        });
        $("[name='sel_all']").each(function(){
            return this.checked = a.checked ? "checked": "";
        });
    }
    $('#offBtn').bind('click',function(){
        if(checkallid()==''){
            $("#tipBtn").click();$("#tipText").html("请选择订单对象");
            return false;
        }
        if(confirm('确认下架此任务，请联系业务员审核下架!')){
            $.ajax({
                type:'POST',
                cache:false,
                url:'/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_edit_refundoff.html',
                dataType:'text',
                data:"id="+checkallid(),
                success:function(data){
                    if(data=='success'){
                        $("#tipBtn").click();$("#tipText").html("操作成功");
                        setTimeout(function(){
                            window.location.reload();
                        },2000);
                    }else{
                        $("#tipBtn").click();$("#tipText").html(data);
                    }
                }
            });
        }
    });
   function edit_refundoff(id){
        if(confirm('确认下架此任务，请联系业务员审核下架!')){
            $.ajax({
                type:'POST',
                cache:false,
                url:'/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_edit_refundoff.html',
                dataType:'text',
                data:"id="+id,
                success:function(data){
                    if(data=='success'){
                        $("#tipBtn").click();$("#tipText").html("操作成功");
                        setTimeout(function(){
                            window.location.reload();
                        },2000);
                    }else{
                        $("#tipBtn").click();$("#tipText").html(data);
                    }
                }
            });
        }
    }
    function edit_finish(id){
        if(confirm('确定结算吗!')){
            $.ajax({
                type:'POST',
                cache:false,
                url:'/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_edit_finish.html',
                dataType:'text',
                data:"id="+id,
                success:function(data){
                    if(data=='success'){
                        $("#tipBtn").click();$("#tipText").html("操作成功");
                        setTimeout(function(){
                            window.location.reload();
                        },2000);
                    }else{
                        $("#tipBtn").click();
                        $("#tipText").html(data);
                    }
                }
            });
        }
    }
    function edit_yc(id){
        if(confirm('确定发起异常吗!')){
            $.ajax({
                type:'POST',
                cache:false,
                url:'/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_edit_yc.html',
                dataType:'text',
                data:"id="+id,
                success:function(data){
                    if(data=='success'){
                        $("#tipBtn").click();$("#tipText").html("操作成功");
                        setTimeout(function(){
                            window.location.reload();
                        },2000);
                    }else{
                        $("#tipBtn").click();
                        $("#tipText").html(data);
                    }
                }
            });
        }
    }
//    $("[rel=wang]").on('click',function(){
//        $(this).children().first().hide();
//        var wangwang = $(this).children().last().val();
//        $(this).children().last().show().val('').focus().val(wangwang);
//    });
//    $("#tbody").on('blur','[name=wang]',function(){
//        var id = $(this).attr("oid");
//        var wangwang = $(this).val();
//        var thiss = $(this);
//        $.ajax({
//            type:'POST',
//            cache:false,
//            url:'/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_edit_wangwang2.html',
//            dataType:'text',
//            data:{id:id,wangwang:wangwang},
//            success:function(data)
//            {
////                console.log(data);
//                if(data=='success'){
//                    thiss.hide();
//                    thiss.prev().html(wangwang).show();
//                    window.location.reload();
//                }else{
//                    thiss.hide();
//                    thiss.prev().html(wangwang).show();
//                    $("#tipBtn").click();
//                    $("#tipText").html(data);
//                    setTimeout(function(){
//                        window.location.reload();
//                    },2000);
//                }
//            }
//        });
//    });
</script>

</body>
</html>

