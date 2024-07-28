<?php 
class ItemManager{

    private $itemDal;
    public function __construct(MysqlItemDal $itemDal){
        $this->itemDal = $itemDal;
    }

    public function list($id){
        $r=  $this->itemDal->list($id);
    
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
        

        if(!isset($postData['item_todo_id']) || !is_numeric($postData['item_todo_id'])){
            return array(
                'status'=>'error',
                'type'=>'value_error',
                'message'=> 'Değer Hatalıdır'
            );
        }

        if(!isset($postData['item_content']) || ($postData['item_content'] == '')){
            return array(
                'status'=>'error',
                'type'=>'required_error',
                'message'=> 'Item içeriği Yazmalısınız'
            );
        }

        $postData['item_created']=time();
        $postData['item_modified']=time();
        $postData['item_status']=1;

        $r=  $this->itemDal->add($postData);
    
        if($r!==false){
            return array(
                'status'=>'ok',
                'message'=>'Item ekleme işlemi başarılıdır'
            );
        }
        else{
            return array(
                'status'=>'error',
                'type'=>'add_error',
                'message'=> 'Item eklenemedi'
            );
        }
    }

    public function update($id){
        $postData = (array)json_decode(file_get_contents("php://input"));
        
        

        if(!isset($postData['item_todo_id']) || !is_numeric($postData['item_todo_id'])){
            return array(
                'status'=>'error',
                'type'=>'value_error',
                'message'=> 'Değer Hatalıdır'
            );
        }

        if(!isset($postData['item_content']) || ($postData['item_content'] == '')){
            return array(
                'status'=>'error',
                'type'=>'required_error',
                'message'=> 'Liste Adı Yazmalısınız'
            );
        }
        $postData['item_id']=$id;
        $postData['item_modified']=time();
        $postData['item_status']=1;

        
        $r=  $this->itemDal->update($postData);
    
        if($r!==false){
            return array(
                'status'=>'ok',
                'message'=>'Item Güncellendi'
            );
        }
        else{
            return array(
                'status'=>'error',
                'type'=>'update_error',
                'message'=> 'Item Güncellenemedi'
            );
        }



    }
    public function delete($id){
        

        $r=  $this->itemDal->delete($id);
    
        if($r!==false){
            return array(
                'status'=>'ok',
                'message'=>'Item Silindi'
            );
        }
        else{
            return array(
                'status'=>'error',
                'type'=>'delete_error',
                'message'=> 'Item Silinemedi'
            );
        }
    }
}