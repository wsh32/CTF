//Globals
var loggedin = false;

var helix = false;

function update_ranking()	{	
	$.ajax({
		type: 'GET',
		url: 'ajax.php?m=get_ranking',
		processData: false,
		contentType: false,
		success: function(xml) {
			var table = $( '<table>' );
			
			var thead = $( '<thead><tr>' );
			$( '<td><b>' ).text( 'Rank' ).appendTo( thead );
			$( '<td><b>' ).text( 'Team Name' ).appendTo( thead );
			$( '<td><b>' ).text( 'Score' ).appendTo( thead );
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

function get_challenges()	{
	if(!loggedin)	{
		return false;
	}
	
	$.ajax({
		type: 'POST',
		url: 'ajax.php?m=get_challenges',
		processData: false,
		contentType: false,
		success: function(xml) {
			var list = $( '<ul>' );
			
			$( xml ).find( 'challenge' ).each
			(
				function()
				{
					var fd = new FormData();
					formData.append( 'id', $( this ).find( 'id' ).text() );
					
					$.ajax({
						type: 'POST',
						url: 'ajax.php?m=get_challenge',
						data: fd,
						processData: false,
						contentType: false,
						success: function(xml) {
							var element = $( '<li>' );
							
							var title = $( xml ).find( 'title' ).text();
							var description = $( xml ).find( 'description' ).text();
							
							$( '<div class="collapsible-header">' ).text( title ).appendTo( element );
							$( '<div class="collapsible-body">' ).text( description ).appendTo( element );
							
							element.appendTo(list);
						}
					});
				}
			);

			$( '#challenges' ).html( list.html() );
		}
	});
	return false;
}