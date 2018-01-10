<?php 
	
	// 专门负责上传文件
	class Upload
	{

		public function singleFile($savePath='../upload/', $allowType=array('image'))
		{
			// 1. 判断错误
				// 1.1 $_FILES 是否有值
				if( empty($_FILES) ){
					return '您的文件过大, 请重新上传';
				}

				// 1.2 error 号是否为0
				$key = key($_FILES);
				$error = $_FILES[$key]['error'];
				if( $error > 0 ){
					switch( $error ){
						case '1': return '您的文件过大, 请重新上传';
						case '2': return '您的文件过大, 请重新上传';
						case '3': return '请检查您的网络';
						case '4': return '您上传的文件为空';
						case '6': return '服务器繁忙';
						case '7': return '服务器繁忙';
					}
				}

			// 2. 判断post协议
				$tmp = $_FILES[$key]['tmp_name'];
				if( !is_uploaded_file( $tmp ) ){
					return '非法上传';
				}

			// 3. 判断文件类型
				// 3.1 获取文件的类型 type =  image/jpeg
				$type = strtok($_FILES[$key]['type'], '/');

				// 3.2 判断是否允许
				if( !in_array($type, $allowType) ){
					return '不支持该文件类型';
				}

			// 4. 设置新的文件名
				//  20180104xxxxx.jpg
				$suffix = strrchr($_FILES[$key]['name'], '.');
				$filename = date('Ymd').uniqid().$suffix;

			// 5. 设置新的目录
				// upload/2018/01/04/
				$dir = $savePath.date('/Y/m/d/');
				if( !file_exists($dir) ){
					mkdir($dir, 0777, true);
				}

			// 6. 移动临时文件
				if( move_uploaded_file( $tmp, $dir.$filename ) ){
					$arr[] = $filename;
					return $arr;
				}
//				var_dump($_FILES);die;
			return '上传失败';
		}



	}

