<?php
/**
 * Description of classes
 * @author Damien.D & Stephane.G
 */
require_once "config.php";

interface Winds_Insert {
    public function valuesDB_toInsert();   // all values needed to insert in DB
}
interface Winds_Update {
    public function valuesDB_toUpdate();   // all values needed to update in DB
}
interface Winds_News {
    public function formateAsNews();
}

/*OK*/abstract class WindsClass {
    protected $id;                                      // int : ID used in DB as PK
    static public $columns;                             // must be in same order like in DB - DON'T FORGET "id" COLUMN
    // static public function init();                   // use this constructor to instanciate an object
    final protected function __construct(){}            // constructor reserved to instanciate from DB
    final public function getId() {
        return $this->id;
    }
}
/*OK*/abstract class Addon extends WindsClass
        implements Winds_Insert, Winds_News, JsonSerializable {
    
    static public $columns = ['id','name','description','creationDate','filePath','idCreator'];
    protected $name,                  // text : 64 chars, unique
              $description,           // text : 512 chars
              $creationDate,          // datetime
              $filePath,              // text : 255 chars, unique
              $idCreator;             // int  : user ID
    
    // -- METHODS --
    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
    public function compareCreationDateTo(Addon $addon){
        $currentDate = new DateTime($this->creationDate);
        $addonDate   = new DateTime($addon->creationDate);;
        return $currentDate->getTimestamp() - $addonDate->getTimestamp();
    }
    
    // -- ACCESSORS --
    public function getName() {
        return $this->name;
    }
    public function getDescription() {
        return $this->description;
    }
    public function getCreationDate() {
        return $this->creationDate;
    }
    public function getFilePath(){
        return $this->filePath;
    }
    public function getIdCreator() {
        return $this->idCreator;
    }

}

/*OK*/class User extends WindsClass
        implements Winds_Insert, Winds_Update, JsonSerializable {
    static public $columns = ['id','email','password','pseudo','registrationDate','forgotPassword','token','userType','userStatus'];
    private $email,                 // text : 64 chars, unique
            $password,              // text : 64 chars, MD5 encoding
            $pseudo,                // text : 64 chars, unique
            $registrationDate,      // datetime
            $forgotPassword,        // datetime
            $token,                 // text : 64 chars
            $userType,              // text : 64 chars - use constant of USER_TYPE
            $userStatus;            // text : 64 chars - use constant of USER_STATUS

    //-- CONSTRUCTORS --
    static public function init($email, $password, $pseudo) {
        $user = new self();
        $user->id               = NULL;
        $user->email            = $email;
        $user->password         = md5($password);
        $user->pseudo           = $pseudo;
        $user->registrationDate = Tools::now();
        $user->forgotPassword   = NULL;
        $user->token            = NULL;
        $user->userType         = USER_TYPE::PLAYER;
        $user->userStatus       = USER_STATUS::CREATED;
        return $user;
    }
    static public function initFrom(array $assocUser){
        $user = new self();
        $user->id               = $assocUser['id'];
        $user->email            = $assocUser['email'];
        $user->password         = $assocUser['password'];
        $user->pseudo           = $assocUser['pseudo'];
        $user->registrationDate = $assocUser['registrationDate'];
        $user->forgotPassword   = $assocUser['forgotPassword'];
        $user->token            = $assocUser['token'];
        $user->userType         = $assocUser['userType'];
        $user->userStatus       = $assocUser['userStatus'];
        return $user;
    }
    
    // -- METHODS --
    public function valuesDB_toInsert(){
        return array(
            $this->email,
            $this->password,
            $this->pseudo,
            $this->registrationDate,
            $this->forgotPassword,
            $this->token,
            $this->userType,
            $this->userStatus
        );
    }
    public function valuesDB_toUpdate(){
        return array(
            'password'       => $this->password,
            'forgotPassword' => $this->forgotPassword,
            'token'          => $this->token,
            'userType'       => $this->userType,
            'userStatus'     => $this->userStatus
        );
    }
    public function isSuperUser(){
        return $this->userType == USER_TYPE::MODERATOR ||
               $this->userType == USER_TYPE::ADMINISTRATOR;
    }
    public function isBanished(){
        return $this->userStatus === USER_STATUS::BANISHED;
    }
    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
    public function toAssocArray(){
        return array(
            'id'                => $this->id,
            'email'             => $this->email,
            'password'          => $this->password,
            'pseudo'            => $this->pseudo,
            'registrationDate'  => $this->registrationDate,
            'forgotPassword'    => $this->forgotPassword,
            'token'             => $this->token,
            'userType'          => $this->userType,
            'userStatus'        => $this->userStatus
        );
    }
    
    //-- ACCESSORS --
    public function getEmail() {
        return $this->email;
    }
    public function getPassword() {
        return $this->password;
    }
    public function getPseudo() {
        return $this->pseudo;
    }
    public function getRegistrationDate() {
        return $this->registrationDate;
    }
    public function getForgotPassword() {
        return $this->forgotPassword;
    }
    public function getToken() {
        return $this->token;
    }
    public function getUserType() {
        return $this->userType;
    }
    public function getUserStatus() {
        return $this->userStatus;
    }
    public function setPassword($password) {
        $this->password = md5($password);
    }
    public function setForgotPassword($forgotPassword) {
        $this->forgotPassword = $forgotPassword;
    }
    public function setToken($token) {
        $this->token = $token;
    }
    public function setUserType($userType) {
        $this->userType = $userType;
    }
    public function setUserStatus($userStatus) {
        $this->userStatus = $userStatus;
    }

}
/*OK*/class Theme extends Addon {
    static public $columns = ['id','name','description','creationDate','filePath','imagePath','idCreator'];
    private $imagePath;         // text : 255 chars, unique
    
    // -- CONSTRUCTORS --
    static public function init($name, $description, $filePath, $imagePath, $idCreator) {
        $addon = new self();
        $addon->id           = NULL;
        $addon->name         = $name;
        $addon->description  = $description;
        $addon->creationDate = Tools::now();
        $addon->filePath     = $filePath;
        $addon->imagePath    = $imagePath;
        $addon->idCreator    = $idCreator;
        return $addon;
    }
    
    // -- METHODS --
    public function valuesDB_toInsert(){
        return array(
            $this->name,
            $this->description,
            $this->creationDate,
            $this->filePath,
            $this->imagePath,
            $this->idCreator
        );
    }
    public function formateAsNews() {
        return new News($this->creationDate, "available theme", "shop.php");
    }
    
    // -- ACCESSORS --
    public function getImagePath(){
        return "../resources/".$this->imagePath;
    }
}
/*OK*/class Level extends Addon
        implements Winds_Update {
    static public $columns = ['id','name','description','creationDate','filePath','timeMax',
                              'levelType','levelStatus','levelMode','idCreator','idTheme'];
    private $timeMax,               // int  : number of second
            $levelType,             // text : 20 char - use constant of LEVEL_TYPE
            $levelStatus,           // text : 20 char - use constant of LEVEL_STATUS
            $levelMode,             // text : 20 char - use constant of LEVEL_MODE
            $idTheme;               // int  : theme ID
    private $gameData;
    
    // -- CONSTRUCTORS --
    static public function init($name, $description, $filePath, $timeMax, $idCreator, $idTheme) {
        $level = new self();
        $level->id           = NULL;
        $level->name         = $name;
        $level->description  = $description;
        $level->creationDate = Tools::now();
        $level->filePath     = $filePath;
        $level->timeMax      = $timeMax;
        $level->levelType    = LEVEL_TYPE::CUSTOM;
        $level->levelStatus  = LEVEL_STATUS::TOMODERATE;
        $level->levelMode    = LEVEL_MODE::STANDARD;
        $level->idCreator    = $idCreator;
        $level->idTheme      = $idTheme;
        return $level;
    }
    static public function initFromUpload($data, $idLvl){
        $infosCreator = UserManager::init()->get("SELECT id, userType FROM "
                      . "user WHERE pseudo='".$data['creator']."'")[0];
        
        $level = new self();
        $level->id           = $idLvl;
        $level->name         = $data['name'];
        $level->description  = $data['description'];
        $level->creationDate = $data['date'];
        $level->filePath     = str_replace($_SERVER['DOCUMENT_ROOT'].'/', '', $data['path']);
        $level->timeMax      = $data['timeMax'];
        $level->levelType    = $data['type'];
        $level->levelMode    = LEVEL_MODE::STANDARD;
        $level->idTheme      = $data['idTheme'];
        $level->idCreator    = $infosCreator['id'];
        $level->levelStatus  = $infosCreator['userType'] == USER_TYPE::ADMINISTRATOR
                             ? LEVEL_STATUS::ACCEPTED : LEVEL_STATUS::TOMODERATE;
        
        $level->gameData['creator'] = $data['creator'];
        $level->gameData['startPosition'] = $data['startPosition'];
        $level->gameData['endPosition'] = $data['endPosition'];
        $level->gameData['matrix'] = $data['matrix'];
        $level->gameData['interactions'] = $data['interactions'];
        $level->gameData['uploaded'] = 'true';
        
        return $level;
    }
    
    // -- METHODS --
    public function valuesDB_toInsert(){
        return array(
            $this->name,
            $this->description,
            $this->creationDate,
            $this->filePath,
            $this->timeMax,
            $this->levelType,
            $this->levelStatus,
            $this->levelMode,
            $this->idCreator,
            $this->idTheme
        );
    }
    public function valuesDB_toUpdate(){
        return array(
            'levelStatus' => $this->levelStatus
        );
    }
    public function formateAsNews(){
        return new News($this->creationDate, "available $this->levelType level", "shop.php");
    }
    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
    public function values_toZipFile(){
        $data = [
            'creator' => $this->gameData['creator'],
            'date' => $this->creationDate,
            'name' => $this->name,
            'description' => $this->description,
            'idDB' => $this->id,
            'idTheme' => $this->idTheme,
            'mode' => $this->levelMode,
            'type' => $this->levelType,
            'uploaded' => true,
            'startPosition' => $this->gameData['startPosition'],
            'endPosition' => $this->gameData['endPosition'],
            'matrix' => $this->gameData['matrix'],
            'interactions' => $this->gameData['interactions'],
            'timeMax' => $this->timeMax
        ];
        return json_encode($data);
    }
    
    // -- ACCESSORS --
    public function getTimeMax(){
        return $this->timeMax;
    }
    public function getLevelType() {
        return $this->levelType;
    }
    public function getLevelStatus() {
        return $this->levelStatus;
    }
    public function getLevelMode() {
        return $this->levelMode;
    }
    public function getIdTheme() {
        return $this->idTheme;
    }
    public function setLevelStatus($levelStatus) {
        $this->levelStatus = $levelStatus;
    }

}
/*OK*/class Score extends WindsClass
        implements Winds_Insert, Winds_Update, JsonSerializable {
    static public $columns = ['id','idPlayer','idLevel','time','nbClicks','nbItems'];
    private $idPlayer,              // int : user ID
            $idLevel,               // int : level ID
            $time,                  // int : seconds
            $nbClicks,              // int
            $nbItems;               // int
    static private $points = ['time'=> 100, 'nbClicks'=> 10, 'nbItems'=> 75];
    
    // -- CONSTRUCTORS --
    static public function init($idPlayer, $idLevel, $time, $nbClicks, $nbItems) {
        $instance = new self();
        $instance->id       = NULL;
        $instance->idPlayer = $idPlayer;
        $instance->idLevel  = $idLevel;
        $instance->time     = $time;
        $instance->nbClicks = $nbClicks;
        $instance->nbItems  = $nbItems;
        return $instance;
    }
    
    // -- METHODS --
    public function valuesDB_toInsert(){
        return array(
            $this->idPlayer,
            $this->idLevel,
            $this->time,
            $this->nbClicks,
            $this->nbItems
        );
    }
    public function valuesDB_toUpdate(){
        return array(
            'time'      => $this->time,
            'nbClicks'  => $this->nbClicks,
            'nbItems'   => $this->nbItems
        );
    }
    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
    public function compareTo(Score $score, $timeMaxLevel){
        return $this->calculate($timeMaxLevel) > $score->calculate($timeMaxLevel);
    }
    public function calculate($timeMaxLevel){
        $time   = $timeMaxLevel - $this->time;
        $points = 10000
				+ $time           * self::$points['time']
                - $this->nbClicks * self::$points['nbClicks']
                + $this->nbItems  * self::$points['nbItems'];
        return $points;
    }
    public function updateFrom(Score $score){
        $this->time     = $score->getTime();
        $this->nbClicks = $score->getNbClicks();
        $this->nbItems  = $score->getNbItems();
    }
    
    // -- ACCESSORS --
    public function getIdPlayer() {
        return $this->idPlayer;
    }
    public function getIdLevel() {
        return $this->idLevel;
    }
    public function getTime() {
        return $this->time;
    }
    public function getNbClicks() {
        return $this->nbClicks;
    }
    public function getNbItems(){
        return $this->nbItems;
    }
    public function setTime($time) {
        $this->time = $time;
    }
    public function setNbClicks($nbClicks) {
        $this->nbClicks = $nbClicks;
    }
    public function setNbItems($nbItems){
        $this->nbItems = $nbItems;
    }

}
/*OK*/class Subject extends WindsClass
        implements Winds_Insert, Winds_Update, Winds_News {
    static public $columns = ['id','title','message','date','subjectStatus','idAuthor'];
    private $title,                 // text : 64 chars
            $message,               // text : 512 chars
            $date,                  // datetime
            $subjectStatus,         // text : 20 chars - use constant of SUBJECT_STATUS
            $idAuthor;              // int  : user ID
    
    // -- CONSTRUCTORS --
    static public function init($title, $message, $idAuthor) {
        $subject = new self();
        $subject->id            = NULL;
        $subject->title         = $title;
        $subject->message       = $message;
        $subject->date          = Tools::now();
        $subject->subjectStatus = SUBJECT_STATUS::ACTIVE;
        $subject->idAuthor      = $idAuthor;
        return $subject;
    }
    
    // -- METHODS --
    public function valuesDB_toInsert(){
        return array(
            $this->title,
            $this->message,
            $this->date,
            $this->subjectStatus,
            $this->idAuthor
        );
    }
    public function valuesDB_toUpdate(){
        return array(
            'subjectStatus' => $this->subjectStatus
        );
    }
    public function formateAsNews(){
        return new News($this->date, "subject", "forum.php?id=$this->id");
    }
    public function isActive(){
        return $this->subjectStatus == SUBJECT_STATUS::ACTIVE;
    }
            
    // -- ACCESSORS --
    public function getTitle() {
        return $this->title;
    }
    public function getMessage() {
        return $this->message;
    }
    public function getDate() {
        return $this->date;
    }
    public function getSubjectStatus() {
        return $this->subjectStatus;
    }
    public function getIdAuthor() {
        return $this->idAuthor;
    }
    public function setId($id){
        $this->id = $id;
    }
    public function setSubjectStatus($subjectStatus) {
        $this->subjectStatus = $subjectStatus;
    }

}
/*OK*/class Post extends WindsClass
        implements Winds_Insert, Winds_News {
    static public $columns = ['id','date','message','idAuthor','idSubject'];
    private $date,                  // datetime
            $message,               // text : 512 chars
            $idAuthor,              // int  : user ID
            $idSubject;             // int  : subject ID
    
    // -- CONSTRUCTORS --
    static public function init($message, $idAuthor, $idSubject) {
        $post = new self();
        $post->id        = NULL;
        $post->date      = Tools::now();
        $post->message   = $message;
        $post->idAuthor  = $idAuthor;
        $post->idSubject = $idSubject;
        return $post;
    }
    
    // -- METHODS --
    public function valuesDB_toInsert(){
        return array(
            $this->date,
            $this->message,
            $this->idAuthor,
            $this->idSubject
        );
    }
    public function formateAsNews(){
        return new News($this->date, "post", "forum.php?id=$this->idSubject");
    }
    
    // -- ACCESSORS --
    public function getDate() {
        return $this->date;
    }
    public function getMessage() {
        return $this->message;
    }
    public function getIdAuthor() {
        return $this->idAuthor;
    }
    public function getIdSubject() {
        return $this->idSubject;
    }
    public function setId($id){
        $this->id = $id;
    }
}

/*OK*/class News {
    private $date,
            $object,
            $url,
            $author;
    
    // -- CONSTRUCTORS --
    public function __construct($date, $object, $url){
        $this->date   = (new DateTime($date))->format("d-m-Y");
        $this->object = $object;
        $this->url    = $url; 
    }
    
    // -- METHODS --
    public function getMessage(){
        return "<tr><td><a href='$this->url'>$this->date : New $this->object by $this->author</a></td></tr>";
    }
    
    // -- ACCESSORS --
    public function setAuthor($author) {
        $this->author = $author;
    }
}
class LevelManipulator {
    const filename = 'level.src';
    private $file, $lvl, $output=['result'=>NULL, 'error'=>NULL];
    
    // -- CONSTRUCTORS --
    static public function init($file){
        $manip = new self();
        $manip->file = $file;
        return $manip;
    }
    
    // -- METHODS --
    public function run(){
        $inserted = FALSE;
        $idLvl = LevelManager::init()->get("SELECT MAX(id)+1 AS 'max' FROM level")[0]['max'];
        $dest = Tools::getLevelsPath()."$idLvl.jar";
        try {
            $this->file = $this->file['tmp_name'];
            $data = $this->extractLevelData();
            $data['path'] = $dest;
            
            $this->lvl = Level::initFromUpload($data, $idLvl);
            $this->updateZip();
            
            $inserted = is_numeric( LevelManager::init()->insert($this->lvl) );
            $moved = move_uploaded_file($this->file, $dest);
            if( !$moved ){
                // overwrite if exists
                $this->output['error'] = "Unable to store the level";
                return $this;
            }
        
            $this->output['result'] = "Level uploading succeeded";
        } catch(PDOException $e){
            $this->output['error'] = "Uploaded level removed :\n"
                                   . "This level file already exists";
        } catch (Exception $e){
            if( !is_null($inserted) ){
                LevelManager::init()->deleteMulti([$idLvl]);
            }
            $this->output['error'] = "Uploaded level removed :\n"
                                   . $e->getMessage();			
        }        
        return $this;
    }
    /*OK*/private function extractLevelData(){
        $zip = new ZipArchive;
        if( !$zip->open($this->file) ){
            throw new Exception("Unable to open the level file to extract its content");
        }
        $data = $zip->getFromName(self::filename);
        $zip->close();
        return json_decode($data, true);
    }
    /*OK*/private function updateZip(){
        $zip = new ZipArchive;
        if( !$zip->open($this->file) ){
            throw new Exception("Unable to open the level file to update its content");
        }
        $zip->deleteName(LevelManipulator::filename);
        $written = $zip->addFromString(self::filename, $this->lvl->values_toZipFile());
        $zip->close();
        if( !$written ){
            throw new Exception("Unable to update the level file");
        }
    }
	
    // -- ACCESSORS --
    public function getResult(){
        return $this->output['result'];
    }
    public function getError(){
        return $this->output['error'];
    }
    
}
/*OK*/class ThemeManipulator {
    private $zipPath, $name;
    
    // -- CONSTRUCTORS --
    static public function init($file, $themeName){
        $manip = new self();
        $manip->zipPath = $file;
        $manip->name = $themeName;
        return $manip;
    }
    
    // -- METHODS --
    public function getLogoName(){
        $zip = new ZipArchive;
        if( !$zip->open($this->zipPath) ){ return Tools::getEmptyLogoName(); }
        
        $logoName = 'logo-'.$this->name.'.png';
        $logoPath = Tools::getResourcesPath().$logoName;
        $logoFound = FALSE;
        
        for($i=0; $i<$zip->numFiles; $i++){
            $entry = $zip->getNameIndex($i);
            if(preg_match('#.*logo\.png$#i', $entry)){
                $logoFound = TRUE;
                $copied = copy("zip://".$this->zipPath."#".$entry, $logoPath);
                break;
            }
        }
        
        $zip->close();
        $logo = $logoFound && $copied ? $logoName : Tools::getEmptyLogoName();
        return $logo;
    }
    
}