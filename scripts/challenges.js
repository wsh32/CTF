$(document).ready(function(){
	get_challenges();
	
	//submit flag
	$( 'form' ).submit(function(){
		var formData = new FormData();    
		formData.append( 'key', $( '[name="flag"]' ).prop( 'value' ) );
		formData.append( 'team', $( '[name="team"]' ).prop( 'value' ) );
		formData.append( 'id', $( '[name="id"]' ).prop( 'value' ) );
		formData.append( 'token', $( '[name="id"]' ).prop( 'value' ) );
			
		$.ajax({
			type: 'POST',
			url: 'check.php',
			data: formData,
			dataType: 'JSON',
			processData: false, 
			contentType: false,
			success: function( data )
			{
				if( data.correct == 1 )
				{
					$( '#response' ).empty();
					$( '#response' ).text( data.reply ).css( 'color', '#0e0' ).show();
				}
				else
				{
					$( '#response' ).empty();
					$( '#response' ).text( data.reply ).css( 'color', 'red' ).show().fadeOut( 2000 );
				}
			}
		});
		
		return false;
	});
	
});