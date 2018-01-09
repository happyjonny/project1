<?php

  class goodsController extends Controller
  {
    private $goods;

    public function __construct()
    {
      parent::__construct();
      $this->goods = new goodsModel;
    }

    public function index()
    {
      //接受来源的分类id
//      $cid = $_GET['id'];
//      echo $cid;die;
      // 接收 search 值

      $search = $_GET['search'];
      $where = null;
      $order = null;
      if (!empty($search)) {
        $where = 'name like "%' . $search . '%"';
      }

      if (!empty($_GET['order'])) {
        $order .= $_GET['order'] . ' desc';
      }
//      echo $order ; die;
      // 实例化 page.php
      $page = new Page;
      // 统计 总条数
      $count = $this->count($where);

      // 计算 分页下标
      $limit = $page->cNum($count);


      $data = $this->goods->showAll($where, $limit, $order);
      $cid = $_GET['id'];
//      echo $data['cid'];die;

//      die;
//      var_dump($page->param);die;
//      var_dump($count,$limit,$data);die;
      include_once 'View/goods/index.html';
    }

    public function count($where = '')
    {
      $data = $this->goods->doCount($where);
      return $data;
    }



  }
