    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $pegawai ?></h3>
                            <p>Jumlah Karyawan</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="<?= base_url('manajer/pegawai') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $hadir ?></h3>
                            <p>Karyawan hadir hari ini</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="<?= base_url('manajer/absensi') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $izin ?></h3>

                            <p>Jumlah Izin / Sakit Hari ini</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="<?= base_url('manajer/absensi') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $izin + $cuti ?></h3>

                            <p>Karyawan Tidak Hadir</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->
            <!-- Main row -->
            <div class="row">

                <section class="col-lg-12 connectedSortable">

                    <!-- Map card -->

                    <div class="card">
                        <div class="card-header"> Notifikasi </h3>
                        </div>
                            <div class="card-body">
                              <p align="center">Hai,&nbsp<b><?= $this->session->userdata('nama') ?></b>&nbsp silahkan mengambil kode untuk absensi</p>
                              <p align="center"><button id="kode" class="btn btn-primary">Buka QR Code</button><img id="gambarkode" hidden></p>
                            </div>
                        <div class="card-body">
                            Selamat datang <b><?= $this->session->userdata('nama') ?></b>, saat ini anda login menggunakan akun <b><?= $this->session->userdata('level') ?></b>.
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
    <script>
      //yo ini yang qr pegawai
      //dari sini ad yang salah, di bagian key mkony dk bs decrypt
      //buat fungsi get date hr ini, cuma tanggal  b, bulan dk sah, sm tahun jg, tpi sebenerny boleh b sih klo nk tanggl,bulan,tahun

      var today = new Date();
      var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
      console.log(date);

      var isi = "<?= $kodepegawai ?>";
      console.log("isi kode pegawai :" + isi);
      //isi,key
      var enkripsitext = CryptoJS.AES.encrypt(isi, date);
      console.log(typeof(enkripsitext));
      console.log("enskripsi " + enkripsitext);
      var test1=enkripsitext.toString();
      console.log(typeof(test1));
    //dari sini
      var dekripsitext = CryptoJS.AES.decrypt(enkripsitext.toString(), date);
      console.log("dekripsi " + dekripsitext);
      var hasildekripsi = dekripsitext.toString(CryptoJS.enc.Utf8);
      console.log("hasil decrypt " + hasildekripsi);
    //sampe sini
      var urlD = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=";
      var urlComplete = urlD + enkripsitext;
      document.getElementById('gambarkode').src = urlComplete;
      // kode

      document.getElementById('kode').addEventListener("click", function() {
        if (document.getElementById("kode").hidden == true) {
          document.getElementById("kode").hidden = false;
          document.getElementById("gambarkode").hidden = true;
        } else {
          document.getElementById("kode").hidden = true;
          document.getElementById("gambarkode").hidden = false;
        }
      });
    </script>
