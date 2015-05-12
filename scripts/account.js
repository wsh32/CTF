$(document).ready(function(){
	console.log(loggedin);
	if(loggedin==true)	{
		$('#account').load('account/login.html');
	}	else	{
		$('#account').load('account/logout.html');
	}
});