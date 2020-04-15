<?php
namespace app\common\model;

use think\Model;
use think\Db;

class UserModel extends Model{
    public static function tbname(){
        return db('user');
    }
    //添加一条记录
    public static function add($data){
        return self::tbname()->insertGetId($data);
    }
    //查询一条记录
    public static function find($where=''){
        return self::tbname()->where("1=1 $where")->find();
    }
    //查询多条记录
    public static function select($where=''){
        return self::tbname()->where("1=1 $where")->order('id desc')->select();
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
    /*
     * 帐号审核
     */
    const enumStatus1 = 1;
    const enumStatus2 = 2;
    const enumStatus3 = 3;
    const enumStatus4 = 4;
    public static function enum_status_arr()
    {
        $arr[1] = '未完善';
        $arr[2] = '待审核';
        $arr[3] = '已审核';
        $arr[4] = '未通过';
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
     * 实名认证
     */
    const enumStatus2_1 = 1;
    const enumStatus2_2 = 2;
    const enumStatus2_3 = 3;
    const enumStatus2_4 = 4;
    public static function enum_status2_arr()
    {
        $arr[1] = '待认证';
        $arr[2] = '待审核';
        $arr[3] = '已认证';
        $arr[4] = '未通过';
        return $arr;
    }
    public static function enum_status2_text($key)
    {
        $arr = self::enum_status2_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    public static function enum_sex_arr()
    {
        $arr[1] = '男';
        $arr[2] = '女';
        return $arr;
    }
    public static function enum_sex_text($key)
    {
        $arr = self::enum_sex_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    public static function enum_preference_arr()
    {
        $arr[1] = '潮流女装';
        $arr[2] = '时尚男装';
        $arr[3] = '鞋子箱包';
        $arr[4] = '文娱运动';
        $arr[5] = '珠宝首饰';
        $arr[6] = '数码家电';
        $arr[7] = '护肤彩妆';
        $arr[8] = '母婴用品';
        $arr[9] = '家庭保健';
        $arr[10] = '家纺家装';
        $arr[11] = '日用百货';
        $arr[12] = '美食特产';
        $arr[13] = '车用周边';
        $arr[14] = '内衣内裤';
        $arr[15] = '中老年';
        $arr[16] = '配件配饰';
        $arr[17] = '健康养身';
        $arr[18] = '医疗辅助';
        $arr[19] = '计生情趣';
        return $arr;
    }
    public static function enum_preference_text($key)
    {
        $arr = self::enum_preference_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    public static function enum_marry_arr()
    {
        $arr[1] = '未婚';
        $arr[2] = '已婚';
        return $arr;
    }
    public static function enum_marry_text($key)
    {
        $arr = self::enum_marry_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    public static function enum_income_arr()
    {
        $arr[1] = '2千以上';
        $arr[2] = '2千-4千';
        $arr[3] = '4千-8千';
        $arr[4] = '8千-1.5万';
        $arr[5] = '1.5万以上';
        return $arr;
    }
    public static function enum_income_text($key)
    {
        $arr = self::enum_income_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    public static function enum_degree_arr()
    {
        $arr[1] = '初中';
        $arr[2] = '高中(中专)';
        $arr[3] = '大专';
        $arr[4] = '本科';
        $arr[5] = '硕士研究生';
        $arr[6] = '博士';
        return $arr;
    }
    public static function enum_degree_text($key)
    {
        $arr = self::enum_degree_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    public static function enum_job_arr()
    {
        $arr[1] = '学生';
        $arr[2] = '家庭主妇';
        $arr[3] = '上班族';
        $arr[4] = '个体户';
        $arr[5] = '自由职业者';
        $arr[6] = '企业主';
        return $arr;
    }
    public static function enum_job_text($key)
    {
        $arr = self::enum_job_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    
    public static function enum_tbgrade_arr()
    {
        $arr[1] = '一星';
        $arr[2] = '二星';
        $arr[3] = '三星';
        $arr[4] = '四星';
        $arr[5] = '五星';
        $arr[6] = '一钻';
        $arr[7] = '二钻';
        $arr[8] = '三钻';
        $arr[9] = '四钻';
        $arr[10] = '五钻';
        $arr[11] = '一皇冠';
        $arr[12] = '二皇冠';
        $arr[13] = '三皇冠';
        $arr[14] = '四皇冠';
        $arr[15] = '五皇冠';
        return $arr;
    }
    public static function enum_tbgrade_text($key)
    {
        $arr = self::enum_tbgrade_arr();
        if(!isset($arr[$key])){
            return '';
        }
        return $arr[$key];
    }
    
    public static function enum_birthyear_arr()
    {
        $arr = [];
        for($i=1980;$i<=2006;$i++){
            $arr[] = $i;
        }
        return $arr;
    }
    //查询aid下所有商家Id
    public static function getStrId($aid)
    {
        $lists = self::select("and aid={$aid}");
        if(empty($lists)){
            return 99999999;
        }
        $str = "";
        foreach($lists as $v){
            $str .= $v['id'].',';
        }
        return substr($str, 0, -1);
    }
    
    
}
?>