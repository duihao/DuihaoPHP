<?php

return [
    /**
     * 必选项，设置时间区
     */
    'timezone' => 'PRC',

    /**
     * 默认模块
     */
    'mca' => [
        //设置模块 碰到 http://{host}/admin/ 认为进入了后台模块 数组 0 标识默认 m
        'module' => ['api', 'admin'],  //支持的应用模块，默认为第一个
        'controller' => 'Main', //controller 命名一定不能合 m 命名相同否则路由  m 有限会算作模块
        'action' => 'home', //action 默认值,
        'isRewrite' => true //是否开启伪静态 .htaccess 文件配置
    ],



    /**  调试开关    */
    'debug' => true,
    /**  日志开关    */
    'logger'=>true,
    
    /**  接口签名key    */
    'apikey'=>'duihaoJo4',    
];
