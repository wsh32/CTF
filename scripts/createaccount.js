$(document).ready(function(){
	
	$('#ca').submit(function(){
		if(loggedin){
			Materialize.toast("You are already logged in!", 4000);
			account_load();
			return false;
		}	else if(!$( '[name="agree"]' ).prop( 'checked' ))	{
			Materialize.toast("Please agree to the terms and conditions", 4000);
			return false;
		}	else	{
			var fd = new FormData();
			fd.append( 'team_name', $( '[name="name"]' ).prop( 'value' ) );
			fd.append( 'email', $( '[name="email"]' ).prop( 'value' ) );
			fd.append( 'password', $( '[name="password"]' ).prop( 'value' ) );
			fd.append( 'repeat', $( '[name="repeat"]' ).prop( 'value' ) );
			fd.append( 'code', $( '[name="code"]' ).prop( 'value' ) );
			fd.append( 'token', token );
			$.ajax({
				type: 'POST',
				url: 'ajax.php?m=create_account',
				data: fd,
				processData: false,
				contentType: false,
				success: function(xml) {
					
					if($(xml).find('code').text() == '1'){
						login();
						window.location = "account.html";
					}	else	{
						Materialize.toast($( xml ).find( 'text' ).text(), 4000);
					}
					load_session();
				},
				error: function(data)	{
					console.log(data);
				}
			});
			return false;
		}
	
	});
	
});