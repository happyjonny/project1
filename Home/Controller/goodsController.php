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
      $where = null;

      // 实例化 page.php
      $page = new Page;
      $order = null;

      //根据 热卖/促销/推荐/新品 进行搜索
      if (!empty($_GET['order'])) {
        switch ($_GET['order']) {
          case 'new':
            $where .= '  new = 1  ';
            break;
          case 'hot':
            $where .= '  hot = 1  ';
            break;
          case 'recommend':
            $where .= '  recommend = 1  ';
            break;
          case 'sale':
            $where .= '  sale = 1  ';
            break;
          default :
            $order .= $_GET['order'] . ' desc ';
            break;
        }
      }

      // 接收 search 值
      $search = $_GET['search'];
      if (!empty($search)) {
        if (empty($where)) {
          $where .= ' name like "%' . $search . '%"';
        } else {
          $where .= ' and name like "%' . $search . '%"';
        }
      }

      // 统计 总条数
      $count = $this->count($where);
      // 计算 分页下标
      $limit = $page->cNum($count);


      $data = $this->goods->showAll($where, $limit, $order);
      $cid = $_GET['id'];

      include_once 'View/goods/index.html';
    }

    public function count($where = '')
    {
      $data = $this->goods->doCount($where);
      return $data;
    }

    public function detail()
    {
      $data = $this->goods->showOne();
      if (empty($data)) {
        myNotice('你要找的商品不存在');
      } else {
        if ($data['up'] == 2) {
          myNotice('该商品已下架', './index.php');
        }

        $data['icon'] = $this->goods->getImgs($data['id']);
      }
      include_once 'View/goods/detail.html';
    }


  }
