<?php
  /**
   * Created by PhpStorm.
   * User: ijonny
   * Date: 2018/1/13
   * Time: 20:31
   */

  class orderController
  {

    private $order;

    public function __construct()
    {
      $this->order = new orderModel();
    }


    //获取订单列表
    /*
     * 订单编号
     * 订单状态
     * 收货人
     * 收货地址
     * 订单金额
     * 下单时间
     */

    public function index()
    {

      // 接收 search 值
      $search = $_GET['search'];
      $where = null;
      if (!empty($search)) {
        $where = ' ordernum = ' . $search;
      }

      // 实例化 page.php
      $page = new Page;
      // 统计 总条数
      $count = $this->count($where);

      // 计算 分页下标
      $limit = $page->cNum($count);

      $data = $this->order->getAllOrderList($where, $limit);
//      var_dump($data);

      include_once 'View/order/index.html';
    }

    //订单详情页
    public function orderDetail()
    {
      $where = ' id = ' . $_GET['id'];
      $check = $this->order->validOrder($where);
      if (empty($check)) {
        myNotice('非法访问', './index.php');
      }
      $where = ' o.id = ' . $_GET['id'];

      $data = $this->order->getOrder($where);


      include_once 'View/order/orderdetail.html';
    }

    //发货
    public function doDelivery()
    {
      $where = ' status = 2 and id = ' . $_GET['id'];

      $check = $this->order->validOrder($where);
      if (empty($check)) {
        myNotice('非法访问', './index.php');
      }
      $arr['status'] = 3;

      $res = $this->order->changeOrderStatus($where, $arr);
//      var_dump($res);die;
      if ($res) {
        myNotice('发货成功');
      }

      myNotice('发货失败', './index.php');


    }

    //取消订单
    public function doCancel()
    {
      $where = ' status = 1 and id = ' . $_GET['id'];

      $check = $this->order->validOrder($where);
      if (empty($check)) {
        myNotice('非法访问', './index.php');
      }
      $arr['status'] = -1;

      $res = $this->order->changeOrderStatus($where, $arr);

      if ($res) {
        myNotice('取消成功');
      }

      myNotice('取消失败', './index.php');
    }


    public function count($where = '')
    {
      $data = $this->order->doCount($where);
      return $data;
    }


  }