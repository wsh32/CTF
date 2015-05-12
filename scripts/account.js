$(document).ready(function(){
	if(loggedin)	{
		$("#header").text("Logout");
		$('#lin').hide();
		$('#lout').show();
	}	else	{
		$("#header").text("Login");
		$('#lin').show();
		$('#lout').hide();
	}
	
	$('#login').submit(function(){
		if(loggedin){
			Materialize.toast("You are already logged in!");
		}	else	{
			var fd = new FormData();
			fd.append( 'password', $( '[name="password"]' ).prop( 'value' ) );
			fd.append( 'team_name', $( '[name="name"]' ).prop( 'value' ) );
			fd.append( 'token', token );
			$.ajax({
				type: 'POST',
				url: 'ajax.php?m=login',
				data: fd,
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
	
	$('#logout').click(function(){
		if(!loggedin){
			Materialize.toast("You are already logged out!");
		}	else	{
			var fd = new FormData();
			formData.append( 'token', token );
			$.ajax({
				type: 'POST',
				url: 'ajax.php?m=logout',
				data: fd,
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