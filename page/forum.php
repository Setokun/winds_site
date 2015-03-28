<?php
include_once "../common/header.php";
include_once "../common/banner.php";
include_once "../common/menu.php";
require_once "../core/config.php";

$_SESSION['user'] = UserManager::init()->getByID(8);


$params = Tools::getParamsURL( $_SERVER['QUERY_STRING'] );
$user   = $_SESSION['user'];
?>

<script type="text/javascript" src="../js/forum.js" ></script>
<section class="col-sm-8 col-md-9 col-lg-10">
    
    <article class="modal" id="ajax">
        <input id="idUser" type="hidden" value="<?php echo $user->getId(); ?>" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button id="ajax-close" type="button" class="close" data-dismiss="modal">&times</button>
                    <h4 class="modal-title">Action in progress</h4>
                </div>
                <div class="modal-body">
                    <div id="ajax-loader" style="text-align:center">
                        <img src="..\resources\loader.gif">
                        <h5 style="margin-bottom:0"><b>Please, wait.</b></h5>
                    </div>
                    <div id="ajax-message"></div>
                </div>
                <div class="modal-footer">
                    <button id="ajax-closer" class="btn" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </article>
    
    <article id="forum">
        <?php if(empty($params)){
        /* Display the subjects */?>
        <div id="subject">
            <div id="new-subject" style="display:none" >
                <h3>New subject to create</h3>
                <div class="col-xs-12 form-row">
                    <div class="col-xs-12 col-md-3"><label for="title-new-subject">Title :</label></div>
                    <div class="col-xs-12 col-md-9"><input id="title-new-subject" class="form-control" type="text" placeholder="Type the title here" ></div>
                </div>
                <div class="col-xs-12 form-row">
                    <div class="col-xs-12 col-md-3"><label for="message-new-subject">Message : (Max.&nbsp;255&nbsp;characters)</label></div>
                    <div class="col-xs-12 col-md-9"><textarea id="message-new-subject" maxlength="255" class="form-control" rows="5" placeholder="Your message"></textarea></div>
                </div>
                <div class="col-xs-12 form-row text-center">
                    <button id="create-subject" class="btn btn-primary">Create</button>
                    <button id="cancel-subject" class="btn btn-danger">Cancel</button>
                </div>
            </div>

            <div id="display-subject">
                <button id="btn-new-subject" class="btn btn-success">Create a new subject</button>
                <table id="table-subjects" class="table table-bordered">
                    <tr>
                        <th class="th-winds">Subjects</th>
                        <th class="th-winds">Status</th>
                        <th class="th-winds">Last update</th>
                    <tr>
                    <?php ForumController::displaySubjects(); ?>
                </table>
            </div>
        </div>
        <?php }else
        if(isset($params['id'])){
            $subject = SubjectManager::init()->getByID($params['id']);
            /* Display the posts of the specified subject */ ?>
        <div class="align-mobile-left" id="post">
            <input id="idSubject" type="hidden" value="<?php echo $subject->getId(); ?>" >
            <div id="new-post" style="display:none" >
                <h3>New post to create</h3>
                <div>
                    <label for="message-new-post">Message : (Max. 255 characters)</label>
                    <textarea id="message-new-post"  maxlength="255" class="form-control" rows="5" placeholder="Your message"></textarea>
                </div>
                <div class="form-row text-center">
                    <button id="create-post" class="btn btn-primary">Create</button>
                    <button id="cancel-post" class="btn btn-danger">Cancel</button>
                </div>
            </div>
            <div id="display-post">
                <div>
                    <div style="margin-bottom:20px" class="col-xs-6 col-md-3"><?php if($subject->isActive()){ ?>
                        <button id="btn-new-post" class="btn btn-success" style="width:135px;">Create a new post</button>
                    </div>
                    <div class="col-xs-6 col-md-3"><?php if($user->isSuperUser()){ ?>
                        <button id="btn-close-subject" class="btn btn-warning" style="width:135px;">Close this subject</button>
                    </div>
                    <div style="margin-bottom:20px" class="col-xs-8 col-md-4"><?php }} if($user->isSuperUser()){ ?>
                        <button id="btn-delete-subject" class="btn btn-danger" style="width:135px;">Delete this subject</button>
                    </div>
                    <div class="col-xs-4 col-md-2"><?php } ?>
                        <button id="btn-back" class="btn btn-primary">Back</button>
                    </div>
                </div>
                <div id="table-posts" class="col-xs-12">
                    <?php ForumController::displayInfosSubject($subject);
                          ForumController::displayPosts($subject, $user); ?>
                </div>
            </div>
        </div>
        <?php }else {
            /* Display "Unknown parameters" */
        } ?>
    </article>
</section>

<?php
include "../common/footer.php";
?>