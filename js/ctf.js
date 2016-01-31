//Globals
var loggedin = false;
var token;
var teamname = '';

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
				teamname = $( xml ).find( 'teamname' ).text();
			}
			else
			{
				logout();
				teamname = '';
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
			$( '<td><b>' ).text( 'Username' ).appendTo( thead );
			$( '<td><b>' ).text( 'School' ).appendTo( thead );
			$( '<td><b>' ).text( 'Score' ).appendTo( thead );
			thead.appendTo(table);
			
			$( xml ).find( 'team' ).each
			(
				function()
				{
					var trow = $( '<tr>' );
					$( '<td>' ).text( $( this ).find( 'rank' ).text() ).appendTo( trow );
					$( '<td>' ).text( $( this ).find( 'name' ).text() ).appendTo( trow );
					$( '<td>' ).text( $( this ).find( 'school' ).text() ).appendTo( trow );
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
			$( '<td><b>' ).text( 'Username' ).appendTo( thead );
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
			var delimiter = "--=+=+=+=+=--"
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

function index_state()	{
	$.ajax({
		type: 'GET',
		url: 'ajax.php?m=get_state',
		processData: false,
		contentType: false,
		success: function(xml) {
			$( xml ).find( 'info' ).each
			(
				function()
				{
					var state = $( this ).find('state').text();
					var s = ''
					if(state == '6')	{
						s = 'The competition has not started yet!';
					}	else if(state == '1')	{
						s = 'It\'s the SPRINT round!';
					}	else if(state == '2')	{
						s = 'We are currently on a break right now. Check back later.';
					}	else if(state == '3')	{
						s = 'It\'s the TARGET round!';
					}	else if(state == '4')	{
						s = 'The competition is over! Your results have been recorded and the awards will be sent out soon.';
					}
					console.log(s);
					$( '#state' ).text( s );
				}
			);
			
		},
		error: function(data)	{
			console.log(data);
		}
	});
	return false;
}

function projector_state()	{
	$.ajax({
		type: 'GET',
		url: 'ajax.php?m=get_state',
		processData: false,
		contentType: false,
		success: function(xml) {
			$( xml ).find( 'info' ).each
			(
				function()
				{
					var state = $( this ).find('state').text();
					var s = ''
					if(state == '6')	{
						s = 'The competition has not started yet!';
					}	else if(state == '1')	{
						s = 'Sprint Round: 45 Minutes';
					}	else if(state == '2')	{
						s = 'Break: 5 Minutes';
					}	else if(state == '3')	{
						s = 'Target Round: 30 Minutes';
					}	else if(state == '4')	{
						s = 'The competition is over!';
					}
					console.log(s);
					$( '#state' ).text( s );
				}
			);
			
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
			var array = new Array();
			var groupid = new Array();
			console.log(xml);
			$( xml ).find( 'challenge' ).each
			(
				function()
				{
					var fd = new FormData();
					fd.append( 'id', $( this ).find( 'id' ).text() );
					var solved = $( this ).find( 'solved' ).text();
					var locked = $( this ).find( 'locked' ).text();
					var group = $( this ).find( 'groupid' ).text();
					
					var title = $( this ).find( 'title' ).text();
					var score = $( this ).find( 'score' ).text();
					var id = $( this ).find( 'id' ).text();
					
					var list = '';
					
					if(solved == '1')	{
						list = '<a onclick="challenge_load('+id+');$(\'ul.tabs\').tabs(\'select_tab\', \'answer\');" class="green lighten-4 accent-1 collection-item blue-text text-darken-2" id="q">'+title+'</a>';
					}	else if(locked == '1')	{
						list = '<a onclick="challenge_load('+id+');$(\'ul.tabs\').tabs(\'select_tab\', \'answer\');" class="grey lighten-2 collection-item blue-text text-darken-2" id="q">'+title+'</a>';
					}	else	{
						list = '<a onclick="challenge_load('+id+');$(\'ul.tabs\').tabs(\'select_tab\', \'answer\');" class="collection-item blue-text text-darken-2" id="q">'+title+'</a>';
					}
							
					array[parseInt(id)] = list;
					groupid[parseInt(id)] = parseInt(group);
				}
			);
			
			setTimeout(function(){
				var a = '';
				console.log(array);
				console.log(groupid);
				for(var i = 0; i < array.length; i++)	{
					if(array[i]!=null)	{
						a+=array[i];
					}
				}
				$( '#a' ).html(a);
			}, 500);
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
			$( '#desc' ).html( '<div class="center"><p>'+description+'</p></div>' );
			MathJax.Hub.Queue(["Typeset",MathJax.Hub,'p']);
			$('#id').val(id);
			return false;
		}
	});
}

$(document).ready(function(){
	load_session();
});
