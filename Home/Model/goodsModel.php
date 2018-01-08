<?php

  class goodsModel
  {
    private $pdo;

    public function __construct()
    {
      $this->pdo = new DB;
    }

    // 查询所有的商品数据
    public function showAll($where, $limit)
    {
      // select * from goods
      $res = $this->pdo
        ->field('* ')
        ->table('goods')
        ->where($where)
        ->order('id desc')
        ->limit($limit)
        ->select();

      foreach ($res as $k => $v) {
        $tmp = $this->pdo
          ->field('icon')
          ->table('goodsimg')
          ->where('face = 2 and gid = ' . $res[$k]['id'])
          ->order('id desc')
          ->limit($limit)
          ->find();
        $res[$k]['icon'] = $tmp['icon'];
        unset($tmp);
        $tmp = $this->pdo
          ->field('name')
          ->table('category')
          ->where(' id = ' . $res[$k]['cid'])
          ->find();
        // cname 分类名
        $res[$k]['cname'] = $tmp['name'];
        unset($tmp);
      }

//			var_dump($res);die;

      return $res;
    }

    // 查询单个商品
    public function showOne()
    {
      // 接收 商品id
//				$id = $_GET['id'];
//
//			// 准备sql
//			// select * from goods where id = xxx
//				$res = $this->pdo
//							->field('*')
//							->table('goods')
//							->where(' id = '.$id)
//							->find();
//				$tmp =  $this->pdo
//          ->field('icon')
//          ->table('goodsimg')
//          ->where('face = 2 and gid = '.$res['id'])
//          ->find();
//				$res['icon'] = $tmp['icon'];
//				unset($tmp);
////      unset($tmp);
//      $tmp = $this->pdo
//        ->field('name')
//        ->table('category')
//        ->where(' id = '.$res['cid'])
//        ->find();
//      // cname 分类名
//      $res['cname'] = $tmp['name'];
//      unset($tmp);
////				var_dump($res);die;
//			return $res;

      // 接收 商品id
      $id = $_GET['id'];

      // 准备sql
      // select * from goods where id = xxx
      $res = $this->pdo
        ->field('*')
        ->table('goods')
        ->where(' id = ' . $id)
        ->find();
      return $res;

    }

    // 统计总个数
    public function doCount($where = '')
    {
      $res = $this->pdo
        ->field(' count(id) as count')
        ->table('goods')
        ->where($where)
        ->select();
//      var_dump($this->pdo->sql);die;
      return $res[0]['count'];
    }

    public function doAdd()
    {
      // 数据验证

      // 其余验证自己写 ...
      $_POST['addtime'] = time();
      $_POST['uptime'] = time();

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
          //保存 后期补图片
          return $gid;
        }
      }

      return false;
    }

    public function doEdit()
    {
      // 数据验证

      $gid = $_POST['id'];


      $key = key($_FILES);
      if ($_FILES[$key]['error'] != 4) {
        $file = new Upload;
        $icon = $file->singleFile();

        if (is_array($icon)) {
          $arr['gid'] = $gid;
          $arr['icon'] = $icon[0];
          // 把原来的封面图删除
          $tmp = $this->pdo
            ->table('goodsimg')
            ->where('face  = 2 and gid = ' . $_POST['id'])
            ->delete();
          //添加新图片入库
          $gid = $this->pdo
            ->table('goodsimg')
            ->insert($arr);
          return $gid;
        } else {
          // 删除该商品
          //保存 后期补图片
          return $gid;
        }
      }

      $_POST['uptime'] = time();

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

      $res = $this->pdo
        ->table('goodsimg')
        ->where('gid = ' . $id)
        ->delete();
      return $res;
    }

    public function doStatus()
    {
      // 查询该商品的状态
      $id = $_GET['id'];
      // select status from goods where id = xx
      $res = $this->pdo
        ->field('up')
        ->table('goods')
        ->where('id = ' . $id)
        ->find();

      $res['up'] = ($res['up'] == 1 ? 2 : 1);

      $res['uptime'] = time();
      $res = $this->pdo
        ->table('goods')
        ->where('id = ' . $id)
        ->update($res);
    }


  }

