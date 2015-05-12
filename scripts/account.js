$(document).ready(function(){
	if(loggedin)	{
		$('#account').load('account/login.html');
	}	else	{
		$('#account').load('account/logout.html');
	}
});