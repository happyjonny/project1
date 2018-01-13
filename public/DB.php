<?php 
	
	class DB
	{

    /*
     * 错误代码:
     * 10001: select语句错误
     * 10002: find语句错误
     */
		public $field = '*';
		public $table = '';
		public $where = '';
		public $group = '';
		public $having = '';
		public $order = '';
		public $limit = '';
		public $db;
		public $sql;

		public function __construct()
		{
			// 自动生成属性
			$this->db = new PDO(DSN, USER, PWD);
		}

		public function select()
		{
			/*			
				select xxx
				from xxx
				where xxx
				grouy by xx
				having xxx
				order by xxx 
				limit xx,xx
			*/

			$sql =   $this->field.'
					from '.$this->table.'
					'.$this->where.'
					'.$this->group.'
					'.$this->having.'
					'.$this->order.' 
					'.$this->limit;
			$this->sql = $sql;
      $this->initialization();

			$res = $this->db->query($sql);
//			var_dump($sql);die;

      if (!$res) {
        throw new Exception('数据库查询语句出错:' . $this->sql, 10001);
      }

			$data = $res->fetchALL(PDO::FETCH_ASSOC);
//			var_dump($data);die;
			return $data;
		}

		public function find()
		{
			$sql =   $this->field.'
					from '.$this->table.'
					'.$this->where.'
					'.$this->group.'
					'.$this->having.'
					'.$this->order.' 
					'.$this->limit;
			$this->sql = $sql;
      $this->initialization();

      echo $this->sql;
			$res = $this->db->query($sql);
      if (!$res) {
        throw new Exception('数据库查询语句出错', 10002);

      }
			$data = $res->fetch(PDO::FETCH_ASSOC);
			return $data;
		}

		public function insert( $arr = array() )
		{
			// insert into xxx() values()
			// 1. 判断$arr 是否有值
				if( empty($arr) ){
          return false;
				}

			// 2. 拼接数据
				$field = '';
				$value = '';
				foreach($arr as $k => $v){
					$field .= "`{$k}`,";
					$value .= "'{$v}',";
				}
				$field = rtrim($field, ',');
				$value = rtrim($value, ',');

			// 3. 准备sql
				$sql = 'insert into '.$this->table.'('.$field.') values('.$value.')';
				$this->sql = $sql;
      $this->initialization();
//        var_dump($sql);die;
			// 4. 执行sql 
				$res = $this->db->exec($sql);

				if($res){
					return $this->db->lastInsertId();
				}
				
				return false;
		}


		public function update( $arr = array() )
		{
			/*
				update xxx
				set  `` = "", `` = "", 
				where xxx
			 */
			// 1. 判断$arr 是否有值
				if( empty($arr) ){
					return false;
				}

			// 2. 拼接数据
				$field = '';
				foreach($arr as $k => $v){
					$field .= "`{$k}`='{$v}',";
				}
				$field = rtrim($field, ',');

      // 3. SQL
      $sql = 'update ' . $this->table . ' set ' . $field . $this->where;
      $this->sql = $sql;
      $this->initialization();

      // 4. 执行sql
      $res = $this->db->exec($sql);

      return $res;
    }

    //带函数的update
    // value 带mysql 函数 不需要'' 包起来
    public function funcUpdate($arr = array())
    {
      /*
        update xxx
        set  `` = "", `` = "",
        where xxx
       */
      // 1. 判断$arr 是否有值
      if (empty($arr)) {
        return false;
      }

      // 2. 拼接数据
      $field = '';
      foreach ($arr as $k => $v) {
        $field .= "`{$k}`= {$v},";
      }
      $field = rtrim($field, ',');

      // 3. SQL
      $sql = 'update ' . $this->table . ' set ' . $field . $this->where;
      $this->sql = $sql;
      $this->initialization();

      // 4. 执行sql
      $res = $this->db->exec($sql);

      return $res;
    }

		
		public function delete()
		{
			// delete from xxx where xxx 
      $this->sql = 'DELETE FROM ' . $this->table . $this->where;
      $this->initialization();
      $res = $this->db->exec($this->sql);


			return $res;
		}


		public function field($tj = '')
		{
			if(empty($tj)){
				$this->field = ' select * ';
			}else{
				$this->field = ' select '. $tj;
			}
			return $this;
		}

		public function table($tj = '')
		{
			if(empty($tj)){
				$this->table = '';
			}else{
				$this->table = ' '.$tj;
			}
			return $this;
		}

		public function where($tj = '')
		{
			if(empty($tj)){
				$this->where = '';
			}else{
				$this->where = ' where '.$tj;
			}
			return $this;
		}

		public function group($tj = '')
		{
			if(empty($tj)){
				$this->group = '';
			}else{
				$this->group = ' group by '.$tj;
			}
			return $this;
		}

		public function having($tj = '')
		{
			if(empty($tj)){
				$this->having = '';
			}else{
				$this->having = ' having '.$tj;
			}
			return $this;
		}

		public function order($tj = '')
		{
			if(empty($tj)){
				$this->order = '';
			}else{
				$this->order = ' order by '.$tj;
			}
			return $this;
		}

		public function limit($tj = '')
		{
			if(empty($tj)){
				$this->limit = '';
			}else{
				$this->limit = ' limit '.$tj;
			}
			return $this;
		}

    public function initialization()
    {
      $this->field = '*';
      $this->table = '';
      $this->where = '';
      $this->order = '';
      $this->group = '';
      $this->having = '';
      $this->limit = '';

    }

	}



 ?>