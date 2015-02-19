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
    protected $id;                                  // int : ID used in DB as PK
    static public $columns;                         // must be in same order like in DB - DON'T FORGET "id" COLUMN
    static public function _new_(){}                // use this constructor to instanciate an object
    final protected function __construct(){}        // constructor reserved to instanciate from DB
    final public function getId() {
        return $this->id;
    }
}
/*OK*/class User extends WindsClass implements Winds_Insert, Winds_Update {
    static public $columns = ['id','email','password','pseudo','registrationDate','forgotPassword','userType','userStatus'];
    private $email,                 // text : 64 chars, unique
            $password,              // text : 64 chars, MD5 encoding
            $pseudo,                // text : 64 chars, unique
            $registrationDate,      // datetime
            $forgotPassword,        // datetime
            $userType,              // enum : constant of USER_TYPE
            $userStatus;            // enum : constant of USER_STATUS

    /*-- CONSTRUCTORS --*/
    /*OK*/static public function _new_($email, $password, $pseudo) {
        $instance = new self();
        $instance->id = NULL;
        $instance->email = $email;
        $instance->password = md5($password);
        $instance->pseudo = $pseudo;
        $instance->registrationDate = Tools::now();
        $instance->forgotPassword = NULL;
        $instance->userType = USER_TYPE::PLAYER;
        $instance->userStatus = USER_STATUS::CREATED;
        return $instance;
    }
    
    /*-- METHODS --*/
    /*OK*/public function valuesDB_toInsert(){
        return array(
            $this->email,
            $this->password,
            $this->pseudo,
            $this->registrationDate,
            $this->forgotPassword,
            $this->userType,
            $this->userStatus
        );
    }
    /*OK*/public function valuesDB_toUpdate(){
        return array(
            'password'       => $this->password,
            'forgotPassword' => $this->forgotPassword,
            'userType'       => $this->userType,
            'userStatus'     => $this->userStatus
        );
    }
    /*OK*/public function isSuperUser(){
        return $this->userType == USER_TYPE::MODERATOR ||
               $this->userType == USER_TYPE::ADMINISTRATOR;
    }
    
    /*-- ACCESSORS --*/
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
    public function getUserType() {
        return $this->userType;
    }
    public function getUserStatus() {
        return $this->userStatus;
    }
    public function setPassword($password) {
        $this->password = md5($password);
    }
    public function setForgotPassword(Date $forgotPassword) {
        $this->forgotPassword = $forgotPassword;
    }
    public function setUserType($userType) {
        $this->userType = $userType;
    }
    public function setUserStatus($userStatus) {
        $this->userStatus = $userStatus;
    }

}
/*OK*/class Addon extends WindsClass implements Winds_Insert, Winds_Update, Winds_News {
    static public $columns = ['id','name','description','creationDate','uriLogo',
                              'addonType','addonStatus','levelType','idCreator','idTheme'];
    private $name,                  // text : 64 chars, unique
            $description,           // text : 512 chars
            $creationDate,          // datetime
            $uriLogo,               // text : 200 chars, unique
            $addonType,             // enum : constant of ADDON_TYPE
            $addonStatus,           // enum : constant of ADDON_STATUS
            $levelType,             // enum : constant of LEVEL_TYPE
            $idCreator,             // enum : user ID
            $idTheme;               // int  : addon ID or NULL
    
    /*-- CONSTRUCTORS --*/
    /*OK*/static public function _new_($name, $description, $uriLogo, $addonType, $levelType, $idCreator, $idTheme) {
        $instance = new self();
        $instance->id           = NULL;
        $instance->name         = $name;
        $instance->description  = $description;
        $instance->creationDate = Tools::now();
        $instance->uriLogo      = $uriLogo;
        $instance->addonType    = $addonType;
        $instance->addonStatus  = ($addonType == ADDON_TYPE::THEME) ? ADDON_STATUS::ACCEPTED : ADDON_STATUS::TOMODERATE;
        $instance->levelType    = ($addonType == ADDON_TYPE::THEME) ? LEVEL_TYPE::NONE : $levelType;
        $instance->idCreator    = $idCreator;
        $instance->idTheme      = ($addonType == ADDON_TYPE::THEME) ? NULL : $idTheme;
        return $instance;
    }
    
    /*-- METHODS --*/
    /*OK*/public function valuesDB_toInsert(){
        return array(
            $this->name,
            $this->description,
            $this->creationDate,
            $this->uriLogo,
            $this->addonType,
            $this->addonStatus,
            $this->levelType,
            $this->idCreator,
            $this->idTheme
        );
    }
    /*OK*/public function valuesDB_toUpdate(){
        return array(
            'addonStatus' => $this->addonStatus
        );
    }
    /*OK*/public function formateAsNews(){
        $isLevel = $this->addonType == ADDON_TYPE::LEVEL;
        $object  = ($isLevel ? "$this->levelType " : NULL)." $this->addonType";
        return new News($this->creationDate, "available $object");
    }
    
    /*-- ACCESSORS --*/
    public function getName() {
        return $this->name;
    }
    public function getDescription() {
        return $this->description;
    }
    public function getCreationDate() {
        return $this->creationDate;
    }
    public function getUriLogo(){
        return $this->uriLogo;
    }
    public function getAddonType() {
        return $this->addonType;
    }
    public function getAddonStatus() {
        return $this->addonStatus;
    }
    public function getLevelType() {
        return $this->levelType;
    }
    public function getIdCreator() {
        return $this->idCreator;
    }
    public function getIdTheme() {
        return $this->idTheme;
    }
    public function setAddonStatus($addonStatus) {
        $this->addonStatus = $addonStatus;
    }

}
/*OK*/class Score extends WindsClass implements Winds_Insert, Winds_Update {
    static public $columns = ['id','idPlayer','idLevel','time','nbClicks','nbItems'];
    private $idPlayer,              // int : user ID
            $idLevel,               // int : addon ID
            $time,                  // int : seconds
            $nbClicks,              // int
            $nbItems;               // int
    
    /*-- CONSTRUCTORS --*/
    /*OK*/static public function _new_($idPlayer, $idLevel, $time, $nbClicks, $nbItems) {
        $instance = new self();
        $instance->id       = NULL;
        $instance->idPlayer = $idPlayer;
        $instance->idLevel  = $idLevel;
        $instance->time     = $time;
        $instance->nbClicks = $nbClicks;
        $instance->nbItems  = $nbItems;
        return $instance;
    }
    
    /*-- METHODS --*/
    /*OK*/public function valuesDB_toInsert(){
        return array(
            $this->idPlayer,
            $this->idLevel,
            $this->time,
            $this->nbClicks,
            $this->nbItems
        );
    }
    /*OK*/public function valuesDB_toUpdate(){
        return array(
            'time'      => $this->time,
            'nbClicks'  => $this->nbClicks,
            'nbItems'   => $this->nbItems
        );
    }
    
    /*-- ACCESSORS --*/
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
/*OK*/class Subject extends WindsClass implements Winds_Insert, Winds_Update, Winds_News {
    static public $columns = ['id','title','message','date','subjectStatus','idAuthor'];
    private $title,                 // text : 64 chars
            $message,               // text : 512 chars
            $date,                  // datetime
            $subjectStatus,         // enum : constant of SUBJECT_STATUS
            $idAuthor;              // int  : user ID
    
    /*-- CONSTRUCTORS --*/
    /*OK*/static public function _new_($title, $message, $idAuthor) {
        $instance = new self();
        $instance->id            = NULL;
        $instance->title         = $title;
        $instance->message       = $message;
        $instance->date          = Tools::now();
        $instance->subjectStatus = SUBJECT_STATUS::ACTIVE;
        $instance->idAuthor      = $idAuthor;
        return $instance;
    }
    
    /*-- METHODS --*/
    /*OK*/public function valuesDB_toInsert(){
        return array(
            $this->title,
            $this->message,
            $this->date,
            $this->subjectStatus,
            $this->idAuthor
        );
    }
    /*OK*/public function valuesDB_toUpdate(){
        return array(
            'subjectStatus' => $this->subjectStatus
        );
    }
    /*OK*/public function formateAsNews(){
        return new News($this->date, "subject");
    }
            
    /*-- ACCESSORS --*/
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
    public function setSubjectStatus($subjectStatus) {
        $this->subjectStatus = $subjectStatus;
    }

}
/*OK*/class Post extends WindsClass implements Winds_Insert, Winds_News {
    static public $columns = ['id','date','message','idAuthor','idSubject'];
    private $date,                  // datetime
            $message,               // text : 512 chars
            $idAuthor,              // int  : user ID
            $idSubject;             // int  : subject ID
    
    /*-- CONSTRUCTORS --*/
    /*OK*/static public function _new_($message, $idAuthor, $idSubject) {
        $instance = new self();
        $instance->id        = NULL;
        $instance->date      = Tools::now();
        $instance->message   = $message;
        $instance->idAuthor  = $idAuthor;
        $instance->idSubject = $idSubject;
        return $instance;
    }
    
    /*-- METHODS --*/
    /*OK*/public function valuesDB_toInsert(){
        return array(
            $this->date,
            $this->message,
            $this->idAuthor,
            $this->idSubject
        );
    }
    /*OK*/public function formateAsNews(){
        return new News($this->date, "post");
    }
    
    /*-- ACCESSORS --*/
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

}

/*OK*/class News {
    private $date,
            $object,
            $creator;
    
    /*-- CONSTRUCTORS --*/
    /*OK*/public function __construct($date, $object){
        $this->date   = (new DateTime($date))->format("d-m-Y");
        $this->object = $object;
    }
    
    /*-- METHODS --*/
    /*OK*/public function getMessage(){
        return "<tr><td>$this->date : New $this->object by $this->creator</td></tr>";
    }
    
    /*-- ACCESSORS --*/
    /*OK*/public function setCreator(User $creator) {
        $this->creator = $creator->getPseudo();
    }
}