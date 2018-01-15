<?php

  class loginController
  {
    private $login;

    public function __construct()
    {
      $this->login = new loginModel;
    }

    public function index()
    {
      include 'View/login/index.html';
    }

    public function doLogin()
    {
      // 调用Model类
      $data = $this->login->doLogin();

      if ($data) {
        $arr['ipaddress'] = '\'' . $_SERVER['REMOTE_ADDR'] . '\'';
        $arr['lcount'] = " lcount + 1";
        $arr['lasttime'] = time();
        $this->login->addInfo($arr);
        myNotice('登录成功', 'index.php');
      }
      myNotice('登录失败', 'index.php?c=login');
    }

    public function __call($k, $v)
    {
      myNotice('您访问的页面不存在', 'index.php');
    }

    public function logout()
    {
      unset($_SESSION['admin']);
      header('location: ./index.php');
      die;
    }

  }
