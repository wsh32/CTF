function update_ranking()	{	
	$.ajax({
		type: 'GET',
		url: 'ajax.php?m=get_ranking',
		processData: false,
		contentType: false,
		success: function(xml) {
			var table = $( '<table>' );
			
			var thead = $( '<thead><tr>' );
			$( '<td>' ).text( 'Rank' ).appendTo( thead );
			$( '<td>' ).text( 'Team Name' ).appendTo( thead );
			$( '<td>' ).text( 'Score' ).appendTo( thead );
			thead.appendTo(table);
			
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
		},
		error: function(data)	{
			console.log(data);
		}
	});
	return false;
}

function last_solves()	{
	$.ajax({
		type: 'GET',
		url: 'ajax.php?m=get_attacks',
		processData: false,
		contentType: false,
		success: function(xml) {
			var div = $( '<div>' );
			
			$( xml ).find( 'solve' ).each
			(
				function()
				{
					var row = $( '<p>' );
					$( '<b>' ).text( $( this ).find( 'team' ).text() ).appendTo( row );
					$().text( ' recently solved ' ).appendTo( row );
					$( '<b>' ).text( $( this ).find( 'challenge' ).text() ).appendTo( row );
					
					row.appendTo( div );
				}
			);

			$( '#solves' ).html( table.html() );
		},
		error: function(data)	{
			console.log(data);
		}
	});
	return false;
}