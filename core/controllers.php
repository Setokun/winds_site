<?php
require_once "config.php";
define("NB_NEWS_TO_DISPLAY", 5);

class AccountController {
    // -- API --
    static function checkIDs($email,$password){//echo "test1<br>";
		$mgr = UserManager::init();//echo "test2<br>";
		$test = $mgr->getAll("WHERE email='$email' AND password='".md5($password)."'");//echo "test3<br>";
		//var_dump($test);
        return !empty($test);
    }
    static function getUserProfile($email){
        return UserManager::init()->getAll("WHERE email='$email'")[0];
    }
	static function getPseudoById($idUser){
		return UserManager::init()->getAll("WHERE id='$idUser'")[0]->getPseudo();
    }
    static function activateAccount($idUser, $token){
        $user = UserManager::init()->getByID($idUser);
        if( is_null($user) ){ return "Unknown account ID"; }
        if($user->getToken() !== $token){ return "Unknown token"; }
        
        $user->setUserStatus(USER_STATUS::ACTIVATED);
        $user->setToken(NULL);
        return UserManager::init()->update($user)
            ?  TRUE : "Account activation failed";
    }
    static function generateToken($email){
        $users = UserManager::init()->getAll("WHERE email='$email'");
        if( empty($users) ){ return "Unknown e-mail account"; }
        
        $users[0]->setToken( Tools::generateRandomString() );
        return UserManager::init()->update($users[0]) ? TRUE : "DB error";
    }
    
    // -- WEBSITE --
    static function displayList(User $current){
        $users = UserManager::init()->getAll("WHERE id<>".$current->getId()
                ." AND userStatus<>'deleted'");
        foreach($users as $user){
            echo "<div class='align-mobile-left panel panel-default account'"
                    ."style='margin-bottom:2px' data-iduser='".$user->getId()."'"
                    ."data-usertype='".$user->getUserType()."'>
                <div class='panel-heading'> 
                    <h3 class='panel-title'>".Tools::capitalize($user->getPseudo())
                    .($user->isBanished() ? " (banished)" : NULL)."</h3>
                </div>
                <div class='panel-collapse collapse account-actions'>
                    <div class='panel-body'>
                        <div class='col-xs-12'>
                            <div class='col-xs-12 col-md-3'><label>Rights :</label></div>
                            ";
                            foreach(USER_TYPE::getConstants() as $type){
                                echo "<div class='col-xs-12 col-md-3'><input value='$type' "
                                    .($user->isBanished() ? "disabled " : NULL)
                                    ."type='radio' name='".$user->getPseudo()."' />"
                                    .Tools::capitalize($type)."</div>
                            ";
                            }
                        echo "</div>
                        <div style='margin-top:10px' class='col-xs-12'>
                            <div class='col-xs-12 col-md-3'>
                                <label>Actions :</label>
                            </div>
                            <div class='col-xs-12 col-sm-4 col-md-3 align-mobile-button-down'>
                                <button class='btn btn-success center-block' "
                                    .($user->isBanished() ? "style='display:none' " : NULL)
                                    .">Valid rights</button>
                            </div>
                            <div class='col-xs-12 col-sm-4 col-md-3 align-mobile-button-down'>
                                <button class='btn btn-danger center-block'>Delete</button>
                            </div>
                            <div class='col-xs-12 col-sm-4 col-md-3'>
                                <button class='btn btn-warning center-block align-mobile-button-down' "
                                    .($user->isBanished() ? "style='display:none' " : NULL)
                                    .">Banish</button>
                                <button class='btn btn-primary center-block' "
                                    .($user->isBanished() ? NULL : "style='display:none' ")
                                    .">Unbanish</button>
                            </div>       
                        </div>
                    </div>
                </div>
            </div>
    ";
        }
    }
    static function displayDeletionList(User $current){
        $users = UserManager::init()->getAll("WHERE id<>"
                .$current->getId()." AND userStatus='"
                .USER_STATUS::DELETING."'");
        foreach($users as $user){
            echo "<tr data-iduser='".$user->getId()."'><td class='col-xs-12 bold' "
                ."colspan='100%'><span><h5>".Tools::capitalize($user->getPseudo())
                ."</h5></span></td></tr>";
        }
    }

}

class AddonController {
    // -- API --
    static function getTheme($id=NULL){
        return is_null($id) ?
               ThemeManager::init()->getAll() :
               ThemeManager::init()->getByID($id);
    }
    static function getLevel($id=NULL, $levelType=NULL, $levelStatus=LEVEL_STATUS::ACCEPTED, $levelMode=LEVEL_MODE::STANDARD){
        if( !is_null($id) ){
            return LevelManager::init()->getByID($id);
        }
        
        $params = array();
        if( !is_null($levelType) ){
            array_push($params, "levelType='$levelType'");
        }
        array_push($params, "levelStatus='$levelStatus'", "levelMode='$levelMode'");
        return LevelManager::init()->get("SELECT timeMax, idTheme, name, description, creationDate, user.pseudo AS creator, level.id AS idLevel FROM `level` JOIN `user` ON idCreator = user.id WHERE ".implode(" AND ", $params));        
    }
    
    // -- WEBSITE --
    static function displayLastNews(){
        $criterias = "ORDER BY creationDate DESC LIMIT ".NB_NEWS_TO_DISPLAY;
        $themes  = ThemeManager::init()->getAll($criterias);
        $levels  = LevelManager::init()->getAll("WHERE levelStatus='"
                                .LEVEL_STATUS::ACCEPTED."' $criterias");
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
    static function displayThemes(){
        $themes = ThemeManager::init()->getAll();
        foreach($themes as $theme){
            echo "<tr><td class='image-column'><img class='logo-level' "
                ."src='".$theme->getImagePath()."'/></td>"
                ."<td style='vertical-align: middle'>"
                .Tools::capitalize($theme->getName())."</td></tr>";
        }
    }
    static function displayBasicLevels(){
        $basics  = self::getLevel(NULL, LEVEL_TYPE::BASIC);
        $images   = ThemeManager::init()->getImagePath();
        $creators = LevelManager::init()->getCreators();
        
        foreach($basics as $level){
            echo "<tr class='custom-level'>"
                    ."<td class='image-column'><img class='logo-level' "
                        ."src='".$images[ $level->getIdTheme() ]."'/></td>"
                    ."<td style='vertical-align: middle'>"
                        .Tools::capitalize($level->getName())
                        ." created by ".$creators[ $level->getId() ]
                        ."<br><span class='description'>"
                        .$level->getDescription()."</td></tr>";
        }
    }
    static function displayCustomLevels($source){
        $customs  = self::getLevel(NULL, LEVEL_TYPE::CUSTOM);
        $images   = ThemeManager::init()->getImagePath();
        $creators = LevelManager::init()->getCreators();
        
        foreach($customs as $level){
            if($source == "shop"){
                echo "<tr class='custom-level'>"
                        ."<td class='image-column'><img class='logo-level' "
                            ."src='".$images[ $level->getIdTheme() ]."'/></td>"
                        ."<td style='vertical-align: middle'>"
                            .Tools::capitalize($level->getName())
                            ." created by ".$creators[ $level->getId() ]
                            ."<br><span class='description'>"
                            .$level->getDescription()."</td></tr>";
            }
            if($source == "addon"){
                echo "<tr>"
                        ."<td style='line-height:3.5' class='col-xs-1 col-md-1'>"
                            ."<input type='checkbox' data-idlevel='"
                            .$level->getId()."'></input></td>"
                        ."<td style='line-height:3.5' class='col-xs-2 col-md-2'>"
                            ."<img class='logo-level' src='"
                            .$images[ $level->getIdTheme() ]."'/></td>"
                        ."<td style='line-height:3.5' class='col-xs-8 col-md-9'>"
                            .Tools::capitalize($level->getName())." created by "
                            .$creators[ $level->getId() ]."</td></tr>";
            }
        }
    }
    static function displayLevelsToModerate(){
        $tomoderates = self::getLevel(NULL, LEVEL_TYPE::CUSTOM, LEVEL_STATUS::TOMODERATE);
        $imagePaths  = ThemeManager::init()->getImagePath();
        $creators    = LevelManager::init()->getCreators();
        
        foreach($tomoderates as $level){
            echo "<tr data-idlevel='".$level->getId()."'>
                    <td style='vertical-align: middle;' class='col-xs-2 col-sm-1 image-column'>
                        <img class='logo-level' src='".$imagePaths[ $level->getIdTheme() ]."'/></td>
                    <td style='vertical-align: middle;' class='col-xs-6 col-sm-7'>"
                        .Tools::capitalize($level->getName())." by "
                        .Tools::capitalize($creators[ $level->getIdCreator() ])."</td>
                    <td style='vertical-align: middle;'>
                    <div class='col-xs-12 col-md-6' style='margin-bottom:5px;'>
                        <button class='btn btn-success'>Accept</button></div>
                    <div class='col-xs-12 col-md-6'>
                        <button class='btn btn-danger'>Refuse</button></div>
                </td></tr>";
        }
    }
}

class ScoreController {
    // -- API --
    static function getScores($idPlayer){
        return ;
    }
    static function getRanksByPlayer($idPlayer){
        return ;
    }
    
    // -- WEBSITE --
    static function displayHeaders(Level $level=NULL){
        if(is_null($level)){ ?><tr>
                <th class="th-winds col-xs-4 col-sm-3 col-md-2">Rank</th>
                <th class="th-winds col-xs-4 col-sm-3 col-md-2">Player</th>
                <th class="th-winds col-xs-4 col-sm-6 col-md-8">Points</th>
            </tr><?php
        }else{ ?><tr>
            <th style="vertical-align: middle" class="th-winds col-xs-2">Rank</th>
            <th style="vertical-align: middle" class="th-winds col-xs-2">Player</th>
            <th style="vertical-align: middle" class="th-winds col-xs-2">Points</th>
            <th style="vertical-align: middle" class="th-winds col-xs-2">Time</th>
            <th style="vertical-align: middle" class="th-winds col-xs-2">Number of clicks</th>
            <th style="vertical-align: middle" class="th-winds col-xs-2">Number of gathered items</th>
        </tr><?php }
    }
    static function displayRanking(Level $level=NULL){
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
    static function displayInfosScore(Level $level){
        $creator   = UserManager::init()->getByID($level->getIdCreator())->getPseudo();
        $imagePath = ThemeManager::init()->getImagePath($level->getIdTheme());
        
        echo "<h4><img class='logo-level' src='$imagePath'> Ranking of \""
            .Tools::capitalize($level->getName())."\" created by "
            .Tools::capitalize($creator)."</h4>";
    }
    static function displayScoredBasicLevels(){
        $basics = LevelManager::init()->getLevelsHavingScores(LEVEL_TYPE::BASIC);
        self::formateLevels($basics);
    }
    static function displayScoredCustomLevels(){
        $customs = LevelManager::init()->getLevelsHavingScores(LEVEL_TYPE::CUSTOM);
        self::formateLevels($customs);
    }
    static private function formateLevels(array $levels){
        if(empty($levels)){
            echo "<div>No level with scores</div>";
            return;
        }
        
        $imagePaths = ThemeManager::init()->getImagePath();
        foreach($levels as $level){
            echo "<tr class='level' data-idlevel='".$level['id']."'>"
                ."<td class='image-column'><img class='logo-level' src='"
                .$imagePaths[ $level['idTheme'] ]."'/></td>"
                ."<td style='vertical-align: middle'>".Tools::capitalize($level['name'])
                ."</td></tr>";
        }
    }
}

class ForumController {
    // -- WEBSITE --
    static function displayLastNews(){
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
    static private function displayNews($item, $authors){
        $news   = $item->formateAsNews();
        $author = $authors[ $item->getIdAuthor() ];
        $news->setAuthor($author);
        echo $news->getMessage();
    }
    static function displaySubjects(){
        $subjects = SubjectManager::init()->getAll();
        foreach($subjects as $subject){
            echo self::formateSubject($subject);
        }
    }
    static function formateSubject(Subject $subject){
        $last = SubjectManager::init()->getLastUpdate($subject);
        $date = (new DateTime($last['date']))->format("d-m-Y");
        return "<tr class='subject' data-idsubject='".$subject->getId()."'>"
              ."<td>".$subject->getTitle()."</td>"
              ."<td>".$subject->getSubjectStatus()."</td>"
              ."<td>$date by ".Tools::capitalize($last['author'])."</td>"
              ."</tr>";
    }
    static function displayInfosSubject(Subject $subject){
        if(is_null($subject)){ return; }
        $colorStatus = ($subject->getSubjectStatus() === SUBJECT_STATUS::ACTIVE)? 'green' : 'red';
        echo "<div class='col-xs-12'><h4>Title  : ".Tools::capitalize($subject->getTitle())
            ."</h4></div><div class='col-xs-12'><h5 id='status-subject' "
            ."style='font-weight: bold; color:".$colorStatus."'>Status : "
            .$subject->getSubjectStatus()."</h5></div>";
    }
    static function displayPosts(Subject $subject, $isSuperUser){
        $authors = UserManager::init()->getPseudos();
        $posts   = PostManager::init()->getAll("WHERE idSubject=".$subject->getId()." ORDER BY date ASC");
        array_unshift($posts, $subject);

        foreach($posts as $post){
            echo self::formatePost($post, $authors[$post->getIdAuthor()], $isSuperUser);
        }        
    }
    static function formatePost($post, $author, $isSuperUser){
        $date = (new DateTime($post->getDate()))->format("d-m-Y H:i:s");
        $echo = "<div class='row-post'><div class='col-xs-8 col-sm-9 col-md-10' "
              . "style='border-top: 2px solid #aaa; padding-top:10px;'>"
              . "$date by $author :<br>".$post->getMessage()."</div>";
        if($isSuperUser && !$post instanceof Subject){
            $echo .= "<div class='col-xs-4 col-sm-3 col-md-2'><button class='btn "
                    ."btn-danger' style='margin-top:15px' data-idpost='"
                    .$post->getId()."'>Delete</button></div>";
        }
        $echo .= "</div>";
        return $echo;
    }
}

class ApiController {
    // -- API --
    static function existsAction($action){
        $constants = API_ACTION::getConstants();
        return array_search($action, $constants);
    }
    static function displayResponse($data, $error=NULL){
        $response = array();
        if($data){  $response['data']  = $data;  }
        if($error){ $response['error'] = $error; }
        echo json_encode($response);
        die;
    }
    // Each function must have its name like the value in API_ACTION 
    // and declare following as arguments :
    //     - $user   : the user account
    //     - $params : the array of parameters sent in URL / optionnal
    static function getThemes(User $user, array $params=[]){
		if(isset($params['idTheme'])){
			$rawTheme = AddonController::getTheme($params['idTheme']);
			$theme = array();
			$theme[0]["id"] = $rawTheme->getId();
			$theme[0]["name"] = $rawTheme->getName();
			$theme[0]["description"] = $rawTheme->getDescription();
			$theme[0]["fileName"] = str_replace("resources/themes/", "", $rawTheme->getFilePath());
			echo json_encode($theme);
		}
		else{
			$themes = AddonController::getTheme();
			echo json_encode($themes);
		}
    }
    static function getLevelInfos(User $user, array $params=[]){
		$rawLevel = AddonController::getLevel($params['idLevel'], NULL);
		$level[0]["timeMax"] = $rawLevel->getTimeMax();
		$level[0]["levelType"] = $rawLevel->getLevelType();
		$level[0]["levelStatus"] = $rawLevel->getLevelStatus();
		$level[0]["levelMode"] = $rawLevel->getLevelMode();
		$level[0]["idTheme"] = $rawLevel->getIdTheme();
		$level[0]["name"] = $rawLevel->getName();
		$level[0]["description"] = $rawLevel->getDescription();
		$level[0]["creationDate"] = $rawLevel->getCreationDate();
		$level[0]["filePath"] = $rawLevel->getFilePath();
		$level[0]["creator"] = AccountController::getPseudoById($rawLevel->getIdCreator());
		$level[0]["id"] = $rawLevel->getId();
		echo json_encode($level);
	}
	static function getBasicLevels(User $user, array $params=[]){
		$basics = AddonController::getLevel(NULL, LEVEL_TYPE::BASIC);
		echo json_encode($basics);
    }
    static function getCustomLevels(User $user, array $params=[]){
        $customs = AddonController::getLevel(NULL, LEVEL_TYPE::CUSTOM);
        //self::displayResponse( $customs );
		echo json_encode($customs);
    }
    static function getLevelsToModerate(User $user, array $params=[]){
        $toModerates = AddonController::getLevel(NULL, LEVEL_TYPE::CUSTOM, LEVEL_STATUS::TOMODERATE);
        //self::displayResponse( $toModerates );
		echo json_encode($toModerates);
    }
    static function getScores(User $user, array $params=[]){
		$scores = ScoreManager::init()->getAllByPlayer($user->getId());
        //self::displayResponse( $scores );
		echo json_encode($scores);
    }
    static function getRanks(User $user, array $params=[]){
        $playerRanks = ScoreManager::init()->getRanksByPlayer($user->getId());
        self::displayResponse( $playerRanks );
    }
    static function downloadProfile(User $user, array $params=[]){
		echo json_encode($user);
    }
    static function downloadTheme(User $user, array $params=[]){
        if(!isset($params['idTheme'])){
            self::displayResponse(NULL, "Missing theme ID");
        }
        
        $theme = ThemeManager::init()->getByID($params['idTheme']);
		$name = $theme->getFilePath();
		// ouvre un fichier en mode binaire
		$fp = fopen($name, 'rb');

		// envoie les bons en-têtes
		header("Content-Type: application/java-archive");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . filesize($name));
		// envoie le contenu du fichier, puis stoppe le script
		fpassthru($fp);
		exit;
		
    }
    static function downloadBasicLevel(User $user, array $params=[]){
        if(!isset($params['idBasicLevel'])){
            self::displayResponse(NULL, "Missing basic level ID");
        }
		
        $basicLevel = LevelManager::init()->getByID($params['idBasicLevel']);
        $name = $basicLevel->getFilePath();
        // ouvre un fichier en mode binaire
        $fp = fopen($name, 'rb');
        // envoie les bons en-têtes
        header("Content-Type: application/java-archive");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($name));
        // envoie le contenu du fichier, puis stoppe le script
        fpassthru($fp);
        exit;
		
    }
    static function downloadCustomLevel(User $user, array $params=[]){
        if(!isset($params['idCustomLevel'])){
            self::displayResponse(NULL, "Missing custom level ID");
        }
        
        $customLevel = LevelManager::init()->getByID($params['idCustomLevel']);
        $name = $customLevel->getFilePath();
        // ouvre un fichier en mode binaire
        $fp = fopen($name, 'rb');
        // envoie les bons en-têtes
        header("Content-Type: application/java-archive");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($name));
        // envoie le contenu du fichier, puis stoppe le script
        fpassthru($fp);
        exit;
    }
    static function downloadLevelToModerate(User $user, array $params=[]){
        if(!isset($params['idLevelToModerate'])){
            self::displayResponse(NULL, "Missing level-to-moderate ID");
        }
        
        $levelToModerate = LevelManager::init()->getByID($params['idLevelToModerate']);
        $name = $levelToModerate->getFilePath();
        // ouvre un fichier en mode binaire
        $fp = fopen($name, 'rb');
        // envoie les bons en-têtes
        header("Content-Type: application/java-archive");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($name));
        // envoie le contenu du fichier, puis stoppe le script
        fpassthru($fp);
        exit;
    }
    static function uploadCustomLevel(User $user, array $params=[]){

        if(!isset($params['level'])){
            self::displayResponse(NULL, "Missing uploaded level file");
        }
        
        $manip = LevelManipulator::init($params['level'])->run();
        self::displayResponse($manip->getResult(), $manip->getError());
    }
    static function uploadScores(User $user, array $params=[]){
        if(!isset($params['scores'])){
            self::displayResponse(NULL, "Missing scores to upload");
        }
        $scoresSended = json_decode( urldecode($params['scores']), TRUE );
		//var_dump($scoresSended);
        $counter = 0;
		
        foreach($scoresSended as $value){
			
			$uploaded = Score::init($user->getId(), $value['idLevel'],
                    $value['time'], $value['nbClicks'], $value['nbItems']);

			$scoresDB = ScoreManager::init()->getScoreById($user->getId(),$value['idLevel']);
			//var_dump($scoresDB);
			
            if( empty($scoresDB) ){
                // no score found for this user and level
                ScoreManager::init()->insert($uploaded);
            }
            else {
                $current = $scoresDB;
                $needUpdate = $uploaded->compareTo($current) == TRUE;
                $needUpdate ? $current->updateFrom($uploaded) : NULL;
                //var_dump($needUpdate);
				//die;
                $retour = ScoreManager::init()->update($current);
				//echo 'valeur de retour : '.$retour;
            }
            $counter++;
        }
		echo $counter;
        exit;
        $all = $counter == count($scoresSended);
        $message = "All of uploaded scores have ".($all ? NULL : "not ")."been inserted into DB";
        $all ? self::displayResponse($message): self::displayResponse(NULL, $message);
    }
}
