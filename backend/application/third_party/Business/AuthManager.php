<?php

require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;

class AuthManager
{
    private $authDal;
    private $ci;
    public function __construct(MysqlAuthDal $authDal)
    {
        $this->ci=&get_instance();
        $this->authDal = $authDal;
    }

    public function user()
    {
        try {
            $token = $this->ci->input->get('token');
            if(!isset($token)||$token ==''){
                return array(
                    'status' => 'error',
                    'type' => 'header_error',
                    'message' => 'Yetkisiz erişim isteği hatalı'
                );
            }
            
    
            $token_data = JWT::decode($token,new Key(JWT_SECRET,'HS256'));
            return array(
                'status' => 'ok',
                'message' => $token_data
            );
            
        } catch (DomainException $th) {
            return array(
                'status' => 'error',
                'type' => 'token_error',
                'message' => 'Token Hatalıdır'
            );
        }catch (ExpiredException $th) {
            return array(
                'status' => 'error',
                'type' => 'expired_error',
                'message' => 'Oturum Süresi Doldu'
            );
        }
    }

    public function is_authorize($user_id,$token){
        try {
            
            $token_data = JWT::decode($token,new Key(JWT_SECRET,'HS256'));
            return $token_data->user_id!==$user_id ? false : true;
            
        } catch (DomainException $th) {
            return false;
        }catch (ExpiredException $th) {
            return false;
        }
    }
    



    public function login()
    {
        $postData = get_all_post_data();

        if (!isset($postData["user_email"]) || !email_validate($postData["user_email"])) {

            return array(
                'status' => 'error',
                'type' => 'type_error',
                'message' => 'E-posta hatalı'
            );

        }
        if (!isset($postData['user_pass']) || ($postData['user_pass'] == '')) {
            return array(
                'status' => 'error',
                'type' => 'password_error',
                'message' => 'Şifre Yazmalısınız'
            );
        }

        $r=  $this->authDal->login($postData);
        $user_data = array('user_id'=>$r->user_id,'user_fullname'=>$r->user_fullname,'user_email'=>$r->user_email,'exp'=> time() + 60);
        $token = JWT::encode($user_data,JWT_SECRET,'HS256');
        if($r!==false){
            return array(
                'status'=>'ok',
                'message'=>'Giriş başarılı',
                'token'=> $token,
                'user_data'=> $user_data
            );
        }
        else{
            return array(
                'status'=>'error',
                'message'=> 'Giriş başarısız'
            );
        }
    }
}