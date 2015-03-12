<?php
/**
 * Description of managers
 * @author Damien.D & Stephane.G
 */
require_once "config.php";

/*OK*/abstract class ManagerDB {
    protected $linkDB,
              $nameDB = "winds";
    
    // VARIABLES - MUST BE OVERRIDEN IN CONSTRUCTOR OF THE DERIVED CLASS
    protected $nameTable,
              $columns;             // must be in same order like in DB
    
    // static public function init();       // to define in derived classes
    
    protected function __construct(){}
    protected function connectDB() {
        try {
            $this->linkDB = new PDO('mysql:host=localhost;dbname='.$this->nameDB, 'root', ''); // local
            $this->linkDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        }
        catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
            exit;
        }
    }
    protected function filterClauses(array $clauses=NULL){
        $filteredClauses = array();
        if( !is_null($clauses) ){
            $columnMatches = array_intersect(array_keys($clauses), $this->columns);
            foreach($columnMatches as $column){
                $filteredClauses[$column] = $clauses[$column];
            }
        }
        return $filteredClauses;
    }

    final protected function query_select(array $whereClauses){
        $select = "SELECT * FROM $this->nameTable";
        if( !empty($whereClauses) ){
            $clauseFields = array_keys($whereClauses);
            $select .= " WHERE ";
            $nbClauses = count($clauseFields);
            for($i=0; $i<count($clauseFields); $i++){
                $select .= $clauseFields[$i].'=?';
                $select .= (--$nbClauses) ? ' AND ' : NULL;
            }
        }
        return $select;
    }
    final protected function query_insert(){
        $columns = $this->columns;
        array_shift($columns);    // delete the 'id' column
        $valuesPlaces = implode(',',  array_fill(0, count($columns), '?'));

        return "INSERT INTO $this->nameTable (".
                implode(',', $columns).
                ") VALUES ($valuesPlaces)";
    }
    final protected function query_update(WindsClass $item){
        $id = $item->getId();
        $fields = array_keys($item->valuesDB_toUpdate());
        
        $update = "UPDATE $this->nameTable SET ";
        $nbFields = count($fields);
        for($i=0; $i<count($fields); $i++){
            $update .= $fields[$i].'=?';
            $update .= (--$nbFields) ? ' , ' : NULL;
        }
        
        return $update." WHERE id=$id";
    }
    final protected function query_delete(WindsClass $item){
        return "DELETE FROM $this->nameTable WHERE id=".$item->getId();
    }
    
    final protected function parent_select(array $whereClauses=NULL, $queryEnd=NULL){
        $clauses = $this->filterClauses($whereClauses);
        $values  = array_values($clauses);
        $query   = $this->query_select($clauses)." ".$queryEnd;
        
        $request = $this->linkDB->prepare($query);
        empty($values) ? $request->execute() : $request->execute($values);
        return $request;
    }
    final protected function parent_insert(WindsClass $item){
        return $this->linkDB
                ->prepare($this->query_insert())
                ->execute($item->valuesDB_toInsert());
    }
    final protected function parent_update(WindsClass $item){
        $values = array_values($item->valuesDB_toUpdate());
        return $this->linkDB
                    ->prepare($this->query_update($item))
                    ->execute($values);
    }
    final protected function parent_delete(WindsClass $item){
        return $this->linkDB
                ->prepare($this->query_delete($item))
                ->execute();
    }
    final protected function parent_execute($query){
        $request = $this->linkDB->prepare($query);
        $request->execute();
        return $request->fetchAll();
    }
}
/*OK*/class ManagerUser extends ManagerDB {
    static public function init(){
        $mgr = new self();
        $mgr->nameTable = "users";
        $mgr->columns   = User::$columns;
        $mgr->connectDB();
        return $mgr;
    }
    public function getList(){
        return $this->parent_select()
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "User");
    }
    public function getByID($id){
        return $this->parent_select(array('id'=>$id))
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "User")[0];
    }
    public function get(array $whereClauses=NULL, $queryEnd=NULL){
        return $this->parent_select($whereClauses, $queryEnd)
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "User");
    }
    public function insert(User $user){
        return $this->parent_insert($user);
    }
    public function update(User $user){
        if(is_null($user->getId())){  return FALSE;  }
        return $this->parent_update($user);
    }
    public function delete(User $user){
        if(is_null($user->getId())){  return FALSE;  }
        return $this->parent_delete($user);
    }
}
/*OK*/class ManagerTheme extends ManagerDB {
    static public function init(){
        $mgr = new self();
        $mgr->nameTable = "themes";
        $mgr->columns   = Theme::$columns;
        $mgr->connectDB();
        return $mgr;
    }
    public function getList(){
        return $this->parent_select()
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Theme");
    }
    public function getByID($id){
        return $this->parent_select(array('id'=>$id))
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Theme")[0];
    }
    public function get(array $whereClauses=NULL, $queryEnd=NULL){
        return $this->parent_select($whereClauses, $queryEnd)
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Theme");
    }
    public function insert(Theme $theme){
        return $this->parent_insert($theme);
    }
    public function update(Theme $theme){
        if(is_null($theme->getId())){  return FALSE;  }
        return $this->parent_update($theme);
    }
    public function delete(Theme $theme){
        if(is_null($theme->getId())){  return FALSE;  }
        return $this->parent_delete($theme);
    }
}
/*OK*/class ManagerLevel extends ManagerDB {
    static public function init(){
        $mgr = new self();
        $mgr->nameTable = "levels";
        $mgr->columns   = Level::$columns;
        $mgr->connectDB();
        return $mgr;
    }
    public function getList(){
        return $this->parent_select()
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Level");
    }
    public function getByID($id){
        return $this->parent_select(array('id'=>$id))
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Level")[0];
    }
    public function get(array $whereClauses=NULL, $queryEnd=NULL){
        return $this->parent_select($whereClauses, $queryEnd)
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Level");
    }
    public function getLevelHavingScores($levelType=NULL){
        $query = "SELECT DISTINCT levels.* FROM levels JOIN scores "
                ."WHERE scores.idLevel=levels.id "
                ."AND levelStatus='".LEVEL_STATUS::ACCEPTED."' "
                ."AND levelMode='".LEVEL_MODE::STANDARD."'";
        $query .= is_null($levelType) ? NULL : " AND levelType='$levelType'";
        
        $levels = $this->parent_execute($query);
        $assoc = array();
        foreach($levels as $level){
            $assoc[$level['id']] = $level;
        }
        return $assoc;
    }
    public function insert(Theme $theme){
        return $this->parent_insert($theme);
    }
    public function update(Theme $theme){
        if(is_null($theme->getId())){  return FALSE;  }
        return $this->parent_update($theme);
    }
    public function delete(Theme $theme){
        if(is_null($theme->getId())){  return FALSE;  }
        return $this->parent_delete($theme);
    }
}
/*OK*/class ManagerScore extends ManagerDB {
    static public function init() {
        $score = new self();
        $score->nameTable = "scores";
        $score->columns   = Score::$columns;
        $score->connectDB();
        return $score;
    }
    public function getList() {
        return $this->parent_select()
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Score");
    }
    public function getListByPlayer($idPlayer){
        return $this->parent_select(array("idPlayer"=>$idPlayer))
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Score");
    }
    public function getByID($id) {
        return $this->parent_select(array('id'=>$id))
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Score")[0];
    }
    public function get(array $whereClauses=NULL, $queryEnd=NULL){
        return $this->parent_select($whereClauses, $queryEnd)
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Score");
    }
    public function getRanking($idLevel=NULL){
        $nullID = is_null($idLevel);
        $query = "SELECT pseudo, scores.* "
                ."FROM scores JOIN users, levels "
                ."WHERE scores.idPlayer=users.id AND scores.idLevel=levels.id "
                ."AND levelStatus='".LEVEL_STATUS::ACCEPTED."' "
                ."AND levelMode='".LEVEL_MODE::STANDARD."'";
        $query .= $nullID ? NULL : " AND idLevel=$idLevel";
        $dataDB = $this->parent_execute($query);
        
        $ranks = array();
        foreach($dataDB as $data){
            $player   = $data['pseudo'];
            $idPlayer = $data['idPlayer'];
            $score    = Score::init( $idPlayer, $data['idLevel'], $data['time'],
                                     $data['nbClicks'], $data['nbItems'] );
            $points   = $score->calculate();
            if($nullID){
                $exPoints = isset($ranks[$idPlayer]) ? $ranks[$idPlayer] : 0;
                $ranks[$idPlayer] = ["player"=>$player, "points"=>$exPoints + $points];
            }else{
                array_push($ranks,["player"=>$player, "points"=>$points, "score"=>$score]);
            }
        }
        //if($nullID){ arsort($ranks); }
        return $ranks;
    }
    public function getRanksByPlayer($idPlayer){
        $levels = $this->parent_execute("SELECT DISTINCT idLevel FROM $this->nameTable")->fetchAll();
        $order = "ORDER BY time ASC, nbClicks ASC, nbItems DESC";
        $ranks = array();
        for($i=0; $i<count($levels); $i++){
            $idLevel = $levels[$i]['idLevel'];
            $ranking = $this->get(array("idLevel"=>$idLevel),$order);
            for($j=0; $j<count($ranking); $j++){
                $score = $ranking[$j];
                if($score->getIdPlayer() == $idPlayer){
                    $ranks[$idLevel] = $j+1;
                }
            }
        }
        return $ranks;
    }
    public function insert(Score $score){
        return $this->parent_insert($score);
    }
    public function update(Score $score){
        if(is_null($score->getId())){  return FALSE;  }
        return $this->parent_update($score);
    }
    public function delete(Score $score){
        if(is_null($score->getId())){  return FALSE;  }
        return $this->parent_delete($score);
    }
}
/*OK*/class ManagerSubject extends ManagerDB {
    static public function init() {
        $mgr = new self();
        $mgr->nameTable = "subjects";
        $mgr->columns   = Subject::$columns;
        $mgr->connectDB();
        return $mgr;
    }
    public function getList() {
        return $this->parent_select()
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Subject");
    }
    public function getByID($id) {
        return $this->parent_select(array('id'=>$id))
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Subject")[0];
    }
    public function get(array $whereClauses=NULL, $queryEnd=NULL){
	return $this->parent_select($whereClauses, $queryEnd)
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Subject");
    }
    public function insert(Subject $subject){
        return $this->parent_insert($subject);
    }
    public function update(Subject $subject){
        if(is_null($subject->getId())){  return FALSE;  }
        return $this->parent_update($subject);
    }
    public function delete(Subject $subject){
        if(is_null($subject->getId())){  return FALSE;  }
        return $this->parent_delete($subject);
    }
}
/*OK*/class ManagerPost extends ManagerDB {
    static public function init() {
        $mgr = new self();
        $mgr->nameTable = "posts";
        $mgr->columns = Post::$columns;
        $mgr->connectDB();
        return $mgr;
    }
    public function getList(){
        return $this->parent_select()
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Post");
    }
    public function getByID($id){
        return $this->parent_select(array('id'=>$id))
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Post")[0];
    }
    public function get(array $whereClauses=NULL, $queryEnd=NULL){
	return $this->parent_select($whereClauses, $queryEnd)
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Post");
    }
    public function insert(Post $post){
        return $this->parent_insert($post);
    }
    public function delete(Post $post){
         if(is_null($post->getId())){  return FALSE;  }
        return $this->parent_delete($post);
    }
}