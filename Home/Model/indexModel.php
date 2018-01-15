<?php

  class indexModel
  {
    private $pdo;

    public function __construct()
    {
      $this->pdo = new DB;


    }


    public function getGoodsInfo($where = '', $order = '', $limit = '', $group = '')
    {
      try {
        $res = $this->pdo
          ->field(' g.id , cid , g.name as goodsName, price , stock , stock , sold , up , hot ,`desc`, addtime, uptime, new, recommend , sale, rate, c.id as categoryId, c.name as categoryName')
          ->table(' goods as g, category as c  ')
          ->order($order)
          ->where($where)
          ->group($group)
          ->limit($limit)
          ->select();
      return $res;
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }
    }

    //获取一级分类
    public function getFirstCategory($where)
    {

      try {
        $res = $this->pdo
          ->field('id')
          ->table('category')
          ->where($where)
          ->select();
        return $res;
      } catch (Exception $e) {
        myNotice('查询错误', './index.php');
      }
    }

    public function getChildCategory($id)
    {
      try {
        $cateId = $this->pdo
          ->field('id')
          ->table('category')
          ->where(' path like "%,' . $id . ',%" and display = 1 ')
          ->select();
      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }

      // 将 cid 数组 转成 字符串
      $cid = '';
      foreach ($cateId as $k => $v) {
        $cid .= $v['id'] . ',';
      }
      $cid .= $id;
      return $cid;
    }

    public function getSingleIcon($where)
    {
      try {
        $res = $this->pdo
          ->field('icon')
          ->table('goodsimg')
          ->where($where)
          ->find();
        return $res['icon'];

      } catch (Exception $e) {
        myNotice('非法访问', './index.php');
      }
    }



  }

