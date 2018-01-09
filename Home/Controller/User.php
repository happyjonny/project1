<?php
  /**
   * Created by PhpStorm.
   * User: ijonny
   * Date: 2018/1/9
   * Time: 19:57
   */

  class User
  {
    static public $flag = null;

    protected function __construct()
    {
      session_start();
    }

    static public function sessionStart()
    {
      if (is_null(self::$flag)) {
        self::$flag = new self;
      }
      return self::$flag;
    }
  }