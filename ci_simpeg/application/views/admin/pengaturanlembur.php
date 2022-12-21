<section class="content">
    <div class="container-fluid">
        <!-- Main row -->
        <div class="row">

            <section class="col-lg-12 connectedSortable">

                <!-- Map card -->
                <div class="card">
                    <div class="card-header"> <?= $title ?> </h3>
                        <a style="float: right;" href="<?= base_url('admin/pengaturanlembur_add') ?>" class="btn btn-sm btn-primary">Tambah data</a>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="myTable" class="table table-bordered table-striped text-center">
                            <thead>
                                <th>No</th>
                                <th>Ket Lembur</th>
                                <th>Waktu Absen</th>
                                <th>Tipe Lembur</th>
                                <th>Title Lembur</th>
                                <th>Tanggal Lembur</th>
                                <th>Opsi</th>
                            </thead>
                            <tbody>
                              <?php $no = 1;
                              foreach ($pengaturanlembur as $d) { ?>
                              <tr>
                                <td width="1%"><?= $no++ ?></td>
                                <td><?= $d->ket_lembur?></td>
                                <td>
                                  <?php
                                  $timeawalmasuk=strtotime($d->waktu_awal);
                                  $timeakhirmasuk=strtotime($d->waktu_akhir);
                                  $timeformatawalmasuk = date('H',$timeawalmasuk);
                                  $timeformatakhirmasuk = date('H',$timeakhirmasuk);
                                  if($timeformatawalmasuk >= "01" && $timeformatawalmasuk <= "11"){
                                    $ketmasuk = "Pagi";
                                  }
                                  else if($timeformatawalmasuk >= "12" && $timeformatawalmasuk <= "15"){
                                      $ketmasuk = "Siang";
                                  }else if($timeformatawalmasuk >="16" && $timeformatawalmasuk <= "18"){
                                        $ketmasuk = "Sore";
                                  }else{
                                    $ketmasuk = "Malam";
                                  }

                                  if($timeformatakhirmasuk >= "01" && $timeformatakhirmasuk <= "11"){
                                    $ket = "Pagi";
                                  }
                                  else if($timeformatakhirmasuk >= "12" && $timeformatakhirmasuk <= "15"){
                                      $ket = "Siang";
                                  }else if($timeformatakhirmasuk >="16" && $timeformatakhirmasuk <= "18"){
                                        $ket = "Sore";
                                  }else{
                                    $ket = "Malam";
                                  }

                                  echo date('h:i',$timeawalmasuk)," ",$ketmasuk," - ",date('h:i',$timeakhirmasuk)," ",$ket;
                                  ?>
                                </td>
                                <td>
                                  <?php
                                    $x = $d->tipe_lembur;
                                    if($x == "masuk")
                                    {
                                      $a = "Masuk";
                                    }else{
                                      $a = "Pulang";
                                    }
                                    echo $a;
                                  ?>
                                </td>
                                <td><?= $d->ket_lembur ?></td>
                                <td>
                                  <?php
                                  $time = strtotime($d->tanggal_lembur);
                                  $dateformat = date('D', $time);
                                  if ($dateformat == "Sun") {
                                      $nama_hari = "Minggu";
                                  } else if ($dateformat == "Mon") {
                                      $nama_hari = "Senin";
                                  } else if ($dateformat == "Tue") {
                                      $nama_hari = "Selasa";
                                  } else if ($dateformat == "Wed") {
                                      $nama_hari = "Rabu";
                                  } else if ($dateformat == "Thu") {
                                      $nama_hari = "Kamis";
                                  } else if ($dateformat == "Fri") {
                                      $nama_hari = "Jumat";
                                  } else if ($dateformat == "Sat") {
                                      $nama_hari = "Sabtu";
                                  }
                                  $formatbulan = date('F', $time);
                                  if ($formatbulan == "January") {
                                      $nama_bulan = "Januari";
                                  } else if ($formatbulan == "February") {
                                      $nama_bulan = "Februari";
                                  } else if ($formatbulan == "March") {
                                      $nama_bulan = "Maret";
                                  } else if ($formatbulan == "April") {
                                      $nama_bulan = "April";
                                  } else if ($formatbulan == "May") {
                                      $nama_bulan = "Mei";
                                  } else if ($formatbulan == "June") {
                                      $nama_bulan = "Juni";
                                  } else if ($formatbulan == "July") {
                                      $nama_bulan = "Juli";
                                  } else if ($formatbulan == "August") {
                                      $nama_bulan = "Agustus";
                                  } else if ($formatbulan == "September") {
                                      $nama_bulan = "September";
                                  } else if ($formatbulan == "October") {
                                      $nama_bulan = "Oktober";
                                  } else if ($formatbulan == "November") {
                                      $nama_bulan = "November";
                                  } elseif ($formatbulan == "December") {
                                      $nama_bulan = "Desember";
                                  }
                                  echo $nama_hari, ", ", date('d', $time), " ", $nama_bulan, " ", date('Y', $time);
                                  ?>
                                </td>
                                <td>
                                  <a href="<?= base_url('admin/pengaturanlembur_edit/' . $d->no_lembur) ?>" class="btn btn-primary btn-sm"><span class="fa fa-edit"></span></a>
                                  <a onclick="return confirm('apakah anda yakin ingin menghapus set jam lembur ini?')" href="<?= base_url('admin/pengaturanlembur_delete/' . $d->no_lembur) ?>" class="btn btn-danger btn-sm"><span class="fa fa-trash"></span></a>
                                </td>
                              </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>
