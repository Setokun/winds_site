<?php
/**
 * Description of managers file
 * @author Damien.D & Stephane.G
 */

require_once "config.php";
require_once "tools.php";

/**
 * Interface used by final managers to initialize them.
 */
interface ManagerInit {
    static function init();
}

/**
 * Abstract class which contains some common variables and methods between the final managers.
 */
/*OK*/abstract class ManagerDB implements ManagerInit {
    protected $PDO;			  
    
    // VARIABLES - MUST BE OVERRIDEN IN CONSTRUCTOR OF THE DERIVED CLASS
    protected $nameTable,           // the name of the table in DB and of the class to use to instanciate objects 
              $columns;             // must be in same order like in DB
    static  $host   = "windsgamqiwinds.mysql.db",
            $nameDB = "windsgamqiwinds",
            $user   = "windsgamqiwinds",
            $pwd    = "Wind2084";
    
    /**
     * Refuses the manager's initialization by the default way.
     */
    protected function __construct(){}
    /**
     * Connects the manager to the MySQL sever.
     */
    protected function connectDB() {
        try {
            $this->PDO = new PDO("mysql:host=".self::$host.";dbname=".self::$nameDB, self::$user, self::$pwd);
            $this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (Exception $e) {}
    }
    /**
     * Checks if the database is available.
     * @return boolean
     */
    static public function availableDB(){
        try {
            new PDO("mysql:host=".self::$host.";dbname=".self::$nameDB, self::$user, self::$pwd);
            return TRUE;
        }
        catch (Exception $e){ return FALSE; }
    }
    
    /**
     * Get all entries from the managed table which match the specified conditions.
     * $clauses can be null.
     * @param string $clauses The string SQL conditions which filters the entries
     * @return array
     */
    public function getAll($clauses=NULL){
		return $this->parent_select("SELECT * FROM $this->nameTable $clauses")
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->nameTable);
    }
    /**
     * Get the entry from the managed table which match the specified ID.
     * @param int $id The ID of the searched entry
     * @return Object
     */
    public function getByID($id){
        $query  = "SELECT * FROM $this->nameTable WHERE id=$id";
        $result = $this->parent_select($query)
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->nameTable);
        return empty($result) ? NULL : $result[0];
    }
    /**
     * Get all entries from the managed table which match the specified query.
     * @param string $query The SQL query
     * @return array
     */
    public function get($query){
        return $this->parent_select($query)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Prepares a SQL insertion query from the managed table.
     * @return string
     */
    protected function query_insert(){
        $columns = $this->columns;
        array_shift($columns);    // delete the 'id' column
        $valuesPlaces = implode(',',  array_fill(0, count($columns), '?'));

        return "INSERT INTO $this->nameTable (".
                implode(',', $columns).
                ") VALUES ($valuesPlaces)";
    }
    /**
     * Prepares a SQL update query with the specified item.
     * @param WindsClass $item The item to update
     * @return string
     */
    protected function query_update(WindsClass $item){
        $fields = array_keys($item->valuesDB_toUpdate());
        $update = "UPDATE $this->nameTable SET ";
        $nbFields = count($fields);
        for($i=0; $i<count($fields); $i++){
            $update .= $fields[$i].'=?';
            $update .= (--$nbFields) ? ' , ' : NULL;
        }
        return $update." WHERE id=".$item->getId();
    }
    
    /**
     * Returns the result of the executed select query.
     * @param string $query The SQL select query to execute
     * @return array
     */
    protected function parent_select($query){
        $request = $this->PDO->prepare($query);
        $request->execute();
        return $request;
    }
    /**
     * Returns the ID of the specified item which has been inserted into DB.
     * @param WindsClass $item The item to insert
     * @return int
     */
    protected function parent_insert(WindsClass $item){
        $this->PDO->prepare($this->query_insert())
                  ->execute($item->valuesDB_toInsert());
        return $this->PDO->lastInsertId();
    }
    /**
     * Returns the number of modified entries after the update query execution for the specified item.
     * @param WindsClass $item The item to update
     * @return int
     */
    protected function parent_update(WindsClass $item){
		$values  = array_values($item->valuesDB_toUpdate());
        $query   = $this->query_update($item);
        $request = $this->PDO->prepare($query);
        $request->execute($values);
        return $request->rowCount();
                         
    }
    /**
     * Returns the number of deleted entries after the delete query execution for the specified item.
     * @param WindsClass $item The item to delete
     * @return int
     */
    protected function parent_delete(WindsClass $item){
        $query   = "DELETE FROM $this->nameTable WHERE id=".$item->getId();
        $request = $this->PDO->prepare($query);
        $request->execute();
        return $request->rowCount();                    
    }
    /**
     * Returns the request object after the specified query has been executed.
     * @param string $query The SQL query to execute
     * @return PDOStatement
     */
    protected function parent_execute($query){
        $request = $this->PDO->prepare($query);
        $request->execute();
        return $request;
    }

}

/**
 * Class used to manage User objects into DB.
 */
/*OK*/class UserManager extends ManagerDB {
    
    /**
     * Initializes a new manager of users.
     */
    static public function init(){
        $mgr = new self();
        $mgr->nameTable = "user";
        $mgr->columns   = User::$columns;
        $mgr->connectDB();
        return $mgr;
    }
    /**
     * Returns the ID of the specified user insertion into DB.
     * @param User $user The user to insert
     * @return int
     */
    public function insert(User $user){
        return $this->parent_insert($user);
        
    }
    /**
     * Returns the success statement of the specified user update onto DB.
     * @param User $user The user to update
     * @return int
     */
    public function update(User $user){
        if(is_null($user->getId())){  return FALSE;  }
        return $this->parent_update($user);
    }
    /**
     * Returns the success statement of the specified user deletion from DB.
     * @param User $user The user to delete
     * @return int
     */
    public function delete(User $user){
        if(is_null($user->getId())){  return FALSE;  }
        return $this->parent_delete($user);
    }
    /**
     * Get an array of IDs and pseudos of all users into DB.
     * @return array
     */
    public function getPseudos(){
        $values = $this->get("SELECT id, pseudo FROM user");
        $data = array();
        foreach($values as $value){
            $data[ $value['id'] ] = $value['pseudo'];
        }
        return $data;
    }

}

/**
 * Class used to manage User objects into DB.
 */
/*OK*/class ThemeManager extends ManagerDB {
    
    /**
     * Initializes a new manager of themes.
     */
    static public function init(){
        $mgr = new self();
        $mgr->nameTable = "theme";
        $mgr->columns   = Theme::$columns;
        $mgr->connectDB();
        return $mgr;
    }
    /**
     * Returns the ID of the specified theme insertion into DB.
     * @param Theme $theme The theme to insert
     * @return int
     */
    public function insert(Theme $theme){
        return $this->parent_insert($theme);
    }
    /**
     * Returns the success statement of the specified theme deletion from DB.
     * @param Theme $theme The theme to delete
     * @return int
     */
    public function delete(Theme $theme){
        if(is_null($theme->getId())){  return FALSE;  }
        return $this->parent_delete($theme);
    }
    /**
     * Get the image path of all themes in DB or for the theme which matches the specified ID.
     * @param int $idTheme The theme ID which the image path must be found
     * @return mixed : array or string
     */
    function getImagePath($idTheme=NULL){
        $query = "SELECT theme.id, imagePath FROM theme "
                .(is_null($idTheme) ? NULL :" WHERE id=$idTheme");
        $values = $this->get($query);
        if( is_null($idTheme) ){
            $data = array();
            foreach($values as $value){
                $data[ $value['id'] ] = "../resources/".$value['imagePath'];
            }
            return $data;
        }else{
            return "../resources/".$values[0]['imagePath'];
        }
        
    }
    
}

/**
 * Class used to manage Level objects into DB.
 */
/*OK*/class LevelManager extends ManagerDB {
    
    /**
     * Initializes a new manager of levels.
     */
    static public function init(){
        $mgr = new self();
        $mgr->nameTable = "level";
        $mgr->columns   = Level::$columns;
        $mgr->connectDB();
        return $mgr;
    }
    /**
     * Returns the ID of the specified level insertion into DB.
     * @param Level $level The level to insert
     * @return int
     */
    public function insert(Level $level){
        return $this->parent_insert($level);
    }
    /**
     * Returns the success statement of the specified level update onto DB.
     * @param Level $level The level to update
     * @return int
     */
    public function update(Level $level){
        if(is_null($level->getId())){  return FALSE;  }
        return $this->parent_update($level);
    }
    /**
     * Returns the success statement of the specified level deletion from DB.
     * @param Level $level The level to delete
     * @return int
     */
    public function delete(Level $level){
        if(is_null($level->getId())){  return FALSE;  }
        return $this->parent_delete($level);
    }
    
    /**
     * Get an array of levels having scores into DB.
     * The search can be filtered by the type of levels to found.
     * @param string $levelType a constant of LEVEL_TYPE
     * @return array
     */
    public function getLevelsHavingScores($levelType=NULL){
        $query = "SELECT DISTINCT level.* FROM level JOIN score "
                ."WHERE score.idLevel=level.id "
                ."AND levelStatus='".LEVEL_STATUS::ACCEPTED."' "
                ."AND levelMode='".LEVEL_MODE::STANDARD."'";
        $query .= is_null($levelType) ? NULL : " AND levelType='$levelType'";
        return $this->get($query);
    }
    /**
     * Get an array of IDs and maximum times of all levels into DB.
     * @return array
     */
    public function getLevelsTimeMax(){
        $dataDB = $this->get("SELECT id, timeMax FROM level ORDER BY id");
        $times  = array();
        foreach($dataDB as $data){
            $times[$data['id']] = $data['timeMax'];
        }        
        return $times;
    }
    /**
     * Get an array of IDs and pseudos of all users into DB which had made some levels.
     * @return array
     */
    public function getCreators(){
        $query  = "SELECT level.id AS idLevel, pseudo FROM level "
                 ."JOIN user WHERE level.idCreator=user.id ";
        $values = $this->get($query);
        
        $data = array();
        foreach($values as $value){
            $data[$value['idLevel']] = $value['pseudo'];
        }
        return $data;
    }
    
    /**
     * Returns the success statement of the specified level deletions from DB.
     * @param array $levelIds The array of level IDs to delete
     * @return boolean
     */
    public function deleteMulti(array $levelIds){
        $query = "DELETE FROM level WHERE id IN (".implode(',', $levelIds).")";
        $nbDel = $this->parent_execute($query)->rowCount();
        return $nbDel === count($levelIds);
    }
    
}

/**
 * Class used to manage Score objects into DB.
 */
/*OK*/class ScoreManager extends ManagerDB {
    
    /**
     * Initializes a new manager of scores.
     */
    static public function init() {
        $score = new self();
        $score->nameTable = "score";
        $score->columns   = Score::$columns;
        $score->connectDB();
        return $score;
    }
    /**
     * Returns the ID of the specified score insertion into DB.
     * @param Score $score The score to insert
     * @return int
     */
    public function insert(Score $score){

        $query = "INSERT INTO score (time, nbClicks, nbItems, idPlayer, idLevel) VALUES (?,?,?,?,?)";
		/*$query = "INSERT INTO score (time, nbClicks, nbItems, idPlayer, idLevel) VALUES (:time,:nbclicks,:nbitems,:idplayer,:idlevel)";
		$request = $this->PDO->prepare($query);
		$request->bindValue('time', $score->getTime(), PDO::PARAM_INT);
		$request->bindValue('nbclicks', $score->getNbClicks(), PDO::PARAM_INT);
		$request->bindValue('nbitems', $score->getNbItems(), PDO::PARAM_INT);
		$request->bindValue('idplayer', $score->getIdPlayer(), PDO::PARAM_STR);
		$request->bindValue('idlevel', $score->getIdLevel(), PDO::PARAM_INT);

		$request->execute();*/
		$values = array($score->getTime(), $score->getNbClicks(), $score->getNbItems(), $score->getIdPlayer(), $score->getIdLevel());
		$request = $this->PDO->prepare($query);
		
		$request->execute($values);
		return $request->rowCount();
    }
    /**
     * Returns the success statement of the specified score update onto DB.
     * @param Score $score The score to update
     * @return int
     */
    public function update(Score $score){

		$query = "UPDATE $this->nameTable SET time=?, nbClicks=?, nbItems=? where idPlayer=? AND idLevel=?";
		$values = array($score->getTime(), $score->getNbClicks(), $score->getNbItems(), $score->getIdPlayer(), $score->getIdLevel());
		$request = $this->PDO->prepare($query);
		
		$request->execute($values);
		return $request->rowCount();
    }
    /**
     * Returns the success statement of the specified score deletion from DB.
     * @param Score $score The score to delete
     * @return int
     */
    public function delete(Score $score){
        if(is_null($score->getIdPlayer()) || is_null($score->getIdLevel())){  return FALSE;  }
        $query = "DELETE FROM $this->nameTable where idPlayer=? AND idLevel=?";
		$values = array($score->getIdPlayer(), $score->getIdLevel());
		$request->execute($values);
		return $request->rowCount();
    }
    /**
     * Returns the success statement of the specified score deletions from DB.
     * @param array $scoreIds The array of score IDs to delete
     * @return boolean
     */
    public function deleteMulti(array $scoreIds){
        $query = "DELETE FROM score WHERE id IN (".implode(',', $scoreIds).")";
        $nbDel = $this->parent_execute($query)->rowCount();
        return $nbDel === count($scoreIds);
    }
    
    /**
     * Get the score which matches with the specified user and level IDs.
     * @param int $idPlayer The user ID of the score which must be found
     * @param int $idLevel The level ID of the score which must be found
     * @return Score
     */
    public function getScoreById($idPlayer, $idLevel){
		$query = "SELECT * from score WHERE idPLayer=$idPlayer AND idLevel=$idLevel";
		$dataDB = $this->get($query)[0];
		if(!$dataDB) return null;
		return Score::init((int)$dataDB['idPlayer'],(int)$dataDB['idLevel'],(int)$dataDB['time'],(int)$dataDB['nbClicks'],(int)$dataDB['nbItems']);
	}

    /**
     * Get all scores which matches with the specified user ID.
     * @param int $idPlayer The user ID of the score which must be found
     * @return Score
     */
    public function getAllByPlayer($idPlayer){
		return $this->get("SELECT idLevel, nbClicks, nbItems, time, name "
                        . "AS levelName FROM `score` JOIN `level` ON idLevel = level.id WHERE idPlayer = $idPlayer ORDER BY idLevel");
    }
    /**
     * Get all level rankings which matches with the specified user ID.
     * @param int $idPlayer The user ID of the ranks which must be found
     * @return array
     */
    public function getRanksByPlayer($idPlayer){
        $dataDB = $this->get("SELECT DISTINCT idLevel FROM $this->nameTable");
        $idsLevel = array_map(function($value){ return $value['idLevel']; },$dataDB);

        $playerRanks = array();
        foreach($idsLevel as $idLevel){
            $ranking = $this->getRanking($idLevel);
            $nbRanks = count($ranking);
            
            for($i=0; $i<$nbRanks; $i++){
                if($ranking[$i]['idPlayer'] == $idPlayer){
                    array_push($playerRanks, array(
                        'idLevel'   => $idLevel,
                        'points'    => $ranking[$i]['points'],
                        'rank'      => $i +1,
                        'nbPlayers' => $nbRanks
                    ));
                }
            }
        }
        return $playerRanks;
    }
    /**
     * Get the players ranking of all levels.
     * The search can be filtered by the ID of a level.
     * @param int $idLevel The level ID of the ranks which must be found
     * @return array
     */
    public function getRanking($idLevel=NULL){
        $query = "SELECT pseudo, score.* FROM score JOIN user, level "
                ."WHERE score.idPlayer=user.id AND score.idLevel=level.id "
                ."AND levelStatus='".LEVEL_STATUS::ACCEPTED."' "
                ."AND levelMode='".LEVEL_MODE::STANDARD."' "
                .(is_null($idLevel) ? NULL : "AND idLevel=$idLevel ");
        $dataDB = $this->get($query);
        return $this->formateRanking($dataDB, is_null($idLevel));
    }
    /**
     * Formates the ranking for the specified data.
     * @param array $dataDB The values given by "getRanking" method.
     * @param boolean $nullIdLevel The existance of the level ID
     * @return array
     */
    private function formateRanking($dataDB, $nullIdLevel){
        $times = LevelManager::init()->getLevelsTimeMax();
        $ranks = array();
        foreach($dataDB as $data){
            $idPlayer = $data['idPlayer'];
            $score    = Score::init($idPlayer, $data['idLevel'], $data['time'],
                                    $data['nbClicks'],$data['nbItems'] );
            $points   = $score->calculate($times[ $data['idLevel'] ]);
            $exPoints = isset($ranks[$idPlayer]) ? $ranks[$idPlayer]['points'] : 0;
            $ranks[$idPlayer] = array("idPlayer" => $idPlayer,
                                      "player"   => $data['pseudo'],
                                      "points"   => $exPoints + $points);
            if(!$nullIdLevel){ $ranks[$idPlayer]["score"] = $score; }
        }
        
        $sorted = array_values($ranks);
        usort($sorted, function($a,$b){
            return ($a['points'] == $b['points']) ? 0 :
                   ($a['points'] < $b['points']) ? 1 : -1;
        });
        return $sorted;
    }
    
}

/**
 * Class used to manage forum Subject objects into DB.
 */
/*OK*/class SubjectManager extends ManagerDB {
    
    /**
     * Initializes a new manager of subjects.
     */
    static public function init() {
        $mgr = new self();
        $mgr->nameTable = "subject";
        $mgr->columns   = Subject::$columns;
        $mgr->connectDB();
        return $mgr;
    }
    /**
     * Returns the ID of the specified subject insertion into DB.
     * @param Subject $subject The subject to insert
     * @return int
     */
    public function insert(Subject $subject){
        return $this->parent_insert($subject);
    }
    /**
     * Returns the success statement of the specified subject update onto DB.
     * @param Subject $subject The subject to update
     * @return int
     */
    public function update(Subject $subject){
        if(is_null($subject->getId())){  return FALSE;  }
        return $this->parent_update($subject);
    }
    /**
     * Returns the success statement of the specified subject deletion from DB.
     * @param Subject $subject The subject to delete
     * @return int
     */
    public function delete(Subject $subject){
        if(is_null($subject->getId())){  return FALSE;  }
        return $this->parent_delete($subject);
    }
    
    /**
     * Get an array of last posts into DB for the specified subject.
     * @param Subject $subject The subjects which the last posts must be found
     * @return array
     */
    public function getLastUpdate(Subject $subject){
        $posts   = PostManager::init()->getAll("WHERE idSubject=".$subject->getId()." ORDER BY date DESC");
        $authors = UserManager::init()->getPseudos();
        return empty($posts) ?
               array("date"=> $subject->getDate() ,"author"=> $authors[ $subject->getIdAuthor() ]) :
               array("date"=> $posts[0]->getDate(),"author"=> $authors[ $posts[0]->getIdAuthor() ]);
    }
    
}

/**
 * Class used to manage forum Post objects into DB.
 */
/*OK*/class PostManager extends ManagerDB {
    
    /**
     * Initializes a new manager of posts.
     */
    static public function init() {
        $mgr = new self();
        $mgr->nameTable = "post";
        $mgr->columns = Post::$columns;
        $mgr->connectDB();
        return $mgr;
    }
    /**
     * Returns the ID of the specified post insertion into DB.
     * @param Post $post The post to insert
     * @return int
     */
    public function insert(Post $post){
        return $this->parent_insert($post);
    }
    /**
     * Returns the success statement of the specified post deletion from DB.
     * @param Post $post The post to delete
     * @return int
     */
    public function delete(Post $post){
         if(is_null($post->getId())){  return FALSE;  }
        return $this->parent_delete($post);
    }
    /**
     * Returns the success statement of the specified post deletions from DB.
     * @param array $postIds The array of post IDs to delete
     * @return boolean
     */
    public function deleteMulti(array $postIds){
        $query = "DELETE FROM post WHERE id IN (".implode(',', $postIds).")";
        $nbDel = $this->parent_execute($query)->rowCount();
        return $nbDel === count($postIds);
    }
    
}
