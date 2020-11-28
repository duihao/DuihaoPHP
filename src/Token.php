<?php
namespace duihao;

use \Firebase\JWT\JWT; //导入JWT

class Token
{ 
	private $key;   //key
	private $iss;   //$iss
	private $aud;   //$aud
	
	public function __construct()
    { 
    		$this->key= $GLOBALS['config']['api']['appId'].'_'.$GLOBALS['config']['api']['appKey'].'_'.$GLOBALS['config']['api']['appSecrect'];
    		$this->iss= $GLOBALS['config']['api']['iss'];
    		$this->aud= $GLOBALS['config']['api']['aud'];
    }
 
    /*签发Token 
     */
    public function getToken($data)
    {   
    		$key= $this->key;
    		$iss= $this->iss;
    		$aud= $this->aud;
        $time = time(); //当前时间
        $tokenData = [
            'iss' => $iss, //签发者 可选
               'aud' => $aud, //接收该JWT的一方，可选
               'iat' => $time, //签发时间
               'nbf' => $time , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
               'exp' => $time+7200, //过期时间,这里设置2个小时
                'data' => $data
        ];
        //
        return JWT::encode($tokenData, $key); //输出Token
    }
    
    /*解析Token 
     * $jwt 待解析的token
     */  
    public function verifyToken($jwt)
    { 
    		$key= $this->key;
    		$iss= $this->iss;
    		$aud= $this->aud;
 
        try {
                   JWT::$leeway = 60;//当前时间减去60，把时间留点余地
                   $decoded = JWT::decode($jwt, $key, ['HS256']); //HS256方式，这里要和签发的时候对应
                   $arr = (array)$decoded;
                   return $arr; 
                   
            } catch(\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
                return $e->getMessage();
            }catch(\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
                return $e->getMessage();
            }catch(\Firebase\JWT\ExpiredException $e) {  // token过期
                return $e->getMessage();
           }catch(Exception $e) {  //其他错误
                return $e->getMessage();
            }
        //Firebase定义了多个 throw new，我们可以捕获多个catch来定义问题，catch加入自己的业务，比如token过期可以用当前Token刷新一个新Token
    }
    
   /*
    * 创建授权信息 
     */
    public function authorizations($data)
    {
    		$key= $this->key;
    		$iss= $this->iss;
    		$aud= $this->aud;
    		
        $time = time(); //当前时间
 
        //公用信息
        $tokenData = [
            'iss' => $iss, //签发者 可选
            'iat' => $time, //签发时间
            'data' => $data
        ];
 
        $access_token = $tokenData;
        $access_token['scopes'] = 'role_access'; //token标识，请求接口的token
        $access_token['exp'] = $time+7200; //access_token过期时间,这里设置2个小时
 
        $refresh_token = $tokenData;
        $refresh_token['scopes'] = 'role_refresh'; //token标识，刷新access_token
        $refresh_token['exp'] = $time+(86400 * 30); //access_token过期时间,这里设置30天
 
        $tokens = [
            'access_token'=>JWT::encode($access_token,$key),
            'refresh_token'=>JWT::encode($refresh_token,$key),
            'token_type'=>'bearer' //token_type：表示令牌类型，该值大小写不敏感，这里用bearer
        ];
        
        return $tokens; 
    }


}
