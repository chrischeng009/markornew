<?php
namespace app\common\model;

use think\Model;
use think\Db;

class TaskModel extends Model{
    public static function tbname(){
        return db('task');
    }
    //添加一条记录
    public static function add($data){
        return self::tbname()->insertGetId($data);
    }
    //查询一条记录
    public static function find($where=''){
        return self::tbname()->where("1=1 $where")->order('id desc')->find();
    }
    //查询多条记录
    public static function select($where='',$ascDesc='desc'){
        return self::tbname()->where("1=1 $where")->order("id $ascDesc")->select();
    }
    //删除一条记录或多条记录
    public static function del($where){
        return self::tbname()->where("1=1 $where")->delete();
    }
    //修改一条记录
    public static function edit($where,$data){
        return self::tbname()->where("1=1 $where")->update($data);
    }
    //统计表的记录条数
    public static function count($where=''){
        return self::tbname()->where("1=1 $where")->count();
    }
    public static function enum_cat_arr()
    {
        $arr[1] = '女装/男装/内衣';
        $arr[2] = '鞋装/箱包/配件';
        $arr[3] = '童装玩具/孕产/用品';
        $arr[4] = '家电/数码/手机';
        $arr[5] = '美妆/洗护/保健品';
        $arr[6] = '珠宝/眼镜/手表';
        $arr[7] = '运动/户外/乐器';
        $arr[8] = '游戏/动漫/影视';
        $arr[9] = '美食/生鲜/零食';
        $arr[10] = '鲜花/宠物/农资';
        $arr[11] = '房产/装修/建材';
        $arr[12] = '家具/家饰/家纺';
        $arr[13] = '汽车/二手车/用品';
        $arr[14] = '办公/DIY/五金电子';
        $arr[15] = '百货/餐厨/家庭保健';
        $arr[16] = '学习/卡券/本地服务';
        return $arr;
    }
    public static function enum_cat_text($key)
    {
        $arr = self::enum_cat_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    const enumstatus1 = 1;
    const enumstatus2 = 2;
    const enumstatus3 = 3;
    const enumstatus4 = 4;
    const enumstatus5 = 5;
    const enumstatus6 = 6;
    const enumstatus7 = 7;
    public static function enum_status_arr()
    {
        $arr[1] = '待上架';//待付款
        $arr[2] = '待审核';//已付款
        $arr[3] = '已上架';//已付款
        $arr[4] = '已拒绝';//已退款
        //$arr[5] = '进行中';//已付款
        $arr[6] = '已完结';//已付款
        $arr[7] = '已下架';//已付款
        return $arr;
    }
    public static function enum_status_text($key)
    {
        $arr = self::enum_status_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }

    /*
     * 当天的任务数和订单数
     */
    public static function get_day_count($merchant_id)
    {
        $arr = [];
        $arr['count'] = 0;
        $arr['count_order'] = 0;

        $day = date("Y-m-d");
        $start_time = strtotime($day." 00:00:00");
        $last_time = strtotime($day." 23:59:59");
        $lists = self::select("and merchant_id={$merchant_id} and intime>={$start_time} and intime<={$last_time}");
        if(empty($lists)){
            return $arr;
        }

        //取数组里的一个字段组成新数组 再把数组转成字符串
        $strId = implode(',',array_column($lists,'id'));

        $count = count($lists);
        $count_order = OrderModel::count("and task_id in({$strId})");

        $arr['count'] = $count;
        $arr['count_order'] = $count_order;
        return $arr;
    }
    public static function get_tasksn()
    {
        $day = date("Y-m-d");
        $start_time = strtotime($day." 00:00:00");
        $last_time = strtotime($day." 23:59:59");
        $count = self::count("and intime>={$start_time} and intime<={$last_time}");
        $num = 100000000+$count+1;
        $str = (string)$num;
        $str = date("Ymd").substr($str,1,8);//字符串截取，从第二个字符开始截取8位，字符共9位
        return $str;
    }
    const enumworktype1 = 1;
    const enumworktype2 = 2;
    public static function enum_worktype_arr()
    {
        $arr[1] = '标签今日单';
        $arr[2] = '标签明日单';
        return $arr;
    }
    public static function enum_worktype_text($key)
    {
        $arr = self::enum_worktype_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    public static function enum_ishuabei_arr()
    {
        $arr[1] = '不允许';
        $arr[2] = '允许';
        return $arr;
    }
    public static function enum_ishuabei_text($key)
    {
        $arr = self::enum_ishuabei_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    public static function enum_iscredit_arr()
    {
        $arr[1] = '不允许';
        $arr[2] = '允许';
        return $arr;
    } 
    public static function enum_istime_arr()
    {
        $arr[1] = '实时化AI自动派单';
        $arr[2] = '人工设置时间派单';
        return $arr;
    }
    public static function enum_istag_arr()
    {
        $arr[1] = '强化宝贝类目标签';
        $arr[2] = '蹭对手竟品标签';
        return $arr;
    }
    public static function enum_iscredit_text($key)
    {
        $arr = self::enum_iscredit_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    public static function do_price_html($params){
        Db::startTrans();
        try{
            $merchant_id = $params['merchant_id'];
            //商家最大订单截止价
            $without_maxend = MerchantwithoutModel::get_maxend($merchant_id);
            //粉丝最大订单截止价
            $within_maxend = WithinModel::get_maxend();

            $money = 0;//本金总额
            $num = 0;//总数量
            $without_money = 0;//服务费总额
            foreach($params['searchkeywords'] as $k=>$v){
                $detail = [];
                $detail['price'] = (float)$params['price'][$k];
                $without_price = MerchantwithoutModel::get_price($merchant_id,$detail['price']);
                $detail['num'] = (int)$params['num'][$k];
                $detail['without_price'] = $without_price;
                if($detail['price']<=0){
                    exception("下单实际价格不能小于0！");
                }
                if($detail['price']>$without_maxend){
                    exception("下单实际价格不能大于{$without_maxend}！");
                }
                if($detail['price']>$within_maxend){
                    exception("下单实际价格不能大于{$within_maxend}！");
                }
                if($detail['num']<=0){
                    exception("单数不能小于0！");
                }
                $money = $money+($detail['price']*$detail['num']);
                $num = $num+$detail['num'];
                $without_money = $without_money+($without_price*$detail['num']);
            }

            $total_money = $money+$without_money;

            //提交
            Db::commit();
            //返回
            return "本次任务总单数{$num}单，发布花费本金{$money}+服务费{$without_money}={$total_money}";
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            return $e->getMessage();//把异常消息捕获并抛出
        }
    }
    public static function do_add($params){
        Db::startTrans();
        try{
            $merchant_id = $params['merchant_id'];
            $find_merchant = MerchantModel::find("and id={$merchant_id}");
            if($find_merchant['status']!=MerchantModel::enumStatus2){
                $merchant_status_name = MerchantModel::enum_status_text($find_merchant['status']);
                exception("您的帐号状态为{$merchant_status_name}，请联系导师。");
            }
            $dayArr = self::get_day_count($merchant_id);
            $find_shop = ShopModel::find("and id={$params['shop_id']}");
            //商家最大订单截止价
            $without_maxend = MerchantwithoutModel::get_maxend($merchant_id);
            //粉丝最大订单截止价
            $within_maxend = WithinModel::get_maxend();

            $data = [];
            //今日订单
            if($params['worktype']==self::enumworktype1){
                 $data['worktime'] = time();
            }elseif($params['worktype']==self::enumworktype2){
                $data['worktime'] = time()+86400;
            } else{
                exception("暂时只开通今日单、明日单！");
            }
            // 判断当前是否晚上8点之后
            $time1=time();
            if($time1 >= strtotime(date("Y-m-d") . " 20:00:00")){
                $params['worktype']=self::enumworktype2;
            }

            //拍下规格
            if($params['isattr']==1){
                if(empty($params['attrcolor']) && empty($params['attrsize'])){
                    exception("非任意规格时颜色和尺寸不能同时为空！");
                }
                $data['attrcolor'] = $params['attrcolor'];
                $data['attrsize'] = $params['attrsize'];
            }

            if($params['istag']==2){
                $data['istag'] = $params['istag'];
                $data['tagkeyworks'] = $params['tagkeyworks'];
                $data['tagurl'] = $params['tagurl'];

            }
            
            $data['merchant_id'] = $merchant_id;
            $data['shop_id'] = $params['shop_id'];
            $data['shop_name'] = $find_shop['name'];
            $data['worktype'] = $params['worktype'];
            $data['cat'] = $params['cat'];
            $data['tags'] = $params['tags'];
            $data['goodstitle'] = $params['goodstitle'];
            $data['goodsurl'] = $params['goodsurl'];
            $data['mainimage'] = $params['mainimage'];
            $data['ishuabei'] = $params['ishuabei'];
            $data['iscredit'] = $params['iscredit'];
            $data['present'] = $params['present'];
            $data['isattr'] = $params['isattr'];
            $data['istime'] = $params['istime'];
            $data['tasksn'] = self::get_tasksn();
            $data['status'] = 1;//待付款
            $data['intime'] = time();
            $data['uptime'] = time();
            //task
            $task_id = self::add($data);

            $money = 0;//本金总额
            $num = 0;//总数量
            $without_money = 0;//服务费总额
            foreach($params['searchkeywords'] as $k=>$v){
                $detail = [];
                $detail['task_id'] = $task_id;
                $detail['searchkeywords'] = $params['searchkeywords'][$k];
                $detail['price'] = (float)$params['price'][$k];
                $detail['priceremark'] = !empty($params['priceremark'][$k])?$params['priceremark'][$k]:'';
                $without_price = MerchantwithoutModel::get_price($merchant_id,$detail['price']);
                $detail['num'] = (int)$params['num'][$k];
                $detail['without_price'] = $without_price;
                $detail['istalk'] = !empty($params['istalk'][$k])?2:1;
                $detail['isparity'] = !empty($params['isparity'][$k])?2:1;
                $detail['iscart'] = !empty($params['iscart'][$k])?2:1;
                $detail['iscollect'] = !empty($params['iscollect'][$k])?2:1;
                $detail['isfocus'] = !empty($params['isfocus'][$k])?2:1;
                $detail['isbrowseshop'] = !empty($params['isbrowseshop'][$k])?2:1;
                $detail['isbrowseinfo'] = !empty($params['isbrowseinfo'][$k])?2:1;
                $detail['remark'] = $params['remark'][$k];
                if($params['istime']==2){
                $detail['time8'] = $params['time8'][$k];
                $detail['time9'] = $params['time9'][$k];
                $detail['time10'] = $params['time10'][$k];
                $detail['time11'] = $params['time11'][$k];
                $detail['time12'] = $params['time12'][$k];
                $detail['time13'] = $params['time13'][$k];
                $detail['time14'] = $params['time14'][$k];
                $detail['time15'] = $params['time15'][$k];
                $detail['time16'] = $params['time16'][$k];
                $detail['time17'] = $params['time17'][$k];
                $detail['time18'] = $params['time18'][$k];
                $detail['time19'] = $params['time19'][$k];
                $detail['time20'] = $params['time20'][$k];
                $detail['time21'] = $params['time21'][$k];
            }
                if(empty($detail['searchkeywords'])){
                    exception("搜索关键词不能为空！");
                }
                if($detail['price']<=0){
                    exception("下单实际价格不能小于0！");
                }
                if($detail['price']>$without_maxend){
                    exception("下单实际价格不能大于{$without_maxend}！");
                }
                if($detail['price']>$within_maxend){
                    exception("下单实际价格不能大于{$within_maxend}！");
                }
                if($detail['num']<=0){
                    exception("单数不能小于0！");
                }
                $money = $money+($detail['price']*$detail['num']);
                $num = $num+$detail['num'];
                $without_money = $without_money+($without_price*$detail['num']);

                $detail['intime'] = time();
                $detail['uptime'] = time();
                //taskdetail
                $taskdetail_id = TaskdetailModel::add($detail);
                //order
                for($i=1;$i<=$detail['num'];$i++){
                    $orderdata = [];
                    $orderdata['merchant_id'] = $merchant_id;
                    $orderdata['shop_id'] = $params['shop_id'];
                    $orderdata['shop_name'] = $find_shop['name'];
                    $orderdata['task_id'] = $task_id;
                    $orderdata['taskdetail_id'] = $taskdetail_id;
                    $orderdata['goodstitle'] = $data['goodstitle'];
                    $orderdata['mainimage'] = $data['mainimage'];
                    $orderdata['verifycode'] = OrderModel::get_verifycode();
                    $orderdata['price'] = $detail['price'];
                    $orderdata['priceremark'] = $detail['priceremark'];
                    $orderdata['without_price'] = $without_price;
                    $orderdata['within_price'] = WithinModel::get_price($detail['price']);
                    $orderdata['type'] = 1;//正常订单
                    $orderdata['status1'] = 1;//待领取
                    $orderdata['worktype'] = $data['worktype'];
                    $orderdata['worktime'] = $data['worktime'];
                    $orderdata['remark'] = $detail['remark'];
                    $orderdata['intime'] = time();
                    $orderdata['uptime'] = time();
                    OrderModel::add($orderdata);
                }
            }

            if($dayArr['count_order']+$num>$find_merchant['worknum']){
                exception("您的每日单数为{$find_merchant['worknum']}单，已上限！");
            }
            $data['money'] = $money;
            $data['num'] = $num;
            $data['without_money'] = $without_money;
            $data['total_money'] = $money+$without_money;
            self::edit("and id={$task_id}",$data);
        if($params['add_type'] && $params['add_type']=='fz') {
            //提交
            Db::commit();
            //返回
            return "success";

        }else {
            //发布邮件
            $time = date("Y-m-d H:i:s");
            $address = "2804364558@qq.com";
            $title = '商家端任务审核';
            $url = $_SERVER['SERVER_NAME'] . "/home.php/admin/task/act_editurl.html?id={$task_id}";
            $content = "主题：{$title}<br /> 
            任务编号：{$data['tasksn']},任务本金：{$data['money']}/任务单数：{$data['num']}/任务服务费：{$data['without_money']}，申请时间：{$time}
            <br />点击链接审核即可通过：{$url}";
            send_mail($address, $title, $content);
//             $wchat = new \wechat\WechatOauth();
//            $wchat->sendmessage('oHJid1S-dWytkhE1ItYOyMZ8z3eE',$content);
//            $wchat->sendmessage('oHJid1Rd8X_DV8vdnEBMxeb1dK1I',$content);
//            $wchat->sendmessage('oHJid1esIom5qfrGcCYMGb5Y4xLA',$content);
            //直接付款20190309
            if (self::do_edit_status2($task_id) == 'success') {
                //提交
                Db::commit();
                //返回
                return "success";
            } else {
                //提交
                Db::commit();
                //返回
                return "error";
            }
        }
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            return $e->getMessage();//把异常消息捕获并抛出
        }
    }
    //编辑任务信息
    public static function do_act_edit($params){
        Db::startTrans();
        try{
            $merchant_id = $params['merchant_id'];
            $find_merchant = MerchantModel::find("and id={$merchant_id}");
            if($find_merchant['status']!=MerchantModel::enumStatus2){
                $merchant_status_name = MerchantModel::enum_status_text($find_merchant['status']);
                exception("您的帐号状态为{$merchant_status_name}，请联系导师。");
            }
            $dayArr = self::get_day_count($merchant_id);
            $find_shop = ShopModel::find("and id={$params['shop_id']}");
            //商家最大订单截止价
            $without_maxend = MerchantwithoutModel::get_maxend($merchant_id);
            //粉丝最大订单截止价
            $within_maxend = WithinModel::get_maxend();

            $data = [];
            //今日订单
            if($params['worktype']==self::enumworktype1){
                $data['worktime'] = time();
            }elseif($params['worktype']==self::enumworktype2){
                $data['worktime'] = time()+86400;
            } else{
                exception("暂时只开通今日单、明日单！");
            }
            // 判断当前是否晚上7点之后
            $time1=time();
            if($time1 >= strtotime(date("Y-m-d") . " 19:00:00")){
                $params['worktype']=self::enumworktype2;
            }

            //拍下规格
            if($params['isattr']==1){
                if(empty($params['attrcolor']) && empty($params['attrsize'])){
                    exception("非任意规格时颜色和尺寸不能同时为空！");
                }
                $data['attrcolor'] = $params['attrcolor'];
                $data['attrsize'] = $params['attrsize'];
            }
            $task_id=$data['id'] = $params['task_id'];
            $data['shop_id'] = $params['shop_id'];
            $data['shop_name'] = $find_shop['name'];
            $data['worktype'] = $params['worktype'];
            $data['cat'] = $params['cat'];
            $data['tags'] = $params['tags'];
            $data['goodstitle'] = $params['goodstitle'];
            $data['goodsurl'] = $params['goodsurl'];
            $data['mainimage'] = $params['mainimage'];
            $data['ishuabei'] = $params['ishuabei'];
            $data['iscredit'] = $params['iscredit'];
            $data['present'] = $params['present'];
            $data['isattr'] = $params['isattr'];
            $data['istime'] = $params['istime'];
            $data['istag'] = $params['istag'];
            $data['tagurl'] = $params['tagurl'];
            $data['tagkeyworks'] = $params['tagkeyworks'];
            // 先删除当前任务的详情
            $all=TaskdetailModel::del(" and task_id={$params['task_id']}");
            //先删除当前任务所属订单
            OrderModel::del(" and task_id={$params['task_id']}");
            $money = 0;//本金总额
            $num = 0;//总数量
            $without_money = 0;//服务费总额
            foreach($params['searchkeywords'] as $k=>$v){
                $detail = [];
                $detail['task_id'] = $task_id;
                $detail['searchkeywords'] = $params['searchkeywords'][$k];
                $detail['price'] = (float)$params['price'][$k];
                $detail['priceremark'] = !empty($params['priceremark'][$k])?$params['priceremark'][$k]:'';
                $without_price = MerchantwithoutModel::get_price($merchant_id,$detail['price']);
                $detail['num'] = (int)$params['num'][$k];
                $detail['without_price'] = $without_price;
                $detail['istalk'] = !empty($params['istalk'][$k])?2:1;
                $detail['isparity'] = !empty($params['isparity'][$k])?2:1;
                $detail['iscart'] = !empty($params['iscart'][$k])?2:1;
                $detail['iscollect'] = !empty($params['iscollect'][$k])?2:1;
                $detail['isfocus'] = !empty($params['isfocus'][$k])?2:1;
                $detail['isbrowseshop'] = !empty($params['isbrowseshop'][$k])?2:1;
                $detail['isbrowseinfo'] = !empty($params['isbrowseinfo'][$k])?2:1;
                $detail['remark'] = $params['remark'][$k];
                if($params['istime']==2){
                $detail['time8'] = $params['time8'][$k];
                $detail['time9'] = $params['time9'][$k];
                $detail['time10'] = $params['time10'][$k];
                $detail['time11'] = $params['time11'][$k];
                $detail['time12'] = $params['time12'][$k];
                $detail['time13'] = $params['time13'][$k];
                $detail['time14'] = $params['time14'][$k];
                $detail['time15'] = $params['time15'][$k];
                $detail['time16'] = $params['time16'][$k];
                $detail['time17'] = $params['time17'][$k];
                $detail['time18'] = $params['time18'][$k];
                $detail['time19'] = $params['time19'][$k];
                $detail['time20'] = $params['time20'][$k];
                $detail['time21'] = $params['time21'][$k];
            }

                if(empty($detail['searchkeywords'])){
                    exception("搜索关键词不能为空！");
                }
                if($detail['price']<=0){
                    exception("下单实际价格不能小于0！");
                }
                if($detail['price']>$without_maxend){
                    exception("下单实际价格不能大于{$without_maxend}！");
                }
                if($detail['price']>$within_maxend){
                    exception("下单实际价格不能大于{$within_maxend}！");
                }
                if($detail['num']<=0){
                    exception("单数不能小于0！");
                }
                $money = $money+($detail['price']*$detail['num']);
                $num = $num+$detail['num'];
                $without_money = $without_money+($without_price*$detail['num']);

                $detail['intime'] = time();
                $detail['uptime'] = time();
                //taskdetail
                $taskdetail_id = TaskdetailModel::add($detail);
                //order
                for($i=1;$i<=$detail['num'];$i++){
                    $orderdata = [];
                    $orderdata['merchant_id'] = $merchant_id;
                    $orderdata['shop_id'] = $params['shop_id'];
                    $orderdata['shop_name'] = $find_shop['name'];
                    $orderdata['task_id'] = $task_id;
                    $orderdata['taskdetail_id'] = $taskdetail_id;
                    $orderdata['goodstitle'] = $data['goodstitle'];
                    $orderdata['mainimage'] = $data['mainimage'];
                    $orderdata['verifycode'] = OrderModel::get_verifycode();
                    $orderdata['price'] = $detail['price'];
                    $orderdata['priceremark'] = $detail['priceremark'];
                    $orderdata['without_price'] = $without_price;
                    $orderdata['within_price'] = WithinModel::get_price($detail['price']);
                    $orderdata['type'] = 1;//正常订单
                    $orderdata['status1'] = 1;//待领取
                    $orderdata['worktype'] = $data['worktype'];
                    $orderdata['worktime'] = $data['worktime'];
                    $orderdata['remark'] = $detail['remark'];
                    $orderdata['intime'] = time();
                    $orderdata['uptime'] = time();
                    OrderModel::add($orderdata);
                }
            }
           
            if($dayArr['count_order']+$num>$find_merchant['worknum']){
                exception("您的每日单数为{$find_merchant['worknum']}单，已上限！");
            }
           

            $data['money'] = $money;
            $data['num'] = $num;
            $data['without_money'] = $without_money;
            $data['total_money'] = $money+$without_money;
            self::edit("and id={$task_id}",$data);
                //提交
                Db::commit();
                //返回
                return "success";
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            return $e->getMessage();//把异常消息捕获并抛出
        }
    }    
    public static function do_edit_status2($id){
        Db::startTrans();
        try{
            $find = self::find("and id={$id}");
            $find_merchant = MerchantModel::find("and id={$find['merchant_id']}");
            //费用扣除
            if($find['total_money'] > $find_merchant['money']){
                exception("您的余额不足，请充值。");
            }
            //余额变动
            $mdata['money'] = $find_merchant['money']-$find['total_money'];
            $mdata['freeze_money'] = $find_merchant['freeze_money']+$find['total_money'];
            MerchantModel::edit("and id={$find['merchant_id']}",$mdata);

            //记录商家财务明细
            $ldata = [];
            $ldata['algorithm'] = 2;//减法
            $ldata['type'] = 5;//任务
            $ldata['merchant_id'] = $find['merchant_id'];//商家Id
            $ldata['shop_id'] = $find['shop_id'];//店铺Id
            $ldata['startmoney'] = $find_merchant['money'];//操作前余额
            $ldata['money'] = $find['total_money'];//变动金额
            $ldata['endmoney'] = $find_merchant['money']-$find['total_money'];//操作后金额
            $ldata['order_code'] = $find['tasksn'];//订单号
            $ldata['tradeno'] = "";//流水号
            $ldata['remark'] = "任务Id：{$find['tasksn']}";//详情
            $ldata['intime'] = time();
            $ldata['uptime'] = time();
            MerchantmlogsModel::add($ldata);

            $data = [];
            $data['status'] = 2;//待审核-已付款
            $data['paytime'] = time();
            $data['uptime'] = time();
            self::edit("and id={$id}",$data);

            //发布邮件
            $time = date("Y-m-d H:i:s");
            $address = "2804364558@qq.com";
            $title = '商家端任务审核';
            $url = $_SERVER['SERVER_NAME'] . "/home.php/admin/task/act_editurl.html?id={$id}";
            $content = "主题：{$title}<br /> 
            任务编号：{$find['tasksn']},任务本金：{$find['money']}/任务单数：{$find['num']}/任务服务费：{$find['without_money']}，申请时间：{$time}
            <br />点击链接审核即可通过：{$url}";
            send_mail($address, $title, $content);
//             $wchat = new \wechat\WechatOauth();
//            $wchat->sendmessage('oHJid1S-dWytkhE1ItYOyMZ8z3eE',$content);
//            $wchat->sendmessage('oHJid1Rd8X_DV8vdnEBMxeb1dK1I',$content);
//            $wchat->sendmessage('oHJid1esIom5qfrGcCYMGb5Y4xLA',$content);
            Db::commit();
            //返回
            return "success";
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            return $e->getMessage();//把异常消息捕获并抛出
        }
    }
    //通过
    public static function do_edit_status3($id){
        Db::startTrans();
        try{
            $strId = dostr($id);
            $arrId = explode(',',$strId);
            foreach($arrId as $id) {
                $find = self::find("and id={$id}");
                if ($find['status'] != self::enumstatus2) {
                    exception("状态非已付款");
                }
                    $find_merchant = MerchantModel::find("and id={$find['merchant_id']}");
                    //余额变动
                    $mdata['freeze_money'] = $find_merchant['freeze_money'] - $find['total_money'];
                    MerchantModel::edit("and id={$find['merchant_id']}", $mdata);

                    $data = [];
                    $data['status'] = self::enumstatus3;//通过
                    $data['uptime'] = time();
                    if (date("i") > 30) {
                        $data['worktime'] = time() + 3600;
                    } else {
                        $data['worktime'] = time();
                    }
                    self::edit("and id={$id}", $data);
                    Db::commit();
                }
            //返回
            return "success";
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            return $e->getMessage();//把异常消息捕获并抛出
        }
    }
    //  明日单任务今天紧急发布
    public static function do_edit_shelf($id){
        Db::startTrans();
        try{
            $find = self::find("and id={$id}");
            if($find['status']!=self::enumstatus3){
                exception("状态非已上架");
            }
            $data = [];
            $data['worktype'] = self::enumworktype1;//今日发布
            $data['uptime'] = time();
            $data['worktime'] = time();
            self::edit("and id={$id}",$data);
            $data['intime'] = time();
            OrderModel::edit("and task_id={$id}",$data);
            Db::commit();
            //返回
            return "success";
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            return $e->getMessage();//把异常消息捕获并抛出
        }
    }
    //未通过
    public static function do_edit_status4($id){
        Db::startTrans();
        try{
            $find = self::find("and id={$id}");
            if($find['status']!=self::enumstatus2){
                exception("状态非已付款");
            }
            $find_merchant = MerchantModel::find("and id={$find['merchant_id']}");

            //余额变动
            $mdata['money'] = $find_merchant['money']+$find['total_money'];
            $mdata['freeze_money'] = $find_merchant['freeze_money']-$find['total_money'];
            MerchantModel::edit("and id={$find['merchant_id']}",$mdata);

            //记录商家财务明细
            $ldata = [];
            $ldata['algorithm'] = 1;//加法
            $ldata['type'] = 6;//任务退回
            $ldata['merchant_id'] = $find['merchant_id'];//商家Id
            $ldata['shop_id'] = $find['shop_id'];//店铺Id
            $ldata['startmoney'] = $find_merchant['money'];//操作前余额
            $ldata['money'] = $find['total_money'];//变动金额
            $ldata['endmoney'] = $find_merchant['money']+$find['total_money'];//操作后金额
            $ldata['order_code'] = $find['tasksn'];//订单号
            $ldata['tradeno'] = "";//流水号
            $ldata['remark'] = "任务Id：{$find['tasksn']}";//详情
            $ldata['intime'] = time();
            $ldata['uptime'] = time();
            MerchantmlogsModel::add($ldata);

            $data = [];
            $data['status'] = self::enumstatus4;//未通过
            $data['uptime'] = time();
            self::edit("and id={$id}",$data);

            Db::commit();
            //返回
            return "success";
        }catch(\Exception $e){
            Db::rollback();
            //exception($e);//直接报调试模式错误信息
            return $e->getMessage();//把异常消息捕获并抛出
        }
    }
    































}
?>