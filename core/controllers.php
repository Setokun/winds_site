<?php
require_once "config.php";
define("NB_NEWS_TO_DISPLAY", 5);

/*OK*/class AccountController {
    /*OK*/static function checkIDs($email,$password){
        return !empty(UserManager::init()->getAll("WHERE email='$email' AND password='$password'"));
    }
    /*OK*/static function getUserProfile($email){
        return UserManager::init()->getAll("WHERE email='$email'")[0];
    }
}
/*OK*/class AddonController {
    /*OK*/static function displayLastNews(){
        $criterias = "ORDER BY creationDate DESC LIMIT ".NB_NEWS_TO_DISPLAY;
        $themes  = ThemeManager::init()->getAll($criterias);
        $levels  = LevelManager::init()->getAll("WHERE levelStatus='".LEVEL_STATUS::ACCEPTED."' $criterias");
        $authors = UserManager::init()->getPseudos();
        
        // extract the 5 recent addons (theme or level)
        $merge = array_merge($themes, $levels);
        usort($merge, function($addon1,$addon2){
            $addon1->compareCreationDateTo($addon2);
        });
        $addons = array_slice($merge, 0, NB_NEWS_TO_DISPLAY);
        
        // formate the news
        foreach($addons as $addon){
            $news   = $addon->formateAsNews();
            $author = $authors[ $addon->getIdCreator() ];
            $news->setAuthor($author);
            echo $news->getMessage();
        }
    }
    /*todo*/static function displayThemes(){
        $themes = ThemeManager::init()->getAll();
        foreach($themes as $theme){
            echo "<div class='col-xs-3'><img src='".
                    
                 "../resources/logo-honey.png"  // mettre l'image
                
                ."' class='theme-image' />"
            ."<span style='margin-left:10px'> ".Tools::capitalize($theme->getName())."</span></div>";
        }
    }
    /*todo*/static function displayCustomLevels(){
        $customs  = self::getLevel(NULL, LEVEL_TYPE::CUSTOM);
        $creators = LevelManager::init()->getCreators();
        
        foreach($customs as $level){
            echo "<div class='custom-level'><img src='".
                    
                 "../resources/logo-ice.png"    // mettre l'image
                    
                ."' class='theme-image' /> "
                .Tools::capitalize($level->getName())." created by ".$creators[ $level->getId() ]
                ."<br><span class='description'>".$level->getDescription()."</span></div>";
        }
    }
    /*todo*/static function displayLevelsToModerate(){
        $tomoderates = self::getLevel(NULL, LEVEL_TYPE::CUSTOM, LEVEL_STATUS::TOMODERATE);
        $creators    = LevelManager::init()->getCreators();
        
        foreach($tomoderates as $level){
            echo "<tr><td class='col-xs-10'><img src='".
                    
                 "../resources/logo-honey.png"  // mettre l'image
                    
                 ."' class='theme-image' />"
                 ."<span>".Tools::capitalize($level->getName())." by "
                 .Tools::capitalize($creators[ $level->getIdCreator() ])."</span></td><td class='col-xs-1'>"
                 ."<button class='button-green moderation-button' data-idLevel='".$level->getId()
                 ."'>Accept</button></td><td class='col-xs-1'>"
                 ."<button class='button-red moderation-button' data-idLevel='".$level->getId()."'>"
                 ."Refuse</button></td></tr>";
        }
    }
    /*OK*/static function getTheme($id=NULL){
        return is_null($id) ?
               ThemeManager::init()->getAll() :
               ThemeManager::init()->getByID($id);
    }
    /*OK*/static function getLevel($id=NULL, $levelType=NULL, $levelStatus=LEVEL_STATUS::ACCEPTED, $levelMode=LEVEL_MODE::STANDARD){
        if( !is_null($id) ){
            return LevelManager::init()->getByID($id);
        }
        
        $params = array();
        if( !is_null($levelType) ){
            array_push($params, "levelType='$levelType'");
        }
        array_push($params, "levelStatus='$levelStatus'", "levelMode='$levelMode'");
        return LevelManager::init()->getAll("WHERE ".implode(" AND ", $params));        
    }
}
class ScoreController {
    /*todo*/static function getScores($idPlayer){
        return ;
    }
    /*todo*/static function getRanksByPlayer($idPlayer){
        return ;
    }
    /*OK*/static function displayHeaders(Level $level=NULL){
        if(is_null($level)){ ?><tr>
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
    /*OK*/static function displayRanking(Level $level=NULL){
        $ranks = ScoreManager::init()->getRanking(is_null($level) ? NULL : $level->getId());
        $i = 1;
        foreach($ranks as $data){
            if( is_null($level) ){
                echo "<tr class='score-data'><td>".$i++."</td><td>".$data['player']
                ."</td><td>".$data['points']."</td></tr>";
            }else{
                $score  = $data['score'];
                echo "<tr class='score-data'><td>".$i++."</td><td>".$data['player']
                    ."</td><td>".$data['points']."</td><td>".$score->getTime()
                    ."</td><td>".$score->getNbClicks()."</td><td>".$score->getNbItems()
                    ."</td></tr>";

            }
        }
    }
    /*todo*/static function displayInfosScore(Level $level){
        $creator = UserManager::init()->getByID($level->getIdCreator())->getPseudo();
        echo "<h4>[image] Ranking of \"".Tools::capitalize($level->getName())
            ."\" created by ".Tools::capitalize($creator)."</h4>";
    }
    /*OK*/static function displayScoredBasicLevels(){
        $basics = LevelManager::init()->getLevelsHavingScores(LEVEL_TYPE::BASIC);
        self::formateLevels($basics);
    }
    /*OK*/static function displayScoredCustomLevels(){
        $customs = LevelManager::init()->getLevelsHavingScores(LEVEL_TYPE::CUSTOM);
        self::formateLevels($customs);
    }
    /*todo*/static private function formateLevels(array $levels){
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
    /*OK*/static function displayLastNews(){
        $orderByDate = "ORDER BY date DESC, id DESC LIMIT ".NB_NEWS_TO_DISPLAY;
        $subjects    = SubjectManager::init()->getAll($orderByDate);
        $posts       = PostManager::init()->getAll($orderByDate);
        $authors     = UserManager::init()->getPseudos();
        $count       = NB_NEWS_TO_DISPLAY;

        while($count--){
            if(empty($subjects)){
                self::displayNews($posts[0],$authors);
                array_shift($posts);
                continue;
            }
            if(empty($posts)){
                self::displayNews($subjects[0],$authors);
                array_shift($subjects);
                continue;
            }
            
            $dateSubject = new DateTime($subjects[0]->getDate());
            $datePost    = new DateTime($posts[0]->getDate());
            
            if($dateSubject >= $datePost){
                self::displayNews($subjects[0],$authors);
                array_shift($subjects);
            }
            if($dateSubject < $datePost){
                self::displayNews($posts[0],$authors);
                array_shift($posts);
            }            
        }
    }
    /*OK*/static private function displayNews($item, $authors){
        $news   = $item->formateAsNews();
        $author = $authors[ $item->getIdAuthor() ];
        $news->setAuthor($author);
        echo $news->getMessage();
    }
    /*OK*/static function displaySubjects(){
        $subjects = SubjectManager::init()->getAll();
        foreach($subjects as $subject){
            echo self::formateSubject($subject);
        }
    }
    /*OK*/static function formateSubject(Subject $subject){
        $last = SubjectManager::init()->getLastUpdate($subject);
        $date = (new DateTime($last['date']))->format("d-m-Y");
        return "<tr class='subject' data-idsubject='".$subject->getId()."'>"
              ."<td>".$subject->getTitle()."</td>"
              ."<td>".$subject->getSubjectStatus()."</td>"
              ."<td>$date by ".Tools::capitalize($last['author'])."</td>"
              ."</tr>";
    }
    /*OK*/static function displayInfosSubject(Subject $subject){
        if(is_null($subject)){ return; }
        echo "<div><div class='forum-post-title'><p>Title  : ".Tools::capitalize($subject->getTitle())
            ."<p></div><p>Status : ".$subject->getSubjectStatus()."</p></div>";
    }
    /*OK*/static function displayPosts(Subject $subject, $isSuperUser){
        $authors = USerManager::init()->getPseudos();
        $posts   = PostManager::init()->getAll("WHERE idSubject=".$subject->getId()." ORDER BY date ASC");
        array_unshift($posts, $subject);

        foreach($posts as $post){
            $date = (new DateTime($post->getDate()))->format("d-m-Y H:i:s");
            echo "<div><div class='col-xs-8 col-sm-9 col-md-10' style='border-top: 2px solid #aaa; padding-top:10px;'>"
                ."$date by ".$authors[ $post->getIdAuthor() ]." :<br>".$post->getMessage()."</div>";
            if($isSuperUser && !$post instanceof Subject){
                echo "<div class='col-xs-4 col-sm-3 col-md-2'><button id='".$post->getId()
                    ."' class='btn-delete-post'>Delete</button></div>";
            }
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
        if($data){  $response['data']  = $data;  }
        if($error){ $response['error'] = $error; }
        echo json_encode($response);
        die;
    }
    
    /* Each function must have its name like the value in API_ACTION 
     * and declare following as arguments :
     *     - $user   : the user account
     *     - $params : the array of parameters sent in URL / optionnal  */
    /*OK*/static function getThemes(User $user, array $params=[]){
        $themes = AddonController::getTheme();
        self::displayResponse( json_encode($themes) );
    }
    /*OK*/static function getBasicLevels(User $user, array $params=[]){
        $basics = AddonController::getLevel(NULL, LEVEL_TYPE::BASIC);
        self::displayResponse( json_encode($basics) );
    }
    /*OK*/static function getCustomLevels(User $user, array $params=[]){
        $customs = AddonController::getLevel(NULL, LEVEL_TYPE::CUSTOM);
        self::displayResponse( json_encode($customs) );
    }
    /*OK*/static function getLevelsToModerate(User $user, array $params=[]){
        $toModerates = AddonController::getLevel(NULL, LEVEL_TYPE::CUSTOM, LEVEL_STATUS::TOMODERATE);
        self::displayResponse( json_encode($toModerates) );
    }
    /*OK*/static function getScores(User $user, array $params=[]){
        $scores = ScoreManager::init()->getListByPlayer($user->getId());
        self::displayResponse( json_encode($scores) );
    }
    /*OK*/static function getRanks(User $user, array $params=[]){
        $playerRanks = ScoreManager::init()->getRanksByPlayer($user->getId());
        self::displayResponse( json_encode($playerRanks) );
    }
    /*OK*/static function downloadProfile(User $user, array $params=[]){
        self::displayResponse(json_encode($user) );
    }
    /*todo*/static function downloadTheme(User $user, array $params=[]){
        if(!isset($params['idTheme'])){
            self::displayResponse(NULL, "Missing theme ID");
        }
        
        $theme = ThemeManager::init()->getByID($params['idTheme']);
        var_dump($theme);
    }
    /*todo*/static function downloadBasicLevel(User $user, array $params=[]){
        if(!isset($params['idBasicLevel'])){
            self::displayResponse(NULL, "Missing basic level ID");
        }
        
        $basicLevel = LevelManager::init()->getByID($params['idBasicLevel']);
        var_dump($basicLevel);
    }
    /*todo*/static function downloadCustomLevel(User $user, array $params=[]){
        if(!isset($params['idCustomLevel'])){
            self::displayResponse(NULL, "Missing custom level ID");
        }
        
        $customLevel = LevelManager::init()->getByID($params['idCustomLevel']);
        var_dump($customLevel);
    }
    /*todo*/static function downloadLevelToModerate(User $user, array $params=[]){
        if(!isset($params['idLevelToModerate'])){
            self::displayResponse(NULL, "Missing level-to-moderate ID");
        }
        
        $levelToModerate = LevelManager::init()->getByID($params['idLevelToModerate']);
        var_dump($levelToModerate);
    }
    /*todo*/static function uploadCustomLevel(User $user, array $params=[]){
        var_dump($user,$params);
    }
    /*totest*/static function uploadScores(User $user, array $params=[]){
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
            $scoresDB = (new ScoreManager())->get(array(
                    "idPlayer" => $user->getId(),
                    "idLevel"  => $value['idLevel']));
            if( empty($scoresDB) ){
                // no score found for this user and level
                //OK //(new ScoreManager())->insert($uploaded);
            }
            else {
                $current = $scoresDB[0];
                $needUpdate = $uploaded->compareTo($current) == TRUE;
                $needUpdate ? $current->updateFrom($uploaded) : NULL;
                var_dump(intval($needUpdate));
                //(new ScoreManager())->update($current);
            }
            $counter++;
        }
        exit;
        $all = $counter == count($scoresSended);
        $message = "All of uploaded scores have ".($all ? NULL : "not ")."been inserted into DB";
        $all ? self::displayResponse($message): self::displayResponse(NULL, $message);
    }
}
