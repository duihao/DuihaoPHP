<?php
declare (strict_types = 0);
namespace duihao;
use \duihao\Application;
$urlPrefix = "/duihaophp/public/index.php/";  //当前应用模块路由前缀

/**
 * 默认
 */
Application::router()->get($urlPrefix.'*', function () {   echo "Hello, world"; }); 