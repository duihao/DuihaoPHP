<?php
//应用程序主目录
//可以修改app目录名，每个目录相当于一个应用，一个系统下可以有多个入口，多个应用目录
define('APP_NAME', 'app');
define('APP_DIR', realpath('./../'.APP_NAME));

//引入DuihaoPHP核心框架入口
include_once('../src/Application.php');

//自动加载 PSR 标准的各种模块
require_once __DIR__.'/../vendor/autoload.php';

// 开始运行程序
$app = new \duihao\Application(APP_DIR);
//加载路由文件
$routePath = __DIR__.'/../route';
foreach( glob( $routePath."/*.php" ) as $filename ){
   require_once ($filename);
}
$app->run();
