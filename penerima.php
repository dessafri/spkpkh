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
        <div class="container" id="content">
            <div class="form d-flex justify-content-center" style="width:100%;">
                <div class="d-flex flex-wrap" style="width:70%;">
                    <span class="inline-block text-center" style="width:100%;">Masukkan Jumlah Kuota Penerima
                        Bantuan</span><br>
                    <div class="row d-flex justify-content-center mt-3" style="width: 100%;">
                        <div class="col text-center col-10">
                            <div class="form-group">
                                <input type="text" id="jmlData" name="inputJml" class="form-control"
                                    aria-describedby="emailHelp">
                            </div>
                        </div>
                        <div class="col text-center col-2">
                            <div class="form-group">
                                <button class="btn btn-primary" style="margin-left: -50px;"
                                    id="penerima">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
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
        $("#penerima").on("click", function() {
            let jmlData = $("#jmlData").val() - 1;
            fetch("dataHasil.php", {
                method: "POST"
            }).then(response => {
                return response.json()
            }).then(responseJson => {
                console.log(responseJson)
                let bantuanHtml = `<div style="display: flex; justify-content: space-between">
                    <h1 class="h1-brand" style="font-size:22px;">DATA PENERIMAAN BANTUAN</h1>
                </div>
                <div class="normalisasi-data" id="normalisasi" style="margin-bottom:50px;">
                <table id="example" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="table-data">
                        <tr>
                            <th>No</th>
                            <th>PESERTA</th>
                            <th>NIK</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                    ${responseJson.map((data,index)=>
                        `<tr>
                            <td>${index+1}</td>
                            <td>${data.nama}</td>
                            <td>${data.nik}</td>
                            <td>${index <= jmlData ? '<span class="d-inline-block text-success font-weight-bold">LOLOS</span>' : '<span class="d-inline-block text-danger font-weight-bold">TIDAK LOLOS</span>'}</td>
                        </tr>`
                    ).join('')}
                    </tbody>
                </table>
                </div>`
                $("#content").html(bantuanHtml);
                $('#text').css("display", "none");
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
            })
        })
    });
    </script>
</body>

</html>