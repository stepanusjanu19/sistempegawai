<!DOCTYPE html>
<html>

<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link href="logo.png" rel="icon" type="image/png">
  <title>SIMPEG PT. PSSI Scan Absen QR Code</title>
  <script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
  <div class="container">
    <div class="row">
      <h1>Silahkan Scan Absen disini :)</h1>
      <style>
        h1 {
          margin-left: 620px;
          color: black;
          -webkit-text-fill-color: white;
          -webkit-text-stroke-width: 1px;
          -webkit-text-stroke-color: lightgreen;
        }
      </style>
      <div id="clock" class="col"></div>
      <style>
        @import url('https://fonts.googleapis.com/css?family=Orbitron');

        body {
          background-image: url('komputer.png');
        }

        #clock {
          font-family: 'Orbitron', sans-serif;
          color: black;
          -webkit-text-fill-color: white;
          -webkit-text-stroke-width: 1px;
          -webkit-text-stroke-color: lightgrey;
          font-size: 56px;
          text-align: center;
          padding-top: 5px;
          padding-bottom: 10px;
          padding-left: 500px;
        }
      </style>
      <div class="col">
        <div class="col" style="padding-left:550px"><video id="preview"></video></div>
        <style>
          #preview {
            border-radius: 30px;
            width: 500px;
            border: 10px solid white;
          }
        </style>
      </div>
      <div class="col">
        <div id="tes" class="col">
          <div class="col">
            <span><b>Nama Pegawai : <span id="namapegawai"></span> </b></span>
            <br><span><b>Jam Absen : <span id="jamabsensekarang"></span></b></span>
            <br><span><b>Status Absen : <span id="statusabsen"></span></b></span>
          </div>

        </div>
        <style>
          #tes {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 30px;
            padding: 120px;
            margin: 10px;
            border: 10px solid black;
          }
        </style>
      </div>
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
  <script type="text/javascript">
    function currentTime() {
      var date = new Date(); /* creating object of Date class */
      var hour = date.getHours();
      var min = date.getMinutes();
      var sec = date.getSeconds();
      hour = updateTime(hour);
      min = updateTime(min);
      sec = updateTime(sec);
      document.getElementById("clock").innerText = hour + " : " + min + " : " + sec; /* adding time to the div */
      var t = setTimeout(function() {
        currentTime()
      }, 1000); /* setting timer */
    }

    function updateTime(k) {
      if (k < 10) {
        return "0" + k;
      } else {
        return k;
      }
    }

    currentTime();

    console.log((new Date()).getHours());
    console.log((new Date()).getTimezoneOffset());
    var awaljammasuk;
    var akhirjammasuk;
    var awaljampulang;
    var akhirjampulang;
    var hariini;
    var jamabsen;
    var tipe;
    var message;


    let scanner = new Instascan.Scanner({
      video: document.getElementById('preview')
    });


    scanner.addListener('scan', function(content) {
      console.log(content);
      var today1 = new Date();
      var date = today1.getFullYear() + '-' + (today1.getMonth() + 1) + '-' + today1.getDate();
      var dekripsitext = CryptoJS.AES.decrypt(content.replace(/\s/g, '+'), date); //benerlah, contentny string. barcode kw yg sdh tuo ckny. akrena it hasilny ''eh ? mdkke? td be biso lo
      console.log("dekripsi " + dekripsitext);
      var hasildekripsi = dekripsitext.toString(CryptoJS.enc.Utf8);
      console.log("hasil decrypt " + hasildekripsi);
      $.ajax({
        url: 'fetchdatajam.php',
        type: 'get',
        data: {
          idpegawai: hasildekripsi, // content itu kau jdike kode_pegai dlu tok, ak dk pake, ak pake id default dulu td tp qr codeny beda ck mn tau ?dk, ak cek dulu eror ajaxny ap ok deh
        },
        dataType: 'JSON',
        success: function(response) {
          console.log(typeof(response));
          // var data = JSON.parse(response);
          //tadi nk ngapoin habis dapet datany? ak lupo wkwk
          // di mnculke abseny tok nawa pegawai dan lain2 sm ke post databaseny pas scan
          message = response.message;
          document.getElementById("namapegawai").innerHTML = response.namapegawai;
          document.getElementById("jamabsensekarang").innerHTML = response.jamsekarang;
          document.getElementById("statusabsen").innerHTML = response.statusabsen;
          alert(message);
          // var len = response.length;
          // for (var i = 0; i < len; i++) {
          //   w_awal = response[i].waktuawal;
          //   w_akhir = response[i].waktuakhir;
          //   tipe = response[i].tipe;
          // }
          // var jamSekarang = new Date();
          // var j_awal = new Date(jamSekarang.getTime());
          // var j_akhir = new Date(jamSekarang.getTime());
          // j_awal.setHours(w_awal.split(":")[0]);
          // j_awal.setMinutes(w_awal.split(":")[1]);
          // j_awal.setSeconds(w_awal.split(":")[2]);
          // j_akhir.setHours(w_akhir.split(":")[0]);
          // j_akhir.setMinutes(w_akhir.split(":")[1]);
          // j_akhir.setSeconds(w_akhir.split(":")[2]);


          // //ini scanny  
          // //17:00:00 <- Time <- String <- w_awal1.getHours();
          // var today1 = new Date();
          // var date = today1.getFullYear() + '-' + (today1.getMonth() + 1) + '-' + today1.getDate();
          // console.log(date);
          // console.log(content);

          // // var contentTest = "U2FsdGVkX1+j9BotitToZ/Z8OPGJ2XKOwxGjyfpbAl0=";

          // var dekripsitext = CryptoJS.AES.decrypt(content.replace(/\s/g, '+'), date);
          // console.log("dekripsi " + dekripsitext);
          // var hasildekripsi = dekripsitext.toString(CryptoJS.enc.Utf8);
          // console.log("hasil decrypt " + hasildekripsi);


          // console.log(j_awal);
          // console.log(j_akhir);
          // if (jamSekarang >= j_awal && jamSekarang <= j_akhir && tipe == "masuk") {
          //   alert("Selamat Pagi");
          //   alert("Hasil scan : " + hasildekripsi + "Jam: " + j_awal + "-" + j_akhir);

          //   //nah disini, kw pake insert be tinggal. cuma kau butuh buat fungsi php biaso dulu cak kemarin, cma untuk insert be di controller se
          //   //tapi tok ini buatny diluar project ak 
          //   //brrti pke biaso be dak pp ?
          //   //mksdny di luar projek kw
          // } else if (jamSekarang >= j_awal && jamSekarang <= j_akhir && tipe == "pulang") {
          //   alert("Selamat Sore");
          //   alert("Hasil scan : " + hasildekripsi + "Jam: " + j_awal + "-" + j_akhir);
          // } else {
          //   alert("Diluar dari jam absen");

          // }
        },
        error: function(xhr, execptio, err) {
          // var err = eval("(" + xhr.responseText + ")");
          console.log("error: " + xhr.responseText);

        }
      });

      harini = new Date();
      jamabsen = harini.getHours() + ":" + harini.getMinutes() + ":" + harini.getSeconds();
      alert("Jam Absen: " + jamabsen);
    });
    Instascan.Camera.getCameras().then(function(cameras) {
      if (cameras.length > 0) {
        scanner.start(cameras[0]);
      } else {
        console.error('No cameras found.');
      }
    }).catch(function(e) {
      console.error(e);
    });


    /* calling currentTime() function to initiate the process */
  </script>
</body>

</html>