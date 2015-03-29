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
        $operator->user   = !isset($data['idUser']) ? NULL :
            UserManager::init()->getByID($data['idUser']);
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
    /*OK*/private function createSubject(){sleep(2);
        $subject = Subject::init( $this->params['title'],
                                  $this->params['message'],
                                  $this->user->getId() );
        $insertedID = SubjectManager::init()->insert($subject);
        $insertedID ? $subject->setId($insertedID) : NULL;
        $insertedID ? $this->response['created'] = ForumController::formateSubject($subject) :
                      $this->response['error'] = "Subject insertion failed";
    }
    /*OK*/private function closeSubject(){
        $subject = SubjectManager::init()->getByID($this->params['idSubject']);
        $subject->setSubjectStatus(SUBJECT_STATUS::CLOSED);
        SubjectManager::init()->update($subject) ?
            $this->response['closed'] = TRUE :
            $this->response['error'] = "Subject closing failed";
    }
    /*OK*/private function deleteSubject(){
        $subject  = SubjectManager::init()->getByID($this->params['idSubject']);
        $posts    = PostManager::init()->getAll("WHERE idSubject=".$subject->getId());
        $postIds  = array_map(function($post){ return $post->getId(); }, $posts);

        $delPosts = empty($posts) ? TRUE : PostManager::init()->deleteMulti($postIds);
        $delSubj  = SubjectManager::init()->delete($subject);

        $delPosts && $delSubj ?
            $this->response['deleted'] = TRUE :
            $this->response['error'] = "Subject deletion failed";
    }
    /*OK*/private function createPost(){
        $post = Post::init( $this->params['message'],
                            $this->user->getId(),
                            $this->params['idSubject'] );
        $inserted = PostManager::init()->insert($post);
        $inserted ? $post->setId($inserted) : NULL;
        $inserted ? $this->response['created'] = ForumController::formatePost(
                        $post, $this->user->getPseudo(),
                        $this->user->isSuperUser()) :
                    $this->response['error'] = "Post insertion failed";
    }
    /*OK*/private function deletePost(){
        $post = PostManager::init()->getByID($this->params['idPost']);
        PostManager::init()->delete($post) ?
            $this->response['deleted'] = TRUE :
            $this->response['error'] = "Post deletion failed";
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
    
    // -- ADDON --
    private function uploadAddon(){
        $addonType = $this->params['addonType'];
        
        
        
        
        true ?
            $this->response['uploaded'] = TRUE :
            $this->response['error']   = "Addon uploading failed";
    }
    /*OK*/private function removeAddons(){
        $levelIds = $this->params['idLevels'];
        $scores   = ScoreManager::init()->getAll("WHERE idLevel in ("
                   .implode(',',$levelIds).")");
        $scoreIds = array_map(function($score){ return $score->getId(); }, $scores);
        
        $delScores = empty($scoreIds) ? TRUE : ScoreManager::init()->deleteMulti($scoreIds);
        $delLevels = LevelManager::init()->deleteMulti($levelIds);
        
        $delScores && $delLevels ?
            $this->response['deleted'] = TRUE :
            $this->response['error']   = "Scores and levels deletion failed";
    }
    
}

echo AjaxOperator::init($_POST)->treat()->getResponse();

