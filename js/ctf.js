function update_ranking()
{	
	$.ajax({
		type: 'GET',
		url: 'ajax.php',
		data: 'm=get_ranking',
		processData: false,
		contentType: false,
		success: function(xml) {
			var table = $( '<table>' );

			$( xml ).find( 'team' ).each
			(
				function()
				{
					var trow = $( '<tr>' );
					$( '<td>' ).text( $( this ).find( 'rank' ).text() ).appendTo( trow );
					$( '<td>' ).text( $( this ).find( 'name' ).text() ).appendTo( trow );
					$( '<td>' ).addClass( 'score' ).text( $( this ).find( 'score' ).text() ).appendTo( trow );

					trow.appendTo( table );
				}
			);

			$( '#ranking' ).html( table.html() );
		}
	});
	return false;
}