$(document).ready(function(){
	if(loggedin)	{
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
			var fd = new FormData();
			formData.append( 'password', $( '[name="password"]' ).prop( 'value' ) );
			formData.append( 'team_name', $( '[name="name"]' ).prop( 'value' ) );
			formData.append( 'token', token );
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