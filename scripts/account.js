function account_load(){
	
	load_session();
	console.log(loggedin);
	if(loggedin)	{
		$("#header").text("Logout");
		$('#lin').hide();
		$('#lout').show();
	}	else	{
		$("#header").text("Login");
		$('#lin').show();
		$('#lout').hide();
	}
	return false;
}


$(document).ready(function(){
	setTimeout(function() {account_load()}, 50);
	$('#login').submit(function(){
		if(loggedin){
			Materialize.toast("You are already logged in!", 4000);
			account_load();
			return false;
		}	else	{
			var fd = new FormData();
			fd.append( 'password', $( '[name="password"]' ).prop( 'value' ) );
			fd.append( 'username', $( '[name="name"]' ).prop( 'value' ) );
			fd.append( 'token', token );
			$.ajax({
				type: 'POST',
				url: 'ajax.php?m=login',
				data: fd,
				processData: false,
				contentType: false,
				success: function(xml) {
					Materialize.toast($( xml ).find( 'text' ).text(), 4000);
					if($(xml).find('code').text() == '1')login();
					load_session();
					console.log(loggedin);
					account_load();
				}
			});
			return false;
		}
	});
	
	$('#logout').click(function(){
		if(!loggedin){
			Materialize.toast("You are already logged out!", 4000);
			account_load();
			return false;
		}	else	{
			var fd = new FormData();
			fd.append( 'token', token );
			$.ajax({
				type: 'POST',
				url: 'ajax.php?m=logout',
				data: fd,
				processData: false,
				contentType: false,
				success: function(xml) {
					Materialize.toast($( xml ).find( 'text' ).text(), 4000);
					if($(xml).find('code').text() == '1')logout();
					load_session();
					console.log(loggedin);
					account_load();
				}
			});
			return false;
		}
	});
	
});
