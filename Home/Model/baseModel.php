<?php
  /**
   * Created by PhpStorm.
   * User: ijonny
   * Date: 2018/1/8
   * Time: 20:30
   */

  class baseModel
  {
    private $pdo;

    public function __construct()
    {
      $this->pdo = new DB;
    }

    public function showAll()
    {
      $res = $this->pdo
        ->field('id, name')
        ->table('category')
        ->where('display = 1 and pid = 0')
        ->order('id desc')
        ->select();

      return $res;
    }
  }