<?php
# Konek ke Web Server Lokal
$myHost	= "localhost";
$myUser	= "root";
$myPass	= "";
$myDbs	= "simpeg";

# Konek ke Web Server Lokal
$koneksidb	= mysqli_connect($myHost, $myUser, $myPass);
if (! $koneksidb) {
  echo "Failed Connection !";
}

# Memilih database pd MySQL Server
mysqli_select_db($koneksidb,$myDbs) or die ("Database not Found !");
