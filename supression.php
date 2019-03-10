<?php

/*conexion connection a la base de données msql*/

try{
    $db = new PDO ('mysql:host=localhost:3306;dbname=evenements;charset=utf8',
                    'root','', array (PDO::ATTR_ERRMODE => PDO:: ERRMODE_EXCEPTION));

}catch (PDOException $e){
    print "Erreur !:". $e->getMessage(). "</br>";
    die();
}
if (isset($_REQUEST["id"])){
	$id = $_REQUEST["id"];
	if (!empty($id) && is_numeric($id)) {
		$db->exec("DELETE FROM evenements WHERE id=".$_REQUEST['id']);
		header ("location:calendrier.php");
	}
}
?>
