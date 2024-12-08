<?php

include("Liste_Patients_Essai.php");

$id_essai=9;
$test_liste=Liste_Patients_Essais($conn, $id_essai);

$conn = null;
?>
