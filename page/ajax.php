<?php
require_once '../core/config.php';

class AjaxOperator {
    private $params,
            $action,
            $user,
            $response = array();
    
    static function init(array $data){
        $operator = new self();
        $operator->params = $data;
        $operator->action = $data['action'];
        $operator->user   = UserManager::init()->getByID($data['idUser']);
        return $operator;
    }
    private function __construct(){}
    public function treat(){
        if(empty($this->params)){
            $this->response['error'] = "Missing parameters";
            return $this;
        }
        
        $action = $this->action;
        $this->$action();
        return $this;
    }
    public function getResponse(){
        return json_encode($this->response,JSON_UNESCAPED_SLASHES);
    }
    private function createSubject(){
        $subject = Subject::init(
                $this->params['title'],
                $this->params['message'],
                $this->user->getId());
        $inserted = SubjectManager::init()->insert($subject);
        var_dump($inserted);
        $this->response['subjectRow'] = ForumController::formateSubject($subject);
    }
    private function closeSubject(){

    }
    private function deleteSubject(){

    }
    
}

//sleep(4);
$_POST['idUser'] = 8;
$_POST['action'] = "createSubject";
$_POST['title'] = "title test";
$_POST['message'] = "message test";
var_dump($_POST);

echo AjaxOperator::init($_POST)->treat()->getResponse();

