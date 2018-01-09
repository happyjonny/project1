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

//      注册新用户
//      返回用户id
    public function doRegister()
    {
      $uid = $this->pdo
        ->table('user')
        ->insert($_POST);
      return $uid;
    }
  }