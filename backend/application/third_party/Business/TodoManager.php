<?php

require APPPATH . 'third_party/Business/AuthManager.php';
require APPPATH . 'third_party/DataAccess/' . DB_LAYER . '/' . DB_LAYER . 'AuthDal.php';

class TodoManager
{

    private $todoDal;
    private $authManager;
    private $authDal;
    private $ci;
    public function __construct(MysqlTodoDal $todoDal)
    {
        $this->ci =& get_instance();
        $this->todoDal = $todoDal;
        $this->authDal = new MysqlAuthDal();
        $this->authManager = new AuthManager($this->authDal);
    }

    public function alltodos()
    {

        $user_id = $this->ci->input->get("user_id");
        $token = $this->ci->input->get('token');
        $authorize = $this->authManager->is_authorize($user_id, $token);

        if (!is_numeric($user_id)) {
            return array(
                'status' => 'error',
                'message' => 'Yetkisiz erişim isteği'
            );
        }
        if (!isset($token) || $token == '') {
            return array(
                'status' => 'error',
                'message' => 'Token eksik'
            );
        }

        if (!$authorize) {
            return array(
                'status' => 'error',
                'message' => 'Bu kullanıcı üzerinde yetkiniz yok!'
            );
        }

        $r = $this->todoDal->alltodos($user_id);

        if ($r !== false) {
            return array(
                'status' => 'ok',
                'data' => $r
            );
        } else {
            return array(
                'status' => 'error',
                'type' => 'data_not_found',
                'message' => 'Kayıt Bulunamadı'
            );
        }
    }

    public function add()
    {
        $postData = get_all_post_data();


        if (!isset($postData['todo_user_id']) || !is_numeric($postData['todo_user_id'])) {
            return array(
                'status' => 'error',
                'type' => 'value_error',
                'message' => 'Değer Hatalıdır'
            );
        }

        if (!isset($postData['todo_detail']) || ($postData['todo_detail'] == '')) {
            return array(
                'status' => 'error',
                'type' => 'required_error',
                'message' => 'İş Adı Yazmalısınız'
            );
        }

        if (!isset($postData['token']) || $postData['token'] == '') {
            return array(
                'status' => 'error',
                'message' => 'Token eksik'
            );
        }


        $authorize = $this->authManager->is_authorize($postData['todo_user_id'], $postData['token']);

        if (!$authorize) {
            return array(
                'status' => 'error',
                'message' => 'Bu kullanıcı üzerinde yetkiniz yok!'
            );
        }

        $postData['todo_created'] = time();
        $postData['todo_modified'] = time();
        $postData['todo_status'] = 1;
        unset($postData['token']);
        $r = $this->todoDal->add($postData);

        if ($r !== false) {
            return array(
                'status' => 'ok',
                'message' => 'Kayıt ekleme işlemi başarılıdır'
            );
        } else {
            return array(
                'status' => 'error',
                'type' => 'add_error',
                'message' => 'Kayıt eklenemedi'
            );
        }
    }

    public function update($id)
    {
        $postData = (array) json_decode(file_get_contents("php://input"));



        if (!isset($postData['todo_user_id']) || !is_numeric($postData['todo_user_id'])) {
            return array(
                'status' => 'error',
                'type' => 'value_error',
                'message' => 'Değer Hatalıdır'
            );
        }

        if (!isset($postData['todo_detail']) || ($postData['todo_detail'] == '')) {
            return array(
                'status' => 'error',
                'type' => 'required_error',
                'message' => 'İş Adı Yazmalısınız'
            );
        }



        $postData['todo_id'] = $id;
        $postData['todo_modified'] = time();
        $postData['todo_status'] = 1;


        $r = $this->todoDal->update($postData);

        if ($r !== false) {
            return array(
                'status' => 'ok',
                'message' => 'Liste Güncellendi'
            );
        } else {
            return array(
                'status' => 'error',
                'type' => 'update_error',
                'message' => 'Liste Güncellenemedi'
            );
        }



    }

    public function done()
    {

        $postData = get_all_post_data();

        if (!is_numeric($postData['todo_id'])) {
            return array(
                'status' => 'error',
                'message' => 'Liste ID Değeri hatalıdır'
            );
        }

        if (!is_numeric($postData['todo_user_id'])) {
            return array(
                'status' => 'error',
                'message' => 'User ID Değeri hatalıdır'
            );
        }

        if (!is_numeric($postData['todo_status_id'])) {
            return array(
                'status' => 'error',
                'message' => 'Status Değeri hatalıdır'
            );
        }

        if (!isset($postData['todo_detail']) || ($postData['todo_detail'] == '')) {
            return array(
                'status' => 'error',
                'type' => 'required_error',
                'message' => 'İş Adı Yazmalısınız'
            );
        }

        if (!isset($postData['token']) || $postData['token'] == '') {
            return array(
                'status' => 'error',
                'message' => 'Token eksik'
            );
        }

        $authorize = $this->authManager->is_authorize($postData['todo_user_id'], $postData['token']);

        if (!$authorize) {
            return array(
                'status' => 'error',
                'message' => 'Bu kullanıcı üzerinde yetkiniz yok!'
            );
        }

        $r = $this->todoDal->done($postData['todo_id'], $postData['todo_user_id'], $postData['todo_status_id']);

        if ($r !== false) {
            return array(
                'status' => 'ok',
                'message' => 'İş Durumu Değiştirildi'
            );
        } else {
            return array(
                'status' => 'error',
                'type' => 'delete_error',
                'message' => 'İş Durumu Değiştirilemedi'
            );
        }
    }





    public function delete()
    {

        $postData = get_all_post_data();

        if (!is_numeric($postData['todo_id'])) {
            return array(
                'status' => 'error',
                'type' => 'id_error',
                'message' => 'Liste ID Değeri hatalıdır'
            );
        }

        if (!is_numeric($postData['todo_user_id'])) {
            return array(
                'status' => 'error',
                'type' => 'id_error',
                'message' => 'User ID Değeri hatalıdırSilinemedi'
            );
        }

        if (!isset($postData['token']) || $postData['token'] == '') {
            return array(
                'status' => 'error',
                'message' => 'Token eksik'
            );
        }

        $authorize = $this->authManager->is_authorize($postData['todo_user_id'], $postData['token']);



        if (!$authorize) {
            return array(
                'status' => 'error',
                'message' => 'Bu kullanıcı üzerinde yetkiniz yok!'
            );
        }

        $r = $this->todoDal->delete($postData['todo_id'], $postData['todo_user_id']);

        if ($r !== false) {
            return array(
                'status' => 'ok',
                'message' => 'Liste Silindi'
            );
        } else {
            return array(
                'status' => 'error',
                'type' => 'delete_error',
                'message' => 'Liste Silinemedi'
            );
        }
    }
}