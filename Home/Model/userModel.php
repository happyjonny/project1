<?php
  /**
   * Created by PhpStorm.
   * User: ijonny
   * Date: 2018/1/9
   * Time: 19:34
   */

  class userModel
  {
    private $pdo;
    private $Order;
    private $Goods;


    public function __construct()
    {
      $this->pdo = new DB;
      $this->Order = new Order;
      $this->Goods = new goodsModel;
    }

    public function getUserInfo()
    {
      try {
        $res = $this->pdo
          ->field('*')
          ->table('user')
          ->where(' id = ' . $_SESSION['user']['uid'])
          ->find();
      } catch (Exception $e) {
        myNotice('服务器出错' . __CLASS__ . ' line:' . __LINE__);
      }
      return $res;
    }

    public function doLogin()
    {
      try {
        $res = $this->pdo
          ->field('id', 'status', 'icon', 'name')
          ->table('user')
          ->where(' mobile = \'' . $_POST['mobile'] . '\' and pwd = \'' . ($_POST['pwd'] . '\''))
          ->find();
      } catch (Exception $e) {
        myNotice('服务器出错' . __CLASS__ . ' line:' . __LINE__);
      }
      return $res;
    }
//      注册新用户
//      返回用户id
    public function doRegister()
    {
      $uid = $this->pdo
        ->table('user')
        ->insert($_POST);
      return $uid;
    }

    //更新个人信息

    public function editProfile()
    {
      $res = $this->pdo
        ->table('user')
        ->where('id = ' . $_SESSION['user']['uid'])
        ->update($_POST);
      return $res;
    }

    //获取购物车所有商品及商品信息(不含图片)
    public function getCartItemInfoAll()
    {
//      TODO 查询字段需增加stock与up 来配合js使用
      try {
        $res = $this->pdo
          ->field('c.id, c.uid, c.gid, c.quantity, g.price, g.stock, g.name , g.desc ')
          ->table('cart as c , goods as g ')
          ->where('c.gid = g.id and c.uid = ' . $_SESSION['user']['uid'])
          ->select();
      } catch (Exception $e) {
        myNotice('服务器出错' . __CLASS__ . ' line:' . __LINE__);
      }
      return $res;
    }

    //获取购物车所有商品
    public function getCartAll()
    {
      try {
        $res = $this->pdo
          ->field('*')
          ->table('cart')
          ->where('uid = ' . $_SESSION['user']['uid'])
          ->select();
      } catch (Exception $e) {
        myNotice('服务器出错' . __CLASS__ . ' line:' . __LINE__);
      }
      return $res;
    }

    //获取购物车单个物品信息 (添加商品至购物车用)/或者用来验证来源是否正确
    public function getCart()
    {
      try {
        if (!empty($_POST['uid'])) {
          $res = $this->pdo
            ->field('*')
            ->table('cart')
            ->where('uid = ' . $_POST['uid'] . ' and gid = ' . $_POST['gid'])
            ->find();
        } else {
          $res = $this->pdo
            ->field('*')
            ->table('cart')
            ->where('uid = ' . $_SESSION['user']['uid'] . ' and gid = ' . $_GET['gid'])
            ->find();
        }
      } catch (Exception $e) {
        myNotice('服务器出错' . __CLASS__ . ' line:' . __LINE__, './index.php?c=user&m=cart');
      }
      return $res;
    }


    //添加商品到购物车表
    public function addCart()
    {
      $res = $this->pdo
        ->table('cart')
        ->where('uid = ' . $_SESSION['user']['uid'] . ' and gid = ' . $_POST['gid'])
        ->insert($_POST);
      return $res;
    }

    //更新购物车
    public function updateCart()
    {
      $res = $this->pdo
        ->table('cart')
        ->where('uid = ' . $_SESSION['user']['uid'] . ' and gid = ' . $_POST['gid'])
        ->update($_POST);
      return $res;
    }

    //删除购物车
    public function deleteCart()
    {
      $res = $this->pdo
        ->table('cart')
        ->where('uid = ' . $_SESSION['user']['uid'])
        ->delete();
      return $res;
    }

    //根据uid获取收货地址信息(所有)
    public function getAddress($where = '')
    {
      $res = $this->pdo
        ->field('*')
        ->table('address')
        ->where($where)
        ->select();
      return $res;
    }

    //新增地址
    public function addressadd()
    {
      $res = $this->pdo
        ->table('address')
        ->insert($_POST);
      return $res;
    }

    //更新收货地址
    public function addressupdate($where = '', $arr = array())
    {
      if (empty($where) || empty($arr)) {
        return false;
      }
      $res = $this->pdo
        ->table('address')
        ->where($where)
        ->update($arr);
      return $res;
    }

    public function addressdoDel($where)
    {
      if (empty($where)) {
        return false;
      }
      $res = $this->pdo
        ->table('address')
        ->where($where)
        ->delete();
      return $res;
    }


//    创建新订单(购物车到创建订单)
    public function validorderCreate()
    {
      //验证数据
      //获取商品
      $gids = '';
      foreach ($_POST as $k => $v) {
        $gids .= $k . ',';
      }
      $gids = rtrim($gids, ',');
//      var_dump($gids);

      $tmp = $this->Goods->getGoodsInfo($gids);

      //重新排序res
      foreach ($tmp as $k => $v) {
        $res[$v['gid']] = $v;
      }
      unset($tmp);

      //验证商品上架与库存情况
      foreach ($res as $k => $v) {
        $res[$k]['quantity'] = $_POST[$k];
        if ($v['stock'] < $_POST[$k]) {
          myNotice('商品: ' . $v['name'] . '数量不足', './index.php?c=user&m=cart', 2);
        } elseif ($v['up'] !== '1') {
          myNotice('商品: ' . $v['name'] . '已下架', './index.php?c=user&m=cart', 2);
        }
      }
      return $res;
    }

    public function doOrderCreate($goodsInfos = array())
    {
//      var_dump($goods);die;
      //把数据存入order与orders表中

      //先创建订单信息(order表字段)
      $orderInfo['ordernum'] = Order::trade_no();
      $orderInfo['uid'] = $_SESSION['user']['uid'];
      $orderInfo['aid'] = $_POST['addressid'];
      $orderInfo['paymenttype'] = $_POST['paymenttype'];
      $orderInfo['total'] = $_POST['total'];
      if ($orderInfo['paymenttype'] === '1') {
        //如果货到付款, 状态为 待发货 已付款
        $orderInfo['status'] = '2';
        $orderInfo['ispay'] = '1';
      } else {
        //如果线上支付, 状态为代付款 , 未付款
        $orderInfo['status'] = '1';
        $orderInfo['ispay'] = '2';
      }
      $orderInfo['addtime'] = $orderInfo['uptime'] = time();

      //插入新数据到order表中 成功返回订单id
      $orderId = $this->Order->orderCreate($orderInfo);
//      var_dump($orderId);die;
      if ($orderId) {
        //如果新建订单成功, 添加订单详情
        foreach ($_SESSION['user']['cart']['goodsinfos'] as $k => $v) {
          unset($_SESSION['user']['cart']['goodsinfos'][$k]['stock']);
          unset($_SESSION['user']['cart']['goodsinfos'][$k]['up']);
          unset($_SESSION['user']['cart']['goodsinfos'][$k]['name']);
          unset($_SESSION['user']['cart']['goodsinfos'][$k]['icon']);
          $_SESSION['user']['cart']['goodsinfos'][$k]['oid'] = $orderId;
          $res = $this->Order->orderdetailCreate($_SESSION['user']['cart']['goodsinfos'][$k]);
          if (!$res) {
            myNotice('创建订单失败(添加订单详情)', './index.php?c=user&m=cart');
          }
          $tmp['stock'] = '  stock - ' . $_SESSION['user']['cart']['goodsinfos'][$k]['quantity'];
          $tmp['sold'] = '  sold + ' . $_SESSION['user']['cart']['goodsinfos'][$k]['quantity'];
          $res = $this->Goods->updateGoodsStock($tmp, ' id = ' . $_SESSION['user']['cart']['goodsinfos'][$k]['gid']);

        }
        //创建成功 清空session
        unset($_SESSION['user']['cart']['goodsinfos']);
        unset($tmp);
        //清空购物车
        $this->deleteCart();
        //全部完成
        return true;
      } else {
        myNotice('创建订单失败', './index.php?c=user&m=cart');
      }


    }


    //购物车内删除某个商品
    public function doDelCart()
    {
      $check = $this->getCart();
      if (empty($check)) {
        myNotice('非法访问', './index.php?c=user&m=cart');
      }
      $res = $this->pdo
        ->table('cart')
        ->where(' gid = ' . $_GET['gid'] . ' and uid = ' . $_SESSION['user']['uid'])
        ->delete();

    }



    //订单功能
    //查询一个订单详情 带条件
    public function getOrder($where = '')
    {
      $res = $this->pdo
        ->field(' o.id, o.ordernum, o.addtime, o.uptime, o.total, o.ispay, o.status, o.paymenttype, o.aid, od.oid, od.price, od.gid, od.quantity, i.icon, g.name, a.address, a.realname, a.tel')
        ->table(' `order` as o , goodsimg as i , orderdetails as od , goods as g , address as a')
        ->where($where . ' and o.id = od.oid and od.gid = i.gid and od.gid = g.id and a.id = o.aid and o.uid = ' . $_SESSION['user']['uid'])
        ->select();
//      var_dump($res);
      return $res;

    }

    //查看订单状态
    public function getOrderStatus($where = '')
    {
      $res = $this->pdo
        ->field('`status`')
        ->table('`order`')
        ->where($where . ' and uid = ' . $_SESSION['user']['uid'])
        ->find();
//      var_dump($res);die;
      return $res;
    }

    public function getAllOrdersLists($where = '', $limit = '')
    {
      $tmp = $this->pdo
        ->field(' ordernum ')
        ->table(' `order` ')
        ->where($where)
        ->limit($limit)
        ->select();
      foreach ($tmp as $k => $v) {
        $res[$v['ordernum']] = array();
      }

      $res = $this->getAllOrdersInfo($res);
//      var_dump($this->pdo->sql);die;
      return $res;

    }

    public function getAllOrdersInfo($arr = array())
    {
//      var_dump($arr);

      foreach ($arr as $k => $v) {
        $arr[$k] = $this->pdo
          ->field(' o.id, o.ordernum, o.addtime, o.uptime, o.total, o.ispay, o.status, o.paymenttype, o.aid, od.oid, od.price, od.gid, od.quantity, i.icon, g.name')
          ->table(' `order` as o , goodsimg as i , orderdetails as od , goods as g')
          ->where(' o.ordernum = ' . $k . ' and o.id = od.oid and od.gid = i.gid and od.gid = g.id')
          ->select();
      }

      return $arr;
    }



    //查询所有订单 带条件 带limit 带order
//    public function getAllOrdersLists($where = '', $limit = '')
//    {
//      $res = $this->pdo
//        ->field(' o.id, o.ordernum, o.addtime, o.uptime, o.total, o.ispay, o.status, o.paymenttype, o.aid, od.oid, od.price, od.gid, od.quantity, i.icon, g.name')
//        ->table(' `order` as o , goodsimg as i , orderdetails as od , goods as g')
//        ->where($where . ' and o.id = od.oid and od.gid = i.gid and od.gid = g.id')
//        ->select();
//      var_dump($this->pdo->sql);
//      return $res;
//
//    }

    public function changeStatus($where = '', $arr = array())
    {
      $res = $this->pdo
        ->table('`order`')
        ->where($where)
        ->update($arr);

      return $res;
    }

    public function delOrderList($where = '')
    {
      $res = $this->pdo
        ->table('`order`')
        ->where($where)
        ->delete();

    }


    public function doCount($where = '')
    {
      $res = $this->pdo
        ->field(' count(id) as count ')
        ->table('`order`')
        ->where($where)
        ->find();
//      var_dump($res);
      return $res['count'];

    }


  }