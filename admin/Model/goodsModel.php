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
          ->where('face = 1 and gid = ' . $res[$k]['id'])
          ->order('id desc')
//          ->limit($limit)
          ->find();
//        echo $this->pdo->sql;die;
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
      $this->pdo->initialization();
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


      // 接收 商品id
      $id = $_GET['id'];

      // 准备sql
      // select * from goods where id = xxx
      $res = $this->pdo
        ->field('g.id , cid , g.name , price , stock , sold , up , hot , `desc` , addtime  , uptime , new , recommend , sale , rate , c.name as cname , i.icon')
        ->table('goods as g ,category as c , goodsimg as i')
        ->where(' g.cid = c.id and i.gid = g.id and  g.id = ' . $id)
        ->find();
//      echo $this->pdo->sql;die;
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
          $arr['face'] = 1;
          $gid = $this->pdo
            ->table('goodsimg')
            ->insert($arr);
          return $gid;
        } else {
          //必须上传图片
          myNotice('上传图片失败', './index.php?c=goods&m=add');
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
          $arr['face'] = 1;
          // 把原来的封面图删除
          $tmp = $this->pdo
            ->table('goodsimg')
            ->where('face  = 1 and gid = ' . $gid)
            ->delete();
          //添加新图片入库
          $gid = $this->pdo
            ->table('goodsimg')
            ->insert($arr);
//          echo $this->pdo->sql;die;
          return $gid;
        } else {
          // 删除该商品
          // 保存 后期补图片
          return $gid;
        }
      }

      $_POST['uptime'] = time();

      // 数据验证完成了,  调用DB, 更新数据
      $data = $this->pdo
        ->table('goods')
        ->where('id = ' . $_POST['id'])
        ->update($_POST);
      echo $this->pdo->sql;
      die;
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

    public function showImgs()
    {

      $id = $_GET['id'];
      $res = $this->pdo
        ->field(' * ')
        ->table(' goodsimg ')
        ->where(' gid = ' . $id)
        ->select();
      return $res;
    }

    public function uploadImg($arr1 = array())
    {
      if (empty($_POST['gid'])) {
        $gid = $arr1['gid'];
      } else {
        $gid = $_POST['gid'];
      }

      //添加图片
      $key = key($_FILES);
      if ($_FILES[$key]['error'] != 4) {
        $file = new Upload;
        $icon = $file->singleFile();

        if (is_array($icon)) {
          if (is_array($icon)) {
            $arr['gid'] = $gid;
            $arr['icon'] = $icon[0];
            $arr['face'] = 1;

            //添加新图片入库
            $gid = $this->pdo
              ->table('goodsimg')
              ->insert($arr);
//          echo $this->pdo->sql;die;
            return $gid;
          }
        }
      }

      return false;
    }

    public function setFace($arr = array())
    {
      if (!empty($arr)) {
        //先判斷來源,如果是get方式傳遞,判断是否合法
        if ($arr['flag'] == 1) {
          $flag = $this->isvalidImg($arr);
          if (empty($flag)) {
            //如果不合法, 返回false
            return false;
          } else {
            //如果合法, unset flag
            unset($arr['flag']);
          }
        }


        //全部设置非封面
        $tmp['face'] = 2;
        $res = $this->pdo
          ->table('goodsimg')
          ->where(' gid = ' . $arr['gid'])
          ->update($tmp);

//        echo '设置全部为2 : '.$this->pdo->sql.'<br>';
        unset($tmp);
        unset($res);
        if (empty($arr['id'])) {
          //如果第一次上传图片, 用gid 作为查询条件
          $res = $this->pdo
            ->table('goodsimg')
            ->where(' gid = ' . $arr['gid'])
            ->update($arr);
        } else {
          //如果已经存在商品图片, 则用图片id作为查询条件
          $res = $this->pdo
            ->table('goodsimg')
            ->where(' id = ' . $arr['id'])
            ->update($arr);
        }

//        echo '设置 1: '.$this->pdo->sql.'<br>';die;
      }
    }

    //判断要操作的图片与此图片的商品id是否匹配(来源:GET方式)
    public function isvalidImg($arr = array())
    {
      //如果合法,返回true
      try {
        if (!empty($arr)) {
          $res = $this->pdo
            ->field('id')
            ->table('goodsimg')
            ->where(' id = ' . $arr['id'] . ' and gid = ' . $arr['gid'])
            ->find();
          return $res;
        }
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }

    }


    public function delImg($arr = array())
    {
      try {
        if ($arr['flag'] == 1) {
          //如果从get方式过来的数据
          $check = $this->isvalidImg($arr);
          if (empty($check)) {
            //如果不合法
            return false;
          } else {
            //如果合法 删除文件,再删除数据
            //先获取图片文件名
            $img = $this->pdo
              ->field('icon , face')
              ->table('goodsimg')
              ->where(' id = ' . $arr['id'])
              ->find();
//            var_dump($img);die;
            if ($img['face'] == 1) {
              return false;
            }

            $img = imgDir($img['icon']);

            if (is_file($img)) {
              //如果文件地址正确(该文件存在于此目录)
              //删除
              if (unlink($img)) {
                //如果删除成, 则操作数据库
                $ddd = $this->pdo
                  ->table('goodsimg')
                  ->where(' id = ' . $arr['id'])
                  ->delete();
//                echo $this->pdo->sql;die;
                return true;
              } else {
                return false;
              }

            } else {
              return false;
            }
          }

        }
      } catch (Exception $e) {

      }

    }


  }

