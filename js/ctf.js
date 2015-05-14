//Globals
var loggedin = false;
var token;

var helix = false;

function load_session()
{
	$.get
	(
		'ajax.php?m=get_session',
		function( xml )
		{
			if( $( xml ).find( 'loggedin' ).text() == '1' )
			{
				login();
			}
			else
			{
				logout();
			}

			token = $( xml ).find( 'token' ).text();
		}
	);
}

function login()	{
	loggedin = true;
}

function logout()	{
	loggedin = false;
}

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
	$.ajax({
		type: 'POST',
		url: 'ajax.php?m=get_challenges',
		processData: false,
		contentType: false,
		success: function(xml) {
			var list = "";
			
			$( xml ).find( 'challenge' ).each
			(
				function()
				{
					var fd = new FormData();
					fd.append( 'id', $( this ).find( 'id' ).text() );
					
					$.ajax({
						type: 'POST',
						url: 'ajax.php?m=get_challenge',
						data: fd,
						processData: false,
						contentType: false,
						success: function(xml) {
							
							var title = $( xml ).find( 'title' ).text();
							var score = $( xml ).find( 'score' ).text();
							var id = $( xml ).find( 'id' ).text();
							
							list += '<a href="#answer" onclick="challenge_load('+id+')" class="collection-item">'+title+': '+score+'</a>';
							
							$( '#challenges' ).html( list );
						}
					});
				}
			);
		}
	});
	return false;
}

function challenge_load(id)	{	
	var fd = new FormData();
	fd.append( 'id', id );

	$.ajax({
		type: 'POST',
		url: 'ajax.php?m=get_challenge',
		data: fd,
		processData: false,
		contentType: false,
		success: function(xml) {
			
			var title = $( xml ).find( 'title' ).text();
			var description = $( xml ).find( 'description' ).text();
			var score = $( xml ).find( 'score' ).text();
			
			$( '#challenge_title' ).text( title + ": " + score );
			$( '#desc' ).html( '<p class="center">'+description+'</p>' );
			$('#id').val(id);
		}
	});
}

$(document).ready(function(){
	load_session();
});