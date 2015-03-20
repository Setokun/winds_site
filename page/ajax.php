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
                                  $this->user->getId() );
        $inserted = SubjectManager::init()->insert($subject);
        if($inserted){
            $subject->setId($inserted);
            $this->response['added'] = ForumController::formateSubject($subject);
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
    /*OK*/private function deleteSubject(){
        $subject  = SubjectManager::init()->getByID($this->params['idSubject']);
        $posts    = PostManager::init()->getAll("WHERE idSubject=".$subject->getId());
        $delPosts = empty($posts) ? TRUE : PostManager::init()->execute(
                    "DELETE FROM post WHERE id IN (".implode(',',
                    array_map(function($post){ return $post->getId(); },
                    $posts)).")");
        $delSubj  = SubjectManager::init()->delete($subject);
        if($delPosts && $delSubj){
            $this->response['deleted'] = TRUE;
        }
        else{
            $this->response['error'] = "Subject deletion failed";
        }
    }
    /*OK*/private function createPost(){
        $post = Post::init( $this->params['message'],
                            $this->user->getId(),
                            $this->params['idSubject'] );
        $inserted = PostManager::init()->insert($post);
        if($inserted){
            $post->setId($inserted);
            $this->response['added'] = ForumController::formatePost($post,
                    $this->user->getPseudo(), $this->user->isSuperUser());
        }
        else{
            $this->response['error'] = "Post insertion failed";
        }
    }
    /*OK*/private function deletePost(){
        $subject  = SubjectManager::init()->getByID($this->params['idSubject']);
        $posts    = PostManager::init()->getAll("WHERE idSubject=".$subject->getId());
        $delPosts = empty($posts) ? TRUE : PostManager::init()->execute(
                    "DELETE FROM post WHERE id IN (".implode(',',
                    array_map(function($post){ return $post->getId(); },
                    $posts)).")");
        $delSubj  = SubjectManager::init()->delete($subject);
        if($delPosts && $delSubj){
            $this->response['deleted'] = TRUE;
        }
        else{
            $this->response['error'] = "Post deletion failed";
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
    /*OK*/private function deleteAccount(){
        $scores    = ScoreManager::init()->getAll("WHERE idPlayer="
                     .$this->user->getID());
        $delScores = empty($scores) ? TRUE : PostManager::init()->execute(
                     "DELETE FROM score WHERE id IN (".implode(',',
                     array_map(function($score){ return $score->getId(); },
                     $scores)).")");
        $this->user->setUserStatus(USER_STATUS::DELETED);
        $delAccount = UserManager::init()->update($this->user);
        if($delScores && $delAccount){
            $this->response['deleted'] = TRUE;
        }
        else{
            $this->response['error'] = "Deletion failed";
        }
    }
    /*OK*/private function banishAccount(){
        $this->user->setUserStatus(USER_STATUS::BANISHED);
        $banished = UserManager::init()->update($this->user);
        if($banished){
            $this->response['banished'] = TRUE;
        }
        else{
            $this->response['error'] = "Banishment failed";
        }
    }
}

echo AjaxOperator::init($_POST)->treat()->getResponse();

