<?php 

	class Page
	{

		private $page;
		private $count;
		private $total;
    public $param;


		public function showPage()
		{

      // 获取当前文件地址 (网址)
      $src = $_SERVER['SCRIPT_NAME'];

      // 拼接原有的网址参数
      $param = '';
      foreach($_GET as $k => $v){
        if( $k == 'page' ){
          continue;
        }
        $param .= "{$k}={$v}&";
      }

      $src .= '?'.$param;

			// 数字链接
			$numList = '';
			for($i = 1; $i <= $this->total; $i++){
				$numList .= '<a href="'.$src.'page='.$i.'" target="mainFrame" onFocus="this.blur()"> '.$i.' </a>&nbsp;&nbsp;';
			}

			$html = $this->count.' 条数据 '.$this->page.'/'.$this->total.' 页&nbsp;&nbsp;';
			$html .= '<a href="'.$src.'page=1" target="mainFrame" onFocus="this.blur()">首页</a>&nbsp;&nbsp;';
			$html .= '<a href="'.$src.'page='.($this->page - 1).'" target="mainFrame" onFocus="this.blur()">上一页</a>&nbsp;&nbsp;';

			$html .= $numList;

			$html .= '<a href="'.$src.'page='.($this->page + 1).'" target="mainFrame" onFocus="this.blur()">下一页</a>&nbsp;&nbsp;';
			$html .= '<a href="'.$src.'page='.$this->total.'" target="mainFrame" onFocus="this.blur()">尾页</a>';

			return $html;
		}

    public function ushowPage()
    {
      // 获取当前文件地址 (网址)
      $src = $_SERVER['SCRIPT_NAME'];

      // 拼接原有的网址参数
      $param = '';
      foreach ($_GET as $k => $v) {
        if ($k == 'page') {
          continue;
        }
        $param .= "{$k}={$v}&";
      }

//      echo $this->param = $param;die;

      $src .= '?' . $param;
//      echo $src;die;
      $numList = '';
//      $html = '<li><a href="'.$src.'page=1>" <span> 1 </span></a> </li>';
//      $html .= '<li><a href="'.$src.'page='.($this->page -1).'><span> 1 </span> </a></li>';

      for ($i = 1; $i <= $this->total; $i++) {
//        if($i>10 && $i!=$this->total){
//          continue;
//        }
        $numList .= '<li><a href="' . $src . 'page=' . $i . '"><span> ' . $i . '</span> </a></li>';
      }


      return $numList;

    }

		public function cNum($count)
		{
			// 接收 page 页
				$this->page = empty($_GET['page'])?1:$_GET['page'];

			// 计算 总页数
      //如果count为0, total也等于0 直接返回 0,ROWS
      if($count == '0'){
        return '0,'.ROWS;
      }
				$this->count = $count;
				$this->total = ceil($count / ROWS);

			// 限制 page 的范围
				$this->page = max($this->page, 1);
				$this->page = min($this->page, $this->total);

			// 根据 page, 求 key
				$key = ($this->page - 1) * ROWS;

			return $key.','.ROWS;
		}




	}


