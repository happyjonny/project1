<?php
  /**
   * Created by PhpStorm.
   * User: ijonny
   * Date: 2018/1/9
   * Time: 19:10
   */

  class userController extends Controller
  {
    private $user;

    public function __construct()
    {
      parent::__construct();
      User::sessionStart();
      $this->user = new userModel();
    }

    public function index()
    {


      include_once 'View/user/index.html';

    }

    public function cart()
    {

      include_once 'View/user/cart.html';
    }

    public function login()
    {
      include_once 'View/user/login.html';
    }

    public function register()
    {
      include_once 'View/user/register.html';
    }

    public function doRegister()
    {
      // 电话
      $preg = '/^1[34578]\d{9}$/';
      if (!preg_match($preg, $_POST['mobile'])) {
        myNotice('手机号码格式不正确');
      }

      //密码
      $preg = '/^[a-zA-Z0-9]{6,20}$/';
      if (!preg_match($preg, $_POST['pwd'])) {
        myNotice('密码格式不正确');
      }
      if ($_POST['pwd'] != $_POST['repwd']) {
        myNotice('两次密码不一致');
      } else {
        unset($_POST['repwd']);
        $_POST['pwd'] = md5($_POST['pwd']);
      }

      //验证通过
      $_POST['regtime'] = time();

      // $uid 为用户id  类型:string
      $uid = $this->user->doRegister();

      if ($uid) {
        setcookie('id', $uid, time() + 3600 * 2);
        setcookie('mobile', $_POST['mobile'], time() + 3600 * 2);


        header('location: ./index.php');
        die;
      }

    }

  }