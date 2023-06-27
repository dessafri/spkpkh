<?php
require './functions.php';

$idPeserta = $_POST['id'];

$data = query("SELECT peserta.id_peserta, peserta.nama, kriteria.nama_kriteria, detail_kriteria.keterangan, detail_kriteria.nilai FROM jawaban JOIN peserta ON jawaban.id_peserta = peserta.id_peserta JOIN kriteria ON jawaban.id_kriteria = kriteria.id_kriteria JOIN detail_kriteria ON detail_kriteria.id_kriteria = jawaban.id_kriteria WHERE peserta.id_peserta = '$idPeserta' AND detail_kriteria.nilai = jawaban.jawaban_peserta");

echo json_encode($data);