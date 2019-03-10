<?php

/*conexion connection a la base de données msql*/

try{
    $db = new PDO ('mysql:host=localhost:3306;dbname=evenements;charset=utf8',
                    'root','', array (PDO::ATTR_ERRMODE => PDO:: ERRMODE_EXCEPTION));

}catch (PDOException $e){
    print "Erreur !:". $e->getMessage(). "</br>";
    die();
}

?>

<?php
//oouvre la session 	
//session_start();

// L'utilisateur est-il identifié? (condition pour verifier si l'itilusateur est identifié )
if ( ! isset( $_COOKIE['user'] ) )
{
	// Si non, attribution d'une chaine aléatoire
	setcookie( 'user', rand(), time()+60*60*24*30 ); // 30j en secondes
}
else
{
	// Si oui, on prolonge de 30j le cookie
	setcookie( 'user', $_COOKIE['user'], time()+60*60*24*30 ); // 30j en secondes	
}

// Mois courant passé par paramètre GENERE  // question au prof ?
if ( isset( $_REQUEST['month'] ) )
{
	$mois_courant = (int)$_REQUEST['month'];
}
// Mois enregistré en cookie 
elseif ( isset( $_COOKIE['mois_courant'] ) )
{
	$mois_courant = (int)$_COOKIE['mois_courant'];
}
else
{
	$mois_courant = date( 'n' );
}

// Année courante passé par paramètre
if ( isset( $_REQUEST['year'] ) )
{
	$années_courante = (int)$_REQUEST['year'];
} 
// Annnée enregistrée en cookie 
elseif ( isset( $_COOKIE['années_courante'] ) )
{
	$années_courante = (int)$_COOKIE['années_courante'];
}
else
{
	$années_courante = date( 'Y' );
}

// Enregistrement en cookies
setcookie( 'mois_courant', $mois_courant, time()+60*60*24*30 ); // 30j en secondes 
setcookie( 'années_courante', $années_courante, time()+60*60*24*30 ); // 30j en secondes 

// Enregistrement d'un événement
if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'save' && isset( $_REQUEST['date'] ) )
{
	$title = isset( $_REQUEST['title'] ) ? $_REQUEST['title'] : '';

	// Récupération des précédents événements
	$events = $_SESSION['events'];
	
	$new_event = array( 'title' => $title );
	
	// Bonus: ajout d'une image
	if ( isset( $_FILES['image'] ) && $_FILES['image']['size'] )
	{
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
			
		if ( finfo_file($finfo, $_FILES['image']['tmp_name']) == 'image/jpeg' )
		{
			move_uploaded_file( $_FILES['image']['tmp_name'], 'upload/' . $_FILES['image']['name'] );
			
			$new_event['image'] = $_FILES['image']['name'];
		}
	}

	//REQETE INSERTION 
	$db->exec('INSERT INTO evenements SET date= "'.$_REQUEST['date'].'", title = "'.$_REQUEST['title'].'", `image-name`= "'.$_REQUEST['image'].'", creator= 1'); // pas mettre id car auto incrémentée
	$db->exec("INSERT INTO evenements(`title`, `date`, `image-name`, `creator`) VALUES ('".  $new_event['title'] . "','" . $_REQUEST['date'] . "','" . $new_event['image'] . "','" .$_COOKIE['user']."')");


	//REQUETE SUPRESSION 
	if (isset($_REQUEST["id"])){
		$id = $_REQUEST["id"];
		if (!empty($id) && is_numeric($id)) {
			$db->exec("DELETE FROM evenements WHERE id=".$_REQUEST['id']);
		
		}
	}


	//requete modification 

	if(isset($_POST["modifier"])){
		$requete ="UPDATE evenements SET title=' ". $_POST['title'] . "'"
		. "WHERE title='". $_POST['TITLEORIGINAL']."'";
		$resultat = mysql_query($requete, $connexion);

	}
}
?>
<html lang="en" class="">
<head>
	<meta charset="UTF-8">
	<meta name="robots" content="noindex">
	<style class="cp-pen-styles" type="text/css">
	* {
		-webkit-font-smoothing: antialiased;
	}

	body {
		font-family: 'helvetica neue';
		background-color: #A25200;
		margin: 0;
	}

	.wrapp {
		width: 450px;
		margin: 30px auto;
		flex-direction: row;
		flex-wrap: wrap;
		justify-content: center;
		align-content: center;
		align-items: center;
		box-shadow: 0 0 10px rgba(54, 27, 0, 0.5);
	}

	.flex-calendar .days,.flex-calendar .days .day.selected,.flex-calendar .month,.flex-calendar .week{
		display:-webkit-box;
		display:-webkit-flex;
		display:-ms-flexbox;
	}
	.flex-calendar{
		width:100%;
		min-height:50px;
		color:#FFF;
		font-weight:200
	}
	.flex-calendar .month {
		position:relative;
		display:flex;
		flex-direction:row;
		flex-wrap: nowrap;
		-webkit-justify-content:space-between;
				justify-content:space-between;
		align-content:flex-start;
		align-items:flex-start;
		background-color:#ffb835;
	}
	
	.flex-calendar .month .arrow,.flex-calendar .month .label {
		height:60px;
		order:0;
		flex:0 1 auto;
		align-self:auto;
		line-height:60px;
		font-size:20px;
	}
	
	.flex-calendar .month .arrow {
		width:50px;
		box-sizing:border-box;
		background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAABqUlEQVR4Xt3b0U3EMBCE4XEFUAolHB0clUFHUAJ0cldBkKUgnRDh7PWsd9Z5Tpz8nyxFspOCJMe2bU8AXgG8lFIurMcurIE8x9nj3wE8AvgE8MxCkAf4Ff/jTEOQBjiIpyLIAtyJpyFIAjTGUxDkADrjhxGkAIzxQwgyAIPxZgQJAFJ8RbgCOJVS6muy6QgHiIyvQqEA0fGhAArxYQAq8SEASvHTAdTipwIoxk8DUI2fAqAc7w6gHu8KkCHeDSBLvAtApng6QLZ4KkDGeBpA1ngKQOb4YYDs8UMAK8SbAVaJNwGsFN8NsFq8FeADwEPTmvPxSXV/v25xNy9fD97v8PLuVeF9FiyD0A1QKVdCMAGshGAGWAVhCGAFhGGA7AgUgMwINICsCFSAjAh0gGwILgCZENwAsiC4AmRAcAdQR5gCoIwwDUAVYSqAIsJ0ADWEEAAlhDAAFYRQAAWEcIBoBAkAIsLX/rV48291MgAEhO747o0Rr82J23GNS+6meEkAw0wwx8sCdCAMxUsDNCAMx8sD/INAiU8B8AcCLT4NwA3CG4Az68/xOu43keZ+UGLOkN4AAAAASUVORK5CYII=) no-repeat;
		background-size:contain;
		background-origin:content-box;
		padding:15px 5px;
		cursor:pointer;
	}
	
	.flex-calendar .month .arrow:last-child {
		-webkit-transform:rotate(180deg);
			-ms-transform:rotate(180deg);
				transform:rotate(180deg);
	}
	
	.flex-calendar .month .arrow.visible {
		opacity:1;
		visibility:visible;
		cursor:pointer;
	}
	
	.flex-calendar .month .arrow.hidden {
		opacity:0;
		visibility:hidden;
		cursor:default;
	}
	
	.flex-calendar .days,.flex-calendar .week {
		line-height:25px;
		font-size:16px;
		display:flex;
		-webkit-flex-wrap: wrap;
				flex-wrap: wrap;
	}
	
	.flex-calendar .days {
		background-color:#FFF;
	}
	
	.flex-calendar .week {
		background-color:#faac1c;
	}
	
	.flex-calendar .days .day,.flex-calendar .week .day {
		flex-grow:0;
		-webkit-flex-basis: calc( 100% / 7 );
		min-width: calc( 100% / 7 );
		text-align:center;
	}
	
	.flex-calendar .days .day {
		min-height:60px;
		box-sizing:border-box;
		position:relative;
		line-height:60px;
		border-top:1px solid #FCFCFC;
		background-color:#fff;
		color:#8B8B8B;
		-webkit-transition:all .3s ease;
				transition:all .3s ease;
	}
	
	.flex-calendar .days .day.out {
		background-color:#fCFCFC;
	}
	
	.flex-calendar .days .day.disabled.today,.flex-calendar .days .day.today {
		color:#FFB835;
		border:1px solid;
	}
	
	.flex-calendar .days .day.selected {
		display:flex;
		flex-direction:row;
		flex-wrap:nowrap;
		-webkit-justify-content:center;
				justify-content:center;
		align-content:center;
		-webkit-align-items:center;
				align-items:center;
	}
	
	.flex-calendar .days .day.selected .number {
		width:40px;
		height:40px;
		background-color:#FFB835;
		border-radius:100%;
		line-height:40px;
		color:#FFF;
	}
	
	.flex-calendar .days .day:not(.disabled):not(.out) {
		cursor:pointer;
	}
	
	.flex-calendar .days .day.disabled {
		border:none;
	}
	
	.flex-calendar .days .day.disabled .number {
		background-color:#EFEFEF;
		background-image:url(data:image/gif;base64,R0lGODlhBQAFAOMAAP/14////93uHt3uHt3uHt3uHv///////////wAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAAAALAAAAAAFAAUAAAQL0ACAzpG0YnonNxEAOw==);
	}
	
	.flex-calendar .days .day.event:before {
		content:"";
		width:6px;
		height:6px;
		border-radius:100%;
		background-color:#faac1c;
		position:absolute;
		bottom:10px;
		margin-left:-3px;
	}
	
	.flex-calendar .days .day .infos{
		padding: 5px 10px;
		position: absolute;
		left: 50%; top: 100%;
		-webkit-transform: translateX(-50%);
				transform: translateX(-50%);
		z-index: 1;
		background: #faac1c;
		color: white;
		font-size: 14px;
		font-weight: bold;
		line-height: normal;
		white-space: nowrap;
		opacity: 0;
		pointer-events: none;
		-webkit-transition:all .3s ease;
				transition:all .3s ease;
	}
	.flex-calendar .days .day:hover .infos{ opacity: 1 }
	
	form{
		padding: 20px;
		position: relative;
		background: white;
		box-sizing: border-box;
	}
	
	form p{ margin: 0 }
	form p + p{ margin-top: 20px }
	
	form label{ color: #8B8B8B }
	
	form input{
		height: 30px;
		font-size: 12px;
	}
	
	form button{
		padding: 10px 20px;
		position: absolute;
		right: 20px; bottom: 20px;
		background: #faac1c;
		border: none;
		color: white;
		font-size: 18px;
	}
	
	#events_list{
		padding: 20px;
		box-sizing: border-box;
		background: white;
		color: #8b8b8b
	}
	
	#events_list h2{ margin: 0; font-weight: normal }
	
	#events_list a{
		font-size: 12px;
		color: #faac1c;
		text-decoration: none;
	}
	#events_list a:hover{ text-decoration: underline }
	</style>

	<title>Calendar</title>
</head>

<body>
	<div class="wrapp">
		<div class="flex-calendar">
			
			<?php
				// Mois/année en cours			
				$this_month = strtotime( $années_courante . '-' . $mois_courant );
			
				// Mois précédent - méthode 1
				if ( $mois_courant == 1 )
				{
					$previous_month = 12;
					$previous_year = $années_courante - 1;
				}
				else
				{
					$previous_month = $mois_courant - 1;
					$previous_year = $années_courante;
				}
				
				// Mois suivant - méthode 1
				if ( $mois_courant == 12 )
				{
					$next_month = 1;
					$next_year = $années_courante + 1;
				}
				else
				{
					$next_month = $mois_courant + 1;
					$next_year = $années_courante;
				}
				
				// Mois précédent - méthode 2
				$previous_month = date( 'm', strtotime( 'previous month', $this_month ) );
				$previous_year = date( 'Y', strtotime( 'previous month', $this_month ) );

				// Mois suivant - méthode 2
				$next_month = date( 'm', strtotime( 'next month', $this_month ) );
				$next_year = date( 'Y', strtotime( 'next month', $this_month ) );
				
			?>
			
			<div class="month">
				<a href="calendrier.php?year=<?php echo $previous_year ?>&month=<?php echo $previous_month ?>" class="arrow visible"></a>

				<div class="label">
					<?php echo date( 'F Y', $this_month ); ?>
				</div>

				<a href="calendrier.php?year=<?php echo $next_year ?>&month=<?php echo $next_month ?>" class="arrow visible"></a>
			</div>

			<div class="week">
				<div class="day">M</div>
				<div class="day">T</div>
				<div class="day">W</div>
				<div class="day">T</div>
				<div class="day">F</div>
				<div class="day">S</div>
				<div class="day">S</div>
			</div>

			<div class="days">
				
			<?php
				
				// Bornes du mois courant
				$first_day_of_month = date( 'N', strtotime( 'first day of ' . $années_courante . '-' . $mois_courant ) );
				$last_day_of_month = date( 'd', strtotime( 'last day of ' . $années_courante . '-' . $mois_courant ) );
				
				$today = new DateTime( 'today' );
				$disabled = array( new DateTime( '2018-05-21' ) );
				$events = array();
				
				// Récupération des événements en session
				$events = $_SESSION['events'];
								
				// Décalage premier jour du mois
				for ( $i = 1; $i < $first_day_of_month; $i++ )
				{
					echo '<div class="day out"><div class="number"></div></div>';
				}
				
				// Calendrier
				for( $i = 1; $i <= $last_day_of_month; $i++ )
				{
					$infos = '';
					$classes = 'day';
					
					// Convertion du jour en cours en objet DateTime
					$current_day = new DateTime( $années_courante . '-' . $mois_courant . '-' . $i );

					// Aujourd'hui?
					if ( $current_day == $today ) $classes .= ' selected';
					
					// Jour désactivé
					if ( in_array( $current_day, $disabled ) ) $classes .= ' disabled';
					
					// Jour avec événements?
					if ( isset( $events[$current_day->format( 'Y-m-d' )] ) ){
						$classes .= ' event';
						
						$event_text = '';
						foreach ( $events[$current_day->format( 'Y-m-d' )] as $event )
							$event_text .= $event['title'] . '<br />';
						
						$infos = '<div class="infos">' . $event_text . '</div>';
					}
					
					echo '<div class="' . $classes . '"><div class="number">' . $i . '</div>' . $infos . '</div>';
				}
			?>
			
			</div>
		</div>
	</div>
	
	<form class="wrapp" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="save" />
		<p>
			<label for="date">Date</label>
			<input type="date" name="date" id="date" value="<?php echo date( 'Y-m-d' ) ?>" required />
		</p>
		<p>
			<label for="title">Titre</label>
			<input type="text" name="title" id="title" size="40" value="" />
		</p>
		<p>
			<label for="image">Image</label>
			<input type="file" name="image" id="image" />
		</p>
		<button type="submit">Valider</button>

	
	</form>
	
	<div class="wrapp" id="events_list">
		<h2>Evénements</h2>
		<ul>
			<?php

		$reponse =$db->query('SELECT * FROM evenements');
			while($infos = $reponse->fetch (PDO::FETCH_ASSOC) )
			{
    			
			
			$current_date = new DateTime( $infos['date'] );//INFORMATION SUR LA DATE
							
			echo '<li><em>' . $current_date->format( 'd.m.Y' ) . '</em> - ' . $infos['title'];//mise en forme la date devant les information de l'evenement / 
							
			
					//BOUTON SURPESSION 
					echo '	<button><a href="supression.php?id='.$infos["id"].'"> Supprimé</a>	</button>';
					echo '	<button><a href="modifier.php?id='.$infos["id"].'"> modifier</a>	</button>';
					// Bonus
					if ( isset( $infos['image'] ) )
					echo '<br/><img src="upload/' . $infos['image'] . '" width="50" />';
			
					echo '</li>';

							
			}
				
			?>

<?php

/*lecture*/
//$reponse =$db->query('SELECT  * FROM evenements');
//while($infos = $reponse->fetch (PDO::FETCH_ASSOC) )
{
  //  echo '<ul>';print_r($infos);echo' </ul> ';
}

/*requete préparé
$query =$db->prepare('SELECT title, date FROM evenements WHERE title = :title');
$title='Le big data';

if($reponse =$query->execute(array(array(':variable'=>$title))){
    $infos = $query->fetch(PDO::FETCH_ASSOC);
      echo '<pre>';print_r($infos); echo'</pre>';
}
/


/* Ecriture*/
//form 1*/


//form 2*/
//$db->exec("INSERT INTO evenements SET date= '2018-04-25', title = 'Cours Php a CREA', creator = 1");


//$db ->exec("UPDATE evenements SET title = 'Cours Php a CREA 5' WHERE DATE = '2019-04-24'");


?>	
		</ul>
	</div>
	
</body>
</html>