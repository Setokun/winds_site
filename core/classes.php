<?php
/**
 * Description of classes files
 * @author Damien.D & Stephane.G
 */

require_once "config.php";


// -- INTERFACES --
/**
 * Interface used to standardize insertion in database.
 */
interface Winds_Insert {
    /**
     * Get a string which represents the current item's values to insert into DB.
     * @return string
     */
    public function valuesDB_toInsert();
}

/**
 * Interface used to standardize update in database.
 */
interface Winds_Update {
    /**
     * Get a string which represents the current item's values to update into DB.
     * @return string
     */
    public function valuesDB_toUpdate();
}

/**
 * Interface used to standardize news formatage.
 */
interface Winds_News {
    /**
     * Formates the current item in a News.
     * @return News
     */
    public function formateAsNews();
}


// -- WINDS ABSTRACT CLASSES --
/**
 * Abstract class used as default class which contains some common variables and methods.
 */
abstract class WindsClass {
    protected $id;                                      // int : ID used in DB as PK
    static public $columns;                             // must be in same order like in DB - DON'T FORGET "id" COLUMN
    // static public function init();                   // use this constructor to instanciate an object
    /**
     * Refuses the initialization by the default way.<br>
     * Reserved to instanciate from DB
     */
    final protected function __construct(){}
    /**
     * Get the ID of the current item.
     * @return int
     */
    final public function getId() {
        return $this->id;
    }
}

/**
 * Abstract class used as default addon class which contains some common variables and methods.
 */
abstract class Addon extends WindsClass
        implements Winds_Insert, Winds_News, JsonSerializable {
    
    static public $columns = ['id','name','description','creationDate','filePath','idCreator'];
    protected $name,                  // text : 64 chars, unique
              $description,           // text : 512 chars
              $creationDate,          // datetime
              $filePath,              // text : 255 chars, unique
              $idCreator;             // int  : user ID
    
    // -- METHODS --
    /*
     * Returns an object representing the JSON-formatted serialization of the current item.
     * @return Object
     */
    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
    /**
     * Compares the creation date between the specified addons.
     * @param Addon $a The first addon to compare
     * @param Addon $b The second addon to compare
     * @return int
     */
    static public function compareCreationDate(Addon $a, Addon $b){
        $aDate = new DateTime($a->creationDate);
        $bDate = new DateTime($b->creationDate);
        $diff  = $aDate->getTimestamp() - $bDate->getTimestamp();
        return $diff == 0 ? 0 : $diff/abs($diff);
    }
    
    // -- ACCESSORS --
    /**
     * Get the addon's name.
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    /**
     * Get the addon's description.
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }
    /**
     * Get the addon's creation date.
     * @return string
     */
    public function getCreationDate() {
        return $this->creationDate;
    }
    /**
     * Get the addon's file path.
     * @return string
     */
    public function getFilePath(){
        return $this->filePath;
    }
    /**
     * Get the addon's creator ID.
     * @return int
     */
    public function getIdCreator() {
        return $this->idCreator;
    }

}


// -- WINDS CLASSES --
/**
 * Class representing a Winds user.
 */
class User extends WindsClass
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
    /**
     * Instanciate a new user with the specified parameters.
     * @param string $email The e-mail address
     * @param string $password The password without encryption
     * @param string $pseudo the pseudonym
     * @return User
     */
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
    /**
     * Instanciate a new user from the specified array given by $_SESSION.
     * @param array $assocUser The User object in an array representation
     * @return User
     */
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
    
    // -- IMPLEMENTED METHODS --
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
    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
    
    // -- METHODS --
    /**
     * Checks if the user is a moderator or administrator.
     * @return boolean
     */
    public function isSuperUser(){
        return $this->userType == USER_TYPE::MODERATOR ||
               $this->userType == USER_TYPE::ADMINISTRATOR;
    }
    /**
     * Checks if the user is banished.
     * @return boolean
     */
    public function isBanished(){
        return $this->userStatus === USER_STATUS::BANISHED;
    }
    /**
     * Checks if the user is deleted.
     * @return boolean
     */
    public function isDeleted(){
        return $this->userStatus === USER_STATUS::DELETED;
    }
    /**
     * Get an associative array which represents the user.
     * @return array
     */
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
    /**
     * Get the user's e-mail address
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }
    /**
     * Get the user's MD5-encrypted password.
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }
    /**
     * Get the user's pseudonym.
     * @return string
     */
    public function getPseudo() {
        return $this->pseudo;
    }
    /**
     * Get the user's registration date.
     * @return string
     */
    public function getRegistrationDate() {
        return $this->registrationDate;
    }
    /**
     * Get the user's forgot password date.
     * @return string
     */
    public function getForgotPassword() {
        return $this->forgotPassword;
    }
    /**
     * Get the user's token.
     * @return string
     */
    public function getToken() {
        return $this->token;
    }
    /**
     * Get the user's type.
     * @return string
     */
    public function getUserType() {
        return $this->userType;
    }
    /**
     * Get the user's status.
     * @return string
     */
    public function getUserStatus() {
        return $this->userStatus;
    }
    /**
     * Set the user's password.
     * @param string $password The new password without encryption
     */
    public function setPassword($password) {
        $this->password = md5($password);
    }
    /**
     * Set the user's forgot password date.
     * @param string $forgotPassword The new forgot password date
     */
    public function setForgotPassword($forgotPassword) {
        $this->forgotPassword = $forgotPassword;
    }
    /**
     * Set the user's token
     * @param string $token The new token
     */
    public function setToken($token) {
        $this->token = $token;
    }
    /**
     * Set the user's type.
     * @param string $userType A constant of USER_TYPE
     */
    public function setUserType($userType) {
        $this->userType = $userType;
    }
    /**
     * Set the user's status.
     * @param string $userStatus A constant of USER_STATUS
     */
    public function setUserStatus($userStatus) {
        $this->userStatus = $userStatus;
    }

}

/**
 * Class representing a Winds theme.
 */
class Theme extends Addon {
    static public $columns = ['id','name','description','creationDate','filePath','imagePath','idCreator'];
    private $imagePath;         // text : 255 chars, unique
    
    // -- CONSTRUCTORS --
    /**
     * Instanciate a new theme with the specified parameters.
     * @param string $name The theme's name
     * @param string $description The theme's description
     * @param string $filePath The theme file's path
     * @param string $imagePath The theme image's path
     * @param int $idCreator The theme's creator ID
     * @return Theme
     */
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
    
    // -- IMPLEMENTED METHODS --
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
    /**
     * Get the theme image's path.
     * @return string
     */
    public function getImagePath(){
        return "../resources/".$this->imagePath;
    }
}

/**
 * Class representing a Winds level.
 */
class Level extends Addon
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
    /**
     * Instanciate a new level with the specified parameters.
     * @param string $name The level's name
     * @param string $description The level's description
     * @param string $filePath The level file's path
     * @param int $timeMax The level's maximum time
     * @param int $idCreator The level's creator ID
     * @param int $idTheme The theme ID used in the level
     * @return Level
     */
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
    /**
     * Instanciate a new level from the specified array given by upload process.
     * @param array $data The array of data contained in the level file
     * @param int $idLvl The ID to set to the level
     * @param boolean $addonPage The upload source is the addon page
     * @return Level
     */
    static public function initFromUpload($data, $idLvl, $addonPage){
        $idCreator = UserManager::init()->get("SELECT id FROM user "
                   . "WHERE pseudo='".$data['creator']."'")[0]['id'];
        
        $level = new self();
        $level->id           = $idLvl;
        $level->name         = $data['name'];
        $level->description  = $data['description'];
        $level->creationDate = $data['date'];
        $level->filePath     = str_replace($_SERVER['DOCUMENT_ROOT'].'/', '', $data['path']);
        $level->timeMax      = $data['timeMax'];
        $level->levelMode    = $addonPage ? $data['mode'] : LEVEL_MODE::STANDARD;
        $level->idTheme      = $data['idTheme'];
        $level->idCreator    = $idCreator;
        $level->levelType    = $addonPage ? $data['type'] : LEVEL_TYPE::CUSTOM;
        $level->levelStatus  = $addonPage ? LEVEL_STATUS::ACCEPTED : LEVEL_STATUS::TOMODERATE;
        
        $level->gameData['creator'] = $data['creator'];
        $level->gameData['startPosition'] = $data['startPosition'];
        $level->gameData['endPosition'] = $data['endPosition'];
        $level->gameData['matrix'] = $data['matrix'];
        $level->gameData['interactions'] = $data['interactions'];
        $level->gameData['uploaded'] = 'true';
        
        return $level;
    }
    
    // -- IMPLEMENTED METHODS --
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
    
    // -- METHODS --
    /**
     * Returns the level's content to update the JAR file.
     * @return string
     */
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
    /**
     * Removes the level archive files which match the specified IDs<br>
     * and returns the deleted levels' number.
     * @param array $idLevels The array of level IDs to delete
     * @return boolean
     */
    static public function deleteFiles(array $idLevels){
        $levels = LevelManager::init()->getAll("WHERE id in (".implode(',',$idLevels).")");
        $paths  = array_map(function($level){ return $level->getFilePath(); }, $levels);
        
        $nbDel = 0;
        foreach($paths as $path){
            $nbDel += unlink($_SERVER['DOCUMENT_ROOT']."/$path") ? 1 : 0;
        }
        return count($idLevels) == $nbDel;
    }
    
    // -- ACCESSORS --
    /**
     * Get the level's maximum time.
     * @return int
     */
    public function getTimeMax(){
        return $this->timeMax;
    }
    /**
     * Get the level's type.
     * @return string
     */
    public function getLevelType() {
        return $this->levelType;
    }
    /**
     * Get the level's status.
     * @return string
     */
    public function getLevelStatus() {
        return $this->levelStatus;
    }
    /**
     * Get the level's mode.
     * @return string
     */
    public function getLevelMode() {
        return $this->levelMode;
    }
    /**
     * Get the level's theme ID.
     * @return int
     */
    public function getIdTheme() {
        return $this->idTheme;
    }
    /**
     * Get the level creator's id.
     * @return int
     */
    public function getIdCreator() {
        return $this->idCreator;
    }
    /**
     * Get the level's name.
     * @return String
     */
    public function getName() {
        return $this->name;
    }
    /**
     * Set the level's status.
     * @param String $levelStatus A constant of LEVEL_STATUS
     */
    public function setLevelStatus($levelStatus) {
        $this->levelStatus = $levelStatus;
    }
    
}

/**
 * Class representing a Winds score.
 */
class Score extends WindsClass
        implements Winds_Insert, Winds_Update, JsonSerializable {
    static public $columns = ['id','idPlayer','idLevel','time','nbClicks','nbItems'];
    private $idPlayer,              // int : user ID
            $idLevel,               // int : level ID
            $time,                  // int : seconds
            $nbClicks,              // int
            $nbItems;               // int
    static private $points = ['time'=> 100, 'nbClicks'=> 10, 'nbItems'=> 75];
    
    // -- CONSTRUCTORS --
    /**
     * Instanciate a new score with the specified parameters.
     * @param int $idPlayer The user ID who has made the score
     * @param int $idLevel The level ID where has made the score
     * @param int $time The past time to finish the level
     * @param int $nbClicks The number of clicks made
     * @param int $nbItems The number of collected items
     * @return Score
     */
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
    
    // -- IMPLEMENTED METHODS --
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
    
    // -- METHODS --
    /**
     * Compares the current score to the specified score.
     * @param Score $score The score to compare with
     * @return int
     */
    public function compareTo(Score $score){
        return $this->calculate() > $score->calculate();
    }
    /**
     * Calculates the score.
     * @return int
     */
    public function calculate(){
        $points = 10000
		- $this->time     * self::$points['time']
                - $this->nbClicks * self::$points['nbClicks']
                + $this->nbItems  * self::$points['nbItems'];
        return $points;
    }
    /**
     * Updates the score from the specified score.
     * @param Score $score The score to update from
     */
    public function updateFrom(Score $score){
        $this->time     = $score->getTime();
        $this->nbClicks = $score->getNbClicks();
        $this->nbItems  = $score->getNbItems();
    }
    
    // -- ACCESSORS --
    /**
     * Get the user ID who has made the score
     * @return int
     */
    public function getIdPlayer() {
        return $this->idPlayer;
    }
    /**
     * The level ID where has made the score
     * @return int
     */
    public function getIdLevel() {
        return $this->idLevel;
    }
    /**
     * The past time to finish the level
     * @return int
     */
    public function getTime() {
        return $this->time;
    }
    /**
     * The number of clicks made
     * @return int
     */
    public function getNbClicks() {
        return $this->nbClicks;
    }
    /**
     * Get the number of collected items
     * @return int
     */
    public function getNbItems(){
        return $this->nbItems;
    }
    /**
     * Set the score's time.
     * @param int $time The new time
     */
    public function setTime($time) {
        $this->time = $time;
    }
    /**
     * Set the score's number of clicks.
     * @param int $nbClicks The new number of clicks
     */
    public function setNbClicks($nbClicks) {
        $this->nbClicks = $nbClicks;
    }
    /**
     * Set the score's number of collected items.
     * @param int $nbItems The new number of collected items
     */
    public function setNbItems($nbItems){
        $this->nbItems = $nbItems;
    }

}

/**
 * Class representing a Winds subject.
 */
class Subject extends WindsClass
        implements Winds_Insert, Winds_Update, Winds_News {
    static public $columns = ['id','title','message','date','subjectStatus','idAuthor'];
    private $title,                 // text : 64 chars
            $message,               // text : 512 chars
            $date,                  // datetime
            $subjectStatus,         // text : 20 chars - use constant of SUBJECT_STATUS
            $idAuthor;              // int  : user ID
    
    // -- CONSTRUCTORS --
    /**
     * Instanciate a new subject with the specified parameters.
     * @param string $title The subject's title
     * @param string $message The subject's message
     * @param int $idAuthor The user ID who has create the subject
     * @return Subject
     */
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
    
    // -- IMPLEMENTED METHODS --
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
    
    // -- METHODS --
    /**
     * Checks if the subject if active.
     * @return boolean
     */
    public function isActive(){
        return $this->subjectStatus == SUBJECT_STATUS::ACTIVE;
    }
            
    // -- ACCESSORS --
    /**
     * Get the subject's title.
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }
    /**
     * Get the subject's message.
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }
    /**
     * Get the subject's creation date.
     * @return string
     */
    public function getDate() {
        return $this->date;
    }
    /**
     * Get the subject's status.
     * @return string
     */
    public function getSubjectStatus() {
        return $this->subjectStatus;
    }
    /**
     * Get the user IDwho has made the subject
     * @return int
     */
    public function getIdAuthor() {
        return $this->idAuthor;
    }
    /**
     * Set the subject's ID.
     * @param int $id The new ID
     */
    public function setId($id){
        $this->id = $id;
    }
    /**
     * Set the subject's status.
     * @param string $subjectStatus A constant of SUBJECT_STATUS
     */
    public function setSubjectStatus($subjectStatus) {
        $this->subjectStatus = $subjectStatus;
    }

}

/**
 * Class representing a Winds post.
 */
class Post extends WindsClass
        implements Winds_Insert, Winds_News {
    static public $columns = ['id','date','message','idAuthor','idSubject'];
    private $date,                  // datetime
            $message,               // text : 512 chars
            $idAuthor,              // int  : user ID
            $idSubject;             // int  : subject ID
    
    // -- CONSTRUCTORS --
    /**
     * Instanciate a new post with the specified parameters.
     * @param string $message The post's message
     * @param int $idAuthor The user ID who has made the post
     * @param int $idSubject The subject ID which is concerned by the post
     * @return Post
     */
    static public function init($message, $idAuthor, $idSubject) {
        $post = new self();
        $post->id        = NULL;
        $post->date      = Tools::now();
        $post->message   = $message;
        $post->idAuthor  = $idAuthor;
        $post->idSubject = $idSubject;
        return $post;
    }
    
    // -- IMPLEMENTED METHODS --
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
    /**
     * Get the post's creation date.
     * @return type
     */
    public function getDate() {
        return $this->date;
    }
    /**
     * Get the post's message.
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }
    /**
     * Get the user ID who has made the post
     * @return int
     */
    public function getIdAuthor() {
        return $this->idAuthor;
    }
    /**
     * Get the subject ID which is concerned by the post
     * @return int
     */
    public function getIdSubject() {
        return $this->idSubject;
    }
    /**
     * Set the post's ID.
     * @param int $id The new ID
     */
    public function setId($id){
        $this->id = $id;
    }
}


// -- UTILITY CLASSES --
/**
 * Class representing a news.
 */
class News {
    private $date,
            $object,
            $url,
            $author;
    
    // -- CONSTRUCTORS --
    /**
     * Instanciate a news with the specified parameters.
     * @param string $date The date to apply to the news
     * @param mixed $object The subject or post which is concerned
     * @param string $url The URL to apply to the news when an user clicked on
     */
    public function __construct($date, $object, $url){
        $this->date   = (new DateTime($date))->format("d-m-Y");
        $this->object = $object;
        $this->url    = $url; 
    }
    
    // -- METHODS --
    /**
     * Returns the news's formated message to display it.
     * @return string
     */
    public function getMessage(){
        return "<tr><td><a href='$this->url'>$this->date : New $this->object by $this->author</a></td></tr>";
    }
    
    // -- ACCESSORS --
    /**
     * Set the news's author name.
     * @param string $author
     */
    public function setAuthor($author) {
        $this->author = $author;
    }
    
}

/**
 * Class used to update a Winds level file.
 */
class LevelManipulator {
    const filename = 'level.src';
    private $file, $lvl, $output=['result'=>NULL, 'error'=>NULL];
    
    // -- CONSTRUCTORS --
    /**
     * Instanciante a new level manipulator with the specified uploaded level file.
     * @param array $file The array of the uploaded file given by $_FILES
     * @return LevelManipulator
     */
    static public function init(array $file){
        $manip = new self();
        $manip->file = $file;
        return $manip;
    }
    
    // -- METHODS --
    /**
     * Stores the uploaded level file into the server,<br>
     * inserts it into DB and updates its content.
     * @param boolean $addonPage The upload source is the addon page
     * @return LevelManipulator
     */
    public function run($addonPage=FALSE){
        $inserted = FALSE;
        $idLvl = LevelManager::init()->get("SELECT MAX(id)+1 AS 'max' FROM level")[0]['max'];
        $dest = Tools::getLevelsPath()."$idLvl.jar";
        try {
            $this->file = $this->file['tmp_name'];
            $data = $this->extractLevelData();
            $data['path'] = $dest;
            
            $this->lvl = Level::initFromUpload($data, $idLvl, $addonPage);
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
    /**
     * Extracts the level's content.
     * @return string The level's JSON-formated content
     * @throws Exception
     */
    private function extractLevelData(){
        $zip = new ZipArchive;
        if( !$zip->open($this->file) ){
            throw new Exception("Unable to open the level file to extract its content");
        }
        $data = $zip->getFromName(self::filename);
        $zip->close();
        return json_decode($data, true);
    }
    /**
     * Updates the level file.
     * @throws Exception
     */
    private function updateZip(){
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
    /**
     * Get the manipulation's result.
     * @return string
     */
    public function getResult(){
        return $this->output['result'];
    }
    /**
     * Get the message if an error occurs during the manipulation.
     * @return string
     */
    public function getError(){
        return $this->output['error'];
    }
    
}

/**
 * Class used to insert a Winds theme file.
 */
class ThemeManipulator {
    private $zipPath, $name;
    
    // -- CONSTRUCTORS --
    /**
     * Instaciates a new theme manipulator with the specified paramters.
     * @param array $file The array of the uploaded file given by $_FILES
     * @param type $themeName The theme's name
     * @return ThemeManipulator
     */
    static public function init($file, $themeName){
        $manip = new self();
        $manip->zipPath = $file;
        $manip->name = $themeName;
        return $manip;
    }
    
    // -- METHODS --
    /**
     * Returns the theme's logo path.<br>
     * Try to extract the theme's logo to the resources folder.<br>
     * If the extraction fails, the path of the default logo is returned.
     * @return string
     */
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