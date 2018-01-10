<?php

  class indexController extends Controller
  {
    private $index;

    public function __construct()
    {
//      echo '111';
      parent::__construct();
//      echo '222';
      $this->index = new indexModel;
    }

    public function index()
    {
      //先获取一级分类的所有id
      $data = $this->index->getFirstCategory();
      // 重组数组, 下标为一级分类id
      $data = firstCategoryToKey($data);

      // 获取所有子分类,添加到 一级分类下的path 包括自己
      foreach ($data as $k => $v) {
        $data[$k]['cids'] = $this->index->getChildCategory($k);
//        var_dump($data);
        //同时查询商品
        $data[$k] = $this->index->getGoodsInfo(' c.id = cid and g.cid in (' . $data[$k]['cids'] . ')', 'addtime desc',
          '6');
//        array_merge($data[$k],$tmp[0]);
      }

      //折扣商品
      $saleGoods = $this->index->getGoodsInfo(' c.id = cid and g.up = 1 and g.sale = 1 ', ' addtime desc', '5');

      //热销标签
      $hotGoods = $this->index->getGoodsInfo(' c.id = cid and g.up = 1 and  g.hot = 1 ', ' addtime desc', '5');


//      var_dump($data);die;


      include_once 'View/index/index.html';
    }

    public function saleGoods()
    {


    }


  }

