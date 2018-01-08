<?php

	class userController
	{
		private $user;

		public function __construct()
		{
			$this->user = new userModel;	
		}

		public function index()
		{

			$search = $_GET['search'];
			$where = null;
			if( !empty($search) ){
				$where = 'mobile like "%'.$search.'%"';
			}

			
			// 实例化 page.php
			$page = new Page;
			$count = $this->count();
			//$count 如果等于0
			$limit = $page->cNum($count);


			$data = $this->user->showAll($where, $limit);
//			echo $this->user->pdo->sql;die;
			include 'View/user/index.html';
		}

		public function count()
		{
			$data = $this->user->doCount();
			return $data;
		}
	
		// 加载 新增用户界面 
		public function add()
		{
			include 'View/user/add.html';
		}

		public function doAdd()
		{
			$data = $this->user->doAdd();

			if($data){
				myNotice('新增成功', 'index.php?c=user');
			}
//			var_dump($data);die;
			myNotice('新增失败');
		}

		// 加载 编辑用户界面
		public function edit()
		{
			// 根据id 查询用户
			$data = $this->user->showOne();

			include 'View/user/edit.html';
		}

		public function doEdit()
		{
			$data = $this->user->doEdit();

			if($data){
				myNotice('编辑成功', 'index.php?c=user');
			}
			myNotice('编辑失败');
		}

		// 执行 删除用户
		public function doDel()
		{
			$this->user->doDel();
			header('location: index.php?c=user'); die;
		}

		// 修改 状态
		public function doStatus()
		{
			$this->user->doStatus();
			header('location: index.php?c=user'); die;
		}

	}
