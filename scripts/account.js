$(document).ready(function(){
	if(loggedin==true)	{
		$('#account').load('account/login.html');
	}	else	{
		$('#account').load('account/logout.html');
	}
});