<?php
require_once "config.php";
define("NB_NEWS_TO_DISPLAY", 5);

/*OK*/class AccountController {
    static function checkIDs($email,$password){
        return !empty(ManagerUser::init()->get(['email'=>$email,'password'=>$password]));
    }
    static function getUserProfile($email){
        return ManagerUser::init()->get(['email'=>$email])[0];
    }
}
/*OK*/class AddonController {
    static function displayLastNews(){
        $criterias = "ORDER BY creationDate DESC LIMIT ".NB_NEWS_TO_DISPLAY;
        $themes = ManagerTheme::init()->get(NULL, $criterias);
        $levels = ManagerLevel::init()->get(["levelStatus"=>LEVEL_STATUS::ACCEPTED],$criterias);
        
        $addons = array_merge($themes, $levels);
        usort($addons, function($addon1,$addon2){
            $addon1->compareCreationDateTo($addon2);
        });
        
        foreach($addons as $addon){
            $creator = ManagerUser::init()->getByID($addon->getIdCreator());
            $news    = $addon->formateAsNews();
            $news->setCreator($creator);
            echo $news->getMessage();
        }
    }
}
/*OK*/class ThemeController {
    static function displayAll(){
        $themes = ManagerTheme::init()->get();
        foreach($themes as $theme){
            // mettre l'image
            echo "[IMAGE] ".Tools::capitalize($theme->getName());
        }
    }
    static function getTheme($id=NULL){
        $params = array();
        if( !is_null($id) ){ $params["id"] = $id; }
        return (new ManagerAddon())->get($params);
    }
}
/*OK*/class LevelController {
    static function displayCustomLevels(){
        $customs = self::getLevel(NULL, LEVEL_TYPE::CUSTOM);
        foreach($customs as $level){
            $creator = ManagerUser::init()->getByID( $level->getIdCreator() );
            // mettre l'image
            echo "<div class='custom-level'>[IMAGE] ".Tools::capitalize($level->getName())
                ." created by ".$creator->getPseudo()."<br><span class='description'>"
                .$level->getDescription()."</span></div>";
        }
    }
    static function getLevel($id=NULL, $levelType=NULL, $levelStatus=LEVEL_STATUS::ACCEPTED, $levelMode=LEVEL_MODE::STANDARD){
        if( !is_null($id) ){
            return ManagerLevel::init()->get(["id" => $id]);
        }
        
        $params = array();
        if( !is_null($levelType) ){
            $params['levelType'] = $levelType;
        }
        array_merge($params,array(
            "levelStatus" => $levelStatus,
            "levelMode"   => $levelMode));
        return ManagerLevel::init()->get($params);        
    }
}
/*OK*/class ScoreController {
    /*todo*/static function getScores($idPlayer){
        return ;
    }
    /*todo*/static function getRanksByPlayer($idPlayer){
        return ;
    }
    static function displayHeaders($idLevel=NULL){
        if(is_null($idLevel)){ ?><tr>
            <th>Rank</th>
            <th>Player</th>
            <th>Points</th>
        </tr><?php
        }else{ ?>
            <th>Rank</th>
            <th>Player</th>
            <th>Points</th>
            <th>Time</th>
            <th>Number of<br>clicks</th>
            <th>Number of<br>gathered items</th>
        <?php }
    }
    static function displayRanking($idLevel=NULL){
        $ranks = ManagerScore::init()->getRanking($idLevel);
        if( is_null($idLevel) ){
            $i = 1;
            foreach($ranks as $data){
                echo "<tr><td>".$i++."</td><td>".$data['player']
                    ."</td><td>".$data['points']."</td></tr>";
            }
        }else{
            $i = 1;
            foreach($ranks as $data){
                $player = $data['player'];
                $points = $data['points'];
                $score  = $data['score'];
                echo "<tr><td>".$i++."</td><td>$player</td><td>$points</td><td>"
                    .$score->getTime()."</td><td>".$score->getNbClicks()."</td><td>"
                    .$score->getNbItems()."</td></tr>";
            }
        }
    }
    static function displayScoredBasicLevels(){
        $basics = ManagerLevel::init()->getLevelHavingScores(LEVEL_TYPE::BASIC);
        self::formateLevels($basics);
    }
    static function displayScoredCustomLevels(){
        $customs = ManagerLevel::init()->getLevelHavingScores(LEVEL_TYPE::CUSTOM);
        self::formateLevels($customs);
    }
    static private function formateLevels(array $levels){
        if(empty($levels)){
            echo "<div>No level with scores</div>";
            return;
        }
        
        foreach($levels as $level){
            // mettre l'image
            echo "<div class='level' data-idlevel='".$level['id']
                ."'>[IMAGE] ".Tools::capitalize($level['name'])."</div>";
        }
    }

}
/*OK*/class ForumController {
    static function displayLastNews(){
        $orderByDate = "ORDER BY date DESC LIMIT ".NB_NEWS_TO_DISPLAY;
        $subjects    = ManagerSubject::init()->get(["subjectStatus"=>SUBJECT_STATUS::ACTIVE], $orderByDate);
        $posts       = ManagerPost::init()->get(NULL, $orderByDate);
        $mgrUser     = ManagerUser::init();
        $count       = NB_NEWS_TO_DISPLAY;
        
        while($count--){
            if(empty($subjects)){
                self::displayNews($mgrUser, $posts[0]);
                array_shift($posts);
                continue;
            }
            if(empty($posts)){
                self::displayNews($mgrUser, $subjects[0]);
                array_shift($subjects);
                continue;
            }
            
            $dateSubject = new DateTime($subjects[0]->getDate());
            $datePost    = new DateTime($posts[0]->getDate());
            
            if($dateSubject >= $datePost){
                self::displayNews($mgrUser, $subjects[0]);
                array_shift($subjects);
            }
            if($dateSubject < $datePost){
                self::displayNews($mgrUser, $posts[0]);
                array_shift($posts);
            }            
        }
    }
    static private function displayNews($mgrUser,$item){
        $news    = $item->formateAsNews();
        $creator = $item->getIdAuthor();
        $news->setCreator($mgrUser->getByID($creator));
        echo $news->getMessage();
    }
    static function displaySubjects(){
        $subjects = ManagerSubject::init()->getList();
        foreach($subjects as $subject){
            $lastPost = ManagerPost::init()->get(
                    ["idSubject" => $subject->getId()],
                    "ORDER BY date DESC LIMIT 1");
            $isPostMoreRecent = empty($lastPost) ? FALSE :
                    $lastPost[0]->getDate() >= $subject->getDate();
            $date = (new DateTime($isPostMoreRecent ? 
                        $lastPost[0]->getDate() : $subject->getDate())
                    )->format("d-m-Y");;
            $author = ManagerUser::init()->getById($isPostMoreRecent ?
                        $lastPost[0]->getIdAuthor() : $subject->getIdAuthor()
                    )->getPseudo();
            echo "<tr class='subject' data-idsubject='".$subject->getId()
                ."'><td>".$subject->getTitle()
                ."</td><td>".$subject->getSubjectStatus()
                ."</td><td>$date by ".Tools::capitalize($author)
                ."</td></tr>";
        }
    }
    static function displayInfosSubject($idSubject){
        $subject = ManagerSubject::init()->getByID($idSubject);
        echo "<div><b><em>Title  : ".Tools::capitalize($subject->getTitle())
            ."<br>Status : ".$subject->getSubjectStatus()."</em></b></div>";
    }
    static function displayPosts($idSubject, $isSuperUser){
        $posts = ManagerPost::init()->get(['idSubject'=>$idSubject],"ORDER BY date DESC");
        foreach($posts as $post){
            $author = ManagerUser::init()->getByID($post->getIdAuthor())->getPseudo();
            $date   = (new DateTime($post->getDate()))->format("d-m-Y");
            echo "<div>$date by $author :<br>".$post->getMessage()
                .($isSuperUser ? "<button id='".$post->getId()
                ."' class='btn-delete'>Delete</button>" : NULL)."</div>";
        }
    }
}
class ApiController {
    /*OK*/static function existsAction($action){
        $constants = API_ACTION::getConstants();
        return array_search($action, $constants);
    }
    /*OK*/static function displayResponse($data, $error=NULL){
        $response = array();
        if($data ){ $response['data']  = $data;  }
        if($error){ $response['error'] = $error; }
        echo json_encode($response);
        die;
    }
    
    /* Each function must have its name like the value in API_ACTION 
     * and declare following as arguments :
     *     - $user   : the user account
     *     - $params : the array of parameters sent in URL   */
    /*OK*/static function getThemes(User $user, array $params){
        $themes = ThemeController::getTheme();
        self::displayResponse( json_encode($themes) );
    }
    /*OK*/static function getBasicLevels(User $user, array $params){
        $basics = LevelController::getLevel(NULL, LEVEL_TYPE::BASIC);
        self::displayResponse( json_encode($basics) );
    }
    /*OK*/static function getCustomLevels(User $user, array $params){
        $customs = LevelController::getLevel(NULL, LEVEL_TYPE::CUSTOM);
        self::displayResponse( json_encode($customs) );
    }
    /*OK*/static function getLevelsToModerate(User $user, array $params){
        $toModerates = LevelController::getLevel(NULL, LEVEL_TYPE::CUSTOM, LEVEL_STATUS::TOMODERATE);
        self::displayResponse( json_encode($toModerates) );
    }
    /*OK*/static function getScores(User $user, array $params){
        $scores = ManagerScore::init()->getListByPlayer($user->getId());
        self::displayResponse( json_encode($scores) );
    }
    /*OK*/static function getRanks(User $user, array $params){
        $ranks = ManagerScore::init()->getRanksByPlayer($user->getId());
        self::displayResponse( json_encode($ranks) );
    }
    /*OK*/static function downloadProfile(User $user, array $params){
        self::displayResponse(json_encode($user) );
    }
    static function downloadTheme(User $user, array $params){
        if(!isset($params['idTheme'])){
            self::displayResponse(NULL, "Missing theme ID");
        }
        
        $theme = ManagerTheme::init()->getByID($params['idTheme']);
        var_dump($theme);
    }
    static function downloadBasicLevel(User $user, array $params){
        if(!isset($params['idBasicLevel'])){
            self::displayResponse(NULL, "Missing basic level ID");
        }
        
        $basicLevel = ManagerLevel::init()->getByID($params['idBasicLevel']);
        var_dump($basicLevel);
    }
    static function downloadCustomLevel(User $user, array $params){
        if(!isset($params['idCustomLevel'])){
            self::displayResponse(NULL, "Missing custom level ID");
        }
        
        $customLevel = ManagerLevel::init()->getByID($params['idCustomLevel']);
        var_dump($customLevel);
    }
    static function downloadLevelToModerate(User $user, array $params){
        if(!isset($params['idLevelToModerate'])){
            self::displayResponse(NULL, "Missing level-to-moderate ID");
        }
        
        $levelToModerate = ManagerLevel::init()->getByID($params['idLevelToModerate']);
        var_dump($levelToModerate);
    }
    static function uploadCustomLevel(User $user, array $params){
        var_dump($user,$params);
    }
    /*totest*/static function uploadScores(User $user, array $params){
        if(!isset($params['scores'])){
            self::displayResponse(NULL, "Missing scores to upload");
        }
        /*                                                                                                                                                   |                ||                    |||                   ||
         * http://localhost/Winds/api.php?email=player2@winds.net&password=912af0dff974604f1321254ca8ff38b6&action=uploadScores&scores=%5B%7B%22idLevel%22%3A4%2C%22time%22%3A60%2C%22nbClicks%22%3A100%2C%22nbItems%22%3A80%7D%5D
         */
        $scoresSended = json_decode( urldecode($params['scores']), TRUE );
        $counter = 0;
        foreach($scoresSended as $value){
            $uploaded = Score::init($user->getId(), $value['idLevel'],
                    $value['time'], $value['nbClicks'], $value['nbItems']);
            $scoresDB = (new ManagerScore())->get(array(
                    "idPlayer" => $user->getId(),
                    "idLevel"  => $value['idLevel']));
            if( empty($scoresDB) ){
                // no score found for this user and level
                //OK //(new ManagerScore())->insert($uploaded);
            }
            else {
                $current = $scoresDB[0];
                $needUpdate = $uploaded->compareTo($current) == TRUE;
                $needUpdate ? $current->updateFrom($uploaded) : NULL;
                var_dump(intval($needUpdate));
                //(new ManagerScore())->update($current);
            }
            $counter++;
        }
        exit;
        $all = $counter == count($scoresSended);
        $message = "All of uploaded scores have ".($all ? NULL : "not ")."been inserted into DB";
        $all ? self::displayResponse($message): self::displayResponse(NULL, $message);
    }
}
