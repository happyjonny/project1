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


      include_once 'View/user/cart.html';

    }

    public function cart()
    {

      include_once 'View/user/cart.html';
    }

    public function login()
    {
      include_once 'View/user/login.html';
    }

    public function doLogin()
    {
      $_POST['pwd'] = md5($_POST['pwd']);

      $data = $this->user->doLogin();

      if (!empty($data)) {
        if ($_POST['rememberme']) {
          //如果选择请记住我,则记录用户信息
          setcookie('mobile', $_POST['mobile'], time() + 3600 * 24 * 7);
          setcookie('pwd', $_POST['pwd'], time() + 3600 * 24 * 7);
        }
        setcookie('uid', $data['id'], time() + 3600 * 24 * 7);
        setcookie('name', $data['name'], time() + 3600 * 24 * 7);
        $_SESSION['uid'] = $data['id'];
        $_SESSION['mobile'] = $_POST['mobile'];
        $_SESSION['status'] = $data['status'];
        $_SESSION['icon'] = $data['icon'];
        $_SESSION['name'] = $data['name'];
      } else {
        myNotice('请检查用户名密码');
      }

      header('location: ./index.php');
      die;

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
//        setcookie('uid', $uid, time() + 3600 * 2);
//        setcookie('mobile', $_POST['mobile'], time() + 3600 * 2);

        $_SESSION['uid'] = $uid;
        $_SESSION['mobile'] = $_POST['mobile'];

        $userInfo = $this->user->getUserInfo();
        $_SESSION['status'] = $userInfo['status'];
        $_SESSION['icon'] = $userInfo['icon'];
        $_SESSION['name'] = $userInfo['name'];
        header('location: ./index.php');
        die;
      }

    }

  }