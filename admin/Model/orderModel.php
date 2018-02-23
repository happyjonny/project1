<?php
  /**
   * Created by PhpStorm.
   * User: ijonny
   * Date: 2018/1/13
   * Time: 20:31
   */

  class orderModel
  {
    private $pdo;

    public function __construct()
    {
      $this->pdo = new DB();
    }

    //获取所有订单和订单的收货信息
    public function getAllOrderList($where = '', $limit = '')
    {
      try {
        if (!empty($where)) {
          $where = ' and ' . $where;
        }
        $res = $this->pdo
          ->field('o.id, o.ordernum, o.aid, o.addtime, o.total, o.status, a.realname, a.address, a.tel')
          ->table('`order` as o, address as a')
          ->where(' o.aid = a.id ' . $where)
          ->order(' addtime desc ')
          ->limit($limit)
          ->select();
//        echo $this->pdo->sql;die;
        return $res;
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }
    }

    //订单功能
    //查询一个订单详情 带条件
    public function getOrder($where = '')
    {
      try {
        $res = $this->pdo
          ->field(' o.id, o.ordernum, o.addtime, o.uptime, o.total, o.ispay, o.status, o.paymenttype, o.aid, od.oid, od.price, od.gid, od.quantity, i.icon, g.name, a.address, a.realname, a.tel')
          ->table(' `order` as o , goodsimg as i , orderdetails as od , goods as g , address as a')
          ->where($where . ' and o.id = od.oid and od.gid = i.gid and od.gid = g.id and a.id = o.aid ')
          ->select();
        return $res;
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }

    }

    //改变订单状态
    public function changeOrderStatus($where = '', $arr = array())
    {
      $res = $this->pdo
        ->table('`order`')
        ->where($where)
        ->update($arr);
      return $res;

    }

    public function doCount($where = '')
    {
      try {
        $res = $this->pdo
          ->field(' count(id) as count')
          ->table('`order`')
          ->where($where)
          ->find();
        return $res['count'];
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }
    }

    //查看订单是否合法
    public function validOrder($where = '')
    {
      if (empty($where)) {
        return false;
      }
      try {
        $res = $this->pdo
          ->field(' id ')
          ->table('`order`')
          ->where($where)
          ->find();
        return $res;
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }

    }
  }