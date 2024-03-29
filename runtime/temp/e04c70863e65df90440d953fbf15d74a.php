<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:44:"./application/admin/view/order/bindlist.html";i:1570502020;s:61:"/www/wwwroot/nouser/application/admin/view/common/header.html";i:1568000924;s:59:"/www/wwwroot/nouser/application/admin/view/common/menu.html";i:1562208198;s:61:"/www/wwwroot/nouser/application/admin/view/common/footer.html";i:1561019635;s:61:"/www/wwwroot/nouser/application/admin/view/order/hongbao.html";i:1568256381;}*/ ?>
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
        <h1 class="title"><?php echo $menu_name; ?></h1>
    </div>
<div class="container-padding">
    <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
                <div class="panel-body">
                    <form action="/home.php/<?php echo $module; ?>/<?php echo $control; ?>/bindlist.html" method="get" autocomplete="off">
                    <div class="col-lg-1">
                        <h5>店铺名</h5>
                        <div class="form-group">              
                            <input type="text" class="form-control" name="shop_name" value="<?php echo $_GET['shop_name']; ?>">
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <h5>手机号</h5>
                        <div class="form-group">              
                            <input type="text" class="form-control" name="mobile" value="<?php echo $_GET['mobile']; ?>">
                        </div>
                    </div>
                        <div class="col-lg-1">
                            <h5>绑定情况</h5>
                            <div class="form-group">
                                <select class="form-control" name="status1">
                                    <option value="">请选择</option>
                                    <option value="3" <?php if(3==$_GET['status1']){ echo 'selected';}?>>已绑定旺旺号</option>
                                    <option value="2" <?php if(2==$_GET['status1']){ echo 'selected';}?>>未绑定旺旺号</option>
                                </select>
                            </div>
                        </div>
                        <?php if($_SESSION['role_id']==1){?>
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
                       <?php }?>
                    <div class="col-lg-1">
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
                        <?php if($_SESSION['role_id']==1){?>
                        <div class="col-sm-1">
                            <h5>开始时间</h5>
                            <div class="form-group">
                                <input type="text" class="form-control" name="starttime" value="<?php echo $_GET['starttime']; ?>" onclick="WdatePicker({el:this,dateFmt:'yyyy-MM-dd HH:mm:ss',onpicked:null})">
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <h5>结束时间</h5>
                            <div class="form-group">
                                <input type="text" class="form-control" name="lasttime" value="<?php echo $_GET['lasttime']; ?>" onclick="WdatePicker({el:this,dateFmt:'yyyy-MM-dd HH:mm:ss',onpicked:null})">
                            </div>
                        </div>
                        <?php }?>
                    <div class="col-sm-2">
                        <h5>操作</h5>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">搜索 </button>
                            <button type="button" class="btn btn-primary" id="sub" <?php if(!empty($kefu)){ echo 'style="display:none"';}?>>领取今日单</button>
                            <!--<button type="button" class="btn btn-danger" id="sub2">领取明日单</button>-->
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h5>领取单数<?php echo $count1; ?> &nbsp; 完成单数<?php echo $count2; ?> &nbsp; 异常单数<?php echo $count3; ?>&nbsp;&nbsp; 退款单数<?php echo $tkcount; ?>&nbsp;<span style="color:#f39c12;font-weight: bold"> 今日剩余单数<?php echo $count4; ?> </span>&nbsp;  <?php if($_SESSION['role_id']==1){ echo '当前条件领取单数：'.$zongshu ;}?><span style="color:red;font-weight: bold"> <?php echo date("H");?>点可领取单数：<?php if($hourordernum >=0){echo $hourordernum;}else{echo '';}?> </span>&nbsp; </h5>
                        <div class="form-group"></div>
                    </div>
                    </form>
                    <div class="col-lg-5">
                        <h5>今日排名</h5>
                        <div class="form-group"><?php foreach($paiming as $k=>$pm){ $i=$k+1;echo'TOP'.$i.'--客服:'.$pm['name'].'--单数：'.$pm['ordernum'].'单'."<br />";} ?></div>
                    </div>
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
                    <td>编码</td>
                    <td>订单日期</td>
                    <td>完成时间</td>
                    <td>店铺</td>
                    <td>主图</td>
                    <td>标签词</td>
                    <td>关键词</td>
                    <td>单价</td>
                    <td>实际下单价</td>
                    <td style="<?php if($_SESSION['role_id']==2|| $_SESSION['admin_id']==21){ echo 'display:none';}?>">服务费</td>
                    <td>佣金</td>
                    <td>红包金额</td>
                    <td>规格</td>
                    <td>旺旺号</td>
                    <td>备注</td>
                    <!--<td>类型</td>-->
                    <td>订单状态</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody id="tbody">
                <?php
                use app\common\model\ShopModel;
                use app\common\model\TaskModel;
                use app\common\model\TaskdetailModel;
                use app\common\model\OrderModel;
                use app\common\model\HongbaoModel;
                foreach($lists as $v){
                $find_task = TaskModel::find("and id=".$v['task_id']);
                $find_taskdetail = TaskdetailModel::find("and id=".$v['taskdetail_id']);
                $find_shop = ShopModel::find("and id=".$v['shop_id']);
                $type_name = OrderModel::enum_type_text($v['type']);
                $task_status_name = TaskModel::enum_status_text($find_task['status']);
                $hongbaoarr = HongbaoModel::find("and order_id =".$v['id']);
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
                $worktype_name = TaskModel::enum_worktype_text($find_task['worktype']);
                if(!empty($v['aid'])){
                    $find_server = AdminModel::find("and id=".$v['aid']);
                }
                $find_server['name'] = isset($find_server['name'])?$find_server['name']:'未领取';
                ?>
                <tr <?php if($v['status1']==3){echo 'style="color:#fd0137"';}?>>
                    <td ><span data-toggle="tooltip" title="客服：<?php echo $find_server['name']; ?>"><?php echo $v['verifycode']; ?></span></td>
                    <td><?php echo date('Y-m-d H:i:s',$v['worktime']);?>  <br/> <?php if( $v['aidtime']!=''){ echo '(领取时间：'.date('Y-m-d H:i:s',$v['aidtime']).')';}?></td>
                    <td><?php if($v['finishtime']){ echo date('Y-m-d H:i:s',$v['finishtime']);}?></td>
                    <td><?php echo $v['shop_name']; ?><br/>(旺旺号：<?php echo $find_shop['wangwang']; ?>)</td>
                    <td><a href="javascript:;" onclick="javascript:imger('<?php echo urlencode($find_task['mainimage']);?>');"><img src="<?php echo $find_task['mainimage']; ?>" style="height:30px;"/></a></td>
                    <td style="color:#51b7a3;"><?php echo $find_task['tags']; ?><br /><span class="label label-default" style="cursor: pointer" onclick='copy("<?php echo $find_task['tags']; ?>")'>复制</span></td>
                    <td><a href="<?php echo $find_task['goodsurl']; ?>" target="_blank"><?php echo $find_taskdetail['searchkeywords']; ?></a><br /><span class="label label-default" style="cursor: pointer" onclick='copy("<?php echo $find_taskdetail['searchkeywords']; ?>")'>复制</span></td>
                    <td><?php echo $v['price']; ?></td>
                    <td rel="payprice" <?php if($v['price']!=$v['payprice']){echo 'style="color:#00e765"';}?>>
                        <span data-toggle="tooltip" title="<?php echo $find_taskdetail['priceremark']; ?>"><?php echo $v['payprice']; ?></span>
                        <input type="text"  class="form-control" name="payprice" oid="<?php echo $v['id']; ?>" value="<?php echo $v['payprice']; ?>" style="display:none;">
                    </td>
                    <td style="<?php if($_SESSION['role_id']==2 || $_SESSION['admin_id']==21){ echo 'display:none';}?>"><?php echo $v['without_price']; ?></td>
                    <td rel="within_price">
                        <span><?php echo $v['within_price']; ?></span>
                        <input type="text" class="form-control" name="within_price" oid="<?php echo $v['id']; ?>" value="<?php echo $v['within_price']; ?>" style="display:none;">
                    </td>
                    <td><?php echo $hongbaoarr['orprice']; ?></td>
                    <td><?php if($find_task['isattr']==2){?>任意规格<?php }else{if($find_task['attrcolor']){?>颜色：<?php echo $find_task['attrcolor']; ?>-<?php }if($find_task['attrsize']){?>尺码：<?php echo $find_task['attrsize']; }}?></td>
                    <td rel="wang">
                        <span><?php echo $v['wangwang']; ?></span>
                        <input type="text" class="form-control" name="wang" oid="<?php echo $v['id']; ?>" value="<?php echo $v['wangwang']; ?>" style="display:none;">
                    </td>
                    <td><?php echo $v['remark']; ?></td>
                    <!--<td><?php echo $type_name; ?></td>-->
                    <td><?php echo $task_status_name; ?>-<?php echo $status_name; ?></td>

                    <td>
                        <?php if($_SESSION['role_id']==2){?>
                        <a href="javascript:;" onclick="javascript:edit_aid(<?php echo $v['id']; ?>);"><span class="label label-warning">退回</span></a>
                        <?php }?>
                        <a href="/home.php/<?php echo $module; ?>/<?php echo $control; ?>/view.html?id=<?php echo $v['id']; ?>"><span class="label label-info">详情</span></a>
                        <?php if($v['status1']==3){?>
                        <a href="javascript:;" onclick="javascript:edit_finish(<?php echo $v['id']; ?>,<?php echo $v['payprice']; ?>,<?php echo $v['verifycode']; ?>,<?php echo $v['merchant_id']; ?>,<?php echo $v['within_price']; ?>);"><span class="label label-danger">结算</span></a>
                        <a href="javascript:;" onclick="javascript:edit_dabiao(<?php echo $v['id']; ?>);"><span class="label label-default">打标</span></a>
                        <a href="javascript:;" onclick="javascript:edit_hongbao(<?php echo $v['id']; ?>,<?php echo $v['payprice']+$v['within_price']; ?>);"><span class="label label-success">红包</span></a>
                        <?php }?>
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

<button class="btn btn-default" data-toggle="modal" id="editBtn9" data-target="#editModal9" style="display:none;">红包金额</button>
<div class="modal fade" id="editModal9" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog" style="width:800px;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">红包金额编辑</h4>
        </div>
        <div class="modal-body">
            <div class="row">
            <form action="" class="form-horizontal" method="post" id="dx_form9" autocomplete="off" enctype="multipart/form-data">
            <div class="col-md-12">
                <div class="panel panel-default" style="padding-top:15px;">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">微信号</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="weixin" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label form-label">红包金额</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="orprice" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-3">
                                <input type="hidden" class="form-control" name="order_id" value="">
                                <button type="button" class="btn btn-primary" id="sub9">提交</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
            </div>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
    function edit_hongbao(id,orprice){
        $("[name=order_id]").val(id);
        $("[name=orprice]").val(orprice);
        $.ajax({
            type:'POST',
            cache:false,
            url:'/home.php/admin/admin/find_code.html',
            dataType:'json',
            data:{order_id:id},
            success:function(data)
            {
                if(data.code=='1'){
                    $("#tipBtn").click();
                    $("#tipText").html("<img src='"+data.msg+"' /><p style='color: #d2430f'>请点开二维码-长按图片-识别图中二维码-领取本用</p>");
                }else{
                    $("#editBtn9").click();
                }
            }
        });
    }
    $("#sub9").on('click',function(){
        var reg = /^[0-9]+.?[0-9]*$/; //判断字符串是否为数字 ，判断正整数用/^[1-9]+[0-9]*]*$/
        var orprice = $('[name=orprice]').val();
        if (!reg.test(orprice)) {
            alert("金额请输入纯数字！");
            return;
        }
        if(orprice<1 || orprice>498){
            alert('输入金额必须在1到499之间！');
            return;
        }
        $.ajax({
            type:'POST',
            cache:false,
            url:'/home.php/admin/admin/getcode.html',
            dataType:'json',
            data:$("#dx_form9").serialize(),
            success:function(data)
            {
                if(data.code=='1'){
                    $("#editModal9").hide();
                    $("#tipBtn").click();
                    $("#tipText").html("<img src='"+data.msg+"' /><p style='color: #d2430f'>请点开二维码-长按图片-识别图中二维码-领取本用</p>");
                }else{
                    $("#editModal9").hide();
                    $("#tipBtn").click();
                    $("#tipText").html(data.msg);
                }
            }
        });
    });
</script>

<script src="/js/dateCalendar/WdatePicker.js"></script>
<script type="text/javascript">
    function edit_aid(id){
        if(confirm('确定操作吗!')){
            $.ajax({
                type:'POST',
                cache:false,
                url:'/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_edit_aid.html',
                dataType:'text',
                data:"id="+id,
                success:function(data){
                    if(data=='success'){
                        window.location.reload();
                    }else{
                        $("#tipBtn").click();
                        $("#tipText").html(data);
                    }
                }
            });
        }
    }
    $("#sub").on('click',function(){
        if(confirm('确定操作吗!')) {
            $("#sub").attr("style", 'display:none');
            setTimeout(function () {
                $.ajax({
                    type: 'POST',
                    cache: false,
                    async: false,
                    url: '/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_pull.html',
                    dataType: 'text',
                    data: "cid=1",
                    success: function (data) {
//                $("#tipBtn").click();
//                $("#tipText").html(data);
                        alert(data);
//                setTimeout(function(){
                    window.location.reload();
//                },2000);
                    }
                });
            }, 1000);
        }
    });
    /*
//    $("#sub2").on('click',function(){
//        $.ajax({
//            type:'POST',
//            cache:false,
//            url:'/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_pull2.html',
//            dataType:'text',
//            data: "cid=1",
//            success:function(data)
//            {
//                $("#tipBtn").click();$("#tipText").html(data);
//                setTimeout(function(){
//                    window.location.reload();
//                },2000);
//            }
//        });
//    });
    */
    $("[rel=wang]").on('click',function(){
        $(this).children().first().hide();
        var wangwang = $(this).children().last().val();
        $(this).children().last().show().val('').focus().val(wangwang);
    });
    $("#tbody").on('blur','[name=wang]',function(){
        var id = $(this).attr("oid");
        var wangwang = $(this).val();
        var thiss = $(this);
        $.ajax({
            type:'POST',
            cache:false,
            url:'/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_edit_wangwang.html',
            dataType:'text',
            data:{id:id,wangwang:wangwang},
            success:function(data)
            {
//                console.log(data);
                if(data=='success'){
                    thiss.hide();
                    thiss.prev().html(wangwang).show();
                        window.location.reload();
                }else{
                    thiss.hide();
                    thiss.prev().html(wangwang).show();
                    $("#tipBtn").click();
                    $("#tipText").html(data);
                    setTimeout(function(){
                        window.location.reload();
                    },2000);
                }
            }
        });
    });
    $("[rel=payprice]").on('click',function(){
        $(this).children().first().hide();
        var payprice = $(this).children().last().val();
        $(this).children().last().show().val('').focus().val(payprice);
    });
    $("#tbody").on('blur','[name=payprice]',function(){
        var id = $(this).attr("oid");
        var payprice = $(this).val();
        var thiss = $(this);
        $.ajax({
            type:'POST',
            cache:false,
            url:'/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_edit_payprice.html',
            dataType:'text',
            data:"id="+id+"&payprice="+payprice,
            success:function(data)
            {
//                console.log(data);
                if(data=='success'){
                    thiss.hide();
                    thiss.prev().html(payprice).show();
                        window.location.reload();
                }else{
                    thiss.hide();
                    thiss.prev().html(payprice).show();
                    $("#tipBtn").click();
                    $("#tipText").html(data);
                    setTimeout(function(){
                        window.location.reload();
                    },2000);
                }
            }
        });
    });
    $("[rel=within_price]").on('click',function(){
        $(this).children().first().hide();
        var within_price = $(this).children().last().val();
        $(this).children().last().show().val('').focus().val(within_price);
    });
    $("#tbody").on('blur','[name=within_price]',function(){
        var id = $(this).attr("oid");
        var within_price = $(this).val();
        var thiss = $(this);
        $.ajax({
            type:'POST',
            cache:false,
            url:'/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_edit_within_price.html',
            dataType:'text',
            data:"id="+id+"&within_price="+within_price,
            success:function(data)
            {
//                console.log(data);
                if(data=='success'){
                    thiss.hide();
                    thiss.prev().html(within_price).show();
                        window.location.reload();
                }else{
                    thiss.hide();
                    thiss.prev().html(within_price).show();
                    $("#tipBtn").click();
                    $("#tipText").html(data);
                    setTimeout(function(){
                        window.location.reload();
                    },2000);
                }
            }
        });
    });
   function edit_dabiao(id) {
        $.ajax({
            type:'POST',
            cache:false,
            url:'/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_edit_dabiao.html',
            dataType:'text',
            data:"id="+id,
            success:function(data)
            {
//                console.log(data);
                if(data=='success'){
                    $("#tipBtn").click();
                    $("#tipText").html('打标成功');
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
    function copy(message) {
        var input = document.createElement("input");
        input.value = message;
        document.body.appendChild(input);
        input.select();
        input.setSelectionRange(0, input.value.length), document.execCommand('Copy');
        document.body.removeChild(input);
    }
    function edit_finish(id,payprice,verifycode,merchant_id,within_price){
        var zs=within_price+payprice;
        if(confirm("订单总费用："+zs+"元（本金+佣金），确定结算吗!")) {
            if ( payprice <= 0) {
                $("#tipBtn").click();
                $("#tipText").html("请填写实际下单价！");
               return ;
            }
            var toid=merchant_id;
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: '/home.php/<?php echo $module; ?>/<?php echo $control; ?>/act_edit_finish.html',
                    dataType: 'text',
                    data: "id=" + id,
                    success: function (data) {
                        if (data == 'success') {
                            $("#tipBtn").click();
                            $("#tipText").html("结算成功");
                            //发送数据
                                var text="任务("+verifycode+")已下单结算完成";
                                var message = '{"type":"say","fromid":"'+fromid+'","toid":"'+toid+'","data":"'+text+'"}';
                                ws.send(message);
                            setTimeout(function () {
                                window.location.reload();
                            }, 2000);
                        } else {
                            $("#tipBtn").click();
                            $("#tipText").html(data);
                        }
                    }
                });
            }

    }
    var fromid=<?php echo $_SESSION['admin_id']; ?>;
    var toid=getUrlParam('toid');
    //创建websocket 对象
    var ws = new WebSocket("ws://127.0.0.1:8282");
    //从服务器收到消息时，该监听器将被调用
    ws.onmessage = function(e){
        var message = eval("("+e.data+")");
        switch (message.type){
            case 'init':
                var bind ='{"type":"bind","fromid":"'+fromid+'"}';
                ws.send(bind);
                return;
            case 'text':
                console.log(message.data);
                return;
            case 'save':
                save_message(message);
                return;
        }

    }

    //当连接关闭时，则触发
    ws.onclose = function(e) {
        console.log(e);
    };
    /**
     * GET取值,用于接受？传值 可接收汉字 推荐使用
     */
    function getUrlParam(name){
        // 用该属性获取页面 URL 地址从问号 (?) 开始的 URL（查询部分）
        var url = window.location.search;
        // 正则筛选地址栏
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        // 匹配目标参数
        var result = url.substr(1).match(reg);
        //返回参数值
        return result ? decodeURIComponent(result[2]) : null;
    }
    function save_message(message) {
        $.post(
                "/home.php/api/chat/save_message.html",
                message,
                function () {
                },'json'
        )
    }
    window.onload = function() {
//禁止F5刷新
        document.onkeydown = function (e) {
            if (e.keyCode === 116) {
                return false;
            }
        };
    }
</script>
</body>
</html>

