<?php

  class Controller
  {
    public function __construct()
    {
//			if( empty($_SESSION['admin']) ){
//				header('location: index.php?c=login'); die;
//			}

      $this->base = new baseModel();
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
      include_once 'View/user/_index.html';
    }


  }
