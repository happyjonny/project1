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
//      var_dump($data);die;die
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
    }

    public function cart()
    {
      self::isLogin();
      //没图片
      $data = $this->user->getCartItemInfoAll();
//      var_dump($data);die;die
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
    }


    public function login()
    {
      include_once 'View/user/login.html';
    }

    public function profile()
    {
      self::isLogin();
      $data = $this->user->getUserInfo();
//      var_dump($data);
      include_once 'View/user/profile.html';
    }

    public function address()
    {
      self::isLogin();

      $data = $this->user->getAddress('uid = ' . $_SESSION['user']['uid']);
//       var_dump($data);die;
      include_once 'View/user/address.html';
    }

    public function addressadd()
    {
      self::isLogin();

      include_once 'View/user/addressadd.html';
    }

    public function doaddressadd()
    {
      if (empty($_POST['address']) || empty($_POST['realname']) || empty($_POST['tel'])) {
        myNotice('输入有误', 3);
      }

      //验证信息
      //验证地址
      $preg = "/\W+/u";
      if (preg_match($preg, $_POST['address'])) {
        myNotice('地址格式不正确');
      }

      $preg = '/^[\x{4e00}-\x{9fa5}]{2,10}$|^[a-zA-Z\s]*[a-zA-Z\s]{2,20}$/isu';
      if (!preg_match($preg, $_POST['realname'])) {
        myNotice('姓名格式不正确');
      }

      $preg = '/^1[34578]\d{9}$/';
      if (!preg_match($preg, $_POST['tel'])) {
        myNotice('手机号码格式不正确');
      }

      //TODO
//      //地区可以不填
//      if (empty($_POST['area'])){
//        unset($_POST['area']);
//      }
      //验证地区
//      $preg ='/^[\x{4e00}-\x{9fa5}]{2,10}$|^[a-zA-Z\s]*[a-zA-Z\s]{2,20}$/isu';
//      if(preg_match($preg,$_POST['area'])){
//        myNotice('地区格式不正确');
//      }


      $_POST['uid'] = $_SESSION['user']['uid'];

      //查询该用户已有收货信息
      $res = $this->user->getAddress('uid = ' . $_SESSION['user']['uid']);
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
      $data = $this->user->addressdoDel('id = ' . $_GET['aid']);
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
        myNotice('请检查用户名密码');
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

        $_SESSION['user']['uid'] = $uid;
        $_SESSION['user']['mobile'] = $_POST['mobile'];

        $userInfo = $this->user->getUserInfo();
        $_SESSION['user']['status'] = $userInfo['status'];
        $_SESSION['user']['icon'] = $userInfo['icon'];
        $_SESSION['user']['name'] = $userInfo['name'];
        header('location: ./index.php');
        die;
      }

    }

    //添加至购物车
    public function addCart()
    {
//      var_dump($_POST);
      self::isLogin();
      //保证 uid为session的uid
      // 即时用户篡改input标签 也可以正常使用
      $_POST['uid'] = $_SESSION['user']['uid'];
      if (empty($_POST['gid'])) {
        header('./index.php?c=user&m=cart');
        die;
      }
      //先判断库存是否为空
      if ($_POST['stock'] < 1) {
        myNotice('该商品已售空');
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
        myNotice('添加购物车失败');
      }

    }

    //修改会员信息
    public function editProfile()
    {
      //密码验证
      //2次密码不一致
//      var_dump($_POST);
      if (empty($_POST['pwd'])) {
        unset($_POST['pwd']);
        unset($_POST['repwd']);
      } else {
        if ($_POST['pwd'] != $_POST['repwd']) {
          myNotice('两次密码不一致,请重新输入');
        }
        $preg = '/^[a-zA-Z0-9]{6,20}$/';
        if (!preg_match($preg, $_POST['pwd'])) {
          myNotice('密码格式不正确');
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
          myNotice('昵称格式不正确');
        }
      }
      //地址
      if (empty($_POST['address'])) {
        unset($_POST['address']);
      } else {
        $preg = "/\W+/u";
        if (preg_match($preg, $_POST['address'])) {

          myNotice('地址格式不正确');
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

      $res = $this->user->editProfile();

      if ($res !== false) {
        myNotice('更新成功');
      } else {
        myNotice('更新失败');
      }

    }

    //创建新订单
    public function ordercreate()
    {
//      var_dump($_POST);die;
      //验证数量: 必须大于0
      $preg = '/^[1-9]\d*$/';

      foreach ($_POST as $k => $v) {
        if (!preg_match($preg, $_POST[$k])) {
          myNotice('请重新确认你购买的数量');
        }
      }

      //验证是否有收货地址
      $add = $this->user->getAddress(' uid = ' . $_SESSION['user']['uid']);
      if (empty($add)) {
        myNotice('请先添加收货地址', './index.php?c=user&m=addressadd');
      }

      $goods = $this->user->validorderCreate();
      if ($goods) {
        $address = $this->user->getAddress(' uid = ' . $_SESSION['user']['uid']);
        $_SESSION['user']['cart']['goodsinfos'] = $goods;
        include_once './View/user/ordercreate.html';
      } else {
        myNotice('你的购物车信息有误,请查看或联系管理员', './index.php?c=user&m=cart', 3);
      }
    }

    public function doOrderCreate()
    {

      //$res 为新创建的订单id
      $res = $this->user->doOrderCreate($this->goodsInfos);
      if ($res) {
        //创建成功
        myNotice('下单成功', './index.php');

      } else {
        //创建失败
        myNotice('出问题了');
      }

    }

    public function doDelCart()
    {
      $this->user->doDelCart();
      header('location: ./index.php?c=user&cart');
      die;
    }

    public function orderList()
    {
      //验证来源
      //param  status  ordernum
      // 实例化 page.php


      $page = new Page;
      // 统计

      //TODO 先获取订单  再每个订单遍历

      //如果带条件
      if (!empty($_GET['ordernum'])) {
        if ($_GET['status'] === '-1' || ($_GET['status'] >= '1' || $_GET['status'] <= '6')) {
          $where = ' uid = ' . $_SESSION['user']['uid'] . ' and status = ' . $_GET['status'] . ' and ordernum = ' . $_GET['ordernum'];
          $count = $this->count($where);
          // 计算 分页下标
          $limit = $page->cNum($count);
          $data = $this->user->getAllOrders($where, $limit);
        } else {

          $where = ' uid = ' . $_SESSION['user']['uid'] . ' and ordernum = ' . $_GET['ordernum'];
          $count = $this->count($where);
          // 计算 分页下标
          $limit = $page->cNum($count);
          $data = $this->user->getAllOrders($where, $limit);
        }
      } elseif ($_GET['status'] === '-1' || ($_GET['status'] >= '1' && $_GET['status'] <= '6')) {

        $where = ' uid = ' . $_SESSION['user']['uid'] . ' and status = ' . $_GET['status'];
        $count = $this->count($where);
        // 计算 分页下标
        $limit = $page->cNum($count);
        $data = $this->user->getAllOrders($where, $limit);
      } else {
        //没有输入订单号查询 也没有订单状态( 订单列表首页, 不带参查询) 或者 只有订单状态 且只为0 或者非其他预留状态值
        $where = ' uid = ' . $_SESSION['user']['uid'];
        $count = $this->count($where);
        // 计算 分页下标
        $limit = $page->cNum($count);
        $data = $this->user->getAllOrders($where, $limit);
      }

      var_dump($data);

      //全部通过, 分页  分页也要带参数和不带参数


      //整理数据


      include_once 'View/user/orderList.html';
    }

    public function count($where = '')
    {
      $data = $this->user->doCount($where);
      return $data;
    }

    public function doOrderList()
    {
      var_dump($_GET);
      die;
    }


    static public function isLogin()
    {
      if (empty($_SESSION['user'])) {
        myNotice('请先登录', './index.php?c=user&m=login');
      }
    }


  }