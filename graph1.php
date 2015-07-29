<?php
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_line.php');
include('init.php');

function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' )
{
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);
   
    $interval = date_diff($datetime1, $datetime2);
   
    return $interval->format($differenceFormat);
   
}
function score_progression()
{
	$database = Database::getConnection();
	$start = "2014-9-3";
	$today = date( "Y-m-d" );
	$date = $start;

	$names = array();

	for( $i = 0; $i <= (int) dateDifference($start, $today); $i++ )
	{
		$date = date( 'Y-m-d', strtotime( $date. ' + 1 days' ) );
		$result = $database->query
		(
			"SELECT
				teams.id,
				teams.name,
				teams.score
			FROM
				teams
			ORDER BY
				teams.id ASC"
		);

		while( $reply = mysqli_fetch_assoc( $result ) )
		{
			${ "data" . $reply['id'] }[$i] = $reply['score'];
			if( $i == 0 )
			{
				array_push( $names, $reply['name'] );
			}
		}
		
	}

	$graph = new Graph(1920,1080);
	$graph->SetScale('int');
	$theme_class = new UniversalTheme();
	$graph->SetTheme($theme_class);

	$graph->img->SetAntiAliasing(true);

	$graph->title->Set('CTF Score Progression');
	$graph->title->SetFont(FF_DV_SANSSERIF, FS_BOLD, 50);

	$graph->SetBox(false);
	$graph->img->SetAntiAliasing();

	//$graph->yaxis->HideZeroLabel();
	$graph->yaxis->HideLine(false);
	$graph->yaxis->HideTicks(false,false);
	$graph->legend->SetColumns(6);

	$graph->xgrid->Show();
	$graph->xgrid->SetLineStyle("solid");
	$graph->xgrid->SetColor('#E3E3E3');

	$counter = 0;
	$max = $database->query( "SELECT MAX(id) AS max FROM teams" );
	$maxi = mysqli_fetch_assoc( $max );
	for( $i = 0; $i <= $maxi['max']; $i++ )
	{
		if( isset( ${ "data" . $i } ) )
		{
			${ "p" . $i } = $p1 = new LinePlot( ${ "data" . $i } );
			$graph->Add( ${ "p" . $i } );
			$p1->SetLegend( $names[$counter] );
			$counter += 1;
		}
	}

	$graph->legend->SetFrameWeight(1);

	// Output line
	$graph->Stroke();
}

function attack_progression()
{
	global $database;
	$start = "2014-11-1";
	$today = date( "Y-m-d" );
	$date = $start;

	$names = array();

	for( $i = 0; $i <= (int) dateDifference($start, $today); $i++ )
	{
		$date = date( 'Y-m-d', strtotime( $date. ' + 1 days' ) );
		$result = $database->query
		(
			"SELECT
				teams.id,
				teams.name,
				(
					SELECT COALESCE( (SUM( interact.value ) * -1), 0 ) FROM interact WHERE interact.from = teams.id AND interact.date <= '$date'
				) AS ap
			FROM
				teams"
		);

		while( $reply = mysqli_fetch_assoc( $result ) )
		{
			${ "data" . $reply['id'] }[$i] = $reply['ap'];
			if( $i == 0 )
			{
				array_push( $names, $reply['name'] );
			}
		}
		
	}

	$graph = new Graph(1920,1080);
	$graph->SetScale('int');
	$graph->yaxis->scale->SetGrace(10);

	$theme_class = new UniversalTheme();
	$graph->SetTheme($theme_class);

	$graph->img->SetAntiAliasing(true);

	$graph->title->Set('CTF AP Progression');
	$graph->title->SetFont(FF_DV_SANSSERIF, FS_BOLD, 50);

	$graph->SetBox(false);
	$graph->img->SetAntiAliasing();

	//$graph->yaxis->HideZeroLabel();
	$graph->yaxis->HideLine(false);
	$graph->yaxis->HideTicks(false,false);


	$graph->xgrid->Show();
	$graph->xgrid->SetLineStyle("solid");
	$graph->xgrid->SetColor('#E3E3E3');

	$counter = 0;

	$max = $database->query( "SELECT MAX(id) AS max FROM teams" );
	$maxi = mysqli_fetch_assoc( $max );

	for( $i = 0; $i <= $maxi['max']; $i++ )
	{
		if( isset( ${ "data" . $i } ) )
		{
			${ "p" . $i } = $p1 = new LinePlot( ${ "data" . $i } );
			$graph->Add( ${ "p" . $i } );
			$p1->SetLegend( $names[$counter] );
			$counter += 1;
		}
	}

	$graph->legend->SetFrameWeight(1);

	// Output line
	$graph->Stroke();
}

score_progression();
?>