<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";

$_SESSION['user'] = ManagerUser::init()->getByID(8);


$params = Tools::getParamsURL( $_SERVER['QUERY_STRING'] );
$user   = $_SESSION['user'];
?>

<link type="text/css" rel="stylesheet" href="../css/forum.css">
<script type="text/javascript" src="../js/forum.js" ></script>
<div id="forum">
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
              $subject = ManagerSubject::init()->getByID($params['id']); ?>
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
                    <button id="create-post">Create</button>
                    <button id="cancel-post">Cancel</button>
                </div>
            </div>
            <div id="display-post">
                <?php if($subject->isActive()){ ?>
                <button id="btn-new-post">Create a new post</button>
                    <?php if($user->isSuperUser()){ ?>
                    <button id="btn-close-subject">Close this subject</button>
                <?php }} if($user->isSuperUser()){ ?>
                <button id="btn-delete-subject">Delete this subject</button>
                <?php } ?>
                <button id="btn-back">Back</button>
                 <?php ForumController::displayInfosSubject($subject);
                       ForumController::displayPosts($params['id'],$user->isSuperUser()); ?>
            </div>
        </div>
    <?php }else {
        // afficher un message d'erreur
    } ?>
</div>

<?php
include "../common/footer.php";
?>