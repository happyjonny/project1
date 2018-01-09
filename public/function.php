<?php 

	// 专门存放 公共函数
	function myNotice($msg, $url = '', $time = 1){

		echo '<div style="width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); position: fixed; top: 0; left: 0;"></div>';

		echo '<div style="width: 400px; height: 200px; background-color: #fff; position: fixed; top: 30%; left: 30%; text-align: center; line-height: 200px;"> '.$msg.' </div>';

		// $url 是空值, 默认跳转到 上一级
		if( empty($url) ){
			$url = $_SERVER['HTTP_REFERER'];
		}

		echo '<meta http-equiv="refresh" content="'.$time.'; url= '.$url.' ">';
		die;
	}

	// 专门用于解析 图片名
	function imgUrl($filename)
	{
		// 201801045a4dd4962370e.jpg
		$url = '/upload/';
		$url .= substr($filename, 0, 4).'/';
		$url .= substr($filename, 4, 2).'/';
		$url .= substr($filename, 6, 2).'/';
		$url .= $filename;
		return $url;
		// /upload/2018/01/04/201801045a4dd4962370e.jpg

	}


?>