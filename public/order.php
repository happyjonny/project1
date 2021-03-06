<?php
  /**
   * Created by PhpStorm.
   * User: ijonny
   * Date: 2018/1/12
   * Time: 09:11
   */

  class Order
  {
    private $pdo;

    public function __construct()
    {
      $this->pdo = new DB;
    }

    //返回订单id
    public function orderCreate($arr = array())
    {
      if (!empty($arr)) {
        $res = $this->pdo
          ->table('`order`')
          ->insert($arr);
        return $res;
        //TODO 查看sql
      }
      return false;
    }

    public function orderdetailCreate($arr = array())
    {
      if (!empty($arr)) {
        $res = $this->pdo
          ->table('orderdetails')
          ->insert($arr);
        return $res;
      }
      return false;
    }

    /**
     * 生成唯一的订单号 20110809111259232312
     * 2011-年日期
     * 08-月份
     * 09-日期
     * 11-小时
     * 12-分
     * 59-秒
     * 2323-微秒
     * 12-随机值
     *
     * @return string
     */
    public static function trade_no()
    {
      list($usec, $sec) = explode(" ", microtime());
      $usec = substr(str_replace('0.', '', $usec), 0, 4);
      $str = rand(10000, 99999);
      return date("YmdHis") . $usec . $str;
    }

  }