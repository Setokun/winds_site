<?php
/**
 * Description of managers
 * @author Damien.D & Stephane.G
 */
require_once "config.php";

/*OK*/abstract class ManagerDB {
    protected $linkDB,
              $nameDB = "winds";
    
    // VARIABLES - MUST BE OVERRIDEN
    protected $nameTable,
              $columns;             // must be in same order like in DB
    
    public function __construct(){
        $this->connectDB();
    }
    final protected function connectDB() {
        try {
            $this->linkDB = new PDO('mysql:host=localhost;dbname='.$this->nameDB, 'root', ''); // local
            $this->linkDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        }
        catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
            exit;
        }
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
    
    final protected function _select(array $whereClauses=NULL, $queryEnd=NULL){
        $clauses = $this->_filterClauses($whereClauses);
        $values  = array_values($clauses);
        $query   = $this->query_select($clauses)." ".$queryEnd;
        
        $request = $this->linkDB->prepare($query);
        empty($values) ? $request->execute() : $request->execute($values);
        return $request;
    }
    final protected function _insert(WindsClass $item){
        return $this->linkDB
                ->prepare($this->query_insert())
                ->execute($item->valuesDB_toInsert());
    }
    final protected function _update(WindsClass $item){
        $values = array_values($item->valuesDB_toUpdate());
        return $this->linkDB
                    ->prepare($this->query_update($item))
                    ->execute($values);
    }
    final protected function _delete(WindsClass $item){
        return $this->linkDB
                ->prepare($this->query_delete($item))
                ->execute();
    }
    final protected function _filterClauses(array $clauses=NULL){
        $filteredClauses = array();
        if( !is_null($clauses) ){
            $columnMatches = array_intersect(array_keys($clauses), $this->columns);
            foreach($columnMatches as $column){
                $filteredClauses[$column] = $clauses[$column];
            }
        }
        return $filteredClauses;
    }
    final protected function _execute($query){
        $request = $this->linkDB->prepare($query);
        $request->execute();
        return $request;
    }
    
    abstract public function getList();
    abstract public function getByID($id);
    abstract public function get(array $whereClauses=NULL, $queryEnd=NULL);
}
/*OK*/class ManagerUser extends ManagerDB {
    public function __construct() {
        $this->nameTable = "users";
        $this->columns = User::$columns;
        parent::__construct();
    }
    public function getList(){
        return $this->_select()
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "User");
    }
    public function getByID($id){
        return $this->_select(array('id'=>$id))
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "User")[0];
    }
    public function get(array $whereClauses=NULL, $queryEnd=NULL){
        return $this->_select($whereClauses, $queryEnd)
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "User");
    }
    public function insert(User $user){
        return $this->_insert($user);
    }
    public function update(User $user){
        if(is_null($user->getId())){  return FALSE;  }
        return $this->_update($user);
    }
    public function delete(User $user){
        if(is_null($user->getId())){  return FALSE;  }
        return $this->_delete($user);
    }
}
/*OK*/class ManagerAddon extends ManagerDB {
    public function __construct(){
        $this->nameTable = "addons";
        $this->columns = Addon::$columns;
        parent::__construct();
    }
    public function getList(){
        return $this->_select()
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Addon");
    }
    public function getByID($id){
        return $this->_select(array('id'=>$id))
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Addon")[0];
    }
    public function get(array $whereClauses=NULL, $queryEnd=NULL){
        return $this->_select($whereClauses, $queryEnd)
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Addon");
    }
    public function insert(Addon $addon){
        return $this->_insert($addon);
    }
    public function update(Addon $addon){
        if(is_null($addon->getId())){  return FALSE;  }
        return $this->_update($addon);
    }
    public function delete(Addon $addon){
        if(is_null($addon->getId())){  return FALSE;  }
        return $this->_delete($addon);
    }
}
/*OK*/class ManagerScore extends ManagerDB {
    public function __construct() {
        $this->nameTable = "scores";
        $this->columns = Score::$columns;
        parent::__construct();
    }
    public function getList() {
        return $this->_select()
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Score");
    }
    public function getListByPlayer($idPlayer){
        return $this->_select(array("idPlayer"=>$idPlayer))
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Score");
    }
    public function getByID($id) {
        return $this->_select(array('id'=>$id))
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Score")[0];
    }
    public function get(array $whereClauses=NULL, $queryEnd=NULL){
        return $this->_select($whereClauses, $queryEnd)
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Score");
    }
    public function getRanking($idLevel=NULL){
        $order = "time, nbClicks, nbItems DESC";
        
        if(is_null($idLevel)){
            $query = "SELECT idPlayer, SUM(time) AS time, "
                    ."SUM(nbClicks) AS nbClicks, SUM(nbItems) AS nbItems "
                    ."FROM $this->nameTable GROUP BY idPlayer ORDER BY $order";
            return $this->_execute($query)
                        ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Score");
        }
        else{
            $where = array('idLevel'=>$idLevel);
            return $this->get($where, "ORDER BY $order");
        }
    }
    public function getRanksByPlayer($idPlayer){
        $levels = $this->_execute("SELECT DISTINCT idLevel FROM $this->nameTable")->fetchAll();
        $order = "ORDER BY time, nbClicks, nbItems DESC";
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
        return $this->_insert($score);
    }
    public function update(Score $score){
        if(is_null($score->getId())){  return FALSE;  }
        return $this->_update($score);
    }
    public function delete(Score $score){
        if(is_null($score->getId())){  return FALSE;  }
        return $this->_delete($score);
    }
}
/*OK*/class ManagerSubject extends ManagerDB {
    public function __construct() {
        $this->nameTable = "subjects";
        $this->columns = Subject::$columns;
        parent::__construct();
    }
    public function getList() {
        return $this->_select()
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Subject");
    }
    public function getByID($id) {
        return $this->_select(array('id'=>$id))
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Subject")[0];
    }
    public function get(array $whereClauses=NULL, $queryEnd=NULL){
	return $this->_select($whereClauses, $queryEnd)
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Subject");
    }
    public function insert(Subject $subject){
        return $this->_insert($subject);
    }
    public function update(Subject $subject){
        if(is_null($subject->getId())){  return FALSE;  }
        return $this->_update($subject);
    }
    public function delete(Subject $subject){
        if(is_null($subject->getId())){  return FALSE;  }
        return $this->_delete($subject);
    }
}
/*OK*/class ManagerPost extends ManagerDB {
    public function __construct() {
        $this->nameTable = "posts";
        $this->columns = Post::$columns;
        parent::__construct();
    }
    public function getList(){
        return $this->_select()
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Post");
    }
    public function getByID($id){
        return $this->_select(array('id'=>$id))
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Post")[0];
    }
    public function get(array $whereClauses=NULL, $queryEnd=NULL){
	return $this->_select($whereClauses, $queryEnd)
                    ->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "Post");
    }
    public function insert(Post $post){
        return $this->_insert($post);
    }
    public function delete(Post $post){
         if(is_null($post->getId())){  return FALSE;  }
        return $this->_delete($post);
    }
}