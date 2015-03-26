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
        $operator->user   = !isset($data['idUser']) ? NULL :
                   UserManager::init()->getByID($data['idUser']);
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
    
    // -- FORUM --
    private function createSubject(){
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
    private function closeSubject(){
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
    private function createPost(){
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
    private function deletePost(){
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
        UserManager::init()->update($this->user) ?
            $this->response['updated'] = TRUE :
            $this->response['error'] = "Right updating failed";
    }
    /*OK*/private function deleteAccount(){
        $scores    = ScoreManager::init()->getAll("WHERE idPlayer="
                   . $this->user->getID());
        $scoreIds  = array_map(function($score){ return $score->getId(); }, $scores);
        $delScores = empty($scores) ? TRUE :
                     ScoreManager::init()->deleteMulti($scoreIds);
        
        $this->user->setUserStatus(USER_STATUS::DELETED);
        $delAccount = UserManager::init()->update($this->user);
        
        $delScores && $delAccount ?
            $this->response['deleted'] = TRUE :
            $this->response['error'] = "Deletion failed";
    }
    /*OK*/private function banishAccount(){
        $this->user->setUserStatus(USER_STATUS::BANISHED);
        UserManager::init()->update($this->user)  ?
            $this->response['banished'] = TRUE :
            $this->response['error'] = "Banishment failed";
    }
    
    // -- MODERATION --
    /*OK*/private function acceptLevel(){
        $level = LevelManager::init()->getByID($this->params['idLevel']);
        $level->setLevelStatus(LEVEL_STATUS::ACCEPTED);
        LevelManager::init()->update($level) ?
            $this->response['accepted'] = TRUE :
            $this->response['error']    = "Level acceptance failed";
    }
    /*OK*/private function refuseLevel(){
        $level = LevelManager::init()->getByID($this->params['idLevel']);
        $level->setLevelStatus(LEVEL_STATUS::REFUSED);
        LevelManager::init()->update($level) ?
            $this->response['refused'] = TRUE :
            $this->response['error']   = "Level refusal failed";
    }
}

echo AjaxOperator::init($_POST)->treat()->getResponse();

