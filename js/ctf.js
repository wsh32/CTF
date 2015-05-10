$(document).ready(function(){
	//initialize code here
	
});

function update_ranking()
{
	$.get
	(
		'ajax.php?m=get_ranking',
		function( xml )
		{
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
	);
}