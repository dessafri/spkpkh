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
    <title>Hasil</title>
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
                        foreach($dataPeserta as $dataPeserta):
                        ?>
                        <tr>
                            <td><?= $index++?></td>
                            <td><?= $dataPeserta["nama"]?></td>
                            <td><?= $dataPeserta["nilai_perangkingan"] ?></td>
                            <td><?= $dataPeserta["label"] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="alternatifPerbatasanMabac" style="margin-bottom: 100px;">
                <h2>Perbandingan</h2>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <select class="form-control" id="exampleFormControlSelect1">
                                <option value="0">Pilih Label</option>
                                <?php
                        $datalabel = query("SELECT DISTINCT label FROM peserta");
                        foreach($datalabel as $data):
                        ?>
                                <option value="<?= $data['label']?>"><?= $data["label"]?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- <table id="tabel5" class="table table-striped table-bordered" style="width: 100%">
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
                        foreach($dataPeserta as $dataPeserta):
                        ?>
                        <tr>
                            <td><?= $index++?></td>
                            <td><?= $dataPeserta["nama"]?></td>
                            <td><?= $dataPeserta["nilai_perangkingan"] ?></td>
                            <td><?= $dataPeserta["label"] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table> -->
                <div id="tabelPerbandingan"></div>
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
        $('select').on('change', function() {
            let val = this.value;
            document.cookie = "value = " + val
            <?php
            $php_var_val= $_COOKIE['value'];
            ?>
            let tabel = `
                    <div class="row">
                    <div class="col-6">
                        <p class="font-weight-bold">Tabel Peserta</p>
                        <table id="tabel5" class="table table-striped table-bordered" style="width: 100%">
                            <thead class="table-data">
                                <tr>
                                    <th>No</th>
                                    <th>PESERTA</th>
                                    <th>LABEL</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $dataPeserta = query("SELECT peserta.id_peserta, peserta.nama, peserta.label, perangkingan.nilai_perangkingan FROM peserta JOIN perangkingan ON peserta.id_peserta = perangkingan.id_peserta WHERE peserta.label LIKE '%$php_var_val%'");
                        $index = 1;
                        foreach($dataPeserta as $dataPeserta):
                        ?>
                        <tr>
                            <td><?= $index++?></td>
                            <td><?= $dataPeserta["nama"]?></td>
                            <td><?= $dataPeserta["label"] ?></td>
                        </tr>
                        <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-6">
                        <p class="font-weight-bold">Tabel Perangkingan</p>
                        <table id="tabel6" class="table table-striped table-bordered" style="width: 100%">
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
                            $dataPeserta = query("SELECT peserta.id_peserta, peserta.nama, peserta.label, perangkingan.nilai_perangkingan FROM peserta JOIN perangkingan ON peserta.id_peserta = perangkingan.id_peserta WHERE peserta.label LIKE '%$php_var_val%'");
                        $index = 1;
                        foreach($dataPeserta as $dataPeserta):
                        ?>
                        <tr>
                            <td><?= $index++?></td>
                            <td><?= $dataPeserta["nama"]?></td>
                            <td><?= $dataPeserta["nilai_perangkingan"]?></td>
                            <td><?= $dataPeserta["label"] ?></td>
                        </tr>
                        <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                    `
            $('#tabelPerbandingan').html(tabel);
            var table = $("#tabel5").DataTable({
                lengthChange: true,
            });
            table
                .buttons()
                .container()
                .appendTo("#tabel5_wrapper .col-md-6:eq(0)");
            var table = $("#tabel6").DataTable({
                lengthChange: true,
            });
            table
                .buttons()
                .container()
                .appendTo("#tabel6_wrapper .col-md-6:eq(0)");
            // if (val == 0) {} else {
            //     let formData = new FormData();
            //     formData.append('value', val);
            //     fetch('dataperbandingan.php', {
            //         method: 'post',
            //         body: formData
            //     }).then(response => {
            //         return response.json()
            //     }).then(response => {
            //         console.log(response)

            //     })
            // }
        });
    });
    </script>
</body>

</html>