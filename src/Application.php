<?php
declare (strict_types = 0);
namespace duihao;

//自动加载类
include_once 'Autoloader.php';

//异常处理类 
include_once 'Exception.php'; 

//助手函数库
include_once 'Helper.php';


//框架基础入口类
class  Application
{
  /**
    *  DuihaoPHP  版本号
    */
   const VERSION = '1.0';

   /**
     * 应用程序路由 router
     */
    protected $router;

    //应用程序响应是否已发送到客户端

    /**
     * 应用程序响应是否已发送到客户端
     * @var bool
     */
    protected $responded = false;
    protected $allowed = true;
    static $_appPath;  //APP应用主目录
    static $_rootPath;  //系统根目录
    static $_instance;
    static $_appName;  //应用模块名称
    static $_config;  //应用配置参数


    public function __construct($appPath)
    {
      //系统分隔符
      defined('DS') or define('DS', DIRECTORY_SEPARATOR);


      //DuihaoPHP核心框架目录
      defined('DUIHAOPATH') or define('DUIHAOPATH', __DIR__);
      //app应用主目录
      defined('APP_PATH') or define('APP_PATH',   $appPath .DS);
      


      //截取最后一个 斜杠
      $cutLength = strrpos($appPath,DIRECTORY_SEPARATOR);
      $rootPath= substr ($appPath, 0,$cutLength);

      //系统路径、应用路径
      self::$_appPath   = $appPath;
      self::$_rootPath  = $rootPath;
      self::$_appName = APP_NAME ? APP_NAME:"app";  //默认应用模块


 
      //自动加载
      $loader=new Autoloader();
      $loader -> register();
      $loader ->addNamespace("duihao", DUIHAOPATH, $prepend = false); //添加核心库目录
      $loader ->addNamespace(APP_NAME, APP_PATH, $prepend = false);  //添加应用程序目录
      //$loader ->addNamespace("plugin", APP_PATH."..".DS.$GLOBALS['plugins'], $prepend = false);  //添加扩展插件目录

        $this->config  = new Config();
        $this->environment = new Environment($_SERVER);
        $this->router      = new Router();
        $this->request     = new Request($this->environment);
        $this->response    = new Response();
     
        //获取配置
        $GLOBALS['config'] = self::$_config = $this->config->get(self::$_appName );  
        
        //判断是否开启自动化路由模式PathInfo模式，默认路由参数为 _r 
        //PathInfo方式自动化MCA 路由 
//      $request=$this->request; 
//      $queryString=$request->getQueryString();
//      
//      //如果存在定义的自动化MCA参数，则转为标准路由 
//      if(strstr($queryString, "_r=")){
//      	   $uri = $request->get("_r"); //获取自动化路由路径 
//      	   $_SERVER['REQUEST_URI']=$_SERVER['SCRIPT_NAME'].$uri;  //自动化标准路由路径   
//      }
//      
//		
        
        
        
      
        $this->initialize();

        $this->instance($this);
 

    }

    /**
   * App初始化操作
   */
  public function initialize()
  { 
	  //设置时区
      date_default_timezone_set(self::$_config['timezone']); 

      if (!self::$_config['debug']) {
          error_reporting(0); 
	           	
	     //注册错误捕捉
	     set_exception_handler("handleException"); 
          
      } else {
          error_reporting(E_ALL);
      }
  }



    //开始启动程序
    public function run() {//

        if (!$this->allowed) {
            return;
        }
        

		//
        $callable = $this->dispatchRequest($this->request, $this->response); 
           
        //路由绑定方式解析，获取MVC层级（模块-支持多级、类与方法名）
        $routes = $this->router->getRoutes(); 
        $mca = $routes[$_SERVER['REQUEST_URI']]['callable'];
        if(strpos($mca,'@') !== false){ 
        		$mca= str_replace(APP_NAME.'\\',"",str_replace("controller".'\\',"",str_replace("Controller@",'\\',$mca)));
        		$mcas=explode("\\",$mca);
        		$mcasn=count($mcas);
        		
        		//倒数第1个为 action
        		$GLOBALS['action']=$mcas[$mcasn-1];
        		
        		//倒数第2个为 controller
        		$GLOBALS['controller']=$mcas[$mcasn-2];
        		
        		//其他的组合后形成模块路径
        		$GLOBALS['module']=str_replace("\\".$GLOBALS['controller']."\\".$GLOBALS['action'],"",$mca);
        		 
        }  

        $content  = call_user_func($callable, $this->request);
		//接口类直接返回json
        if (!headers_sent() && ($GLOBALS['module']=="api" || strpos($mca,'\\api\\') !== false)) {
           header('Content-Type: application/json; charset=utf-8');
        }
       $this->response->setContent($content)->send();
       

    }


    /**
     * 调度请求和构建响应
     *
     * @param Request $request
     * @param Response $response
     * @return \Closure|null
     * @throws FilterException
     * @throws RouteNotFoundException
     * @throws \Exception
     */
    protected function dispatchRequest(Request $request, Response $response)
    {
    	
        $route = $this->router->getMatchedRoutes($request->getMethod(), $request->getPathInfo(), false);
         

        if (is_null($route)) {
       
    	  		$msg="<red>[".$request->getMethod()."]"." ".$request->getPathInfo()."</red>"; 
            throw new RouteNotFoundException("不被允许的uri！".$msg);
        }
        
        

        $filtered   = true;
        $middleware = $this->router->getCurrentRouteMiddleware();
        if ($middleware instanceof \Closure && is_callable($middleware)) {
            $filtered = call_user_func($middleware, $this->request);
        }

        if (!$filtered) {
            throw new FilterException("uri filter not allowed!");
        }

        /**
         * 支持function直接调用
         */
        $lostrcallable = $this->router->getCurrentRouteCallable();
        if ($lostrcallable instanceof \Closure && is_callable($lostrcallable)) {
            return $lostrcallable;
        }

        $callable = null;
        $matches  = array();
        if (is_string($lostrcallable) && preg_match('!^([\a-zA-Z0-9]+)\@([a-zA-Z0-9]+)$!', $lostrcallable, $matches)) {
            $class  = $matches[1];
            $method = $matches[2];

            $callable = function () use ($class, $method) {
                static $obj = null;
                if ($obj === null) {

                    $obj = new $class;
                }

                return call_user_func_array(array($obj, $method), func_get_args());
            };
        }

        if (!is_callable($callable)) {
            throw new \Exception('Route callable must be callable');
        }

        return $callable;
    }

    /**
     * 支持获取方法名调用
     *
     * @param $method
     * @param $parameters
     * @return mixed
     * @throws MethodNotFoundException
     */
    public static function __callStatic($method, $parameters)
    {
        if (isset(self::$_instance->$method)) {
            return self::$_instance->$method;
        }

        throw new MethodNotFoundException('method not found!');
    }

    /**
     * 设置App instance
     *
     * @param $ins
     */
    public function instance($ins)
    {
        if (!isset(self::$_instance)) {
            self::$_instance = $ins;
        }
    }


}
