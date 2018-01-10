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
          ->where(' id = ' . $_SESSION['uid'])
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

    //获取购物车所有商品及商品信息
    public function getCartItemInfoAll()
    {
      try {
        $res = $this->pdo
          ->field('c.id, c.uid, c.gid, c.quantity, g.price, g.stock, g.name , g.desc ,i.icon ')
          ->table('cart as c , goods as g , goodsimg as i')
          ->where('c.gid = g.id and c.gid = i.gid and g.id = i.gid and  i.face = 2  and c.uid = ' . $_SESSION['uid'])
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
          ->where('uid = ' . $_SESSION['uid'])
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
        myNotice('服务器出错' . __CLASS__ . ' line:' . __LINE__);
      }
      return $res;
    }


    //添加商品到购物车表
    public function addCart()
    {
      $res = $this->pdo
        ->table('cart')
        ->where('uid = ' . $_POST['uid'] . ' and gid = ' . $_POST['gid'])
        ->insert($_POST);
      return $res;
    }

    //更新购物车
    public function updateCart()
    {
      $res = $this->pdo
        ->table('cart')
        ->where('uid = ' . $_POST['uid'] . ' and gid = ' . $_POST['gid'])
        ->update($_POST);
      return $res;
    }

  }