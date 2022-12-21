<?php
include "konek.php";

$idpegawai  = $_GET['idpegawai'];
$query = "Select * from pegawai where kode_pegawai like '$idpegawai'";
$result = mysqli_query($koneksidb, $query);
if($result->num_rows >0){
    //data ap be yg dibutuhkn??.
    //nmapegawai token
    //statusabsen
    //jam absen nya jam brp
    //nak simpel carony atau nk ribet??
    //simpel, cuma jalanin be yg penting jalan
    //ribet, cek ulang, cek tipe post atau get, samo cek lagi jamny

//yang ribet be tok
//biar dak ad bug
   //nk cb dwek dk? spaya bs nge... eh wait dulu, ak baru nyadar. ngp dk satu file deng
$datas = $result->fetch_assoc();

} else{
    echo "not data be found cause by $result->error";
    return null;
}
//bener dak namo tabelny?

?>