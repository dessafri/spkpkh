<?php
session_start();
require './functions.php';
$role = $_SESSION["role"];
if ($_SESSION['id'] != '1') {
    header('location: login.php');
    exit();
}

if (isset($_POST["submit_logout"])) {
    logout($_POST);
}
if (isset($_POST["submit_kriteria"])) {
    buatKriteria($_POST);
}
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(nilai_perangkingan) as total FROM perangkingan"));
if ($total["total"] > 1) {
} else {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
    <title>Waspass</title>
</head>

<body>
    <section class="header">
        <div class="container">
            <?php
            include('./navbar.php')
            ?>
        </div>
    </section>
    <section class="content">
        <div class="container">
            <div style="display: flex; justify-content: space-between">
                <h1 class="h1-brand" style="font-size:22px;">PERHITUNGAN WASPASS</h1>
            </div>

            <div class="normalisasi-data-mabac">
                <h2>Normalisasi Matrix</h2>
                <table id="example" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="table-data">
                        <tr>
                            <th>No</th>
                            <th>PESERTA</th>
                            <?php
                            $dataKriteria = query("SELECT * FROM kriteria");
                            $no = 1;
                            foreach ($dataKriteria as $dataKriteria) : ?>
                                <th><?= $dataKriteria["nama_kriteria"] ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dataPeserta = query("SELECT * FROM peserta");
                        $index = 1;
                        foreach ($dataPeserta as $dataPeserta) :
                            $nikPeserta = $dataPeserta["id_peserta"];
                        ?>
                            <tr>
                                <td><?= $index++ ?></td>
                                <td><?= $dataPeserta["nama"] ?></td>
                                <?php
                                $dataKriteriaPeserta = query("SELECT * FROM normalisasi_waspas WHERE id_peserta = $nikPeserta ");
                                foreach ($dataKriteriaPeserta as $dataKriteriaPeserta) :
                                ?>
                                    <td><?= $dataKriteriaPeserta["nilai_normalisasi"] ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="matrixtertimbangmabac">
                <h2>Menghitung Nilai Q</h2>
                <table id="tabel2" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="table-data">
                        <tr>
                            <th>No</th>
                            <th>PESERTA</th>
                            <?php
                            $dataKriteria = query("SELECT * FROM kriteria");
                            $no = 1;
                            foreach ($dataKriteria as $dataKriteria) : ?>
                                <th><?= $dataKriteria["nama_kriteria"] ?></th>
                            <?php endforeach; ?>
                            <th>Sigma</th>
                            <th>Kali 0.5</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dataPeserta = query("SELECT * FROM peserta");
                        $index = 1;
                        foreach ($dataPeserta as $dataPeserta) :
                            $nikPeserta = $dataPeserta["id_peserta"];
                        ?>
                            <tr>
                                <td><?= $index++ ?></td>
                                <td><?= $dataPeserta["nama"] ?></td>
                                <?php
                                $dataKriteriaPeserta = query("SELECT * FROM nilaiq WHERE id_peserta = $nikPeserta ");
                                foreach ($dataKriteriaPeserta as $dataKriteriaPeserta) :
                                ?>
                                    <td><?= $dataKriteriaPeserta["nilaiQ"] ?></td>
                                <?php endforeach; ?>
                                <?php
                                $datasigma = query("SELECT DISTINCT sigma FROM `nilaiq` WHERE id_peserta = $nikPeserta");
                                foreach ($datasigma as $data) :
                                ?>
                                    <td><?= $data["sigma"] ?></td>
                                <?php endforeach; ?>
                                <?php
                                $datasigmakali = query("SELECT DISTINCT nilaikali FROM `nilaiq` WHERE id_peserta = $nikPeserta");
                                foreach ($datasigmakali as $data) :
                                ?>
                                    <td><?= $data["nilaikali"] ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="matrixperbatasan-mabac">
                <h2>Lanjutan Nilai Q</h2>
                <table id="tabel3" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="table-data">
                        <tr>
                            <th>No</th>
                            <th>PESERTA</th>
                            <?php
                            $dataKriteria = query("SELECT * FROM kriteria");
                            $no = 1;
                            foreach ($dataKriteria as $dataKriteria) : ?>
                                <th><?= $dataKriteria["nama_kriteria"] ?></th>
                            <?php endforeach; ?>
                            <th>Sigma</th>
                            <th>Kali 0.5</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dataPeserta = query("SELECT * FROM peserta");
                        $index = 1;
                        foreach ($dataPeserta as $dataPeserta) :
                            $nikPeserta = $dataPeserta["id_peserta"];
                        ?>
                            <tr>
                                <td><?= $index++ ?></td>
                                <td><?= $dataPeserta["nama"] ?></td>
                                <?php
                                $dataKriteriaPeserta = query("SELECT * FROM lanjutanq WHERE id_peserta = $nikPeserta ");
                                foreach ($dataKriteriaPeserta as $dataKriteriaPeserta) :
                                ?>
                                    <td><?= $dataKriteriaPeserta["nilai_lanjutan"] ?></td>
                                <?php endforeach; ?>
                                <?php
                                $datasigma = query("SELECT DISTINCT product FROM `lanjutanq` WHERE id_peserta = $nikPeserta");
                                foreach ($datasigma as $data) :
                                ?>
                                    <td><?= $data["product"] ?></td>
                                <?php endforeach; ?>
                                <?php
                                $datasigmakali = query("SELECT DISTINCT nilaikalilanjutan FROM `lanjutanq` WHERE id_peserta = $nikPeserta");
                                foreach ($datasigmakali as $data) :
                                ?>
                                    <td><?= $data["nilaikalilanjutan"] ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="alternatifPerbatasanMabac" style="margin-bottom: 100px;">
                <h2>Perangkingan</h2>
                <table id="tabel4" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="table-data">
                        <tr>
                            <th>No</th>
                            <th>PESERTA</th>
                            <th>NILAI</th>
                            <th>LABEL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dataPeserta = query("SELECT * FROM (SELECT peserta.id_peserta, peserta.nama, peserta.label, nilai_perangkingan FROM perangkingan LEFT JOIN peserta ON peserta.id_peserta = perangkingan.id_peserta ) AS A ORDER BY nilai_perangkingan DESC");
                        $index = 1;
                        foreach ($dataPeserta as $dataPeserta) :
                        ?>
                            <tr>
                                <td><?= $index++ ?></td>
                                <td><?= $dataPeserta["nama"] ?></td>
                                <td><?= $dataPeserta["nilai_perangkingan"] ?></td>
                                <td><?= $dataPeserta["label"] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="alternatifPerbatasanMabac" style="margin-bottom: 100px;">
                <h2>Perangkingan PKH</h2>
                <table id="tabel5" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="table-data">
                        <tr>
                            <th>No</th>
                            <th>PESERTA</th>
                            <th>NILAI</th>
                            <th>LABEL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dataPeserta = query("SELECT * FROM (SELECT peserta.id_peserta, peserta.nama, peserta.label, nilai_perangkingan FROM perangkingan LEFT JOIN peserta ON peserta.id_peserta = perangkingan.id_peserta WHERE label = 'PKH' ) AS A ORDER BY nilai_perangkingan DESC");
                        $index = 1;
                        foreach ($dataPeserta as $dataPeserta) :
                        ?>
                            <tr>
                                <td><?= $index++ ?></td>
                                <td><?= $dataPeserta["nama"] ?></td>
                                <td><?= $dataPeserta["nilai_perangkingan"] ?></td>
                                <td><?= $dataPeserta["label"] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" integrity="sha512-6PM0qYu5KExuNcKt5bURAoT6KCThUmHRewN3zUFNaoI6Di7XJPTMoT6K0nsagZKk2OB4L7E3q1uQKHNHd4stIQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
            var table = $("#tabel4").DataTable({
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
                .appendTo("#tabel4_wrapper .col-md-6:eq(0)");
            var table = $("#tabel5").DataTable({
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
                .appendTo("#tabel5_wrapper .col-md-6:eq(0)");
        });
    </script>
</body>

</html>