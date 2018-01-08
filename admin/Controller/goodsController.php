<?php

	class goodsController extends Controller
	{
		private $goods;

		public function __construct()
		{
			parent::__construct();
			$this->goods = new goodsModel;	
		}

		public function index()
		{
			// 接收 search 值
				$search = $_GET['search'];
				$where = null;
				if( !empty($search) ){
					$where = 'name like "%'.$search.'%"';
				}
			
			// 实例化 page.php
				$page = new Page;
			// 统计 总条数
				$count = $this->count($where);

			// 计算 分页下标
				$limit = $page->cNum($count);

			$data = $this->goods->showAll($where, $limit);
			include 'View/goods/index.html';
		}

		public function count($where = '')
		{
			$data = $this->goods->doCount($where);
			return $data;
		}
	
		// 加载 新增商品界面 
		public function add()
		{

		  //获取商品分类信息
		  $data = new categoryModel();
		  $data = $data->showAll(1);
//		  var_dump($data);
//		  die;
			include 'View/goods/add.html';
		}

		public function doAdd()
		{
			$data = $this->goods->doAdd();

			if($data){
				myNotice('新增成功', 'index.php?c=goods');
			}
			myNotice('新增失败');
		}

		// 加载 编辑商品界面
		public function edit()
		{
		  $data2 = new categoryModel();
			// 根据id 查询商品
			$data = $this->goods->showOne();
			//获取商品分类信息
      $data2 = $data2->showAll(1);


//			 var_dump($data , $data2);die;

			include 'View/goods/edit.html';
		}

		public function  details(){
      // 根据id 查询商品
      $data = $this->goods->showOne();
//      var_dump($data);die;
      include 'View/goods/details.html';
    }

		public function doEdit()
		{
			$data = $this->goods->doEdit();

			if($data){
				myNotice('编辑成功', 'index.php?c=goods');
			}
			myNotice('编辑失败');
		}

		// 执行 删除商品
		public function doDel()
		{
			$this->goods->doDel();
			header('location: index.php?c=goods'); die;
		}

		// 修改 状态
		public function doStatus()
		{
			$this->goods->doStatus();
			header('location: index.php?c=goods'); die;
		}

	}
