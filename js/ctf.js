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

function index_ranking()	{	
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
			var i = 0;
			$( xml ).find( 'team' ).each
			(
				function()
				{
					if(i < 3)	{
						var trow = $( '<tr>' );
						$( '<td>' ).text( $( this ).find( 'rank' ).text() ).appendTo( trow );
						$( '<td>' ).text( $( this ).find( 'name' ).text() ).appendTo( trow );
						$( '<td>' ).addClass( 'score' ).text( $( this ).find( 'score' ).text() ).appendTo( trow );
						trow.appendTo( table );
					}
					i++;
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

function index_solves()	{	
	$.ajax({
		type: 'GET',
		url: 'ajax.php?m=get_solves',
		processData: false,
		contentType: false,
		success: function(xml) {
			var list = $( '<ul>' );
			var delimiter = "-=+=+=-"
			$( xml ).find( 'solve' ).each
			(
				function()
				{
					var l = $( '<li>' );
					l.text( delimiter ).appendTo( list );
					var li = $( '<li>' );
					var b1 = $( '<b>' );
					var p = $( '<p>' );
					var b2 = $( '<b>' );
					//b1.text( $( this ).find('teamname').text() ).last().after( li );
					//p.text( ' recently solved ' ).last().after( li );
					//b2.text( $( this ).find('challenge').text() ).last().after( li );
					li.text( $( this ).find('teamname').text() + ' recently solved ' + $( this ).find('challenge').text() ).appendTo( list );
					//li.appendTo( list );
				}
			);
			
			var l = $( '<li>' );
			l.text( delimiter ).appendTo( list );
			
			$( '#recentsolves' ).html( list.html() );
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
							
							list += '<a onclick="challenge_load('+id+');$(\'ul.tabs\').tabs(\'select_tab\', \'answer\');" class="collection-item" id="q">'+title+': '+score+'</a>';
							
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
			return false;
		}
	});
}

$(document).ready(function(){
	load_session();
});
