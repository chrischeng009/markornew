<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\AdminModel;
use app\common\model\BankModel;
use app\common\model\AdmincashModel;
use app\common\model\AdminbankModel;//本model放最后方便看清是否弄错

class Adminbank extends Controller
{
    public function lists()
    {
        $this->isAuth();
        $where = '';
        $_GET['admin_id'] = gett('admin_id');
        $_GET['realname'] = gett('realname');
        //非超级管理员以及财务
        if ($_SESSION['role_id'] == 2) {
            $_GET['admin_id'] = $_SESSION['admin_id'];
        }
        if (!empty($_GET['admin_id'])) {
            $where .= " and admin_id = '" . $_GET['admin_id'] . "'";
        }
        if (!empty($_GET['realname'])) {
            $where .= " and realname like '%" . $_GET['realname'] . "%'";
        }
        $limit = !empty($_GET['limit']) ? $_GET['limit'] : 10;
        $page = !empty($_GET['page']) ? $_GET['page'] : 1;
        $params = ['page' => $page, 'query' => ['admin_id' => $_GET['admin_id'], 'realname' => $_GET['realname']]];
        $query = AdminbankModel::tbname()->where("1=1 $where")->order('id desc')->paginate($limit, false, $params);
        $page_show = $query->render();
        $this->assign('page_show', $page_show);
        $this->assign('lists', $query);

        $this->assign('admin_list', AdminModel::select(" and role_id=2"));
        return $this->fetch();
    }

    public function add()
    {
        $this->isAuth();
        return $this->fetch();
    }

    public function act_add()
    {
        $this->isAuth();
        $params = $_POST;
        $params['admin_id'] = $_SESSION['admin_id'];
        $params['intime'] = time();
        $where = '';
        $where .= " and admin_id={$_SESSION['admin_id']} ";
        $where .= " and bankcode={$_POST['bankcode']} ";
        $isarr = AdminbankModel::find($where);
        if ($isarr) {
            echo('此银行卡号已被添加!');
            exit;
        } else {
            $result = AdminbankModel::add($params);
            if ($result) {
                echo 'success';
            } else {
                echo 'error';
            }
        }
    }

    public function del()
    {
        $this->isAuth();
        $result = AdminbankModel::del("and id='" . postt('id') . "'");
        if ($result) {
            echo 'success';
        } else {
            echo '操作失败';
        }
    }

    public function edit()
    {
        $this->isAuth();
        $find = AdminbankModel::find("and id=" . gett('id'));
        $this->assign('find', $find);
        return $this->fetch();
    }

    public function act_edit()
    {
        $this->isAuth();
//        $params = $_POST;
        $params['uptime'] = time();
        $result = AdminbankModel::edit(" and id=" . postt('id'), $params);
        if ($result) {
            echo 'success';
        } else {
            echo 'error';
        }
    }


}
