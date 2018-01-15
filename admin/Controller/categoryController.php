<?php

	class categoryController extends Controller
	{
		private $category;

		public function __construct()
		{
			parent::__construct();
			$this->category = new categoryModel;	
		}

		public function index()
		{
			$data = $this->category->showAll();


			include 'View/category/index.html';
		}

    // 查询并排序
    public function orderCate()
    {
      $data = $this->category->orderCate();
      return $data;
    }

		public function count($where = '')
		{
			$data = $this->category->doCount($where);
			return $data;
		}
	
		// 加载 新增分类界面 
		public function add()
		{
			// 接收父级id
				$pid = empty($_GET['id'])?0:$_GET['id'];
				$path = empty($_GET['path'])? '0,' :  $_GET['path'].$_GET['id'].',';
				if($pid != 0){
				  //获取上级分类名称
        $data = $this->category->showOne();
        }
//        var_dump($data);die;
			include 'View/category/add.html';
		}

		public function doAdd()
		{
      foreach ($_POST as $k => $v) {
        $_POST[$k] = strip_tags($_POST[$k]);
      }
			$data = $this->category->doAdd();
//      var_dump($data);die;
			if($data){
				myNotice('新增成功', 'index.php?c=category');
			}
			myNotice('新增失败');
		}

		// 加载 编辑分类界面
		public function edit()
		{
			// 根据id 查询分类
      foreach ($_POST as $k => $v) {
        $_POST[$k] = strip_tags($_POST[$k]);
      }
			$data = $this->category->showOne();

      $cate = new CategoryController;
      $data2 = $cate->orderCate();
      foreach ($data2 as $k => $v) {
        // 1. 统计逗号的个数 $num
        $num = substr_count($v['px'], ',');
        // 2. 将空格 重复$num次
        $nbsp = str_repeat('-', ($num - 2) * 8);
        // 3. 将空格 塞到$data
        $data2[$k]['nbsp'] = $nbsp;
      }

//      var_dump($data,$data2);die;

			include 'View/category/edit.html';
		}

		public function doEdit()
		{
//      var_dump($_POST);die;
      foreach ($_POST as $k => $v) {
        $_POST[$k] = strip_tags($_POST[$k]);
      }
			$data = $this->category->doEdit();

			if($data){
				myNotice('编辑成功', 'index.php?c=category');
			}
			myNotice('编辑失败');
		}

		// 执行 删除分类
		public function doDel()
		{
			$this->category->doDel();
			header('location: index.php?c=category'); die;
		}

		// 修改 状态
		public function doStatus()
		{
			$this->category->doStatus();
			header('location: index.php?c=category'); die;
		}

	}
