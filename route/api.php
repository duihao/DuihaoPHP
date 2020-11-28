<?php
declare (strict_types = 0);
namespace duihao;
use \duihao\Application;
$urlPrefix = "/duihaophp/public/index.php/api/";  //当前应用模块路由前缀

/**
 * 添加过滤器，过滤器true，不添加到路由器解析中。
 */
Application::router()->filter(function () {
    return true;
}, function () {
    global  $urlPrefix;
    Application::router()->get($urlPrefix.'main/home', 'app\api\controller\MainController@home');
 	Application::router()->get($urlPrefix.'main/token', 'app\api\controller\MainController@token');

   
    


});

// /**
//  * 添加过滤器，过滤器true，不添加到路由器解析中。
//  */
// Application::router()->filter(function () {
//     return false;
// }, function () {
//     Application::router()->get('/forbidden', 'App\Controllers\TestController@forbidden');
// });
