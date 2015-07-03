<?php
require_once '../core/config.php';
include_once '../common/header.php';

$params = Tools::getParamsURL($_SERVER['QUERY_STRING']);
$action = isset($params['action']) ? $params['action'] : NULL;
$id     = isset($params['id'])     ? $params['id'] : NULL;
$token  = isset($params['token'])  ? $params['token'] : NULL;

if($action === 'activate'){
    $activated = AccountController::activateAccount($id, $token);
    $msgActivation = ($activated === TRUE) ?
        "<h4>Your Winds account has been activated.</h4><span>Enjoy it !</span>" :
        "<h4 style='color:indianred'>Activation error</h4><span>$activated.</span>";
}
?>

<script type="text/javascript" src="../js/login.js" ></script>
<link type="text/css" rel="stylesheet" href="../css/login.css">

<article class="modal" id="ajax">
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

<?php if( empty($params) ){ ?>
<div id="login-box" class="mainbox col-xs-12 col-sm-6 col-sm-offset-3 col-lg-offset-4 col-lg-4">
    <div class="panel panel-info" >
        <div class="panel-heading">
            <div class="panel-title">Sign In</div>
            <div style="float:right;font-size:80%;position:relative;top:-10px">
                <span id="login-to-forgot" class="link" >Forgot password ?</span>
            </div>
        </div>
        <form method="post" action="session.php" class="panel-body" style="padding-top:30px" >
            <input name="login" type="hidden" value="true">
            <div class="input-group">
                <span class="input-group-addon">Email address</span>
                <input name="email" type="text" class="form-control" placeholder="Email"
                       value="player1@winds.net">
            </div>
            <div class="input-group" style="margin-top:10px">
                <span class="input-group-addon">Password</span>
                <input name="password" type="password" class="form-control" placeholder="Password"
                       value="player">
            </div>
            <div class="col-sm-12 controls" style="margin-top:10px; padding:0 0" >
                <span id="btn-login" class="btn btn-success">Login  </span>
            </div>
            <div class="col-md-12 controls" style="margin-top:10px;padding:0" >
                <div style="border-top:1px solid#888;padding-top:10px;font-size:85%" >Don't have an account ! 
                    <span id="login-to-signup" class="link" >Sign up here</span>
                </div>
            </div>
        </form>
    </div>                     
</div>
<div id="signup-box" class="mainbox col-xs-12 col-sm-6 col-sm-offset-3 col-lg-offset-4 col-lg-4" style="display:none" >
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="panel-title">Sign Up</div>
            <div style="float:right;font-size:85%;position:relative;top:-10px">
                <span id="signup-to-login" class="link" >Back</span>
            </div>
        </div>  
        <div class="panel-body form-horizontal" >
            <div class="form-group" >
                <label for="signup-email" class="col-md-3 control-label">Email</label>
                <div class="col-md-9">
                    <input value="damien.deloche@gmail.com" id="signup-email" type="text" class="form-control" placeholder="Email address">
                </div>
            </div>
            <div class="form-group" >
                <label for="signup-pseudo" class="col-md-3 control-label">Pseudo</label>
                <div class="col-md-9">
                    <input value="player10" id="signup-pseudo" type="text" class="form-control" placeholder="Pseudo">
                </div>
            </div>
            <div class="form-group" >
                <label for="signup-password1" class="col-md-3 control-label">Password</label>
                <div class="col-md-9">
                    <input value="toto" id="signup-password1" type="password" class="form-control" placeholder="Password">
                </div>
            </div>
            <div class="form-group" >
                <label for="signup-password2" class="col-md-3 control-label">Confirm Password</label>
                <div class="col-md-9">
                    <input value="toto" id="signup-password2" type="password" class="form-control" placeholder="Confirm password" >
                </div>
            </div>
            <div class="form-group" >
                <div class="col-md-offset-3 col-md-9">
                    <button id="btn-signup" type="button" class="btn btn-info"><i class="icon-hand-right"></i>Sign Up</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="forgot-box" class="mainbox col-xs-12 col-sm-6 col-sm-offset-3 col-lg-offset-4 col-lg-4" style="display:none" >
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="panel-title">Forgot password</div>
            <div style="float:right;font-size:80%;position:relative;top:-10px">
                <span id="forgot-to-login" class="link">Back</span>
            </div>
        </div>
        <div style="padding-top:30px" class="panel-body" >
            <div class="input-group">
                <span class="input-group-addon">Email address</span>
                <input value="player1@winds.net" id="forgot-email" type="text" class="form-control" placeholder="Email">
            </div>
            <div class="col-sm-12 controls" style="margin-top:10px; padding:0 0" >
                <span id="btn-send" class="btn btn-success">Send me a mail  </span>
            </div>
        </div>
    </div>
</div>
<?php }else{ ?>
    <input id="id" type="hidden" value="<?php echo $id; ?>" >
    <input id="token" type="hidden" value="<?php echo $token; ?>" >
    <?php if($action === 'activate'){ ?>
        <div id="activate-box" class="mainbox col-xs-12 col-sm-6 col-sm-offset-3 col-lg-offset-4 col-lg-4" >
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">Account activation</div>
                </div>
                <div class="panel-body form-horizontal" >
                    <div class="form-group col-md-12" >
                        <?php echo $msgActivation; ?>
                    </div>
                    <div class="col-md-12 controls" style="margin-top:10px;padding:0" >
                        <div style="border-top:1px solid#888;padding-top:10px;font-size:85%" >
                            <a class="link" href="login.php">Click here</a> to login
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php }else if($action === 'reset'){ ?>
        <div id="reset-box" class="mainbox col-xs-12 col-sm-6 col-sm-offset-3 col-lg-offset-4 col-lg-4" >
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">Change password</div>
                    <div style="float:right;font-size:80%;position:relative;top:-10px">
                        <span id="reset-to-login" class="link" onClick="$('#forgotbox').hide(); $('#loginbox').show()" >Back to login page</span>
                    </div>
                </div>
                <div class="panel-body form-horizontal" >
                    <div class="form-group" >
                        <label for="password1" class="col-md-3 control-label">Password</label>
                        <div class="col-md-9">
                            <input id="password1" type="password" class="form-control" placeholder="Type your new password">
                        </div>
                    </div>
                    <div class="form-group" >
                        <label for="password2" class="col-md-3 control-label">Confirmation</label>
                        <div class="col-md-9">
                            <input id="password2" type="password" class="form-control" placeholder="Confirm your new password">
                        </div>
                    </div>
                    <div class="col-sm-12 controls" style="margin-top:10px; padding:0 0" >
                        <span id="btn-valid" class="btn btn-success">Valid  </span>
                    </div>
                </div>
            </div>
        </div>
    <?php }else{ ?>
        <div class="mainbox col-xs-12 col-sm-6 col-sm-offset-3 col-lg-offset-4 col-lg-4" >
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">Unknown parameters</div>
                </div>
                <div class="panel-body form-horizontal" >
                    <div class="form-group col-md-12" >
                        <h4>You wanna play :)</h4>
                        <p>Same player plays again. Insert coins !</p>
                    </div>
                    <div class="col-md-12 controls" style="margin-top:10px;padding:0" >
                        <div style="border-top:1px solid#888;padding-top:10px;font-size:85%" >
                            <a class="link" href="login.php">Click here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php }
} ?>