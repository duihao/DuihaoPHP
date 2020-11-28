<?php
namespace duihao;

class Request
{

    const METHOD_HEAD     = 'HEAD';
    const METHOD_GET      = 'GET';
    const METHOD_POST     = 'POST';
    const METHOD_PUT      = 'PUT';
    const METHOD_PATCH    = 'PATCH';
    const METHOD_DELETE   = 'DELETE';
    const METHOD_OPTIONS  = 'OPTIONS';
    const METHOD_OVERRIDE = '_METHOD';

    /**
     * 每个实例缓存的请求路径
     * @var array
     */
    protected $paths;

    /**
     * 环境对象
     *
     * @var Environment
     */
    protected $env;

    /**
     * pathinfo
     *
     * @var
     */
    protected $pathinfos;

    /**
     * 请求body
     *
     * @var
     */
    protected $body;

    /**
     * 保存get请求参数
     *
     * @var
     */
    protected $getParameters;

    /**
     * 保存post请求参数
     *
     * @var
     */
    protected $postParameters;

    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    /**
     * Get HTTP method
     *
     * @return string
     * @api
     */
    public function getMethod()
    {
        $method = $this->env->get('REQUEST_METHOD');
        return $method;
    }

    /**
     * 获取pathinfo
     *
     * @return mixed
     */
    public function getPathInfo()
    {
        $paths = $this->parsePaths();

        return $paths['virtual'];
    }

    /**
     * Get query string 参数体
     *
     * @return string
     * @api
     */
    public function getQueryString()
    {
        return $this->env->get('QUERY_STRING', '');
    }

    /**
     * 从请求URI解析物理路径和虚拟路径
     *
     * @return array
     */
    protected function parsePaths()
    {
        $this->paths             = array();
        $this->paths['physical'] = $_SERVER['SCRIPT_NAME'];


        if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
            $this->paths['virtual'] = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
        } else {
            $this->paths['virtual'] = $_SERVER['REQUEST_URI'];
        }

        return $this->paths;
    }

    /**
     * 解析url片段
     *
     * @return array
     */
    protected function parsePathinfo()
    {
        $pathinfos = explode('/', $this->getPathInfo());

        $this->pathinfos = array_values(array_filter($pathinfos, function ($v) {
            return $v != '';
        }));

        return $this->pathinfos;
    }

    /**
     * 获取url所有片段
     *
     * @return array
     */
    public function pathinfos()
    {
        if (!is_null($this->pathinfos)) {
            return $this->pathinfos;
        }

        return $this->parsePathinfo();
    }

    /**
     * 获取片段
     * @param $index
     * @param null $default
     * @return mixed|null
     */
    public function pathinfo($index, $default = null)
    {
        $pathinfos = $this->pathinfos();

        $index = $index - 1;
        if (array_key_exists($index, $pathinfos)) {
            return $pathinfos[$index];
        }

        return $default;
    }

    /**
     * 获取请求 body
     *
     * @return string
     */
    public function body()
    {
        if (null === $this->body) {
            $this->body = @file_get_contents('php://input');
        }

        return $this->body;
    }

    /**
     * 获取所有的post请求数据
     *
     * @return mixed
     */
    public function posts()
    {
        if (null === $this->postParameters) {
            $this->postParameters = $_POST;
        }

        return $this->postParameters;
    }

    /**
     *  返回请求参数post，如果不存在，则返回 $defaul
     *
     * @param string $key 要返回的参数的名称
     * @param mixed $default 默认值
     * @return mixed
     */
    public function post($key, $default = null)
    {
        $params = $this->posts();

        return isset($params[$key]) ? $params[$key] : $default;
    }

    public function gets()
    {
        if (null === $this->getParameters) {
            $this->getParameters = $_GET;
        }

        return $this->getParameters;
    }

    /**
     * 返回请求参数get
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        //
        $params = $this->gets();
        return isset($params[$key]) ? $params[$key] : $default;

    }

    /**
     * 按格式读/写request参数，支持 session、cookie、get、post
     *
     * @param string $name  如：session.username\cookie.username
     * @param string $value
     * @return mixed
     */
    public function rw($name, $value = '')
    {
      //以 . 分隔获取 参数类型，目前支持：session、cookie、get、post
      //增加安全过滤
      $result=null;
      if (strpos($name, '.') > -1) {//
        $method = '';
        $key = '';
        list($method, $key) = explode('.', $name);   //赋值获取参数类型和名称
        $method=strtolower($method);

        switch ($method)
          {
          case 'get':
                if (empty($key))
                    $result = inputfilter($_GET);
                $result = inputfilter($_GET[$key] ?? '');
              break;
          case 'post':
                if (empty($key))
                    $result = inputfilter($_POST);
                $result = inputfilter($_POST[$key] ?? '');
              break;
          case 'session':
                    if (empty($value)) {
                        $result = $_SESSION[$key];
                    } else {
                        $_SESSION[$key] = $value;
                        $result = $_SESSION[$key];
                    }
                break;

          case 'cookie':
                    if (empty($value)) {
                        $result = $_COOKIE[$key];
                    } else {
                        setcookie($key, $value, time()+60*60*24*30);
                        $result = $_COOKIE[$key];
                    }
                break;

            case 'server':
                    if (empty($key))
                          $result = $_SERVER[strtoupper($key)];
                break;
          default:

        }////


      } else {///无分隔直接取GET
          $result = $_GET[$name] ?? '';
      }

      return $result;
    }



}
