<?php
include "../common/header.php";

$param = empty($_SERVER['QUERY_STRING']) ? NULL : $_SERVER['QUERY_STRING'];
?>

<script type="text/javascript" src="../js/login.js" ></script>
<link type="text/css" rel="stylesheet" href="../css/login.css">
<?php if( is_null($param) ){ ?>
<div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2" >
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
<div id="signupbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2" style="display:none" >
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
<div id="forgotbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2" style="display:none" >
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
<div id="resetbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2" >
        <div class="panel panel-info">

        </div>
</div>
<?php } ?>