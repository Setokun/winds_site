<?php
require_once '../core/config.php';
include "../common/header.php";

$params = Tools::getParamsURL($_SERVER['QUERY_STRING']);
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
<div id="loginbox" class="mainbox col-xs-12 col-sm-6 col-sm-offset-3 col-lg-offset-4 col-lg-4">
    <div class="panel panel-info" >
        <div class="panel-heading">
            <div class="panel-title">Sign In</div>
            <div style="float:right;font-size:80%;position:relative;top:-10px">
                <span class="link" onClick="$('#loginbox').hide(); $('#forgotbox').show()" >Forgot password ?</span>
            </div>
        </div>
        <div style="padding-top:30px" class="panel-body" >
            <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12">
            </div>
            <div class="input-group">
                    <span class="input-group-addon">Email address</span>
                    <input id="login-email" type="text" class="form-control" name="email" value="" placeholder="email" >
            </div>
            <div class="input-group">
                    <span class="input-group-addon">Password</span>
                    <input id="login-password" type="password" class="form-control" name="password" placeholder="password">
            </div>
            <!-- <div class="input-group">
                <div class="checkbox">
                    <label>
                        <input id="login-remember" type="checkbox" name="remember" value="1"> Remember me
                    </label>
                </div>
            </div> -->
            <div class="col-sm-12 controls" style="margin-top:10px; padding:0 0" >
                <span id="btn-login" class="btn btn-success">Login  </span>
            </div>
            <div class="col-md-12 control" style="margin-top:10px;padding:0" >
                <div style="border-top:1px solid#888;padding-top:10px;font-size:85%" >Don't have an account ! 
                    <span class="link" onClick="$('#loginbox').hide(); $('#signupbox').show()" >Sign up here</span>
                </div>
            </div>
        </div>
    </div>                     
</div>
<div id="signupbox" class="mainbox col-xs-12 col-sm-6 col-sm-offset-3 col-lg-offset-4 col-lg-4" style="display:none" >
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="panel-title">Sign Up</div>
            <div style="float:right;font-size:85%;position:relative;top:-10px">
                <span class="link" onclick="$('#signupbox').hide(); $('#loginbox').show()">Back</span>
            </div>
        </div>  
        <div class="panel-body form-horizontal" >
            <div id="signupalert" style="display:none" class="alert alert-danger">
                <p>Error:</p>
                <span></span>
            </div>
            <div class="form-group" >
                <label for="mail" class="col-md-3 control-label">Email</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="mail" placeholder="Email Address">
                </div>
            </div>
            <div class="form-group" >
                <label for="pseudo" class="col-md-3 control-label">Pseudo</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="pseudo" placeholder="Pseudo">
                </div>
            </div>
            <div class="form-group" >
                <label for="password" class="col-md-3 control-label">Password</label>
                <div class="col-md-9">
                    <input type="password" class="form-control" name="passwd" placeholder="Password">
                </div>
            </div>
            <div class="form-group" >
                <label for="password2" class="col-md-3 control-label">Confirm Password</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="icode" placeholder="Confirm password" >
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
<div id="forgotbox" class="mainbox col-xs-12 col-sm-6 col-sm-offset-3 col-lg-offset-4 col-lg-4" style="display:none" >
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="panel-title">Forgot password</div>
            <div style="float:right;font-size:80%;position:relative;top:-10px">
                <span class="link" onClick="$('#forgotbox').hide(); $('#loginbox').show()" >Back</span>
            </div>
        </div>
        <div style="padding-top:30px" class="panel-body" >
            <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
            <div class="input-group">
                <span class="input-group-addon">Email address</span>
                <input id="email-address" type="text" class="form-control" name="password" placeholder="email">
            </div>
            <div class="col-sm-12 controls" style="margin-top:10px; padding:0 0" >
                <span id="btn-send" class="btn btn-success">Send me a mail  </span>
            </div>
        </div>
    </div>
</div>
<?php }else{ ?>
<div id="resetbox" class="mainbox col-xs-12 col-sm-6 col-sm-offset-3 col-lg-offset-4 col-lg-4" >
    <input id="id" type="hidden" value="<?php echo $params['id']; ?>" >
    <input id="token" type="hidden" value="<?php echo $params['token']; ?>" >
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="panel-title">Change password</div>
            <div style="float:right;font-size:80%;position:relative;top:-10px">
                <span id="reset-back" class="link" onClick="$('#forgotbox').hide(); $('#loginbox').show()" >Back to login page</span>
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
<?php } ?>