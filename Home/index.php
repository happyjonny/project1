<?php
  // 前台的中央控制器

  // 1. 加载 公共配置, 函数 ...
  include '../public/function.php';
  include '../public/config.php';

  // 2. 自动加载类
  function __autoload($k)
  {
    if (file_exists('Controller/' . $k . '.php')) {
      require 'Controller/' . $k . '.php';
    } elseif (file_exists('Model/' . $k . '.php')) {
      require 'Model/' . $k . '.php';
    } elseif (file_exists('../Public/' . $k . '.php')) {
      require '../public/' . $k . '.php';
    } else {
      myNotice('您访问的页面不存在', 'index.php');
    }
  }

  // 3. 接收 c 和 m
  $c = empty($_GET['c']) ? 'index' : $_GET['c'];
  $c .= 'Controller';

  $m = empty($_GET['m']) ? 'index' : $_GET['m'];

//  var_dump($c,$m);

  // 4. 实例化 c
  $control = new $c;

  // 5. 调用方法 m
  $control->$m();