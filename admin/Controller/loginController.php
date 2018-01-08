<?php

	class loginController
	{
		private $login;

		public function __construct()
		{
			$this->login = new loginModel;	
		}

		public function index()
		{
			include 'View/login/index.html';
		}

		public function doLogin()
		{
			// 调用Model类
			$data = $this->login->doLogin();

			if($data){
				myNotice('登录成功', 'index.php');
			}
			myNotice('登录失败', 'index.php?c=login');
		}

		public function __call($k, $v)
		{
			myNotice('您访问的页面不存在', 'index.php');
		}

	}
