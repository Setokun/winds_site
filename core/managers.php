<?php
/**
 * Description of managers
 * @author Damien.D & Stephane.G
 */
require_once "config.php";
require_once "tools.php";

interface ManagerInit {
    static function init();
}

/*OK*/abstract class ManagerDB implements ManagerInit {
    protected $PDO;			  
    
    // VARIABLES - MUST BE OVERRIDEN IN CONSTRUCTOR OF THE DERIVED CLASS
    protected $nameTable,           // the name of the table in DB and of the class to use to instanciate objects 
              $columns;             // must be in same order like in DB
    
    /*OK*/protected function __construct(){}
    /*OK*/protected function connectDB() {
		$host	= "windsgamqiwinds.mysql.db";
        $nameDB = "windsgamqiwinds";
		$user	= "windsgamqiwinds";
		$pwd	= "Wind2084";
	
        try {
            $this->PDO = new PDO("mysql:host=$host;dbname=$nameDB", $user, $pwd);
            $this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
            exit;
        }
    }
    
    /*OK*/public function getAll($clauses=NULL){
		return $this->parent_select("SELECT * FROM $this->nameTable $clauses")
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->nameTable);
    }
    /*OK*/public function getByID($id){
        $query  = "SELECT * FROM $this->nameTable WHERE id=$id";
        $result = $this->parent_select($query)
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->nameTable);
        return empty($result) ? NULL : $result[0];
    }
    /*OK*/public function get($query){
        return $this->parent_select($query)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /*OK*/protected function query_insert(){
        $columns = $this->columns;
        array_shift($columns);    // delete the 'id' column
        $valuesPlaces = implode(',',  array_fill(0, count($columns), '?'));

        return "INSERT INTO $this->nameTable (".
                implode(',', $columns).
                ") VALUES ($valuesPlaces)";
    }
    /*OK*/protected function query_update(WindsClass $item){
        $fields = array_keys($item->valuesDB_toUpdate());
        $update = "UPDATE $this->nameTable SET ";
        $nbFields = count($fields);
        for($i=0; $i<count($fields); $i++){
            $update .= $fields[$i].'=?';
            $update .= (--$nbFields) ? ' , ' : NULL;
        }
        return $update." WHERE id=".$item->getId();
    }
    
    /*OK*/protected function parent_select($query){
        $request = $this->PDO->prepare($query);
        $request->execute();
        return $request;
    }
    /*OK*/protected function parent_insert(WindsClass $item){
        $this->PDO->prepare($this->query_insert())
                  ->execute($item->valuesDB_toInsert());
        return $this->PDO->lastInsertId();
    }
    /*OK*/protected function parent_update(WindsClass $item){
		$values  = array_values($item->valuesDB_toUpdate());
        $query   = $this->query_update($item);
        $request = $this->PDO->prepare($query);
        $request->execute($values);
        return $request->rowCount();
                         
    }
    /*OK*/protected function parent_delete(WindsClass $item){
        $query   = "DELETE FROM $this->nameTable WHERE id=".$item->getId();
        $request = $this->PDO->prepare($query);
        $request->execute();
        return $request->rowCount();                    
    }
    /*OK*/protected function parent_execute($query){
        $request = $this->PDO->prepare($query);
        $request->execute();
        return $request;
    }
}
/*OK*/class UserManager extends ManagerDB {
    /*OK*/static public function init(){
        $mgr = new self();
        $mgr->nameTable = "user";
        $mgr->columns   = User::$columns;
        $mgr->connectDB();
        return $mgr;
    }
    /*OK*/public function insert(User $user){
        return $this->parent_insert($user);
        
    }
    /*OK*/public function update(User $user){
        if(is_null($user->getId())){  return FALSE;  }
        return $this->parent_update($user);
    }
    /*OK*/public function delete(User $user){
        if(is_null($user->getId())){  return FALSE;  }
        return $this->parent_delete($user);
    }
    
    /*OK*/public function getPseudos(){
        $values = $this->get("SELECT id, pseudo FROM user");
        $data = array();
        foreach($values as $value){
            $data[ $value['id'] ] = $value['pseudo'];
        }
        return $data;
    }
}
/*OK*/class ThemeManager extends ManagerDB {
    /*OK*/static public function init(){
        $mgr = new self();
        $mgr->nameTable = "theme";
        $mgr->columns   = Theme::$columns;
        $mgr->connectDB();
        return $mgr;
    }
    /*OK*/public function insert(Theme $theme){
        return $this->parent_insert($theme);
    }
    /*OK*/public function delete(Theme $theme){
        if(is_null($theme->getId())){  return FALSE;  }
        return $this->parent_delete($theme);
    }
    
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
/*OK*/class LevelManager extends ManagerDB {
    /*OK*/static public function init(){
        $mgr = new self();
        $mgr->nameTable = "level";
        $mgr->columns   = Level::$columns;
        $mgr->connectDB();
        return $mgr;
    }
    /*OK*/public function insert(Level $level){
        return $this->parent_insert($level);
    }
    /*OK*/public function update(Level $level){
        if(is_null($level->getId())){  return FALSE;  }
        return $this->parent_update($level);
    }
    /*OK*/public function delete(Level $level){
        if(is_null($level->getId())){  return FALSE;  }
        return $this->parent_delete($level);
    }
    
    /*OK*/public function getLevelsHavingScores($levelType=NULL){
        $query = "SELECT DISTINCT level.* FROM level JOIN score "
                ."WHERE score.idLevel=level.id "
                ."AND levelStatus='".LEVEL_STATUS::ACCEPTED."' "
                ."AND levelMode='".LEVEL_MODE::STANDARD."'";
        $query .= is_null($levelType) ? NULL : " AND levelType='$levelType'";
        return $this->get($query);
    }
    /*OK*/public function getLevelsTimeMax(){
        $dataDB = $this->get("SELECT id, timeMax FROM level ORDER BY id");
        $times  = array();
        foreach($dataDB as $data){
            $times[$data['id']] = $data['timeMax'];
        }        
        return $times;
    }
    /*OK*/public function getCreators(){
        $query  = "SELECT level.id AS idLevel, pseudo FROM level "
                 ."JOIN user WHERE level.idCreator=user.id ";
        $values = $this->get($query);
        
        $data = array();
        foreach($values as $value){
            $data[$value['idLevel']] = $value['pseudo'];
        }
        return $data;
    }
    public function deleteMulti(array $levelIds){
        $query = "DELETE FROM level WHERE id IN (".implode(',', $levelIds).")";
        $nbDel = $this->parent_execute($query)->rowCount();
        return $nbDel === count($levelIds);
    }
}
/*OK*/class ScoreManager extends ManagerDB {
    /*OK*/static public function init() {
        $score = new self();
        $score->nameTable = "score";
        $score->columns   = Score::$columns;
        $score->connectDB();
        return $score;
    }
    /*OK*/public function insert(Score $score){

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
    /*OK*/public function update(Score $score){

		$query = "UPDATE $this->nameTable SET time=?, nbClicks=?, nbItems=? where idPlayer=? AND idLevel=?";
		$values = array($score->getTime(), $score->getNbClicks(), $score->getNbItems(), $score->getIdPlayer(), $score->getIdLevel());
		$request = $this->PDO->prepare($query);
		
		$request->execute($values);
		return $request->rowCount();
    }
    /*OK*/public function delete(Score $score){
        if(is_null($score->getIdPlayer()) || is_null($score->getIdLevel())){  return FALSE;  }
        $query = "DELETE FROM $this->nameTable where idPlayer=? AND idLevel=?";
		$values = array($score->getIdPlayer(), $score->getIdLevel());
		$request->execute($values);
		return $request->rowCount();
    }
    /*KO*/public function deleteMulti(array $scoreIds){
        $query = "DELETE FROM score WHERE id IN (".implode(',', $scoreIds).")";
        $nbDel = $this->parent_execute($query)->rowCount();
        return $nbDel === count($scoreIds);
    }
	
	public function getScoreById($idPlayer, $idLevel){
		$query = "SELECT * from score WHERE idPLayer=$idPlayer AND idLevel=$idLevel";
		$dataDB = $this->get($query)[0];
		if(!$dataDB) return null;
		return Score::init((int)$dataDB['idPlayer'],(int)$dataDB['idLevel'],(int)$dataDB['time'],(int)$dataDB['nbClicks'],(int)$dataDB['nbItems']);
	}
	
    /*OK*/public function getAllByPlayer($idPlayer){
		return $this->get("SELECT idLevel, nbClicks, nbItems, time, name AS levelName FROM `score` JOIN `level` ON idLevel = level.id WHERE idPlayer = 1");
		//var_dump($dataDB);
		//return $this->getAll(" WHERE idPlayer=$idPlayer");
    }
    /*OK*/public function getRanksByPlayer($idPlayer){
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
    /*OK*/public function getRanking($idLevel=NULL){
        $query = "SELECT pseudo, score.* FROM score JOIN user, level "
                ."WHERE score.idPlayer=user.id AND score.idLevel=level.id "
                ."AND levelStatus='".LEVEL_STATUS::ACCEPTED."' "
                ."AND levelMode='".LEVEL_MODE::STANDARD."' "
                .(is_null($idLevel) ? NULL : "AND idLevel=$idLevel ");
        $dataDB = $this->get($query);
        return $this->formateRanking($dataDB, is_null($idLevel));
    }
    /*OK*/private function formateRanking($dataDB, $nullIdLevel){
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
/*OK*/class SubjectManager extends ManagerDB {
    /*OK*/static public function init() {
        $mgr = new self();
        $mgr->nameTable = "subject";
        $mgr->columns   = Subject::$columns;
        $mgr->connectDB();
        return $mgr;
    }
    /*OK*/public function insert(Subject $subject){
        return $this->parent_insert($subject);
    }
    /*OK*/public function update(Subject $subject){
        if(is_null($subject->getId())){  return FALSE;  }
        return $this->parent_update($subject);
    }
    /*OK*/public function delete(Subject $subject){
        if(is_null($subject->getId())){  return FALSE;  }
        return $this->parent_delete($subject);
    }
    
    /*OK*/public function getLastUpdate(Subject $subject){
        $posts   = PostManager::init()->getAll("WHERE idSubject=".$subject->getId()." ORDER BY date DESC");
        $authors = UserManager::init()->getPseudos();
        return empty($posts) ?
               array("date"=> $subject->getDate() ,"author"=> $authors[ $subject->getIdAuthor() ]) :
               array("date"=> $posts[0]->getDate(),"author"=> $authors[ $posts[0]->getIdAuthor() ]);
    }
}
/*OK*/class PostManager extends ManagerDB {
    /*OK*/static public function init() {
        $mgr = new self();
        $mgr->nameTable = "post";
        $mgr->columns = Post::$columns;
        $mgr->connectDB();
        return $mgr;
    }
    /*OK*/public function insert(Post $post){
        return $this->parent_insert($post);
    }
    /*OK*/public function delete(Post $post){
         if(is_null($post->getId())){  return FALSE;  }
        return $this->parent_delete($post);
    }
    /*OK*/public function deleteMulti(array $postIds){
        $query = "DELETE FROM post WHERE id IN (".implode(',', $postIds).")";
        $nbDel = $this->parent_execute($query)->rowCount();
        return $nbDel === count($postIds);
    }
}
