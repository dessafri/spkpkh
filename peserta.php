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
if (isset($_POST["submit_peserta"])) {
    buatPeserta($_POST);
}
if (isset($_POST["edit_peserta"])) {
    var_dump($_POST);
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
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap4.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Daftar Peserta</title>
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
                <h1 class="h1-brand">Data Peserta Bantuan</h1>
                <?php
                if ($role != "kades") {
                    echo '<button class="btn btn-primary" data-toggle="modal" data-target="#modalPeserta">
                    Tambah Peserta
                </button>';
                }
                ?>
            </div>
            <div class="tabel">
                <table id="example" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="table-data">
                        <tr>
                            <th>No</th>
                            <th>NAMA</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dataPeserta = query("SELECT * FROM peserta");
                        $index = 1;
                        foreach ($dataPeserta as $dataPeserta) :
                        ?>
                        <tr>
                            <td><?= $index++ ?></td>
                            <td><?= $dataPeserta["nama"] ?></td>
                            <td class="text-center">
                                <button class="btn btn-primary viewPeserta"
                                    onclick="viewPeserta(<?= $dataPeserta['id_peserta'] ?>)"><i
                                        class="fas fa-eye"></i></button>
                                <!-- <?php
                                    if ($role != "kades") {
                                        echo '<button class="btn btn-danger btn-delete-peserta" data-id=' . $dataPeserta["id_peserta"] . '><i
                                        class="fas fa-trash"></i></button>';
                                    }
                                    ?> -->
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalPeserta" tabindex="-1" aria-labelledby="modalPesertaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPesertaLabel">Tambah Peserta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <div class="biodata" id="biodata">
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input type="text" required name="nama" class="form-control" id="nama" />
                            </div>
                        </div>
                        <div class="pertanyaan d-none" id="pertanyaan">
                            <?php
                            $dataKriteria = query("SELECT * FROM kriteria");
                            foreach ($dataKriteria as $dataKriteria) :
                            ?>
                            <div class="form-group">
                                <label for="exampleFormControlSelect1"><?= $dataKriteria["nama_kriteria"] ?></label>
                                <input type="hidden" name="keterangan[]" value="<?= $dataKriteria["id_kriteria"] ?>">
                                <select class="form-control" required id="exampleFormControlSelect1"
                                    name="<?= $dataKriteria["id_kriteria"] ?>">
                                    <option value="0">- Silahkan Pilih Sesuai Kriteria -</option>
                                    <?php
                                        $idKriteria = $dataKriteria["id_kriteria"];
                                        $dataDetailKriteria = query("SELECT * FROM detail_kriteria WHERE id_kriteria = '$idKriteria'");
                                        foreach ($dataDetailKriteria as $dataDetailKriteria) :
                                        ?>
                                    <option value="<?= $dataDetailKriteria["nilai"] ?>">
                                        <?= $dataDetailKriteria["keterangan"] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endforeach; ?>
                        </div>
                </div>
                <div class="row">
                    <div class="col col-12">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Close
                            </button>
                            <button type="button" class="btn btn-primary d-none" id="sebelumnya">Sebelumnya</button>
                            <button type="button" class="btn btn-primary" id="selanjutnya">Selanjutnya</button>
                            <button type="submit" class="btn btn-primary d-none" name="submit_peserta"
                                id="simpan">Simpan</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="editModalPeserta"></div>
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

    // $("#selanjutnya").on("click", function() {
    //     $("#sebelumnya").removeClass("d-none");
    //     $("#selanjutnya").addClass("d-none");
    //     $("#biodata").addClass("d-none");
    //     $("#pertanyaan").removeClass("d-none");
    //     $("#simpan").removeClass("d-none");
    // })
    // $("#sebelumnya").on("click", function() {
    //     $("#sebelumnya").addClass("d-none");
    //     $("#selanjutnya").removeClass("d-none");
    //     $("#biodata").removeClass("d-none");
    //     $("#pertanyaan").addClass("d-none");
    //     $("#simpan").addClass("d-none");
    // })
    function viewPeserta(idPeserta) {
        let id = idPeserta
        let formData = new FormData();
        formData.append('id', id);
        fetch('detaildatapeserta.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            return response.json()
        }).then(responseJson => {
            let data = responseJson
            let modal = `
                <div class="modal fade" id="modalEditPeserta" tabindex="-1" aria-labelledby="modalEditPesertaLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalEditPesertaLabel">Edit Peserta</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="" method="POST">
                                    <div class="biodata" id="biodataEdit">
                                        <div class="form-group">
                                            <label for="nama">Nama</label>
                                            <input type="text" disabled name="nama" value='${data[0].nama}' class="form-control" id="nama" />
                                        </div>
                                    </div>
                                    <div class="pertanyaan" id="pertanyaanEdit">
                                    </div>
                            </div>
                            <div class="row">
                                <div class="col col-12">
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            Close
                                        </button>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>`

            let pertanyaan = data.map((data, index) =>
                ` 
                    <div class="form-group">
                        <label for="nama">${data.nama_kriteria}</label>
                        <input type="text" disabled name="nama" value='${data.keterangan}' class="form-control" id="nama" />
                    </div>
                    `)

            $("#editModalPeserta").html(modal);
            $("#modalEditPeserta").modal("show");
            $("#pertanyaanEdit").html(pertanyaan);
        })
    }
    $('.btn-delete-peserta').on("click", function() {
        let id = $(this).attr('data-id');
        Swal.fire({
            icon: "warning",
            position: "top",
            title: "Apakah anda yakin ?",
            text: "Data Peserta Akan Terhapus",
            showConfirmButton: true,
            showCancelButton: true,
            reverseButtons: true
        }).then((result => {
            if (result.isConfirmed) {
                let formData = new FormData;
                formData.append('id', id);
                fetch("hapusPeserta.php", {
                    method: "POST",
                    body: formData
                }).then(response => {
                    return response.json()
                }).then(responseJson => {
                    Swal.fire({
                        title: 'Terhapus!',
                        text: 'Peserta Berhasil Dihapus',
                        icon: 'success',
                        position: "top",
                        showConfirmButton: false
                    })
                    setTimeout(() => {
                        window.location.reload(true);
                    }, 1000);
                })
            } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Peserta Gagal Dihapus',
                    icon: 'error',
                    position: "top",
                    showConfirmButton: false
                })
                setTimeout(() => {
                    window.location.reload(true);
                }, 1000);
            }
        }))
    })
    </script>
</body>

</html>