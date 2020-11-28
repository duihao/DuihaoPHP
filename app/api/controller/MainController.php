<?php
declare (strict_types = 0);
namespace app\api\controller;
 
use duihao\Token;
class MainController extends BaseController
{
  
  public function __construct()
  {
    parent::__construct();
  }


    public function home()
    { 
       return $this->RESTful($code="200",$data=["HelloWord"=>"您好，对号！"]);
    }


    public function token()
    { 
    	   // 
    	   $data=[];  
	 
    	   $data["username"]=request("username","");
    	   $data["password"]=request("password","");
    	   
    	   $Token=new Token(); 
    	   $tokens = $Token->authorizations($data);
    	    
       return $this->RESTful($code="200",$data=$tokens);
    }

}
