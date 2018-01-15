<?php

  class Controller
  {
    public function __construct()
    {
      $this->base = new baseModel();
      //免登陆
      if (!empty($_COOKIE['mobile'])) {
        if (empty($_SESSION['user'])) {
          $res = $this->base->cookieLogin();
          if (!empty($res)) {
            $_SESSION['user']['uid'] = $res['id'];
            $_SESSION['user']['mobile'] = $res['mobile'];
            $_SESSION['user']['status'] = $res['status'];
            $_SESSION['user']['icon'] = $res['icon'];
            $_SESSION['user']['name'] = $res['name'];
          }
        }
      }

    }

    public function __call($k, $v)
    {
      myNotice('您访问的页面不存在', 'index.php');
    }

    public function header()
    {
      include_once 'View/index/_header.html';
    }

    public function footer()
    {
      include_once 'View/index/_footer.html';
    }

    public function contact()
    {
      include_once 'View/index/_contact.html';
    }

    public function nav()
    {

      //加载商品分类

//      $data = $this->base->showAll();

      include_once 'View/index/_nav.html';

    }

    public function userIndex()
    {
      $data2 = $this->base->getUserIndex();

      include_once 'View/user/_index.html';
      unset($data2);
    }


  }
