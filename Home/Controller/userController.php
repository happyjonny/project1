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
    public $goodsInfos;


    public function __construct()
    {
      parent::__construct();
      User::sessionStart();
      $this->user = new userModel();
      $this->goods = new goodsModel();

    }


    public function index()
    {
      self::isLogin();
      //没图片
      $data = $this->user->getCartItemInfoAll();
      //获取图片
      foreach ($data as $k => $v) {
        $tmp = $this->goods->getImgs($v['gid']);
        foreach ($tmp as $key => $value) {
          if ($value['face'] == 1) {
            $data[$k]['icon'] = $value['icon'];
          }
        }
      }

      include_once 'View/user/cart.html';
      unset($data);
    }

    public function cart()
    {
      self::isLogin();
      //没图片
      $data = $this->user->getCartItemInfoAll();
      //获取图片
      foreach ($data as $k => $v) {
        $tmp = $this->goods->getImgs($v['gid']);
        foreach ($tmp as $key => $value) {
          if ($value['face'] == 1) {
            $data[$k]['icon'] = $value['icon'];
          }
        }
      }
      include_once 'View/user/cart.html';
      unset($data);
    }


    public function login()
    {
      include_once 'View/user/login.html';
    }

    public function profile()
    {
      self::isLogin();
      $data = $this->user->getUserInfo();
      include_once 'View/user/profile.html';
      unset($data);
    }

    public function address()
    {
      self::isLogin();

      $data = $this->user->getAddress(' display  = 1 and uid = ' . $_SESSION['user']['uid']);
      include_once 'View/user/address.html';
      unset($data);
    }

    public function addressadd()
    {
      self::isLogin();

      include_once 'View/user/addressadd.html';
    }

    public function doaddressadd()
    {
      self::isLogin();

      if (empty($_POST['address']) || empty($_POST['realname']) || empty($_POST['tel'])) {
        myNotice('输入有误', './index.php?c=user&m=addressadd');
      }

      //验证信息
      //验证地址
      $preg = "/\W+/u";
      if (preg_match($preg, $_POST['address'])) {
        myNotice('地址格式不正确','./index.php?c=user&m=addressadd');
      }

      $preg = '/^[\x{4e00}-\x{9fa5}]{2,10}$|^[a-zA-Z\s]*[a-zA-Z\s]{2,20}$/isu';
      if (!preg_match($preg, $_POST['realname'])) {
        myNotice('姓名格式不正确','./index.php?c=user&m=addressadd');
      }

      $preg = '/^1[34578]\d{9}$/';
      if (!preg_match($preg, $_POST['tel'])) {
        myNotice('手机号码格式不正确','./index.php?c=user&m=addressadd');
      }

      //TODO



      $_POST['uid'] = $_SESSION['user']['uid'];

      //查询该用户已有收货信息
      $res = $this->user->getAddress(' display  = 1 and uid = ' . $_SESSION['user']['uid']);
      //如果没有收货地址
      if (empty($res)) {
        //新增 且该地址设置为默认
        $_POST['defaults'] = 1;
      } else {
        //已存在, 则不用设置为默认
        $_POST['defaults'] = 2;
      }
      $data = $this->user->addressadd();


      header('location: ./index.php?c=user&m=address');
      die;
    }

    public function addressdoDefaults()
    {
      self::isLogin();

      $data = $this->user->getAddress('id = ' . $_GET['aid']);
      if ($data[0]['uid'] != $_SESSION['user']['uid']) {
        myNotice('非法访问', './index.php?c=user&m=address');
      }
      //先设置该用户所有地址为非默认
      $arr['defaults'] = 2;
      $data = $this->user->addressupdate(' uid = ' . $data[0]['uid'], $arr);
      //再设置所选择的收货地址为默认
      $arr['defaults'] = 1;
      $data = $this->user->addressupdate('id = ' . $_GET['aid'], $arr);
      header('location: ./index.php?c=user&m=address');
      die;
    }

    public function addressdoDel()
    {
      self::isLogin();
      //用户验证
      $data = $this->user->getAddress('id = ' . $_GET['aid']);
      if ($data[0]['uid'] != $_SESSION['user']['uid']) {
        myNotice('非法访问', './index.php?c=user&m=address');
      }
      $d = $data[0]['defaults'];
      //删除该收货地址信息
      $data = $this->user->addressdoDel('id = ' . $_GET['aid'], $_GET['aid']);
      if (!$data) {
        myNotice('删除失败', './index.php?c=user&m=address');
      }
      unset($data);
      //如果该收货地址是默认 则要去修改其他地址设置为默认
      if ($d == 1) {
        //获取该用户还有的所有收货地址
        $data = $this->user->getAddress('uid = ' . $_SESSION['user']['uid']);
        if (!empty($data)) {
          //如果还有 设置数据库中该用户查询到的第一个收货地址为默认
          $arr['defaults'] = 1;
          $data = $this->user->addressupdate('id = ' . $data[0]['id'], $arr);
        }
      }

      header('location: ./index.php?c=user&m=address');
      die;
    }


    public function doLogin()
    {
      //如果有cookie

      $_POST['pwd'] = md5($_POST['pwd']);
      //验证 验证码输入是否正确
      if (strtolower($_POST['yzm']) != strtolower($_SESSION['user']['code'])) {
        unset($_SESSION['user']['code']);
        myNotice('验证码错误','./index.php?c=user&m=login');
      }
      unset($_SESSION['user']['code']);
      $data = $this->user->doLogin();

      if (!empty($data)) {
        if ($_POST['rememberme']) {
          //如果选择请记住我,则记录用户信息
          setcookie('mobile', $_POST['mobile'], time() + 3600 * 24 * 7);
          setcookie('pwd', $_POST['pwd'], time() + 3600 * 24 * 7);
        }
        setcookie('uid', $data['id'], time() + 3600 * 24 * 7);
        setcookie('name', $data['name'], time() + 3600 * 24 * 7);
        $_SESSION['user']['uid'] = $data['id'];
        $_SESSION['user']['mobile'] = $_POST['mobile'];
        $_SESSION['user']['status'] = $data['status'];
        $_SESSION['user']['icon'] = $data['icon'];
        $_SESSION['user']['name'] = $data['name'];
      } else {
        myNotice('请检查用户名密码','./index.php?c=user&m=login');
      }

      header('location: ./index.php');
      die;

    }

    public function doLogout()
    {
      setcookie('mobile', '', time() - 1);
      setcookie('pwd', '', time() - 1);
      setcookie('uid', '', time() - 1);
      setcookie('name', '', time() - 1);
      unset($_SESSION['user']['uid']);
      unset($_SESSION['user']['mobile']);
      unset($_SESSION['user']['status']);
      unset($_SESSION['user']['icon']);
      unset($_SESSION['user']['name']);
      unset($_SESSION['user']);
      header('location: ./index.php');
      die;
    }

    public function register()
    {
      include_once 'View/user/register.html';
    }

    public function doRegister()
    {
      if (strtolower($_POST['yzm']) != strtolower($_SESSION['user']['code'])) {
        unset($_SESSION['user']['code']);
        myNotice('验证码错误', './index.php?c=user&m=register');
      }
      unset($_SESSION['user']['code']);
      // 电话
      $preg = '/^1[34578]\d{9}$/';
      if (!preg_match($preg, $_POST['mobile'])) {
        myNotice('手机号码格式不正确', './index.php?c=user&m=register');
      }

      //密码
      $preg = '/^[a-zA-Z0-9]{6,20}$/';
      if (!preg_match($preg, $_POST['pwd'])) {
        myNotice('密码格式不正确', './index.php?c=user&m=register');
      }
      if ($_POST['pwd'] != $_POST['repwd']) {
        myNotice('两次密码不一致', './index.php?c=user&m=register');
      }
      if ($_POST['pwd'] == 'default') {
        myNotice('密码格式不正确', './index.php?c=user&m=register');
      } else {
        unset($_POST['repwd']);
        $_POST['pwd'] = md5($_POST['pwd']);
      }


      //验证通过
      $_POST['regtime'] = time();

      $_POST['status'] = 1;
      unset($_POST['yzm']);

      // $uid 为用户id  类型:string
      $uid = $this->user->doRegister();

      if ($uid) {


        $_SESSION['user']['uid'] = $uid;
        $_SESSION['user']['mobile'] = $_POST['mobile'];

        $userInfo = $this->user->getUserInfo();
        $_SESSION['user']['status'] = $userInfo['status'];
        $_SESSION['user']['icon'] = $userInfo['icon'];
        $_SESSION['user']['name'] = $userInfo['name'];
        header('location: ./index.php');
        die;
      } else {
        myNotice('请检查用户名或已被注册', './index.php?c=user&m=register');
      }

    }

    //验证手机号(获取当前用户信息页面)
//    public function validateMobile()
//    {
//      $data = $this->user->getUserInfo();
//      include_once 'View/user/validateTel.html';
//    }

    //提交验证
//    public function dovalidateMobile()
//    {
//      if (strtolower($_POST['yzm']) != strtolower($_SESSION['user']['code'])) {
//        unset($_SESSION['user']['code']);
//        myNotice('验证码错误');
//      }
//      unset($_SESSION['user']['code']);
//
//      $preg = '/^1[34578]\d{9}$/';
//      if (!preg_match($preg, $_POST['mobile'])) {
//        myNotice('手机号码格式不正确');
//      }
//      //查看输入的手机号是否以存在
//      $check = $this->user->getUserInfoByMobile($_POST['mobile']);
//      if (!empty($check)) {
//        myNotice('该号码以存在,请不要重复设置', './index.php?c=user&m=cart');
//      }
//      $_SESSION['user']['tMobile'] = $_POST['mobile'];
//
//      $random = $_SESSION['user']['validateCode'] = rand(100000, 999999);
//
//
//      $sms = new sendSMS();
//      $sms->sendsms($_POST['mobile'], array($random, 1), "1");
//      include "View/user/doValidate.html";
//    }
//
//    //审核验证
//    public function doValidate()
//    {
////      var_dump($_POST);die;
//
//      if ($_SESSION['user']['validateCode'] != $_POST['validateCode']) {
//        myNotice('验证码错误,请重新验证', './index.php/c=user&m=validateMobile');
//      }
//      if (!empty($_POST['do'])) {
//        $arr['mobile'] = $_SESSION['user']['tMobile'];
//        $arr['status'] = 1;
//        $res = $this->user->editProfile($arr);
//        $_SESSION['user']['mobile'] = $arr['mobile'];
//        $_SESSION['user']['status'] = 1;
//        unset($_SESSION['user']['tMobile']);
//        header('location: ./index.php?c=user&m=cart');
//        die;
//      } else {
//        if (empty($_POST['pwd'])) {
//          myNotice('请输入密码', './index.php');
//        } else {
//          $preg = '/^[a-zA-Z0-9]{6,20}$/';
//          if (!preg_match($preg, $_POST['pwd'])) {
//            myNotice('密码格式不正确', './index.php');
//          }
//          if ($_POST['pwd'] != $_POST['repwd']) {
//            myNotice('两次密码不一致', './index.php');
//          }
//          if ($_POST['pwd'] == 'default') {
//            myNotice('密码格式不正确', './index.php');
//          } else {
//            unset($_POST['repwd']);
//            $tmp['pwd'] = md5($_POST['pwd']);
//            $res = $this->user->editProfile($tmp);
//            if ($res) {
//              myNotice('修改失败', './index.php');
//            }
//            unset($_SESSION['user']['dovalidate']);
//            setcookie('mobile', $_SESSION['user']['tMobile'], time() + 3600 * 24 * 7);
//            setcookie('pwd', $tmp['pwd'], time() + 3600 * 24 * 7);
//            myNotice('修改成功', './index.php?c=user');
//
//          }
//        }
//
//      }
//
//    }
//
//    public function forgetPwd()
//    {
//      if (!empty($_SESSION['user']['uid'])) {
//        $data = $this->user->getUserInfo();
//      }
//      include_once 'View/user/forgetPwd.html';
//    }
//
//    public function dovalidateMobilePwd()
//    {
//      if (strtolower($_POST['yzm']) != strtolower($_SESSION['user']['code'])) {
//        unset($_SESSION['user']['code']);
//        myNotice('验证码错误', './index.php');
//      }
//      unset($_SESSION['user']['code']);
//      //标签: 重设密码
//
//      $mobile = $this->user->getUserInfoByMobile($_POST['mobile']);
//      if (empty($mobile)) {
//        myNotice('你输入的手机号码没有注册或错误', './index.php');
//      }
//      $_SESSION['user']['tMobile'] = $mobile['mobile'];
//
//      $random = $_SESSION['user']['validateCode'] = rand(100000, 999999);
//
//
//      $sms = new sendSMS();
//      $sms->sendsms($_POST['mobile'], array($random, 1), "1");
//
//      include_once 'View/user/doValidatePwd.html';
//
//    }


    //添加至购物车
    public function addCart()
    {
      self::isLogin();
      //验证数量填写是否正确
      $preg = '/^[1-9]\d*$/';

      if (!preg_match($preg, $_POST['quantity'])) {
        myNotice('请重新确认你购买的数量', './index.php');
      }

      //保证 uid为session的uid
      // 即时用户篡改input标签 也可以正常使用
      $_POST['uid'] = $_SESSION['user']['uid'];
      if (empty($_POST['gid'])) {
        header('./index.php?c=user&m=cart');
        die;
      }
      //先判断库存是否为空
      if ($_POST['stock'] < 1) {
        myNotice('该商品已售空', './index.php');
      }
      unset($_POST['stock']);
      //添加至购物车表
      //先查询原来购物车是否存在该物品
      $res = $this->user->getCart();
      if (!empty($res)) {
        //有该物品
        //先把数量增加 再更新
        if (empty($_POST['quantity'])) {
          $_POST['quantity'] = 1;
        }
        $_POST['quantity'] += $res['quantity'];
        $data = $this->user->updateCart($_POST);
      } else {
        // 购物车没有该商品  直接新增
        if (empty($_POST['quantity'])) {
          $_POST['quantity'] = 1;
        }
        $data = $this->user->addCart($_POST);
      }
      if (!empty($data)) {
        // 添加成功,跳转至购物车页面
        $this->cart();
      } else {
        myNotice('添加购物车失败', './index.php');
      }

    }

    //修改会员信息
    public function editProfile()
    {
      self::isLogin();

      //密码验证
      //2次密码不一致
      if (empty($_POST['pwd'])) {
        unset($_POST['pwd']);
        unset($_POST['repwd']);
      } else {
        if ($_POST['pwd'] != $_POST['repwd']) {
          myNotice('两次密码不一致,请重新输入', './index.php?c=user&m=profile');
        }
        $preg = '/^[a-zA-Z0-9]{6,20}$/';
        if (!preg_match($preg, $_POST['pwd'])) {
          myNotice('密码格式不正确', './index.php?c=user&m=profile');
        }

        $_POST['pwd'] = md5($_POST['pwd']);
        unset($_POST['repwd']);
      }
      //昵称
      if (empty($_POST['name'])) {
        unset($_POST['name']);
      } else {
        $preg = "/\W+/u";
        if (preg_match($preg, $_POST['name'])) {
          myNotice('昵称格式不正确', './index.php?c=user&m=profile');
        }
      }
      //地址
      if (empty($_POST['address'])) {
        unset($_POST['address']);
      } else {
        $preg = "/\W+/u";
        if (preg_match($preg, $_POST['address'])) {

          myNotice('地址格式不正确', './index.php?c=user&m=profile');
        }
      }

      //上传icon: 如果用户上传文件则处理,如果不上传则不处理
      if (!empty($_FILES)) {
        $file = new Upload;
        $icon = $file->singleFile();

        if (is_array($icon)) {
          $_POST['icon'] = $icon[0];
        }
      }

      $res = $this->user->editProfile($_POST);

      if ($res !== false) {
        if(!empty($_POST['name'])){
          $_SESSION['user']['name'] = $_POST['name'];
        }
        myNotice('更新成功', './index.php?c=user&m=profile');
      } else {
        myNotice('更新失败', './index.php?c=user&m=profile');
      }

    }

    //创建新订单
    public function ordercreate()
    {
      self::isLogin();



      //验证数量: 必须大于0
      $preg = '/^[1-9]\d*$/';

      foreach ($_POST as $k => $v) {
        if (!preg_match($preg, $_POST[$k])) {
          myNotice('请重新确认你购买的数量', './index.php');
        }

      }

      //验证是否有收货地址
      $add = $this->user->getAddress(' display = 1 and  uid = ' . $_SESSION['user']['uid']);
      if (empty($add)) {
        myNotice('请先添加收货地址', './index.php?c=user&m=addressadd');
      }

      $goods = $this->user->validorderCreate();
      if ($goods) {
        $address = $this->user->getAddress(' display = 1 and uid = ' . $_SESSION['user']['uid']);
        $_SESSION['user']['cart']['goodsinfos'] = $goods;
        include_once './View/user/ordercreate.html';
      } else {
        myNotice('你的购物车信息有误,请查看或联系管理员', './index.php?c=user&m=cart', 3);
      }
    }

    public function doOrderCreate()
    {
      self::isLogin();


      //$res 为新创建的订单id
      $res = $this->user->doOrderCreate($this->goodsInfos);
      if ($res) {
        //创建成功
        myNotice('下单成功', 'index.php?c=user&m=orderList');

      } else {
        //创建失败
        myNotice('出问题了', './index.php');
      }

    }

    public function doDelCart()
    {
      self::isLogin();

      $this->user->doDelCart();
      header('location: ./index.php?c=user&cart');
      die;
    }

    public function orderList()
    {
      self::isLogin();

      //验证来源
      //param  status  ordernum
      // 实例化 page.php

      $page = new Page;
      // 统计

      if (empty($_GET['status'])) {
        $_GET['status'] = '0';
      }
      //TODO 先获取订单  再每个订单遍历

      //如果带条件
      if (!empty($_GET['ordernum'])) {
        if ($_GET['status'] === '-1' || ($_GET['status'] >= '1' && $_GET['status'] <= '6')) {
//          echo '111';
          $where = ' uid = ' . $_SESSION['user']['uid'] . ' and status = ' . $_GET['status'] . ' and ordernum = ' . $_GET['ordernum'];
          $count = $this->count($where);
          // 计算 分页下标
          $limit = $page->cNum($count);

          $data = $this->user->getAllOrdersLists($where, $limit);
        } else {

          $where = ' uid = ' . $_SESSION['user']['uid'] . ' and ordernum = ' . $_GET['ordernum'];
          $count = $this->count($where);
          // 计算 分页下标
          $limit = $page->cNum($count);
          $data = $this->user->getAllOrdersLists($where, $limit);
        }
      } elseif ($_GET['status'] === '-1' || ($_GET['status'] >= '1' && $_GET['status'] <= '6')) {

        $where = ' uid = ' . $_SESSION['user']['uid'] . ' and status = ' . $_GET['status'];
        $count = $this->count($where);
        // 计算 分页下标
        $limit = $page->cNum($count);
        $data = $this->user->getAllOrdersLists($where, $limit);
      } else {
        //没有输入订单号查询 也没有订单状态( 订单列表首页, 不带参查询) 或者 只有订单状态 且只为0 或者非其他预留状态值
        $where = ' uid = ' . $_SESSION['user']['uid'];
        $count = $this->count($where);
        // 计算 分页下标
        $limit = $page->cNum($count);
        $data = $this->user->getAllOrdersLists($where, $limit);
      }


      //全部通过, 分页  分页也要带参数和不带参数


      //整理数据


      include_once 'View/user/orderList.html';
    }

    public function count($where = '')
    {
      $data = $this->user->doCount($where);

      return $data;
    }


    public function orderDetail()
    {
      self::isLogin();

      $where = ' ordernum = ' . $_GET['ordernum'];
      $data = $this->user->getOrder($where);
      if (empty($data)) {
        myNotice('非法访问', './index.php?c=user&m=orderList');
      }

      include_once 'View/user/orderdetail.html';
    }

    //取消订单
    public function cancelOrder()
    {
      self::isLogin();


      $where = ' status = 1 and ordernum = ' . $_GET['ordernum'];
      $do['status'] = '-1';
      $res = $this->user->getOrderStatus($where);
      if (empty($res)) {
        myNotice('非法访问', './index.php?c=user&m=orderList');
      }
      unset($res);
      $res = $this->user->changeStatus($where, $do);
      if ($res === '-1') {
        myNotice('取消失败,请联系客服', './index.php');
      }

      myNotice('取消成功', './index.php');
    }

    //删除订单
    public function delOrderList()
    {
      self::isLogin();

      $where = ' (status = 5 OR status = 6) and ordernum = ' . $_GET['ordernum'];
      $res = $this->user->delOrderList($where);
      if (!empty($res)) {
        myNotice('删除成功', './index.php?c=user&m=orderList');
      } else {
        myNotice('删除失败', './index.php?c=user&m=orderList');
      }
    }

    //确认收货
    public function doOrder()
    {
      //验证
      if (empty($_GET['ordernum'])) {
        myNotice('非法访问', './index.php');
      }
      $where = ' status = 3 and  ordernum = ' . $_GET['ordernum'];
      $res = $this->user->getOrderStatus($where);
      if (empty($res)) {
        myNotice('非法访问', './index.php');
      }
      $tmp['credit'] = $res['total'] * CREDITS;
      $arr['status'] = 6;
      $arr['ordernum'] = $_GET['ordernum'];
      //改变状态
      $where .= ' and uid = ' . $_SESSION['user']['uid'];
      $res = $this->user->changeStatus($where, $arr);

      $res = $this->user->editProfile($tmp);
      unset($arr);
      unset($tmp);
      header('location: ./index.php?c=user&m=orderList');
      die;
    }


    static public function isLogin()
    {
      if (empty($_SESSION['user']['mobile'])) {
        myNotice('请先登录', './index.php?c=user&m=login');
//      }
// elseif ($_SESSION['user']['status'] == 2) {
//        myNotice('该账号已被禁用','./index.php');
      } else {
        $res = new userModel();
        $check = $res->getUserInfo();
        if (empty($check)) {
          setcookie('mobile', '', time() - 1);
          setcookie('pwd', '', time() - 1);
          setcookie('uid', '', time() - 1);
          setcookie('name', '', time() - 1);
          unset($_SESSION['user']['uid']);
          unset($_SESSION['user']['mobile']);
          unset($_SESSION['user']['status']);
          unset($_SESSION['user']['icon']);
          unset($_SESSION['user']['name']);
          unset($_SESSION['user']);
          myNotice('非法用户', './index.php');
        } elseif ($check['status'] == 2) {
          myNotice('你的账号已被禁用,请联系管理员', './index.php');
        }
      }
    }

    public function yzm()
    {
      $yzm = new Validate;
      $yzm->doimg();
      $_SESSION['user']['code'] = $yzm->getCode();
    }


  }