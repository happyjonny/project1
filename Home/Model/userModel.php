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
      $res = $this->pdo
        ->field('*')
        ->table('user')
        ->where(' id = ' . $_SESSION['uid'])
        ->find();
      return $res;
    }

    public function doLogin()
    {
      $res = $this->pdo
        ->field('id', 'status', 'icon', 'name')
        ->table('user')
        ->where(' mobile = \'' . $_POST['mobile'] . '\' and pwd = \'' . ($_POST['pwd'] . '\''))
        ->find();
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

    //获取购物车信息
    public function getCart()
    {
      $res = $this->pdo
        ->field('*')
        ->table('cart')
        ->where('uid = ' . $_POST['uid'] . ' and gid = ' . $_POST['gid'])
        ->find();
      return $res;
    }

    //添加商品到购物车表


  }