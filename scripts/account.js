$(document).ready(function(){
	console.log(loggedin);
		$('#account').load('account/login.html');
	}	else	{
		$('#account').load('account/logout.html');
	}
});