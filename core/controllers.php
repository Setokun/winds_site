<?php
require_once "config.php";
define("NB_NEWS_TO_DISPLAY", 5);

class AccountController {
    /*OK*/static function checkIDs($email,$password){
        return !empty((new ManagerUser())->get(['email'=>$email,'password'=>$password]));
    }
    /*OK*/static function getUserProfile($email){
        return (new ManagerUser())->get(['email'=>$email])[0];
    }
}
class AddonController {
    /*OK*/static function displayLastNews(){
        $addons = (new ManagerAddon())->get(
                    array("addonStatus"=>ADDON_STATUS::ACCEPTED),
                    "ORDER BY creationDate DESC LIMIT ".NB_NEWS_TO_DISPLAY);
        foreach($addons as $addon){
            $creator = (new ManagerUser())->getByID( $addon->getIdCreator() );
            $news    = $addon->formateAsNews();
            $news->setCreator($creator);
            echo $news->getMessage();
        }
    }
    /*OK*/static function displayThemes(){
        $themes = (new ManagerAddon())->get(array(
                    "addonStatus"   => ADDON_STATUS::ACCEPTED,
                    "addonType"     => ADDON_TYPE::THEME));
        foreach($themes as $theme){
            // mettre l'image
            echo "[IMAGE] ".Tools::capitalize($theme->getName());
        }
    }
    /*OK*/static function displayCustomLevels(){
        $customs = self::getLevel(NULL, LEVEL_TYPE::CUSTOM);
        foreach($customs as $level){
            $creator = (new ManagerUser())->getByID( $level->getIdCreator() );
            // mettre l'image
            echo "<div class='custom-level'>[IMAGE] ".Tools::capitalize($level->getName())
                ." created by ".$creator->getPseudo()."<br><span class='description'>"
                .$level->getDescription()."</span></div>";
        }
    }
    /*OK*/static function getTheme($id=NULL){
        $params = array("addonType" => ADDON_TYPE::THEME);
        if( !is_null($id) ){ $params["id"] = $id; }
        return (new ManagerAddon())->get($params);
    }
    /*OK*/static function getLevel($id=NULL, $levelType=NULL, $addonStatus=ADDON_STATUS::ACCEPTED){
        $params = !is_null($id) ? array("id"=>$id) : array(
                "addonStatus"   => $addonStatus,
                "addonType"     => ADDON_TYPE::LEVEL,
                "levelType"     => $levelType);
        return (new ManagerAddon())->get($params);
    }
}
class ScoreController {
    static private $points = array('time'      => 10,
                                   'nbClicks'  => 10,
                                   'nbItems'   => 1);
    /*OK*/static function getScores($idPlayer){
        return ;
    }
    static public function getRanksByPlayer($idPlayer){
        
    }
    /*OK*/static function displayHeaders(){
        ?><tr>
            <th>Rank</th>
            <th>Player</th>
            <th>Points</th>
            <th>Time</th>
            <th>Number of<br>clicks</th>
            <th>Number of<br>gathered items</th>
        </tr><?php
    }
    /*OK*/static function displayRanking($idLevel=NULL){
        $i = 1;
        $scores = (new ManagerScore())->getRanking($idLevel);
        foreach($scores as $score){
            $player = (new ManagerUser())->getByID($score->getIdPlayer())->getPseudo();
            $total = $score->getTime()      * self::$points['time']
                    + $score->getNbClicks() * self::$points['nbClicks']
                    + $score->getNbItems()  * self::$points['nbItems'];
            echo "<tr><td>".$i++."</td><td>$player</td><td>$total</td><td>"
                .$score->getTime()."</td><td>".$score->getNbClicks()."</td><td>"
                .$score->getNbItems()."</td></tr>";
        }
    }
    /*OK*/static function displayBasicLevels(){
        $basics = AddonController::getLevel(NULL, LEVEL_TYPE::BASIC);
        self::formateLevels($basics);
    }
    /*OK*/static function displayCustomLevels(){
        $customs = AddonController::getLevel(NULL, LEVEL_TYPE::CUSTOM);
        self::formateLevels($customs);
    }
    /*OK*/static private function formateLevels(array $levels){
        foreach($levels as $level){
            // mettre l'image
            echo "<div class='level' data-idlevel='".$level->getId()
                ."'>[IMAGE] ".Tools::capitalize($level->getName())."</div>";
        }
    }

}
class ForumController {
    /*OK*/static function displayLastNews(){
        $orderByDate = "ORDER BY date DESC LIMIT ".NB_NEWS_TO_DISPLAY;
        $subjects    = (new ManagerSubject())->get(array("subjectStatus"=>SUBJECT_STATUS::ACTIVE), $orderByDate);
        $posts       = (new ManagerPost())->get(NULL, $orderByDate);
        $mgrUser     = new ManagerUser();
        $count = NB_NEWS_TO_DISPLAY;
        
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
    /*OK*/static private function displayNews($mgrUser,$item){
        $news    = $item->formateAsNews();
        $creator = $item->getIdAuthor();
        $news->setCreator($mgrUser->getByID($creator));
        echo $news->getMessage();
    }
    /*OK*/static function displaySubjects(){
        $subjects = (new ManagerSubject())->getList();
        foreach($subjects as $subject){
            $lastPost = (new ManagerPost())->get(
                    ["idSubject" => $subject->getId()],
                    "ORDER BY date DESC LIMIT 1");
            $isPostMoreRecent = empty($lastPost) ? FALSE :
                    $lastPost[0]->getDate() >= $subject->getDate();
            $date = (new DateTime($isPostMoreRecent ? 
                        $lastPost[0]->getDate() : $subject->getDate())
                    )->format("d-m-Y");;
            $author = (new ManagerUser())->getById($isPostMoreRecent ?
                        $lastPost[0]->getIdAuthor() : $subject->getIdAuthor()
                    )->getPseudo();
            echo "<tr class='subject' data-idsubject='".$subject->getId()
                ."'><td>".$subject->getTitle()
                ."</td><td>".$subject->getSubjectStatus()
                ."</td><td>$date by ".Tools::capitalize($author)
                ."</td></tr>";
        }
    }
    /*OK*/static function displayInfosSubject($idSubject){
        $subject = (new ManagerSubject())->getByID($idSubject);
        echo "<div><b><em>Title  : ".Tools::capitalize($subject->getTitle())
            ."<br>Status : ".$subject->getSubjectStatus()."</em></b></div>";
    }
    /*OK*/static function displayPosts($idSubject, $isSuperUser){
        $posts = (new ManagerPost())->get(['idSubject'=>$idSubject],"ORDER BY date DESC");
        foreach($posts as $post){
            $author = (new ManagerUser())->getByID($post->getIdAuthor())->getPseudo();
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
    }
    
    /* Each function must have its name like the value in API_ACTION 
     * and declare following as arguments :
     *     - $user   : the user account
     *     - $params : the array of parameters sent in URL   */
    /*OK*/static function getThemes(User $user, array $params){
        $themes = AddonController::getTheme();
        self::displayResponse( json_encode($themes) );
    }
    /*OK*/static function getBasicLevels(User $user, array $params){
        $basics = AddonController::getLevel(NULL, LEVEL_TYPE::BASIC);
        self::displayResponse( json_encode($basics) );
    }
    /*OK*/static function getCustomLevels(User $user, array $params){
        $customs = AddonController::getLevel(NULL, LEVEL_TYPE::CUSTOM);
        self::displayResponse( json_encode($customs) );
    }
    /*OK*/static function getLevelsToModerate(User $user, array $params){
        $toModerates = AddonController::getLevel(NULL, LEVEL_TYPE::CUSTOM,  ADDON_STATUS::TOMODERATE);
        self::displayResponse( json_encode($toModerates) );
    }
    /*OK*/static function getScores(User $user, array $params){
        $scores = (new ManagerScore())->getListByPlayer($user->getId());
        self::displayResponse( json_encode($scores) );
    }
    /*OK*/static function getRanks(User $user, array $params){
        $ranks = (new ManagerScore())->getRanksByPlayer($user->getId());
        self::displayResponse( json_encode($ranks) );
    }
    /*OK*/static function downloadProfile(User $user, array $params){
        self::displayResponse(json_encode($user) );
    }
    static function downloadTheme(User $user, array $params){
        if(!isset($params['idTheme'])){
            self::displayResponse(NULL, "Missing theme ID");
            die;
        }
        
        $theme = (new ManagerAddon())->getByID($params['idTheme']);
        var_dump($theme);
    }
    static function downloadBasicLevel(User $user, array $params){
        if(!isset($params['idBasicLevel'])){
            self::displayResponse(NULL, "Missing basic level ID");
            die;
        }
        
        $basicLevel = (new ManagerAddon())->getByID($params['idBasicLevel']);
        var_dump($basicLevel);
    }
    static function downloadCustomLevel(User $user, array $params){
        if(!isset($params['idCustomLevel'])){
            self::displayResponse(NULL, "Missing custom level ID");
            die;
        }
        
        $customLevel = (new ManagerAddon())->getByID($params['idCustomLevel']);
        var_dump($customLevel);
    }
    static function downloadLevelToModerate(User $user, array $params){
        if(!isset($params['idLevelToModerate'])){
            self::displayResponse(NULL, "Missing level-to-moderate ID");
            die;
        }
        
        $levelToModerate = (new ManagerAddon())->getByID($params['idLevelToModerate']);
        var_dump($levelToModerate);
    }
    static function uploadCustomLevel(User $user, array $params){
        
    }
    static function uploadScores(User $user, array $params){
        
    }
}
