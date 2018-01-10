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
//        echo $this->pdo->sql.'<br>';
      } catch (Exception $e) {
        echo $e->getMessage();
      }
      return $res;
    }

    //获取一级分类
    public function getFirstCategory()
    {

      try {
        $res = $this->pdo
          ->field('id')
          ->table('category')
          ->where('pid = 0')
          ->select();
        return $res;
      } catch (Exception $e) {
        myNotice('查询错误', './index.php');
      }
    }

    public function getChildCategory($id)
    {
      $cateId = $this->pdo
        ->field('id')
        ->table('category')
        ->where(' path like "%,' . $id . ',%" and display = 1 ')
        ->select();
//      echo $this->pdo->sql;die;

      // 将 cid 数组 转成 字符串
      $cid = '';
      foreach ($cateId as $k => $v) {
        $cid .= $v['id'] . ',';
      }
//      var_dump($cid);die;
      $cid .= $id;
      return $cid;
    }



  }

