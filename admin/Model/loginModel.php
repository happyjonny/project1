<?php 

	class loginModel
	{
		private $pdo;

		public function __construct()
		{
			$this->pdo = new DB;
		}

		public function doLogin()
		{
			// 1. 接收数据
				// var_dump($_POST); die;
				$nickname = $_POST['nickname'];
				$pwd = md5($_POST['pwd']);

			// 2. 验证数据
				// 手机号 正则验证...
				// 密码 自己玩


			// 3. 准备sql
				// select nickname, id from user where tel = xxx and pwd = xxx
				$res = $this->pdo
							->field('`id`')
							->table('`admin`')
							->where('nickname = "'.$nickname.'" and pwd = "'.$pwd.'" ')
							->find();

			// 5. 存储session
			// 6. 返回结果
				if($res){
					$_SESSION['admin']['nickname'] = $nickname;
					$_SESSION['admin']['id'] = $res['id'];
				}

				return $res;


		}


	

		


	}

