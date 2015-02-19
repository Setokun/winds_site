var btn_login,
	btn_signup;

function controlsAffectation(){
	btn_login = $("#loginbox #btn-login");
	btn_signup = $("#signupbox #btn-signup");
}
function controlsEvents(){
	btn_login.click(function(){});
	btn_signup.click(function(){});
}

$(document).ready(function(){
    controlsAffectation();
	controlsEvents();
	
});