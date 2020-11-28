<?php
namespace duihao;

//路由器实现类
class Router implements RouterInterface
{
    /**
     * 当前最近调度的路由
     */
    protected $currentRoute;

    /**
     * 所有路由设置数据
     */
    protected $routes;

    /**
     * 路由组属性堆栈
     * @var array
     */
    protected $groupStack = [];
    
    
    /**
     *  Get routes
     */
    public function getRoutes()
    { 
        return $this->routes;
    }
    

    /**
     *  Get路由
     */
    public function get()
    {
        $args = func_get_args();
        return $this->map($args, Request::METHOD_GET);
    }
     

    /**
     * POST路由
     */
    public function post()
    {
        $args = func_get_args();

        return $this->map($args, Request::METHOD_POST);
    }

    /**
     * 更新路由分组属性
     *
     * @param  $filter
     * @return void
     */
    protected function updateGroupStack($filter)
    {
        $this->groupStack[] = $filter;
    }

    public function filter($filter, $success)
    {
        $this->updateGroupStack($filter);

        if ($success instanceof \Closure) {
            $success();
        }

        array_pop($this->groupStack);
    }

    /**
     * 添加路由
     * 使用给定属性更新组堆栈
     *
     * @param $mapping
     * @param $method
     * @return null
     */
    public function map($mapping, $method)
    {
    	
    	
        $pattern  = array_shift($mapping);
        $callable = array_pop($mapping);

        $this->routes[$pattern] = [
            "method"     => $method,
            "callable"   => $callable,
            "middleware" => end($this->groupStack),
        ];
        
    }

    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }

    public function getCurrentRouteMiddleware()
    {
        return $this->currentRoute['middleware'];
    }

    public function getCurrentRouteCallable()
    {
        return $this->currentRoute['callable'];
    }

    /**
     * 允许检查http方法
     *
     * @param $route
     * @param $httpMethod
     * @return bool
     */
    public function supportsHttpMethod($route, $httpMethod)
    {
        return $route['method'] == $httpMethod ? true : false;
    }

    /**
     * 遍历路由器请求  获取匹配的路径
     * @param $httpMethod
     * @param string $resourceUri 请求的uri
     * @param bool|false $save
     * @return null
     */
    public function getMatchedRoutes($httpMethod, $resourceUri, $save = false)
    {
        foreach ($this->routes as $key => $val) {
            if ($key == $resourceUri || preg_match('#^' . $key . '$#', $resourceUri)) {
                $this->currentRoute = $val;
                if ($this->supportsHttpMethod($this->currentRoute, $httpMethod)) {
                    return $this->currentRoute;
                }
            }
        } 
        return null;
    }



}


//路由接口类
interface RouterInterface
{
    /**
     * 当前请求的路由
     * @return mixed
     */
    public function getCurrentRoute();

    /**
     * 获取匹配的路径
     * @param $httpMethod
     * @param $resourceUri
     * @param bool|false $reload
     * @return mixed
     */
    public function getMatchedRoutes($httpMethod, $resourceUri, $reload = false);

    /**
     * @param $mapping
     * @param $method
     * @return mixed
     */
    public function map($mapping, $method);

}
