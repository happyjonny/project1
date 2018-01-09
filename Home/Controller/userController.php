<?php
  /**
   * Created by PhpStorm.
   * User: ijonny
   * Date: 2018/1/9
   * Time: 19:10
   */

  class userController
  {
    private $user;

    public function __construct()
    {
      parent::__construct();
      $this->user = new userModel();
    }

    public function index()
    {


      include_once 'View/user/index.html';

    }

    public function cart()
    {
      echo '111';
      die;
      include_once 'Vew/user/cart.html';
    }

  }