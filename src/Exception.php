<?php
namespace duihao;

class Exception extends \Exception
{ 
    public function __construct($msg,$code=0)
    { 
        parent::__construct($msg,$code);
        $this->show();
        exit;
    }

    public function show(){
 
        $msg = $this->message;
        $code = $this->code;
        $file = $this->getFile();
        $line = $this->getLine();
        $trace = $this->getTraceAsString();
        $traces = $this->getTrace();
        
        echo "<html><head><title>DuihaoPHP 异常报告！</title>";  
        	echo "<style>body{padding: 50px; color:#000000; font-size:100%;}red{color:red;}i{font-size:small;color:#888888}hr{ height:1px;border:none;border-top:1px solid #888888;}</style>";
        	echo "</head><body>";

        echo "<h1>DuihaoPHP 异常报告：</h1>";

        	echo "<h3>".$msg."</h3>"; 
        echo "  文件：".$file."<br>";
        echo "  位置：<red>".$line."</red>行";

        echo "  <h3>错误追踪：</h3>";
        foreach ($traces as $t){
          echo '文件：'.$t['file'].'    行：<red>'.$t['line'].'</red>   '.$t['class'].'\\'.$t['function'].'()<br>';
        }
        
        echo "<br><br><hr>";
        	echo "<i>&copy;".date("Y")." DUIHAO.  &nbsp; Powered by DuihaoPHP1.0</i>";
        	echo "</body></html>";

    }
    
    

}

//数据操作异常
class DatabaseException extends Exception
{

}

//类不存在
class ClassNotFoundException extends Exception
{

}

//文件不存在
class FileNotFoundException extends Exception
{

}


class FilterException extends Exception
{

}

class MethodNotFoundException extends Exception
{

} 

class RouteNotFoundException extends Exception
{

}
