<?php
date_default_timezone_set("Asia/Bangkok");
include "konek.php";
$idpegawai = $_GET['idpegawai'];
// $idpegawai  = '15709852';
$return_arr[] = array(
        "waktuawal" => '',
        "waktuakhir" => '',
        "tipe" => '',
        "namapegawai"=>'',
        "jamsekarang"=>'',
        "statusabsen"=>''
    );
$timenow = date('H:i:s');
$datenow = date('Y-m-d');
// echo $datenow; //ini errorny bung
$x = "SELECT * FROM configurasi_absen WHERE tipe_jam = 'pulang'";
if($b = mysqli_query($koneksidb, $x)){
    if(mysqli_num_rows($b) > 0){
        $row = $b->fetch_assoc();
        $jampulang = $row['waktu_awal'];
    }else{
        $jampulang = '00:00:00';
    }
}

$y = "SELECT * FROM configurasi_lembur WHERE tipe_jam = 'pulang' AND tanggal_lembur = '$datenow'";
if ($a = mysqli_query($koneksidb, $y)) {
    if (mysqli_num_rows($a) > 0) {
        $row = $a->fetch_assoc();
        $jampulang = $row['waktu_awal'];
    } else {
        $jampulang = '00:00:00';
    }
}

$query = "SELECT * FROM configurasi_absen where (waktu_awal <= '$timenow' and waktu_akhir >= '$timenow') OR (tipe_jam = 'masuk' AND '$timenow' < '$jampulang' AND waktu_akhir < '$timenow')";
if($result = mysqli_query($koneksidb, $query)){
    if(mysqli_num_rows($result) > 0){
        $query = "Select * from pegawai inner join user on pegawai.kode_pegawai = user.kode_pegawai where pegawai.kode_pegawai like '$idpegawai'";
        $result1 = mysqli_query($koneksidb,$query);
        if($result1->num_rows > 0){
            $row1 = $result->fetch_assoc();
            $row2 = $result1->fetch_assoc();
            $awaljammasuk = $row1['waktu_awal'];
            $akhirjammasuk = $row1['waktu_akhir'];
            $awaljampulang = $row1['tipe_jam'];
            $return_arr["waktuawal"] = $awaljammasuk;
            $return_arr["waktuakhir"] = $akhirjammasuk;
            $return_arr["tipe"] = $awaljampulang;
            $return_arr["namapegawai"] = $row2['nama'];
            $timesekarang = strtotime($timenow);
            $timeformatsekarang = date('H', $timesekarang);
            if ($timeformatsekarang >= "01" && $timeformatsekarang <= "11") {
                $ketmasuk = "Pagi";
            } else if ($timeformatsekarang >= "12" && $timeformatsekarang <= "15") {
                $ketmasuk = "Siang";
            } else if ($timeformatsekarang >= "16" && $timeformatsekarang <= "18") {
                $ketmasuk = "Sore";
            } else {
                $ketmasuk = "Malam";
            }
            $return_arr["jamsekarang"] = date('h:i', $timesekarang) . " " . $ketmasuk;
            if ($awaljampulang == "masuk") {
                $ket = "Masuk";
            } else {
                $ket = "Pulang";
            }
            $return_arr["statusabsen"] = $ket;
            $query = "select * from absen where kode_pegawai like '$idpegawai' and waktu > '$datenow 00:00:00' and keterangan like '$awaljampulang'";
            $result2 = mysqli_query($koneksidb, $query);
            if ($result2->num_rows > 0) {
                $return_arr['message'] = 'Anda Sudah Mengabsen Hari ini';
            } else{
                $query = "Insert into absen (kode_pegawai, waktu,keterangan) values ('$idpegawai',now(),'$awaljampulang')";
                if(mysqli_query($koneksidb, $query)){
                    $return_arr['message'] = "Berhasil mengabsen";   
                } else{
                    $return_arr['message'] = 'Tidak dapat mengabsen karena '.mysqli_error($koneksidb);            
                }
            }
        } else{
            $return_arr['message'] = "Scan absen gagal karena kode salah";
        }
        
    } else{
        //$return_arr['message'] = 'Waktu absensi telah lewat';
        $query = "SELECT * FROM configurasi_lembur where tanggal_lembur = '$datenow' AND (waktu_awal <= '$timenow' and waktu_akhir >= '$timenow')";
        if ($result = mysqli_query($koneksidb, $query)) {
            if (mysqli_num_rows($result) > 0) {
                $query = "Select * from pegawai inner join user on pegawai.kode_pegawai = user.kode_pegawai where pegawai.kode_pegawai like '$idpegawai'";
                $result1 = mysqli_query($koneksidb, $query);
                if ($result1->num_rows > 0) {
                    $row1 = $result->fetch_assoc();
                    $row2 = $result1->fetch_assoc();
                    $awaljammasuk = $row1['waktu_awal'];
                    $akhirjammasuk = $row1['waktu_akhir'];
                    $awaljampulang = $row1['tipe_lembur'];
                    $namalembur = $row1['ket_lembur'];
                    $return_arr["waktuawal"] = $awaljammasuk;
                    $return_arr["waktuakhir"] = $akhirjammasuk;
                    $return_arr["tipe"] = $awaljampulang.' | '.$namalembur;
                    $return_arr["namapegawai"] = $row2['nama'];
                    $timesekarang = strtotime($timenow);
                    $timeformatsekarang = date('H', $timesekarang);
                    if ($timeformatsekarang >= "01" && $timeformatsekarang <= "11") {
                        $ketmasuk = "Pagi";
                    } else if ($timeformatsekarang >= "12" && $timeformatsekarang <= "15") {
                        $ketmasuk = "Siang";
                    } else if ($timeformatsekarang >= "16" && $timeformatsekarang<= "18") {
                        $ketmasuk = "Sore";
                    } else {
                        $ketmasuk = "Malam";
                    }
                    $return_arr["jamsekarang"] = date('h:i',$timesekarang)." ".$ketmasuk;
                    if($awaljampulang == "masuk"){
                        $ket = "Masuk";
                    }else{
                        $ket = "Pulang";
                    }
                    $return_arr["statusabsen"] = $ket;
                    $query = "select * from absen_lembur where kode_pegawai like '$idpegawai' and waktu > '$datenow 00:00:00' and keterangan like '$awaljampulang | $namalembur'";
                    $result2 = mysqli_query($koneksidb, $query);
                    if ($result2->num_rows > 0) {
                        $return_arr['message'] = 'Anda Sudah Mengabsen lembur Hari ini';
                    } else {
                        $query = "Insert into absen_lembur (kode_pegawai, waktu,keterangan) values ('$idpegawai',now(),'$awaljampulang | $namalembur')";
                        if (mysqli_query($koneksidb, $query)) {
                            $return_arr['message'] = "Berhasil Absen Lembur";
                        } else {
                            $return_arr['message'] = 'Tidak dapat mengabsen lembur karena ' . mysqli_error($koneksidb);
                        }
                    }
                }else {
                    $return_arr['message'] = "Scan absen lembur gagal karena kode salah";
                }
            }else {
                $return_arr['message'] = 'Waktu lembur telah lewat';
            }
        }else {
            $return_arr['message'] = mysqli_error($koneksidb);
        }
    }
} else{
    $return_arr['message'] = mysqli_error($koneksidb);
}
// while ($row = mysqli_fetch_array($result)) {
//     $awaljammasuk = $row['waktu_awal'];
//     $akhirjammasuk = $row['waktu_akhir'];
//     $awaljampulang = $row['tipe_jam'];

//     // $return_arr[] = array(
//     //     "waktuawal" => $awaljammasuk,
//     //     "waktuakhir" => $akhirjammasuk,
//     //     "tipe" => $awaljampulang,
//     // );
// }

// Encoding array in JSON format
echo json_encode($return_arr);
