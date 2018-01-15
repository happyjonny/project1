<?php

  class categoryModel
  {
    private $pdo;

    public function __construct()
    {
      $this->pdo = new DB;
    }

    // 查询所有的分类数据
    public function showAll($m = '')
    {
      // 接收 id 当成 pid 使用


      // select * from category
      if (empty($m)) {
        if (empty($_GET['id'])) {
          $pid = 0;
          try {
            $res = $this->pdo
              ->field('*')
              ->table('category')
              ->where('pid = ' . $pid)
              ->order('id desc')
              ->select();
          } catch (Exception $e) {
            myNotice('非法访问', './index.php');
          }
        } else {
          $pid = $_GET['id'];
          try {
            $res = $this->pdo
              ->field('a.* , b.name as pidname')
              ->table('category as a, category as b')
              ->where('a.pid = ' . $pid . ' and b.id = ' . $pid)
              ->order('id desc')
              ->select();
          } catch (Exception $e) {
            myNotice('非法访问', './index.php');
          }
        }
      } else {
        try {
          $res = $this->pdo
            ->field('*')
            ->table('category')
//          ->where('pid = ' . )
            ->order('id desc')
            ->select();
        } catch (Exception $e) {
          myNotice('非法访问', './index.php');
        }
      }

      return $res;
    }

    public function orderCate()
    {
      // select name
      // from category
      // order by concat(path, id , ',')
      try {
        $res = $this->pdo
          ->field('path, name, id, concat(path, id , ",") as px')
          ->table('category')
          ->order('px')
          ->select();
        return $res;
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }
    }

    // 查询单个分类
    public function showOne()
    {
      // 接收 分类id
      $id = $_GET['id'];


      // 准备sql
      // select * from category where id = xxx
      try {
        $res = $this->pdo
          ->field('*')
          ->table('category')
          ->where(' id = ' . $id)
          ->find();
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }
      try {
        $tmp = $this->pdo
          ->field('name')
          ->table('category')
          ->where('id = ' . $res['pid'])
          ->find();
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }
      $res['pidname'] = $tmp['name'];
      unset($tmp);
      return $res;
    }

    // 统计总个数
    public function doCount($where = '')
    {
      try {
        $res = $this->pdo
          ->field(' count(id) as count')
          ->table('category')
          ->where($where)
          ->select();
        return $res[0]['count'];
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }

    }

    public function doAdd()
    {
      // 数据验证


      // 数据验证完成了,  调用DB, 插入数据
      // insert into category() values();
      try {
        $data = $this->pdo
          ->table('category')
          ->insert($_POST);
        return $data;
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }
    }

    public function doEdit()
    {
      // 数据验证
      $id = $_POST['id'];
      unset($_POST['id']);
      //查看分类下面有子分类的个数
      try {
        $tmp = $this->pdo
          ->field('count(id)')
          ->table('category')
          ->where('pid = ' . $id)
          ->select();
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }


      // 数据验证完成了,  调用DB, 更新数据
      //如果分类下面有子分类,则不给修改
      if ($tmp[0]['count(id)'] != 0) {
        return false;
      }
      try {
        $data = $this->pdo
          ->table('category')
          ->where(' id = ' . $id)
          ->update($_POST);

        return $data;
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }
    }

    public function doDel()
    {
      // 1. 接收 id 当pid使用
      $id = $_GET['id'];

      // 2. 根据 id 查询是否有 子类
      try {
        $res = $this->pdo
          ->field('id')
          ->table('category')
          ->where('pid = ' . $id)
          ->find();
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }

      // 3. 没有子类, 才能删除
      if (!$res) {
        try {
          $res = $this->pdo
            ->table('category')
            ->where('id = ' . $id)
            ->delete();
        } catch (Exception $e) {
          myNotice('非法访问', './index.php');
        }
      } else {
        myNotice('请先删除子分类');
      }
    }

    public function doStatus()
    {
      // 查询该分类的状态
      $id = $_GET['id'];
      // select status from category where id = xx
      try {
        $res = $this->pdo
          ->field('display')
          ->table('category')
          ->where('id = ' . $id)
          ->find();
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }
//				echo $res->pdo->sql;die;
      $res['display'] = ($res['display'] == 1 ? 2 : 1);
      try {
        $res = $this->pdo
          ->table('category')
          ->where('id = ' . $id)
          ->update($res);
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }
    }


  }

