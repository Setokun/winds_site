<?php
require_once '../core/config.php';

class AjaxOperator {
    private $params,
            $action,
            $user,
            $response = array();
    
    /*OK*/static function init(array $data){
        $operator = new self();
        $operator->params = $data;
        $operator->action = $data['action'];
        $operator->user   = UserManager::init()->getByID($data['idUser']);
        return $operator;
    }
    /*OK*/private function __construct(){}
    /*OK*/public function treat(){
        if(empty($this->params)){
            $this->response['error'] = "Missing parameters";
            return $this;
        }
        
        $action = $this->action;
        $this->$action();
        return $this;
    }
    /*OK*/public function getResponse(){
        return json_encode($this->response,JSON_UNESCAPED_SLASHES);
    }
    
    // -- FORUM --
    /*OK*/private function createSubject(){
        $subject = Subject::init( $this->params['title'],
                                  $this->params['message'],
                                  $this->user->getId());
        $inserted = SubjectManager::init()->insert($subject);
        if($inserted){
            $subject->setId($inserted);
            $this->response['subjectRow'] = ForumController::formateSubject($subject);
        }
        else{
            $this->response['error'] = "Subject insertion failed";
        }
    }
    /*OK*/private function closeSubject(){
        $subject = SubjectManager::init()->getByID($this->params['idSubject']);
        $subject->setSubjectStatus(SUBJECT_STATUS::CLOSED);
        $updated = SubjectManager::init()->update($subject);
        if($updated){
            $this->response['updated'] = TRUE;
        }
        else{
            $this->response['error'] = "Subject closing failed";
        }
    }
    private function deleteSubject(){
        /*$subject  = SubjectManager::init()->getByID($this->params['idSubject']);
        $posts    = PostManager::init()->getAll("WHERE idSubject=".$subject->getId());
        $delPosts = PostManager::init()->execute("DELETE FROM post WHERE id IN (".implode(
                    ',', array_map(function($post){ return $post->getId(); }, $posts)).")");
        $delSubj  = SubjectManager::init()->delete($subject);*/
        if(FALSE){//$delPosts && $delSubj){
            $this->response['deleted'] = TRUE;
        }
        else{
            $this->response['error'] = "Subject deletion failed";
        }
    }
    
    // -- ACCOUNT --
    /*OK*/private function updateRights(){
        $this->user->setUserType($this->params['userType']);
        $updated = UserManager::init()->update($this->user);
        if($updated){
            $this->response['updated'] = TRUE;
        }
        else{
            $this->response['error'] = "Right updating failed";
        }
    }
    private function deleteAccount(){
        
    }
    /*OK*/private function banishAccount(){
        $this->user->setUserStatus(USER_STATUS::BANISHED);
        $banished = UserManager::init()->update($this->user);
        if($banished){
            $this->response['banished'] = TRUE;
        }
        else{
            $this->response['error'] = "Right updating failed";
        }
    }
}

echo AjaxOperator::init($_POST)->treat()->getResponse();

