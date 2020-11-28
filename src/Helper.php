<?php

/**
 * set handler function
 *
 * @param Throwable $e
 */
function handleException(Throwable $e)
{
//  $error_string = sprintf("Execption:: file: %s line: %s errno: %s msg: %s\n stack:\n%s",
//      $e->getFile(),
//      $e->getLine(),
//      $e->getCode(),
//      $e->getMessage(),
//      $e->getTraceAsString()
//  );
   // echo $error_string;
   // Excption
   
   // return duihao\Exception;
    
    throw new Exception();
    
    //保存日志
    //duihao\Logger::_log(0, $error_string, "root");
}

/**
 * 定义路径方法
 */

if (!function_exists('root_path')) {
    /**
     * 返回项目目录
     *
     * @return string
     */
    function root_path()
    {
        return duihao\Application::$_rootPath;
    }
}

if (!function_exists('app_path')) {
    /**
     * 返回应用目录
     *
     * @return string
     */
    function app_path()
    {
        return duihao\Application::$_appPath;
    }
}

if (!function_exists('config_path')) {
    /**
     * 获取配置目录
     *
     * @return string
     */
    function config_path()
    {
        $env = parse_ini_file(root_path() . "/.env");

        return root_path() . "/config/" . $env['environment'];
    }
}

if (!function_exists('config_root_path')) {
    /**
     * 获取配置根目录
     *
     * @return string
     */
    function config_root_path()
    {
        return root_path() . "/config";
    }
}

if (!function_exists('environment')) { 
    /**
     * 返回环境参数
     *
     * @return mixed
     */
    function environment()
    {
        $env = parse_ini_file(root_path() . ".env");

        return $env['environment'];
    }
}

if (!function_exists('random_token')) {
    /**
     * 生成随机token，最后转为16进制，输出8位随机数
     *
     * @param int $length
     * @return string
     */
    function random_token($length = 32)
    {
        if (!isset($length) || intval($length) <= 8) {
            $length = 32;
        }
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes($length));
        }
        if (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes($length));
        }
    }
}

if (!function_exists('is_https')) {
    /**
     * 判断是否是https请求
     * @return boolean Returns TRUE if connection is made using HTTPS
     */
    function is_https()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            return true;
        } else {
            return false;
        }
    }
}



if (!function_exists('request')) {

    /**
     * request  获取、写入 post、get、session等
     */
    function request($name,$value="")
    { 
        return @duihao\Request::rw($name,$value);
    }
}



if (!function_exists('config')) {
    /**
     * request  获取、写入 post、get、session等
     */
    function config($name)
    {
       return @duihao\Config::get($name);
    }
}



if (!function_exists('htmlencode')) {
    /**
     * htmlencode  html标签转换，为了安全
     */
	function htmlencode($content)
	{
	    if ($content != "") {
	        $content = str_replace('>', '&gt;', $content);
	        $content = str_replace('<', '&lt;', $content);
	        $content = str_replace(chr(32), '&nbsp;', $content);
	        $content = str_replace(chr(13), ' ', $content);
	        $content = str_replace(chr(10) & chr(10), '<br>', $content);
	        $content = str_replace(chr(10), '<BR>', $content);
	    }
	    return $content;
	}
}

if (!function_exists('htmldecode')) {
    /**
     * htmldecode  html标签回转，为了安全
	     */
	function htmldecode($content)
	{
	    if ($content != "") {
	        $content = str_replace("&gt;", ">", $content);
	        $content = str_replace("&lt;", "<", $content);
	        $content = str_replace("&nbsp;", chr(32), $content);
	        $content = str_replace("", chr(13), $content);
	        $content = str_replace("<br>", chr(10) & chr(10), $content);
	        $content = str_replace("<BR>", chr(10), $content);
	    }
	    return $content;
	}
}