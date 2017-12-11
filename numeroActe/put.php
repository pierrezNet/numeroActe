<?php
$fichier = $_POST['filename'];

$inp = file_get_contents("{$fichier}", FILE_USE_INCLUDE_PATH);
$tempArray = json_decode($inp, true);

// quel numéro
$max_value = max($tempArray["actes"]);
$numero = intval($max_value["numero"]+1);
$prefixe = $_POST['pref'];
$zero = $_POST['zero'];

$numeroActe = $prefixe . str_pad($numero, $zero, '0', STR_PAD_LEFT);

$data = array("numero"=>$numero, "code"=>$numeroActe, "libelle"=>$_POST['libelle']);
array_push($tempArray["actes"], $data);
$jsonData = json_encode($tempArray);
file_put_contents("{$fichier}", $jsonData);

?>