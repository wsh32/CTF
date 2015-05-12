$(document).ready(function(){
	if(loggedin==false)	{
		$('#lin').hide();
		$('#lout').show();
	}	else	{
		$('#lin').show();
		$('#lout').hide();
	}
	
	$('#login').submit(function(){
		if(loggedin){
			Materialize.toast("You are already logged in!");
		}	else	{
			$.ajax({
				type: 'POST',
				url: 'ajax.php?m=login',
				data: new FormData(this),
				processData: false,
				contentType: false,
				success: function(data) {
					Materialize.toast(data);
					load_session();
				}
			});
			return false;
		}
	});
});