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
      //获取所有整理好的一级 二级分类
      $handler = new baseModel();
      $category = $handler->getTreeClassList(2);
      $data = array();
      $data2 = array();
      // 获取所有一级分类(包括子分类)下的商品
      foreach ($category as $k => $v) {
        if ($v['deep'] == 1) {
          $data[$v['id']] = $this->index->getChildCategory($v['id']);
          $data[$v['id']] = $this->index->getGoodsInfo(' c.id = cid and g.up = 1 and g.cid in (' . $data[$v['id']] . ')',
            'addtime desc',
            '6');
        } else {
          continue;
        }
      }

      //遍历获取的商品, 查询其商品封面
      foreach ($data as $k => $v) {
        foreach ($data[$k] as $key => $value) {
          $data[$k][$key]['icon'] = $this->index->getSingleIcon(' gid = ' . $data[$k][$key]['id']);
        }
      }





      // 获取所有一级分类(包括子分类)下 推荐 的商品
      foreach ($category as $k => $v) {
        if ($v['deep'] == 1) {
          $data2[$v['id']] = $this->index->getChildCategory($v['id']);
          $data2[$v['id']] = $this->index->getGoodsInfo(' c.id = cid and (g.recommend = 1 or g.hot = 1) and g.up = 1 and g.cid in (' . $data2[$v['id']] . ')',
            'addtime desc',
            '3');

          //获取商品图片
          if(!empty($data2[$v['id']])){
           foreach ($data2[$v['id']] as $key => $value){
             $data2[$v['id']][$key]['icon'] = $this->index->getSingleIcon(' gid = ' . $value['id'] );

            }
          }
        } else {
          continue;
        }

      }




      //折扣商品
      $saleGoods = $this->index->getGoodsInfo(' c.id = cid and g.up = 1 and g.sale = 1 ', ' addtime desc', '6');
      //加载商品图片
      foreach ($saleGoods as $k => $v) {
        $saleGoods[$k]['icon'] = $this->index->getSingleIcon(' gid = ' . $saleGoods[$k]['id']);
      }

      //热销标签
      $hotGoods = $this->index->getGoodsInfo(' c.id = cid and g.up = 1 and  g.hot = 1 ', ' addtime desc', '6');
      foreach ($saleGoods as $k => $v) {
        $hotGoods[$k]['icon'] = $this->index->getSingleIcon(' gid = ' . $hotGoods[$k]['id']);
      }

      include_once 'View/index/index.html';

      unset($data);
      unset($data2);
    }



  }

