<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";

$_SESSION['user'] = UserManager::init()->getByID(8);


$params = Tools::getParamsURL( $_SERVER['QUERY_STRING'] );
$user   = $_SESSION['user'];
?>

<link type="text/css" rel="stylesheet" href="../css/forum.css">
<script type="text/javascript" src="../js/forum.js" ></script>
<section style="padding-bottom:20px; padding-top:20px" class="col-sm-9 col-md-10">
<div id="forum">
    <div id="common">
        <input id="idUser" type="hidden" value="<?php echo $user->getId(); ?>" >
        <div id="loader" style="display: none" >
            <h4>Action in progress</h4>
            <img src="..\resources\loader.gif" style="height: 32px; width: 32px" >
            <h5>Please, wait.</h5>        
        </div>
        <div id="error"></div>
    </div>
    
    <?php if(empty($params)){ ?>
    <!--
        Display the subjects
    -->
    <div id="subject">
        <div id="new-subject" style="display:none" >
            <h3>New subject to create</h3>
            <div>
                <label for="title-new-subject">Title :</label>
                <input id="title-new-subject" type="text" placeholder="Type the title here" ><br/>
                <label for="message-new-subject">Message :</label>
                <input id="message-new-subject" type="text" placeholder="Type the message here" >
            </div>
            <div>
                <button id="create-subject">Create</button>
                <button id="cancel-subject">Cancel</button>
            </div>
        </div>
        <div id="display-subject">
            <button id="btn-new-subject">Create a new subject</button>
            <table id="table-subjects">
                <tr>
                    <th>Subjects</th>
                    <th>Status</th>
                    <th>Last update</th>
                <tr>
                <?php ForumController::displaySubjects(); ?>
            </table>
        </div>
    </div>
    <?php }else
          if(isset($params['id'])){
              $subject = SubjectManager::init()->getByID($params['id']); ?>
            <!--
                Display the posts of the specified subject
            -->
            <div id="post">
                <div id="new-post" style="display:none" >
                    <h3>New post to create</h3>
                    <div>
                        <label for="message-new-post">Message :</label>
                        <input id="message-new-post" type="text" placeholder="Message" >
                    </div>
                    <div>
                        <button id="create-post" class="button-green">Create</button>
                        <button id="cancel-post" class="button-red">Cancel</button>
                    </div>
                </div>
                <div id="display-post">
                    <div class="col-xs-6 col-sm-3"><?php if($subject->isActive()){ ?>
                        <button class="button-green" style="width:135px;" id="btn-new-post">Create a new post</button></div>
                    <div class="col-xs-6 col-sm-3"><?php if($user->isSuperUser()){ ?>
                        <button class="button-orange" style="width:135px;" id="btn-close-subject">Close this subject</button></div>
                    <div class="col-xs-8 col-sm-4"><?php }} if($user->isSuperUser()){ ?>
                        <button class="button-red" style="width:135px;" id="btn-delete-subject">Delete this subject</button></div>
                    <div class="col-xs-4 col-sm-2"><?php } ?>
                        <button class="button-blue" id="btn-back">Back</button></div>
                    <div class="col-xs-12">    <?php ForumController::displayInfosSubject($subject);
                              ForumController::displayPosts($params['id'],$user->isSuperUser()); ?>
                    </div>
                </div>
            </div>
        <?php }else {
            // afficher un message d'erreur
        } ?>
    </div>
</section>

<?php
include "../common/footer.php";
?>