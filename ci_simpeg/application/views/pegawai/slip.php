<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="callout callout-info">
          <h5><i class="fas fa-info"></i> Note:</h5>
          Jika ingin mencetak Slip, gunakan tombol download di pojok kiri bawah
        </div>


        <!-- Main content -->
        <div class="invoice p-3 mb-3">
          <!-- title row -->
          <div class="row">
            <div class="col-12">
              <h4>
                <img style="width: 50px" src="<?= base_url('assets/img/' . $web->logo) ?>"> <?= $web->nama ?>
                <small class="float-right">Date : <?= $this->M_data->hari(date('D')) . ', ' . $this->M_data->tgl_indo(date('Y-m-d')); ?></small>
              </h4>
            </div>
            <!-- /.col -->
          </div>
          <!-- info row -->
          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
              Dari
              <address>
                <strong>HRD <?= $web->nama ?></strong><br>
                <?= $web->alamat ?><br>
                Phone: <?= $web->nohp ?><br>
                Email: <?= $web->email ?>
              </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
              Untuk
              <address>
                <strong><?= ucwords($data->nama) ?></strong><br>
                Kode Pegawai : <?= $data->kode_pegawai ?><br>
                Email: <?= $data->email ?><br>
                Departemen : <?= $data->departemen ?><br>
                Jabatan : <?php
                          if ($data->nama_jabatan == "Supervisor") {
                            echo "Staff";
                          }
                          ?><br>
                Gaji perhari : Rp. <?= number_format($penggajian['gaji_pokok']) ?>
              </address>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->

          <!-- Table row -->
          <div class="row">
            <div class="col-12 table-responsive">
              <table class="table table-striped">
                <tr>
                  <th style="width:50%">Jumlah Kehadiran :</th>
                  <td><?= $absen ?> hari x Rp. <?= number_format($penggajian['gaji_pokok']) ?></td>
                </tr>
              </table>
              <table class="table table-striped">
                <tr>
                  <th style="width:50%">Gaji Pokok :</th>
                  <td>Rp. <?= number_format(($absen * $penggajian['gaji_pokok']) + ($cuti * $penggajian['gaji_pokok']) + ($sakit * $penggajian['gaji_pokok'])) ?></td>
                </tr>
                <tr>
                  <th style="width:50%">Tunjangan : </th>
                  <td>Rp. <?= number_format($penggajian['tunjangan']) ?></td>
                </tr>
                <tr>
                  <th style="width:50%">Uang Makan : </th>
                  <td><?= $absen ?> x Rp. <?= number_format($penggajian['uang_makan']) ?> = Rp. <?= number_format($penggajian['uang_makan'] * $absen) ?></td>
                </tr>
                <tr>
                  <th style="width:50%">Lembur : </th>
                  <td>Rp. <?= number_format(((($absen * $penggajian['gaji_pokok']) + ($cuti * $penggajian['gaji_pokok']) + ($sakit * $penggajian['gaji_pokok'])) * 0.1) * $penggajian['lembur']) ?></td>
                </tr>
                <tr>
                  <th style="width:50%;padding-left:120px">Sub Total Penerimaan : </th>
                  <td>Rp. <?= number_format(((($absen * $penggajian['gaji_pokok']) + ($cuti * $penggajian['gaji_pokok']) + ($sakit * $penggajian['gaji_pokok'])) + $penggajian['tunjangan'] + ($penggajian['uang_makan'] * $absen) + (($penggajian['gaji_pokok'] * 0.1) * $penggajian['lembur']))) ?></td>
                </tr>
                <tr>
                  <th style="width:50%">BPJS Kesehatan : </th>
                  <td>Rp. <?= number_format(($penggajian['bpjs_kesehatan'])) ?></td>
                </tr>
                <tr>
                  <th style="width:50">BPJS Tenaga Kerja : </th>
                  <td>Rp. <?= number_format(($penggajian['bpjs_tkj'])) ?></td>
                </tr>
                <tr>
                  <th style="width:50">Pinjaman : </th>
                  <td>Rp. <?= number_format(((($absen * $penggajian['gaji_pokok']) + ($cuti * $penggajian['gaji_pokok']) + ($sakit * $penggajian['gaji_pokok'])) * 0.1) - $penggajian['pinjaman']) ?></td>
                </tr>
                <tr>
                  <th style="width:50">Vaksin : </th>
                  <td>Rp. <?= number_format(($penggajian['vaksin'])) ?></td>
                </tr>
                <tr>
                  <th style="width:50%">Sanksi : </th>
                  <td>Rp. <?= number_format(($penggajian['sanksi'])) ?></td>
                </tr>
                <tr>
                  <th style="width:50%;padding-left:120px">Sub Total Potongan : </th>
                  <td>Rp. <?= number_format(($penggajian['bpjs_kesehatan'] + $penggajian['bpjs_tkj'] + (((($absen * $penggajian['gaji_pokok']) + ($cuti * $penggajian['gaji_pokok']) + ($sakit * $penggajian['gaji_pokok'])) * 0.1) - $penggajian['pinjaman']) + $penggajian['vaksin'] + $penggajian['sanksi'])) ?></td>
                </tr>
              </table>
              <table class="table table-striped">
                <tr>
                  <th style="text-align: center;">Keterangan Lain</th>
                  <td></td>
                </tr>
                <tr>
                  <th>Jumlah Cuti :</th>
                  <td><?= $cuti ?> hari</td>
                </tr>
                <tr>
                  <th>Jumlah Sakit :</th>
                  <td><?= $sakit ?> hari</td>
                </tr>
                <tr>
                  <th>Jumlah Izin Tidak Masuk :</th>
                  <td><?= $izin ?> hari</td>
                </tr>
              </table>
            </div>
            <!-- /.col -->
          </div>

          <!-- this row will not appear when printing -->
          <div class="row no-print">
            <div class="col-12">
              <a href="<?= base_url('pegawai/print_slip') ?>" target="_blank" class="btn btn-primary"><i class="fas fa-print"></i> Print</a>
            </div>
          </div>
        </div>
        <!-- /.invoice -->
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</section>