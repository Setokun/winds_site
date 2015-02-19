<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";

$_SESSION['user'] = (new ManagerUser())->getList()[0];


$params = Tools::getParamsURL( $_SERVER['QUERY_STRING'] );
$user   = $_SESSION['user'];
?>

<link type="text/css" rel="stylesheet" href="../css/forum.css">
<script type="text/javascript" src="../js/forum.js" ></script>
<div id="forum">
    <?php if(empty($params)){ ?>
    <button id="create-subject">Create a new subject</button>
    <table id="subjects">
        <tr>
            <th>Subjects</th>
            <th>Status</th>
            <th>Last update</th>
        <tr>
        <?php ForumController::displaySubjects(); ?>
    </table>
    <?php }else
          if(isset($params['id'])){ ?>
        <div>
            <button class="btn-new-post">Create a new post</button>
            <?php if($user->isSuperUser()){ ?>
            <button class="btn-close-subject">Close this subject</button>
            <button class="btn-delete-subject">Delete this subject</button>
            <?php } ?>
            <button class="btn-back">Back</button>
        </div>
        <?php ForumController::displayInfosSubject($params['id']);
              ForumController::displayPosts($params['id'],$user->isSuperUser()); ?>
    <?php }else {
        // afficher un message d'erreur
    } ?>
</div>

<?php
include "../common/footer.php";
?>