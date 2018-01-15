<?php

  class loginModel
  {
    private $pdo;

    public function __construct()
    {
      $this->pdo = new DB;
    }

    public function doLogin()
    {
      // 1. 接收数据
      // var_dump($_POST); die;
      $name = $_POST['name'];
      $pwd = md5($_POST['pwd']);

      // 2. 验证数据
      // 手机号 正则验证...
      // 密码 自己玩


      // 3. 准备sql
      // select nickname, id from user where tel = xxx and pwd = xxx
      try {
        $res = $this->pdo
          ->field('`id`')
          ->table('`admin`')
          ->where('name = "' . $name . '" and pwd = "' . $pwd . '" ')
          ->find();
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }

      // 5. 存储session
      // 6. 返回结果
      if ($res) {
        $_SESSION['admin']['name'] = $name;
        $_SESSION['admin']['id'] = $res['id'];
      }

      return $res;
    }

    public function addInfo($arr = array())
    {
      if (empty($arr)) {
        myNotice('非法访问');
      }
      $res = $this->pdo
        ->table('admin')
        ->where(' id = ' . $_SESSION['admin']['id'])
        ->funcUpdate($arr);
//		  echo $this->pdo->sql;die;
    }


  }

