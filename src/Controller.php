<?php
namespace duihao;

class Controller
{
    public $layout;
    private $_v;   //视图对象
    private $_data = array();

    public $module;  //应用模块
    public $controller;   //子模块/控制器
    public $action;   //操作/动作
    public $returnType;   //默认返回类型  view 视图方式、api 接口方式
    public $templateDir ;   //模板目录
    public $viewSkin ;   //视图皮肤
    public $viewTemplate ;   //视图模板
    public $modelName ;   //数据模型
    public $bindModel ;   //是否绑定数据模型  true 是，false 否   ，绑定模型后 增、删、改、查自动生成
    public $actions; //本模块指定允许的擦作（安全验证）


    public function initialize() {

    }

    public function __construct()
    {
        $this->initialize();
        
 		//模版目录
 		if($this->viewSkin!=""){ //定义皮肤
 			$this->templateDir =  APP_DIR . DS .$GLOBALS['module'].DS.  'view' .DS.$this->viewSkin. DS . $GLOBALS['controller'] ;
 		}else{//（单皮肤、默认风格）
 			$this->templateDir =  APP_DIR . DS .$GLOBALS['module'].DS.  'view' . DS . $GLOBALS['controller'] ;
 		}  
 		
 		//视图初始化
 		$this->_v = new View();
      	
      	//echo $this->templateDir;
        //$this->init();
    }

    public function __get($name)
    {
        return $this->_data[$name];
    }

    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }
    
    public function put($name, $value)
    {
        $this->_data[$name] = $value;
    } 
      

    //默认操作
    public function home()
    {  
    	
    	   //传值方法1:通过定义变量给模版传值
       $this->put("data1", "您好，对号！");
       
       //传值方法2:直接将数键值对传给模版
       $this->assign(["data2"=>"您好，对号就！"]);
       $this->view("home");
    }


    //添加操作与视图
    public function add(){

    }

    //编辑操作与视图
    public function edit(){

    }

    //详情操作与视图
    public function details(){

    }

    //列表操作与视图
    public function lists(){

    }
    //保存（新增保存或修改保存）
    public function save(){

    }

    //删除（新增保存或修改保存）
    public function deleteId(){

    }

    //删除（新增保存或修改保存）
    public function deleteIds(){

    }


    //输出到视图方式
    public function view($templateName, $return = false)
    {	
        header("Content-type:text/html;charset=utf-8");
        
    		$templateName=$templateName.$GLOBALS['config']['mca']['tplSuffix'];
        if (!$this->_v) {
            $this->_v = new View();
        }
        //controller 成员对模板外公开
        $this->_v->assign(get_object_vars($this));
        $this->_v->assign($this->_data);

        //if ($this->layout) {
            $this->_v->assign('__render_body',  $this->templateDir . DS . $templateName); 
            //echo  $this->templateDir . DS . $templateName."<br>"; 
            //$templateName = $this->layout;
        //}
         
        //
        
        Trace::page_trace(); 
        
        if ($return) {
            return $this->_v->render( $this->templateDir . DS . $templateName);
        } else {
            echo $this->_v->render( $this->templateDir . DS . $templateName);
        }
    }
    
    //模版直接传递参数
    public function assign($data){
		$this->_v->assign($data);
    }


    //输出为json
    public function json($data)
    {
      header("Content-Type: json/application; charset=UTF-8");
      echo json_encode($data);
    }
      //输出RESTful api 结果
      public function RESTful($code="",$data="",$message="",$status="")
      {

        //默认返回信息
        $MsgS['200']= "服务器成功返回请求的数据";
        $MsgS['201']= "新建或修改数据成功";
        $MsgS['202']= "表示一个请求已经进入后台排队（异步任务）";
        $MsgS['204']= "删除数据成功";
        $MsgS['400']= "请求有错误，服务器没有进行新建或修改数据的操作（幂等操作）";
        $MsgS['401']= "没有权限（令牌、用户名、密码错误）";
        $MsgS['403']= "得到授权（与401错误相对），但是访问是被禁止的";
        $MsgS['404']= "请求记录不存在，服务器没有进行操作（幂等操作）";
        $MsgS['406']= "请求的格式不符合（比如用户请求JSON格式，但是只有XML格式）";
        $MsgS['500']= "服务器发生错误，无法判断发出的请求是否成功";

        //$jsonData=["code":$code,];
        if($code>=100 && $code<400){//成功
          if($status==""){
            $status="success";
          }
          if($message==""){
            if(isset($MsgS[$code])){
            $message=$MsgS[$code];
            }else{
            $message=$MsgS[200];
            }
          }
          //返回成功结果
          $jsonData["code"]=$code;
          $jsonData["status"]=$status;
          $jsonData["data"]=$data;
          $jsonData["message"]=$message;

        }else if($code>=400 && $code<500){//非成功

          if($status==""){
            $status="error";
          }
          if($message==""){
            if(isset($MsgS[$code])){
            $message=$MsgS[$code];
            }else{
            $message=$MsgS[200];
            }
          }
          //返回失败结果
          $jsonData["code"]=$code;
          $jsonData["status"]=$status;
          $jsonData["message"]=$message;

        }else{//服务器异常
          if($status==""){
            $status="error";
          }
          if($message==""){
            if(isset($MsgS[$code])){
            $message=$MsgS[$code];
            }else{
            $message=$MsgS[500];
            }
          }

          //返回无效结果
          $jsonData["code"]=$code;
          $jsonData["status"]=$status;
          $jsonData["message"]=$message;
        }

        return  $this->json($jsonData);


          /*
                  {
                  "code": "200", // HTTP响应码(好多javascript框架并不会获取http状态码，所以包装到body中便于使用)
                  "status": "success/fail/error", // 见下述表格
                  "content/data": []/{}, // 多条记录使用JSON数组，单条记录使用JSON对象
                  "message": []     // 状态为error或fail时，对应的错误信息
                }
                状态码	说明
                200 OK	服务器成功返回请求的数据
                201 CREATED	新建或修改数据成功
                202 Accepted	表示一个请求已经进入后台排队（异步任务）
                204 NO CONTENT	删除数据成功
                400 INVALID REQUEST	请求有错误，服务器没有进行新建或修改数据的操作（幂等操作）
                401 Unauthorized	没有权限（令牌、用户名、密码错误）
                403 Forbidden	得到授权（与401错误相对），但是访问是被禁止的
                404 NOT FOUND	请求记录不存在，服务器没有进行操作（幂等操作）
                406 Not Acceptable	请求的格式不符合（比如用户请求JSON格式，但是只有XML格式）
                500 INTERNAL SERVER ERROR	服务器发生错误，无法判断发出的请求是否成功



                状态	说明
              fail	返回码为 500-599
              error	返回码为 400-499
              success	其他状态码（1xx、2xx、3xx）
          */
      }
      
    
}
