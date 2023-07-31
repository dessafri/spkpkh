<?php
session_start();
require './functions.php';
$role = $_SESSION["role"];
if ($_SESSION['id'] != '1') {
    header('location: login.php');
    exit();
}

if(isset($_POST["submit_logout"])){
  logout($_POST);
}
if(isset($_POST["submit_kriteria"])){
  buatKriteria($_POST);
}
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(nilai_perangkingan) as total FROM perangkingan"));
if($total["total"] > 1){
}else{
    buatHasil($_POST);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style/scss/bootstrap.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap4.min.css" />
    <style>
    .swal2-popup {
        font-size: 12px !important;
        font-family: Georgia, serif;
    }

    h2 {
        margin-top: 30px;
        margin-bottom: 30px;
        font-size: 18px;
    }
    </style>
    <title>Entropy</title>
</head>

<body>
    <section class="header">
        <div class="container">
            <?php 
            include('navbar.php')
            ?>
        </div>
    </section>
    <section class="content">
        <div class="container">
            <div style="display: flex; justify-content: space-between">
                <h1 class="h1-brand" style="font-size:22px;">KNN</h1>
            </div>

            <div class="normalisasi-data">
                <h2>Data Train</h2>
                <table id="example" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="table-data">
                        <tr>
                            <th>No</th>
                            <th>PESERTA</th>
                            <?php
                            $dataKriteria = query("SELECT * FROM kriteria");
                            $no = 1;
                            foreach($dataKriteria as $dataKriteria):?>
                            <th><?=$dataKriteria["nama_kriteria"]?></th>
                            <?php endforeach; ?>
                            <th>Label</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dataPeserta = query("SELECT * FROM peserta");
                        $index = 1;
                        foreach($dataPeserta as $dataPeserta):
                            $nikPeserta = $dataPeserta["id_peserta"];
                        ?>
                        <tr>
                            <td><?= $index++?></td>
                            <td><?= $dataPeserta["nama"]?></td>
                            <?php
                            $dataKriteriaPeserta = query("SELECT * FROM datatrain WHERE id_peserta = $nikPeserta ");
                            foreach($dataKriteriaPeserta as $dataKriteriaPeserta):
                            ?>
                            <td><?= $dataKriteriaPeserta["value_datatrain"] ?></td>
                            <?php endforeach; ?>
                            <td>
                                <?= $dataPeserta["label"] ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="entropy-atribut">
                <h2>Data Test</h2>
                <table id="tabel2" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="table-data">
                        <tr>
                            <th>No</th>
                            <th>PESERTA</th>
                            <?php
                            $dataKriteria = query("SELECT * FROM kriteria");
                            $no = 1;
                            foreach($dataKriteria as $dataKriteria):?>
                            <th><?=$dataKriteria["nama_kriteria"]?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dataPeserta = query("SELECT peserta.nama, data_test.id_peserta FROM data_test LEFT JOIN peserta ON data_test.id_peserta = peserta.id_peserta GROUP BY peserta.nama");
                        $index = 1;
                        foreach($dataPeserta as $dataPeserta):
                            $nikPeserta = $dataPeserta["id_peserta"];
                        ?>
                        <tr>
                            <td><?= $index++?></td>
                            <td><?= $dataPeserta["nama"]?></td>
                            <?php
                            $dataKriteriaPeserta = query("SELECT * FROM data_test WHERE id_peserta = $nikPeserta ");
                            foreach($dataKriteriaPeserta as $dataKriteriaPeserta):
                            ?>
                            <td><?= $dataKriteriaPeserta["value_data_test"] ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="menghitung-bobot-entropy" style="margin-bottom:100px">
                <h2>Menghitung Jarak</h2>
                <table id="tabel3" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="table-data">
                        <tr>
                            <th>ALTERNATIF</th>
                            <?php
                            $dataKriteria = query("SELECT DISTINCT data_test.id_peserta FROM data_test");
                            $no = 1;
                            foreach($dataKriteria as $dataKriteria):?>
                            <th><?=$dataKriteria["id_peserta"]?></th>
                            <?php endforeach; ?>
                            <th>LABEL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dataPeserta = query("SELECT jarak_matrix.id_peserta, peserta.label FROM jarak_matrix LEFT JOIN peserta ON jarak_matrix.id_peserta = peserta.id_peserta GROUP BY jarak_matrix.id_peserta");
                        $index = 1;
                        foreach($dataPeserta as $dataPeserta):
                            $nikPeserta = $dataPeserta["id_peserta"];
                        ?>
                        <tr>
                            <td><?= $dataPeserta["id_peserta"]?></td>
                            <?php
                            $dataKriteriaPeserta = query("SELECT * FROM jarak_matrix WHERE id_peserta = $nikPeserta ");
                            foreach($dataKriteriaPeserta as $dataKriteriaPeserta):
                            ?>
                            <td><?= $dataKriteriaPeserta["nilai_jarak"] ?></td>
                            <?php endforeach; ?>
                            <td><?= $dataPeserta["label"] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="menghitung-bobot-entropy" style="margin-bottom:50px; width:100%">
                <h2>Jarak Terkecil</h2>
                <div class="container">
                    <div class="row">
                        <?php 
                $resultPesertaDataTes = query("SELECT DISTINCT id_peserta FROM data_test");
                foreach($resultPesertaDataTes as $resultTes):
                    ?>
                        <div class="col-md-6">
                            <table id="tabel3" class="table table-striped table-bordered" style="width: 100%">
                                <thead class="table-data">
                                    <tr>
                                        <th>ALTERNATIF</th>
                                        <th><?=$resultTes["id_peserta"] ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                        $idPesertaTesHapus = $resultTes["id_peserta"];
                        $resultTerkecil = query("SELECT * FROM (SELECT * FROM jarak_matrix WHERE id_peserta_data_tes = $idPesertaTesHapus) AS A ORDER BY nilai_jarak ASC LIMIT 3");
                        foreach($resultTerkecil as $resultTerkecil):
                        ?>
                                    <tr>
                                        <td><?= $resultTerkecil["id_peserta"]?></td>
                                        <td><?= $resultTerkecil["nilai_jarak"] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div id="perhitunganakurasi" style="margin-bottom: 50px;">
                <?php
                $datapkh = query("SELECT COUNT(id_peserta) as total_pkh FROM peserta WHERE label LIKE '%PKH%'");
                $databp = query("SELECT COUNT(id_peserta) as total_bp FROM peserta WHERE label LIKE '%Bukan Penerima%'");
                $datablt = query("SELECT COUNT(id_peserta) as total_blt FROM peserta WHERE label LIKE '%BLT - DD%'");
                $databst = query("SELECT COUNT(id_peserta) as total_bst FROM peserta WHERE label LIKE '%BST%'");
                $total = (($datapkh[0]['total_pkh']*$databp[0]['total_bp']*$datablt[0]['total_blt']*$databst[0]['total_bst']) / ($datapkh[0]['total_pkh']*$databp[0]['total_bp']*$datablt[0]['total_blt']*$databst[0]['total_bst'])) * 100;
                ?>
                <h2>RUMUS AKURASI</h2>
                <p>(PKH x BUKAN PENERIMA x BLT x BST) / (PKH x BUKAN PENERIMA x BLT x BST)</p>
                <span>(<?=$datapkh[0]['total_pkh']?> x <?=$databp[0]['total_bp']?> x <?=$datablt[0]['total_blt']?> x <?=$databst[0]['total_bst']?>) / </span><span>(<?=$datapkh[0]['total_pkh']?> x <?=$databp[0]['total_bp']?> x <?=$datablt[0]['total_blt']?> x <?=$databst[0]['total_bst']?>)</span> <br>
                <span class="d-inline-block mt-2 font-weight-bold">TOTAL AKURASI : <?= $total ?> % </span>
            </div>
        </div>
    </section>

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js"
        integrity="sha512-6PM0qYu5KExuNcKt5bURAoT6KCThUmHRewN3zUFNaoI6Di7XJPTMoT6K0nsagZKk2OB4L7E3q1uQKHNHd4stIQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
    $(document).ready(function() {
        var table = $("#example").DataTable({
            lengthChange: true,
            buttons: [{
                    extend: "excel",
                    text: "Export Excel",
                    className: "btn-success",
                },
                {
                    extend: "spacer",
                    style: "bar",
                },
                {
                    extend: "pdf",
                    text: "Export PDF",
                    className: "btn-danger"
                },
                {
                    extend: "spacer",
                    style: "bar",
                },
                {
                    extend: "colvis",
                    text: "SORTIR"
                },
            ],
        });
        table
            .buttons()
            .container()
            .appendTo("#example_wrapper .col-md-6:eq(0)");
        var table = $("#tabel2").DataTable({
            lengthChange: true,
            buttons: [{
                    extend: "excel",
                    text: "Export Excel",
                    className: "btn-success",
                },
                {
                    extend: "spacer",
                    style: "bar",
                },
                {
                    extend: "pdf",
                    text: "Export PDF",
                    className: "btn-danger"
                },
                {
                    extend: "spacer",
                    style: "bar",
                },
                {
                    extend: "colvis",
                    text: "SORTIR"
                },
            ],
        });
        table
            .buttons()
            .container()
            .appendTo("#tabel2_wrapper .col-md-6:eq(0)");
        var table = $("#tabel3").DataTable({
            lengthChange: true,
            buttons: [{
                    extend: "excel",
                    text: "Export Excel",
                    className: "btn-success",
                },
                {
                    extend: "spacer",
                    style: "bar",
                },
                {
                    extend: "pdf",
                    text: "Export PDF",
                    className: "btn-danger"
                },
                {
                    extend: "spacer",
                    style: "bar",
                },
                {
                    extend: "colvis",
                    text: "SORTIR"
                },
            ],
        });
        table
            .buttons()
            .container()
            .appendTo("#tabel3_wrapper .col-md-6:eq(0)");
    });
    </script>
</body>

</html>