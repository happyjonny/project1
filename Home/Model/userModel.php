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

    public function __construct()
    {
      $this->pdo = new DB;
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

    //获取购物车单个物品信息 (添加商品至购物车用)
    public function getCart()
    {
      try {
        $res = $this->pdo
          ->field('*')
          ->table('cart')
          ->where('uid = ' . $_POST['uid'] . ' and gid = ' . $_POST['gid'])
          ->find();
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
      $goods = new goodsModel();
      $tmp = $goods->getGoodsInfo($gids);

      //重新排序res
      foreach ($tmp as $k => $v) {
        $res[$v['gid']] = $v;
      }
      unset($tmp);

      //验证商品上架与库存情况
      foreach ($res as $k => $v) {
        $res[$k]['quantity'] = $_POST[$k];
        if ($v['stock'] < $_POST[$k]) {
          myNotice('商品: ' . $v['name'] . '数量不足', '', 2);
        } elseif ($v['up'] !== '1') {
          myNotice('商品: ' . $v['name'] . '已下架', '', 2);
        }
      }
      var_dump($res);
      return $res;
    }

  }