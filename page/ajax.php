<?php
/**
 * Description of ajax page
 * @author Damien.D & Stephane.G
 *
 * File used for interactions with javascript of pages.
 */

require_once '../core/config.php';

class AjaxOperator {
    private $params,
            $action,
            $user,
            $response = array();
    
    static function init(array $data){
        self::isAvailableDB();
        $operator = new self();
        $operator->params = $data;
        $operator->action = $data['action'];
        $operator->user   = !isset($data['idUser']) ? NULL :
            UserManager::init()->getByID($data['idUser']);
        return $operator;
    }
    static function isAvailableDB(){
        if( !ManagerDB::availableDB() ){
            echo json_encode(['DBdown' => "Unavailable database"],JSON_UNESCAPED_SLASHES);
            die;
        }
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
    
    // -- LOGIN --
    private function checkLogin(){
        $email  = htmlentities($this->params['email'], ENT_QUOTES);
        $pwd    = htmlentities($this->params['password'], ENT_QUOTES);
        
        $logged = AccountController::checkIDs($email, $pwd);
        if( !$logged ){
            $this->response['errorID'] = "Invalid account identifiants";
            return;
        }
        
        $status = AccountController::getUserProfile($email)->getUserStatus();
        $refusedStatus = $status == USER_STATUS::BANISHED
                      || $status == USER_STATUS::CREATED
                      || $status == USER_STATUS::DELETED;
        $refusedStatus ? $this->response['errorStatus'] = "Forbidden account status"
                       : $this->response['allowed'] = "Your will be redirected to your home page";
    }
    /*OK*/private function createAccount(){
        $email  = htmlentities($this->params['email'], ENT_QUOTES);
        $pwd    = htmlentities($this->params['password1'], ENT_QUOTES);
        $pseudo = htmlentities($this->params['pseudo'], ENT_QUOTES);
        
        if(count(UserManager::init()->getAll("WHERE email='$email'")) > 0){
            $this->response['errorEmail'] = "This e-mail address already exists";
            return;
        }
        if(count(UserManager::init()->getAll("WHERE pseudo='$pseudo'")) > 0){
            $this->response['errorPseudo'] = "This pseudo already exists";
            return;
        }
        
        $token = Tools::generateRandomString();
        $user = User::init($email, $pwd, $pseudo);
        $user->setToken($token);
        $idUser = UserManager::init()->insert($user);

        if( !$idUser ){
            $this->response['error'] = "Account creation failed";
            return;
        }
        else{
            $sended = Tools::sendActivationMail($user, $idUser);
            if( !$sended ){
                $this->response['errorMailing'] = "The mail didn't arrive in your mailbox";
                return;
            }
        }

        
        
        $this->response['created'] = TRUE;
    }
    private function resetPassword(){
        $token     = htmlentities($this->params['token'], ENT_QUOTES);
        $today     = Tools::today();
        $forgotDay = (new DateTime($this->user->getForgotPassword()))->format("Y-m-d");
        
        // check reset password constraints
        if($token != $this->user->getToken()){
            $this->response['errorToken'] = "Unknown token";
            return;
        }
        if($today != $forgotDay){
            $this->response['errorTime'] = "Allocated time over";
            return;
        }
        
        // reset password allowed
        $this->user->setPassword( htmlentities($this->params['password1'], ENT_QUOTES) );
        $this->user->setForgotPassword(NULL);
        $this->user->setToken(NULL);
        UserManager::init()->update($this->user) ?
            $this->response['updated'] = TRUE :
            $this->response['error'] = "Password update failed";
        
    }
    
    // -- LOGIN & PROFILE --
    /*OK*/private function forgotPassword(){
        $email = htmlentities($this->params['email'], ENT_QUOTES);
        $users = UserManager::init()->getAll("WHERE email='$email'");
        
        if( empty($users) ){
            $this->response['errorEmail'] = "Unknown e-mail address";
            return;
        }
        
        $token = Tools::generateRandomString();
        $users[0]->setToken($token);
        $users[0]->setForgotPassword(Tools::today());
        $updated = UserManager::init()->update($users[0]);
        
        if( !$updated ){
            $this->response['error'] = "Password forgot failed";
        }
        else{
            $sended = Tools::sendResetMail($users[0]);
            if( !$sended ){
                $this->response['errorMailing'] = "The mail didn't arrive in your mailbox";
                return;
            }
        }
        
        $this->response['forgotten'] = TRUE;            
    }
    
    // -- PROFILE --
    private function askDeletion(){
        $this->user->setUserStatus(USER_STATUS::DELETING);
        UserManager::init()->update($this->user) ?
            $this->response['deleting'] = TRUE :
            $this->response['error'] = "Deletion asking failed";
    }
    
    // -- FORUM --
    private function createSubject(){
        if(empty($this->params['title']) || empty($this->params['message'])){
            $this->response['empty'] = "Empty message";
            return;
        }
        
        $subject = Subject::init( htmlentities($this->params['title'], ENT_QUOTES),
                                  htmlentities($this->params['message'], ENT_QUOTES),
                                  $this->user->getId() );
        $insertedID = SubjectManager::init()->insert($subject);
        $insertedID ? $subject->setId($insertedID) : NULL;
        $insertedID ? $this->response['created'] = ForumController::formateSubject($subject) :
                      $this->response['error'] = "Subject insertion failed";
    }
    private function closeSubject(){
        $subject = SubjectManager::init()->getByID($this->params['idSubject']);
        $subject->setSubjectStatus(SUBJECT_STATUS::CLOSED);
        SubjectManager::init()->update($subject) ?
            $this->response['closed'] = TRUE :
            $this->response['error'] = "Subject closing failed";
    }
    private function deleteSubject(){
        $subject  = SubjectManager::init()->getByID($this->params['idSubject']);
        $posts    = PostManager::init()->getAll("WHERE idSubject=".$subject->getId());
        $postIds  = array_map(function($post){ return $post->getId(); }, $posts);

        $delPosts = empty($posts) ? TRUE : PostManager::init()->deleteMulti($postIds);
        $delSubj  = SubjectManager::init()->delete($subject);

        $delPosts && $delSubj ?
            $this->response['deleted'] = TRUE :
            $this->response['error'] = "Subject deletion failed";
    }
    private function createPost(){
        if(empty($this->params['message'])){
            $this->response['empty'] = "Empty message";
            return;
        }
        
        $post = Post::init( htmlentities($this->params['message'], ENT_QUOTES),
                            $this->user->getId(), $this->params['idSubject'] );
        $inserted = PostManager::init()->insert($post);
        $inserted ? $post->setId($inserted) : NULL;
        $inserted ? $this->response['created'] = ForumController::formatePost(
                    $post, $this->user->getPseudo(),TRUE)
                  : $this->response['error'] = "Post insertion failed";
    }
    private function deletePost(){
        $post = PostManager::init()->getByID($this->params['idPost']);
        PostManager::init()->delete($post) ?
            $this->response['deleted'] = TRUE :
            $this->response['error'] = "Post deletion failed";
    }
    
    // -- ACCOUNT --
    /*OK*/private function updateRights(){
        $this->user->setUserType($this->params['userType']);
        $updated = UserManager::init()->update($this->user);
        
        if($updated){    
            $sended = Tools::sendPromotionMail($this->user, $this->params['userType']);
            
            if( !$sended ){
                $this->response['errorMailing'] = "The mail didn't arrive in your mailbox";
                return;
            }else{
                $this->response['updated'] = TRUE;
            }
        }
        else{
            $this->response['error'] = "Right updating failed";
        }
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
        
        if($this->response['deleted'] == TRUE){
            $sended = Tools::sendAccountDeletionMail($this->user);
            if( !$sended ){
                $this->response['errorMailing'] = "The mail didn't arrive in your mailbox";
                return;
            }
        }
    }
    /*OK*/private function banishAccount(){
        $this->user->setUserStatus(USER_STATUS::BANISHED);
        UserManager::init()->update($this->user)  ?
            $this->response['banished'] = TRUE :
            $this->response['error'] = "Banishment failed";
        
        if($this->response['banished'] == TRUE){
            $sended = Tools::sendBanishMail($this->user);
            if( !$sended ){
                $this->response['errorMailing'] = "The mail didn't arrive in your mailbox";
                return;
            }
        }
    }
    /*OK*/private function unbanishAccount(){
        $this->user->setUserStatus(USER_STATUS::ACTIVATED);
        UserManager::init()->update($this->user)  ?
            $this->response['unbanished'] = TRUE :
            $this->response['error'] = "Unbanishment failed";
        
        if($this->response['unbanished'] == TRUE){
            $sended = Tools::sendUnbanishMail($this->user);
            if( !$sended ){
                $this->response['errorMailing'] = "The mail didn't arrive in your mailbox";
                return;
            }
        }
    }
    
    // -- MODERATION --
    /*mail*/private function acceptLevel(){
        $level = LevelManager::init()->getByID($this->params['idLevel']);
        $level->setLevelStatus(LEVEL_STATUS::ACCEPTED);
        $user = UserManager::init()->getByID($level->getIdCreator());
        
        LevelManager::init()->update($level) ?
            $this->response['accepted'] = TRUE :
            $this->response['error']    = "Level acceptance failed";
        
        if($this->response['accepted'] == TRUE){
            $sended = Tools::sendLevelAcceptedMail($user, $level);
            if( !$sended ){
                $this->response['errorMailing'] = "The mail didn't arrive in your mailbox";
                return;
            }
        }
    }
    /*mail*/private function refuseLevel(){
        $level = LevelManager::init()->getByID($this->params['idLevel']);
        $level->setLevelStatus(LEVEL_STATUS::REFUSED);
        $user = UserManager::init()->getByID($level->getIdCreator());
        LevelManager::init()->update($level) ?
            $this->response['refused'] = TRUE :
            $this->response['error']   = "Level refusal failed";
        
        if($this->response['refused'] == TRUE){
            $sended = Tools::sendLevelDeclinedMail($user, $level);
            if( !$sended ){
                $this->response['errorMailing'] = "The mail didn't arrive in your mailbox";
                return;
            }
        }
        
    }
    
    // -- ADDON --
    private function removeAddons(){
        $levelIds = $this->params['idLevels'];
        $scores   = ScoreManager::init()->getAll("WHERE idLevel in (".implode(',',$levelIds).")");
        $scoreIds = array_map(function($score){ return $score->getId(); }, $scores);
        
        $delFiles  = Level::deleteFiles($levelIds);
        $delLevels = LevelManager::init()->deleteMulti($levelIds);
        $delScores = empty($scoreIds) ? TRUE : ScoreManager::init()->deleteMulti($scoreIds);
        
        $delFiles && $delLevels && $delScores ?
            $this->response['deleted'] = TRUE :
            $this->response['error']   = "Scores and levels deletion failed";
    }
    
}

echo AjaxOperator::init($_POST)->treat()->getResponse();

