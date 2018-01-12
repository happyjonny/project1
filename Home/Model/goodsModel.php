<?php

  class goodsModel
  {
    private $pdo;

    public function __construct()
    {
      $this->pdo = new DB;
    }

    //得到一个分类ID, 返回该分类下所有子分类包括自己的分类ID
    public function showCate($id = '0')
    {

      // 1. 查询该分类的所有后辈id
      // select id
      // from category
      // where path like "%,1,%"
      $cid = self::getPath($id);
      return $cid;
    }

    public function getPath($id)
    {
      $cateId = $this->pdo
        ->field('id')
        ->table('category')
        ->where(' path like "%,' . $id . ',%" ')
        ->select();
//        var_dump($this->pdo->sql);
      $cid = '';
      foreach ($cateId as $k => $v) {
        $cid .= $v['id'] . ',';
      }
      $cid .= $id;

      return $cid;
    }

    //商品购买成功之后 更新库存与销量

    public function updateGoodsStock($arr = array(), $where)
    {
      if (!empty($arr)) {
        $res = $this->pdo
          ->table('goods')
          ->where($where)
          ->funcUpdate($arr);
//        var_dump($this->pdo->sql);die;
      }
    }


    // 查询所有的商品数据
    public function showAll($where = '', $limit = '', $order = '')
    {
      // 1. 查询该分类的所有后辈id
      // select id
      // from category
      // where path like "%,1,%"
      $id = $_GET['id'];
      $cid = self::showCate($id);
//      $cateId = $this->pdo
//        ->field('id')
//        ->table('category')
//        ->where(' path like "%,' . $id . ',%" ')
//        ->select();
//      echo $this->pdo->sql;
//      die;
//
//      // 将 cid 数组 转成 字符串
//      $cid = '';
//      foreach ($cateId as $k => $v) {
//        $cid .= $v['id'] . ',';
//      }
//      $cid .= $id;

      // 2. 查询商品cid 属于cateId之一, 就证明该商品属于该分类
      // select name, `desc`, price, sold, g.id, icon
      // from goods g, goodsimg i
      // where cid in (1,3,4,8,9,10,11,12) and g.id=i.gid and face = 1
      if (!empty($where)) {
        $where .= ' and ';
      }

      if (!empty($cid)) {
        $res = $this->pdo
          ->field('name, `desc`, price, sold, g.id, icon , hot , new , sale , recommend ')
          ->table('goods g, goodsimg i')
          ->where($where . 'cid in (' . $cid . ') and g.id=i.gid and face = 1')
          ->limit($limit)
          ->order($order)
          ->select();
      } else {
        $res = $this->pdo
          ->field('name, `desc`, price, sold, g.id, icon , hot , new , sale , recommend ')
          ->table('goods g, goodsimg i')
          ->where($where . ' g.id=i.gid and face = 1')
          ->limit($limit)
          ->order($order)
          ->select();
      }

//      echo $this->pdo->sql;
      return $res;
    }

    // 查询单个商品
    public function showOne()
    {
      // 接收 商品id
      $id = $_GET['id'];

      // 准备sql
      // select * from goods where id = xxx
      $res = $this->pdo
        ->field(' id, cid, name, price, stock,up, sold , hot, sale, `desc`, recommend, rate')
        ->table('goods')
        ->where('id = ' . $id)
//        ->order(' face desc')
        ->find();
//      echo $this->pdo->sql;
      return $res;
    }

    public function getImgs($id)
    {
      try {
        $res = $this->pdo
          ->field('icon, face')
          ->table('goodsimg')
          ->where(' gid = ' . $id)
          ->select();
        return $res;
      } catch (Exception $e) {
        myNotice('服务器出错了' . $e->getMessage(), './index.php');
      }
    }

    // 统计总个数
    public function doCount($where = '')
    {
      if (!empty($_GET['id'])) {
        $id = $_GET['id'];
        $cateId = $this->pdo
          ->field('id')
          ->table('category')
          ->where(' path like "%,' . $id . ',%" ')
          ->select();
        $cid = '';
        foreach ($cateId as $k => $v) {
          $cid .= $v['id'] . ',';
        }
        $cid .= $id;

        if (!empty($where)) {
          $where .= ' and ';
        }
        $res = $this->pdo
          ->field(' count(id) as count')
          ->table('goods')
          ->where($where . 'cid in (' . $cid . ')')
          ->select();
      } else {
        // 如果搜索所有商品
        $res = $this->pdo
          ->field(' count(id) as count')
          ->table('goods')
          ->where($where)
          ->select();
      }

//      var_dump($res);die;
      return $res[0]['count'];

    }

    public function doAdd()
    {
      // 数据验证

      // 其余验证自己写 ...
      $_POST['addtime'] = time();

      // 数据验证完成了,  调用DB, 插入数据
      // insert into goods() values();
      $gid = $this->pdo
        ->table('goods')
        ->insert($_POST);


      // 如果商品成功上传 , 则处理封面
      if ($gid) {
        // 封面
        $file = new Upload;
        $icon = $file->singleFile();

        // 上传成功, 则 操作 goodsimg表
        if (is_array($icon)) {
          $arr['gid'] = $gid;
          $arr['icon'] = $icon[0];
          $gid = $this->pdo
            ->table('goodsimg')
            ->insert($arr);
          return $gid;
        } else {
          // 删除该商品
        }
      }

      return false;
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

        if (is_string($icon)) {
          myNotice($icon);
        }

        $_POST['icon'] = $icon[0];
      }

      // 查询商品的原头像名
      // 	如果下面的更新成功. 删除该 原头像名  unlink()
      // 	如果下面的更新失败. 则不操作

      // 数据验证完成了,  调用DB, 更新数据
      $data = $this->pdo
        ->table('goods')
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
        ->table('goods')
        ->where('id = ' . $id)
        ->delete();
      return $res;
    }

    public function doStatus()
    {
      // 查询该商品的状态
      $id = $_GET['id'];
      // select status from goods where id = xx
      $res = $this->pdo
        ->field('status')
        ->table('goods')
        ->where('id = ' . $id)
        ->find();

      $res['status'] = ($res['status'] == 1 ? 2 : 1);

      $res = $this->pdo
        ->table('goods')
        ->where('id = ' . $id)
        ->update($res);
    }

    public function getGoodsInfo($gids = '')
    {
      if (empty($gids)) {
        return false;
      }
      try {
        $res = $this->pdo
          ->field('g.id as gid, stock, price , up, name , icon')
          ->table('goods as g, goodsimg as i ')
          ->where('g.id in (' . $gids . ') and g.id = i.gid and face =1')
          ->select();
        return $res;
      } catch (Exception $e) {
        myNotice('服务器出错', './index.php');
      }


    }


  }







  //
  //  class goodsModel
  //  {
  //    private $pdo;
  //
  //    public function __construct()
  //    {
  //      $this->pdo = new DB;
  //    }
  //
  //    // 查询所有的商品数据
  //    public function showAll($where, $limit)
  //    {
  //      // select * from goods
  //      $res = $this->pdo
  //        ->field('* ')
  //        ->table('goods')
  //        ->where($where)
  //        ->order('id desc')
  //        ->limit($limit)
  //        ->select();
  //
  //      foreach ($res as $k => $v) {
  //        $tmp = $this->pdo
  //          ->field('icon')
  //          ->table('goodsimg')
  //          ->where('face = 2 and gid = ' . $res[$k]['id'])
  //          ->order('id desc')
  //          ->limit($limit)
  //          ->find();
  //        $res[$k]['icon'] = $tmp['icon'];
  //        unset($tmp);
  //        $tmp = $this->pdo
  //          ->field('name')
  //          ->table('category')
  //          ->where(' id = ' . $res[$k]['cid'])
  //          ->find();
  //        // cname 分类名
  //        $res[$k]['cname'] = $tmp['name'];
  //        unset($tmp);
  //      }
  //
  ////			var_dump($res);die;
  //
  //      return $res;
  //    }
  //
  //    // 查询单个商品
  //    public function showOne()
  //    {
  //      // 接收 商品id
  ////				$id = $_GET['id'];
  ////
  ////			// 准备sql
  ////			// select * from goods where id = xxx
  ////				$res = $this->pdo
  ////							->field('*')
  ////							->table('goods')
  ////							->where(' id = '.$id)
  ////							->find();
  ////				$tmp =  $this->pdo
  ////          ->field('icon')
  ////          ->table('goodsimg')
  ////          ->where('face = 2 and gid = '.$res['id'])
  ////          ->find();
  ////				$res['icon'] = $tmp['icon'];
  ////				unset($tmp);
  //////      unset($tmp);
  ////      $tmp = $this->pdo
  ////        ->field('name')
  ////        ->table('category')
  ////        ->where(' id = '.$res['cid'])
  ////        ->find();
  ////      // cname 分类名
  ////      $res['cname'] = $tmp['name'];
  ////      unset($tmp);
  //////				var_dump($res);die;
  ////			return $res;
  //
  //      // 接收 商品id
  //      $id = $_GET['id'];
  //
  //      // 准备sql
  //      // select * from goods where id = xxx
  //      $res = $this->pdo
  //        ->field('*')
  //        ->table('goods')
  //        ->where(' id = ' . $id)
  //        ->find();
  //      return $res;
  //
  //    }
  //
  //    // 统计总个数
  //    public function doCount($where = '')
  //    {
  //      $res = $this->pdo
  //        ->field(' count(id) as count')
  //        ->table('goods')
  //        ->where($where)
  //        ->select();
  ////      var_dump($this->pdo->sql);die;
  //      return $res[0]['count'];
  //    }
  //
  //
  //
  //
  //  }
  //
