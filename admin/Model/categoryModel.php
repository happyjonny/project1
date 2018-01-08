<?php 

	class categoryModel
	{
		private $pdo;

		public function __construct()
		{
			$this->pdo = new DB;
		}

		// 查询所有的分类数据
		public function showAll($m = '')
		{
			// 接收 id 当成 pid 使用


			// select * from category
      if(empty($m)) {
        if (empty($_GET['id'])) {
          $pid = 0;
          $res = $this->pdo
            ->field('*')
            ->table('category')
            ->where('pid = ' . $pid)
            ->order('id desc')
            ->select();
        } else {
          $pid = $_GET['id'];
          $res = $this->pdo
            ->field('a.* , b.name as pidname')
            ->table('category as a, category as b')
            ->where('a.pid = ' . $pid . ' and b.id = ' . $pid)
            ->order('id desc')
            ->select();
        }
      }else{
//        $pid = empty($_GET['id'])?0:$_GET['id'];
        $res = $this->pdo
          ->field('*')
          ->table('category')
//          ->where('pid = ' . )
          ->order('id desc')
          ->select();
      }
//			var_dump($res);die;

			return $res;
		}

		// 查询单个分类
		public function showOne()
		{
			// 接收 分类id
				$id = $_GET['id'];



			// 准备sql
			// select * from category where id = xxx
				$res = $this->pdo
							->field('*')
							->table('category')
							->where(' id = '.$id)
							->find();

				$tmp = $this->pdo
                    ->field('name')
                    ->table('category')
                    ->where('id = '.$res['pid'])
                    ->find();
				$res['pidname'] = $tmp['name'];
//				var_dump($res);die;
				unset($tmp);
//				echo $this->pdo->sql;die;
			return $res;
		}

		// 统计总个数
		public function doCount($where = '')
		{
			$res = $this->pdo
						->field(' count(id) as count')
						->table('category')
						->where($where)
						->select();

			return $res[0]['count'];
		}

		public function doAdd()
		{
			// 数据验证
				

			// 数据验证完成了,  调用DB, 插入数据
				// insert into category() values();
				$data = $this->pdo
							 ->table('category')
							 ->insert($_POST);

				return $data;
		}

		public function doEdit()
		{
			// 数据验证
//      var_dump($_POST);die;

			// 数据验证完成了,  调用DB, 更新数据
					$data = $this->pdo
								 ->table('category')
								 ->where('id = '.$_POST['id'])
								 ->update($_POST);
					return $data;
		}

		public function doDel()
		{
			// 1. 接收 id 当pid使用
				$id = $_GET['id'];

			// 2. 根据 id 查询是否有 子类
				$res = $this->pdo
							 ->field('id')
							 ->table('category')
							 ->where('pid = '.$id)
							 ->find();


			// 3. 没有子类, 才能删除
				if(!$res){
					$res = $this->pdo
								 ->table('category')
								 ->where('id = '.$id)
								 ->delete();
				}else{
					myNotice('请先删除子分类');
				}
		}

		public function doStatus()
		{
			// 查询该分类的状态
				$id = $_GET['id'];
				// select status from category where id = xx
				$res = $this->pdo
							  ->field('display')
							  ->table('category')
							  ->where('id = '.$id)
							  ->find();
//				echo $res->pdo->sql;die;
				$res['display'] = ($res['display'] == 1?2:1);

				$res = $this->pdo
							  ->table('category')
							  ->where('id = '.$id)
							  ->update($res);
		}


	}

