<?php $conn = mysqli_connect('localhost', 'root', '', 'spk');
error_reporting(E_ERROR);
if (!$conn) {
    mysqli_error($koneksi);
}

function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}
function login($data)
{
    global $conn;
    $username = $data['username'];
    $password = $data['password'];

    $hasil = query(
        "SELECT * FROM user WHERE username = '$username' AND password = '$password' "
    );
    if ($hasil != null) {
        $_SESSION['id'] = '1';
        $_SESSION['role'] = $hasil[0]["role"];
        header('location: index.php');
        exit();
    } else {
        echo "
        <script src='//cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        
        alert('Username / Password Salah !!');
        </script>
        
        ";
    }
}
function logout()
{
    header('location: login.php');
    session_start();
    session_destroy();
    $_SESSION['id'] = '';
    $_SESSION['role'] = '';
}
function buatKriteria($data){
    global $conn;
    $nama = $data["nama"];
    $keterangan = $data["keterangan"];
    $nilai = $data["nilai"];
    
    $sqlKriteria = "INSERT INTO kriteria (id_kriteria, nama_kriteria) VALUES (NULL, '$nama')";
    mysqli_query($conn, $sqlKriteria);
    $sqlKriteria = query(
        'SELECT * FROM kriteria ORDER BY id_kriteria DESC LIMIT 1'
    );
    $idKriteria = $sqlKriteria[0]['id_kriteria'];
    $sqlDetailKriteria = 'INSERT INTO detail_kriteria (id_detail_kriteria, id_kriteria, keterangan, nilai) VALUES';
    $index = 0;
    foreach($keterangan as $keterangan){
        $nilai1 = $nilai[$index++];
        $sqlDetailKriteria .=
                        "(NULL,'" .
                        $idKriteria .
                        "','" .
                        $keterangan .
                        "','" .
                        $nilai1 .
                        "'),";
    }
    $sqlDetailKriteria = rtrim($sqlDetailKriteria, ', ');
    mysqli_query($conn, $sqlDetailKriteria);
}
function editKriteria($data){
    global $conn;
    $idKriteria = $data["id_kriteria"];
    $nama = $data["nama"];
    $keterangan = $data["keterangan"];
    $nilai = $data["nilai"];

    $sqlUpdateKriteria = "UPDATE kriteria SET nama_kriteria= '$nama' WHERE id_kriteria = '$idKriteria'";
    mysqli_query($conn,$sqlUpdateKriteria);

    $sqlDelete = "DELETE FROM detail_kriteria WHERE id_kriteria = '$idKriteria'";
    mysqli_query($conn,$sqlDelete);
    $sqlDetailKriteria = 'INSERT INTO detail_kriteria (id_detail_kriteria, id_kriteria, keterangan, nilai) VALUES';
    $index = 0;
    foreach($keterangan as $keterangan){
        $nilai1 = $nilai[$index++];
        $sqlDetailKriteria .=
                        "(NULL,'" .
                        $idKriteria .
                        "','" .
                        $keterangan .
                        "','" .
                        $nilai1 .
                        "'),";
    }
    $sqlDetailKriteria = rtrim($sqlDetailKriteria, ', ');
    mysqli_query($conn, $sqlDetailKriteria);
}
function deleteKriteria($data){
    global $conn;

    $id = $data['id'];
    mysqli_query($conn, "DELETE FROM kriteria WHERE id_kriteria='$id'");
    mysqli_query($conn, "DELETE FROM detail_kriteria WHERE id_kriteria='$id'");
}
function deletePEserta($data){
    global $conn;

    $id = $data['id'];
    mysqli_query($conn, "DELETE FROM peserta WHERE nik='$id'");
    mysqli_query($conn, "DELETE FROM perkiraan_perbatasan WHERE nik='$id'");
    mysqli_query($conn, "DELETE FROM perangkingan_alternatif WHERE nik='$id'");
    mysqli_query($conn, "DELETE FROM normalisasi_mabac WHERE nik='$id'");
    mysqli_query($conn, "DELETE FROM normaliasi_entropy WHERE nik='$id'");
    mysqli_query($conn, "DELETE FROM matrix_tertimbang WHERE nik='$id'");
    mysqli_query($conn, "DELETE FROM entropy_tiap_atribut WHERE nik='$id'");
    mysqli_query($conn, "DELETE FROM jawaban WHERE nik='$id'");
}
function buatPeserta($data){
    global $conn;
    $nik = $data["nik"];
    $nama = $data["nama"];
    $jenis_kelamin = $data["jenis_kelamin"];
    $tanggal_lahir = $data["date"];
    $alamat = $data["alamat"];
    $alamatUpper = strtoupper($alamat);
    $rt = $data["rt"];
    $rw = $data["rw"];

    mysqli_query($conn, "INSERT INTO peserta (id_peserta, nik, nama, jenis_kelamin, tanggal_lahir, alamat, rt, rw) VALUES (NULL, '$nik', '$nama', '$jenis_kelamin', '$tanggal_lahir', '$alamatUpper', '$rt', '$rw')");
    
    $keterangan = $data["keterangan"];
    $sqljawaban = "INSERT INTO jawaban (id_jawaban, nik, id_kriteria, jawaban_peserta) VALUES";
    foreach($keterangan as $idKriteria){
        $jawaban = $data[$idKriteria];
        $sqljawaban .=
                        "(NULL,'" .
                        $nik .
                        "','" .
                        $idKriteria .
                        "','" .
                        $jawaban .
                        "'),";
    }
    $sqljawaban = rtrim($sqljawaban, ', ');
    mysqli_query($conn, $sqljawaban);
}

function buatHasil(){
    global $conn;
    $dataPeserta = query("SELECT * FROM peserta");
    // normalisasi waspas
    foreach($dataPeserta as $dataPeserta){
        $sqlNormalisasi =
            'INSERT INTO normalisasi_waspas (id_normalisasi, id_peserta, id_kriteria, nilai_normalisasi) VALUES';
        $idPeserta = $dataPeserta["id_peserta"];
        $dataIdikator = query("SELECT DISTINCT(id_kriteria) FROM jawaban");
        foreach($dataIdikator as $a){
            $valueIndikator = 0;
            $normalisasi = 0;
            $idKriteria = $a['id_kriteria'];
            $atributIndikator = '';
            $resultAtribut = mysqli_fetch_assoc(
                mysqli_query(
                    $conn,
                    "SELECT kriteria.atribut FROM kriteria WHERE kriteria.id_kriteria = $idKriteria "
                )
            );
            $atributIndikator = $resultAtribut['atribut'];
            if ($atributIndikator == 'BENEFIT') {
                $valueMax = mysqli_fetch_assoc(
                    mysqli_query(
                        $conn,
                        "SELECT MAX(jawaban_peserta) AS MAX FROM jawaban WHERE jawaban.id_kriteria = $idKriteria"
                    )
                );
                $valueIndikator = $valueMax['MAX'];
            } else {
                $valueMin = mysqli_fetch_assoc(
                    mysqli_query(
                        $conn,
                        "SELECT MIN(jawaban_peserta) AS MIN FROM jawaban WHERE jawaban.id_kriteria = $idKriteria"
                    )
                );
                $valueIndikator = $valueMin['MIN'];
            }
            $resultJawaban = mysqli_fetch_assoc(
                mysqli_query(
                    $conn,
                    "SELECT jawaban_peserta FROM jawaban WHERE id_peserta = $idPeserta AND id_kriteria = $idKriteria"
                )
            );
            $jawaban = $resultJawaban["jawaban_peserta"];
            if($atributIndikator == "BENEFIT"){
                $normalisasi = $jawaban / $valueIndikator;
            }else{
                $normalisasi = $valueIndikator / $jawaban;
            }
             $sqlNormalisasi .=
                        "(NULL,'" .
                        $idPeserta .
                        "','" .
                        $idKriteria .
                        "','" .
                        $normalisasi .
                        "'),";
        }
        $sqlNormalisasi = rtrim($sqlNormalisasi, ', ');
        mysqli_query($conn, $sqlNormalisasi);
    }
    // nilaiQ
    $dataPeserta = query("SELECT * FROM peserta");
    foreach($dataPeserta as $dataPeserta){
        $sqlNilaiQ =
            'INSERT INTO nilaiq (id_nilaiQ, id_peserta, id_kriteria, nilaiQ) VALUES';
        $idPeserta = $dataPeserta["id_peserta"];
        $dataIdikator = query("SELECT DISTINCT(jawaban.id_kriteria), bobot FROM jawaban INNER JOIN kriteria ON jawaban.id_kriteria = kriteria.id_kriteria");
        foreach($dataIdikator as $a){
            $idKriteria = $a["id_kriteria"];
            $bobot = $a["bobot"];
            $hasilNormalisasi = mysqli_fetch_assoc(
                mysqli_query(
                    $conn,
                    "SELECT nilai_normalisasi FROM normalisasi_waspas WHERE id_peserta = $idPeserta AND id_kriteria = $idKriteria"
                )
                );
            $nilaiQ = $hasilNormalisasi["nilai_normalisasi"] * $bobot/100;
            $sqlNilaiQ .=
                        "(NULL,'" .
                        $idPeserta .
                        "','" .
                        $idKriteria .
                        "','" .
                        $nilaiQ .
                        "'),";
        }
        $sqlNilaiQ = rtrim($sqlNilaiQ, ', ');
        mysqli_query($conn, $sqlNilaiQ);

        $resultSigma = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nilaiq) AS TOTAL FROM nilaiq WHERE id_peserta = $idPeserta"));
        $sigma = $resultSigma["TOTAL"];
        $kali = 0.5 * $sigma;

        mysqli_query($conn, "UPDATE nilaiq SET sigma = $sigma, nilaikali = $kali WHERE id_peserta = $idPeserta");
    }
    // perhitungan ketiga
    $dataPeserta = query("SELECT * FROM peserta");
    foreach($dataPeserta as $dataPeserta){
        $sqlNilaiQ =
            'INSERT INTO lanjutanq (id_lanjutanQ , id_peserta, id_kriteria, nilai_lanjutan) VALUES';
        $idPeserta = $dataPeserta["id_peserta"];
        $dataIdikator = query("SELECT DISTINCT(jawaban.id_kriteria), bobot FROM jawaban INNER JOIN kriteria ON jawaban.id_kriteria = kriteria.id_kriteria");
        foreach($dataIdikator as $a){
            $idKriteria = $a["id_kriteria"];
            $bobot = $a["bobot"];
            $hasilNormalisasi = mysqli_fetch_assoc(
                mysqli_query(
                    $conn,
                    "SELECT nilai_normalisasi FROM normalisasi_waspas WHERE id_peserta = $idPeserta AND id_kriteria = $idKriteria"
                )
                );
            $nilaiQ = $hasilNormalisasi["nilai_normalisasi"] ** ($bobot/100);
            $sqlNilaiQ .=
                        "(NULL,'" .
                        $idPeserta .
                        "','" .
                        $idKriteria .
                        "','" .
                        $nilaiQ .
                        "'),";
        }
        $sqlNilaiQ = rtrim($sqlNilaiQ, ', ');
        mysqli_query($conn, $sqlNilaiQ);

        $resultProduct = query("SELECT nilai_lanjutan FROM lanjutanq WHERE id_peserta = $idPeserta");
        $total = 0;
        foreach($resultProduct as $product){
         $perkalian = $product["nilai_lanjutan"];
         if($total == 0){
            $total = $perkalian;
         }else{
             $total = $total * $perkalian;
         }
        }
        $kalilanjutan = 0.5 * $total;

        mysqli_query($conn, "UPDATE lanjutanq SET product = $total, nilaikalilanjutan = $kalilanjutan WHERE id_peserta = $idPeserta");
    }

    // perangkingan
    $dataPeserta = query("SELECT * FROM peserta");
    foreach($dataPeserta as $dataPeserta){
        $idPeserta = $dataPeserta["id_peserta"];
        $hasilNilaiq = mysqli_fetch_assoc(
                mysqli_query(
                    $conn,
                    "SELECT nilaikali FROM nilaiq WHERE id_peserta = $idPeserta"
                )
                );
        $hasilLanjutanNilaiq = mysqli_fetch_assoc(
                mysqli_query(
                    $conn,
                    "SELECT nilaikalilanjutan FROM lanjutanq WHERE id_peserta = $idPeserta"
                )
                );
        // var_dump($hasilNilaiq["nilaikali"]);
        $total = $hasilNilaiq["nilaikali"] + $hasilLanjutanNilaiq["nilaikalilanjutan"];
        mysqli_query($conn, "INSERT INTO perangkingan (id_perangkingan,id_peserta,nilai_perangkingan) VALUES (NULL, $idPeserta, $total)");
    }
    $dataPeserta = query("SELECT * FROM peserta");
    foreach($dataPeserta as $dataPeserta){
        $sqlScalling =
            'INSERT INTO scalling (id_scalling , id_peserta, id_kriteria, nilai_scalling) VALUES';
        $idPesertaTrain = $dataPeserta["id_peserta"];
        $dataIndikator = query("SELECT DISTINCT id_kriteria FROM datatrain");
        foreach($dataIndikator as $a){
            $idIndikator = $a["id_kriteria"];
            $resultMIN = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MIN(value_datatrain) AS MIN FROM datatrain WHERE id_kriteria = $idIndikator"));
            $resultMAX = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(value_datatrain) AS MAX FROM datatrain WHERE id_kriteria = $idIndikator"));
            $minDataTrain = $resultMIN["MIN"];
            $maxDataTrain = $resultMAX["MAX"];
            $resultValue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT value_datatrain AS value FROM datatrain WHERE id_kriteria = $idIndikator AND id_peserta = $idPesertaTrain"));
            $valueDataTrain = $resultValue["value"];
            $nilaiData = 0;
            if($valueDataTrain == 0){
                $nilaiData = 0;
            }else{
                $nilaiData = ($valueDataTrain - $minDataTrain) / ($maxDataTrain - $minDataTrain);
            }
            $sqlScalling .=
                            "(NULL,'" .
                            $idPesertaTrain .
                            "','" .
                            $idIndikator .
                            "','" .
                            $nilaiData .
                            "'),";
            }
                        $sqlScalling = rtrim($sqlScalling, ', ');
                        mysqli_query($conn, $sqlScalling);
    }
        // knn
        $resultPesertaDataTes = query("SELECT DISTINCT id_peserta FROM data_test");
        $dataPeserta = query("SELECT * FROM peserta");
        foreach($dataPeserta as $datapeserta){
            $idPeserta = $datapeserta["id_peserta"];
            $sqlJarakMatrix =
            'INSERT INTO jarak_matrix (id_jarak_matrik , id_peserta, id_peserta_data_tes, 	nilai_jarak) VALUES';
            foreach($resultPesertaDataTes as $peserta){
                $idPesertaTes = $peserta["id_peserta"];
                $dataTes = query("SELECT * FROM data_test WHERE id_peserta = $idPesertaTes");
                $indexTrain = 0;
                $datascalling = 0;
                foreach($dataTes as $data){
                    $datates = $data["value_data_test"];
                    $dataPeserta = query("SELECT nilai_scalling FROM scalling WHERE id_peserta = $idPeserta");
                    $hasil = $datates - $dataPeserta[$indexTrain]["nilai_scalling"];
                    $pow = pow($hasil,2);
                    if($indexTrain == 0){
                        $datascalling = $pow;
                    }else{
                        $datascalling = $pow + $datascalling;
                    }
                    $indexTrain++;
                }
                $sqrt = sqrt($datascalling);
                $sqlJarakMatrix .=
                            "(NULL,'" .
                            $idPeserta .
                            "','" .
                            $idPesertaTes .
                            "','" .
                            $sqrt .
                            "'),";
                }
                        $sqlJarakMatrix = rtrim($sqlJarakMatrix, ', ');
                        mysqli_query($conn, $sqlJarakMatrix);
        }

        // hapus data tes di tabel jarak
        $resultPesertaDataTes = query("SELECT DISTINCT id_peserta FROM data_test");
        foreach($resultPesertaDataTes as $peserta){
            $idPesertaTesHapus = $peserta["id_peserta"];
            mysqli_query($conn, "DELETE FROM `jarak_matrix` WHERE id_peserta = $idPesertaTesHapus");
            // $resultTerkecil = query("SELECT * FROM (SELECT * FROM jarak_matrix WHERE id_peserta_data_tes = $idPesertaTesHapus) AS A ORDER BY nilai_jarak ASC LIMIT 3");
            // echo '<pre/>';
            // print_r($resultTerkecil);
            // var_dump($idPesertaTesHapus);
        }


}
