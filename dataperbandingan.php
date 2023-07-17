<?php
require './functions.php';

$dataSelect = $_POST["value"];

$dataPeserta = query("SELECT peserta.id_peserta, peserta.nama, peserta.label, perangkingan.nilai_perangkingan FROM peserta JOIN perangkingan ON peserta.id_peserta = perangkingan.id_peserta WHERE peserta.label LIKE '%$dataSelect%'");

echo json_encode($dataPeserta);

?>