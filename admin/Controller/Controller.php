<?php 

	class Controller
	{
		public function __construct()
		{
      if (empty($_SESSION['admin'])) {
        header('location: index.php?c=login');
        die;
      }
		}

		public function __call($k, $v)
		{
			myNotice('您访问的页面不存在', './index.php');
		}

	}
