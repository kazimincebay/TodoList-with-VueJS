<?php 
class TodoManager{

    private $todoDal;
    public function __construct(MysqlTodoDal $todoDal){
        $this->todoDal = $todoDal;
    }

    public function alltodos(){
        $r=  $this->todoDal->alltodos();
    
        if($r!==false){
            return array(
                'status'=>'ok',
                'data'=>$r
            );
        }
        else{
            return array(
                'status'=>'error',
                'type'=>'data_not_found',
                'message'=> 'Kayıt Bulunamadı'
            );
        }
    }

    public function add(){
        $postData = get_all_post_data();
        

        if(!isset($postData['todo_user_id']) || !is_numeric($postData['todo_user_id'])){
            return array(
                'status'=>'error',
                'type'=>'value_error',
                'message'=> 'Değer Hatalıdır'
            );
        }

        if(!isset($postData['todo_name']) || ($postData['todo_name'] == '')){
            return array(
                'status'=>'error',
                'type'=>'required_error',
                'message'=> 'Liste Adı Yazmalısınız'
            );
        }

        $postData['todo_created']=time();
        $postData['todo_modified']=time();
        $postData['todo_status']=1;

        $r=  $this->todoDal->add($postData);
    
        if($r!==false){
            return array(
                'status'=>'ok',
                'message'=>'Kayıt ekleme işlemi başarılıdır'
            );
        }
        else{
            return array(
                'status'=>'error',
                'type'=>'add_error',
                'message'=> 'Kayıt eklenemedi'
            );
        }
    }

    public function update($id){
        $postData = (array)json_decode(file_get_contents("php://input"));
        
        

        if(!isset($postData['todo_user_id']) || !is_numeric($postData['todo_user_id'])){
            return array(
                'status'=>'error',
                'type'=>'value_error',
                'message'=> 'Değer Hatalıdır'
            );
        }

        if(!isset($postData['todo_name']) || ($postData['todo_name'] == '')){
            return array(
                'status'=>'error',
                'type'=>'required_error',
                'message'=> 'Liste Adı Yazmalısınız'
            );
        }
        $postData['todo_id']=$id;
        $postData['todo_modified']=time();
        $postData['todo_status']=1;

        
        $r=  $this->todoDal->update($postData);
    
        if($r!==false){
            return array(
                'status'=>'ok',
                'message'=>'Liste Güncellendi'
            );
        }
        else{
            return array(
                'status'=>'error',
                'type'=>'update_error',
                'message'=> 'Liste Güncellenemedi'
            );
        }



    }
    public function delete($id){
        

        $r=  $this->todoDal->delete($id);
    
        if($r!==false){
            return array(
                'status'=>'ok',
                'message'=>'Liste Silindi'
            );
        }
        else{
            return array(
                'status'=>'error',
                'type'=>'delete_error',
                'message'=> 'Liste Silinemedi'
            );
        }
    }
}