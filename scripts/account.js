$(document).ready(function(){
	if(loggedin==false)	{
		$('#account').load('account/login.html');
	}	else	{
		$('#account').load('account/logout.html');
	}
});