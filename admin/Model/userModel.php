<?php

  class userModel
  {
    private $pdo;

    public function __construct()
    {
      $this->pdo = new DB;
    }

    // 查询所有的用户数据
    public function showAll($where, $limit)
    {
      // select * from user
      $res = $this->pdo
        ->field('*')
        ->table('user')
        ->where($where)
        ->order('id desc')
        ->limit($limit)
        ->select();
//      var_dump($res);
//      die;
      return $res;
    }

    // 查询单个用户
    public function showOne()
    {
      // 接收 用户id
      $id = $_GET['id'];

      // 准备sql
      // select * from user where id = xxx
      $res = $this->pdo
        ->field('*')
        ->table('user')
        ->where(' id = ' . $id)
        ->find();
      return $res;
    }

    // 统计总个数
    public function doCount($where = '')
    {
      $res = $this->pdo
        ->field(' count(id) as count')
        ->table('user')
        ->where($where)
        ->select();
      return $res[0]['count'];


    }

    public function doAdd()
    {
      // 数据验证
      // 1. 电话
      $preg = '/^1[34578]\d{9}$/';
      if (!preg_match($preg, $_POST['mobile'])) {
        myNotice('手机号码格式不正确');
      }

      // 2. 密码
      // 两次密码是否正确
      if ($_POST['pwd'] != $_POST['repwd']) {
        myNotice('两次密码不一致');
      }

      unset($_POST['repwd']);
      $_POST['pwd'] = md5($_POST['pwd']);

      // 3. 头像
      if($_FILES['icon']['error']!=4) {
        $file = new Upload;
        $icon = $file->singleFile();

        if (is_string($icon)) {
          myNotice($icon);
        }
      }
      // 其余验证自己写 ...
      $_POST['regtime'] = time();
      $_POST['icon'] = $icon[0];

      // 数据验证完成了,  调用DB, 插入数据
      // insert into user() values();
      $data = $this->pdo
        ->table('user')
        ->insert($_POST);
//      var_dump($this->pdo->sql, $data);
//      die;
      return $data;
    }

    public function doEdit()
    {
      // 数据验证
      // 密码
      if (empty($_POST['pwd'])) {
        unset($_POST['pwd']);
        unset($_POST['repwd']);
      } elseif ($_POST['repwd'] != $_POST['pwd']) {
        myNotice('两次密码不一致');
      } else {
        unset($_POST['repwd']);
        $_POST['pwd'] = md5($_POST['pwd']);
      }

      // 头像
      $key = key($_FILES);
      if ($_FILES[$key]['error'] != 4) {
        $file = new Upload;
        $icon = $file->singleFile();
        var_dump($icon);
        if (is_string($icon)) {
          myNotice($icon);
        }

        $_POST['icon'] = $icon[0];
      }

      // 查询用户的原头像名
      // 	如果下面的更新成功. 删除该 原头像名  unlink()
      // 	如果下面的更新失败. 则不操作

      // 数据验证完成了,  调用DB, 更新数据
      $data = $this->pdo
        ->table('user')
        ->where('id = ' . $_POST['id'])
        ->update($_POST);
      return $data;
    }

    public function doDel()
    {
      // 1. 接收 id
      $id = $_GET['id'];

      // 2. 调用DB类
      $res = $this->pdo
        ->table('user')
        ->where('id = ' . $id)
        ->delete();
      return $res;
    }

    public function doStatus()
    {
      // 查询该用户的状态
      $id = $_GET['id'];
      // select status from user where id = xx
      $res = $this->pdo
        ->field('status')
        ->table('user')
        ->where('id = ' . $id)
        ->find();

      $res['status'] = ($res['status'] == 1 ? 2 : 1);

      $res = $this->pdo
        ->table('user')
        ->where('id = ' . $id)
        ->update($res);
    }


  }

