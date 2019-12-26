<?php
 
 $db_server="localhost";
 $db_user="root";
 $db_password="";
 $db_name="test_spk";
 $con=mysqli_connect($db_server,$db_user,$db_password) or die("tidak connect");
 mysqli_select_db ($con,$db_name) or die("db salah");
 

 //Buat array bobot { C1 = 35%; C2 = 30%; C3 = 25%; dan C4 = 10%.}
 $bobot = array(0.35,0.30,0.25,0.10);

 //Buat fungsi tampilkan nama
 function getNama($id){
   $db_server="localhost";
   $db_user="root";
   $db_password="";
   $db_name="test_spk";
   $con=mysqli_connect($db_server,$db_user,$db_password) or die("tidak connect");
   mysqli_select_db ($con,$db_name) or die("db salah");
   
  $q =mysqli_query($con,"SELECT * FROM tbcalon where idCalon = '$id'");
  $d = mysqli_fetch_array($q);
  return $d['nama'];
 }
 
 
 
 //Setelah bobot terbuat select semua di tabel Matrik
 $sql = mysqli_query($con,"SELECT * FROM tbmatrik");
 //Buat tabel untuk menampilkan hasil
 echo "<H3>Matrik Awal</H3>
 <table width=500 style='border:1px; #ddd; solid; border-collapse:collapse' border=1>
  <tr>
   <td>No</td>
   <td>Nama</td>
   <td>C1</td>
   <td>C2</td>
   <td>C3</td>
   <td>C4</td>
   <td>jumlah poin</td>
  </tr>
  ";
 $no = 1;
 while ($dt = mysqli_fetch_array($sql)) {
  $jumlah= ($dt['Kriteria1'])+($dt['Kriteria2'])+($dt['Kriteria3'])+($dt['Kriteria4']);
  echo "<tr>
   <td>$no</td>
   <td>".getNama($dt['idCalon'])."</td>
   <td>$dt[Kriteria1]</td>
   <td>$dt[Kriteria2]</td>
   <td>$dt[Kriteria3]</td>
   <td>$dt[Kriteria4]</td>
   <td>$jumlah</td>
  </tr>";
 $no++;
 }
 echo "</table>";

 //Lakukan Normalisasi dengan rumus pada langkah 2
 //Cari Max atau min dari tiap kolom Matrik untuk normalisasi
 $crMax = mysqli_query($con,"SELECT max(Kriteria1) as maxK1, 
      max(Kriteria2) as maxK2,
      max(Kriteria3) as maxK3,
      max(Kriteria4) as maxK4 
   FROM tbmatrik");
 $max = mysqli_fetch_array($crMax);

 //Hitung Normalisasi tiap Elemen
 $sql2 = mysqli_query($con,"SELECT * FROM tbmatrik");
 //Buat tabel untuk menampilkan hasil
 echo "<H3>Matrik Normalisasi</H3>
 <table width=500 style='border:1px; #ddd; solid; border-collapse:collapse' border=1>
  <tr>
   <td>No</td>
   <td>Nama</td>
   <td>C1</td>
   <td>C2</td>
   <td>C3</td>
   <td>C4</td>
  </tr>
  ";
 $no = 1;
 while ($dt2 = mysqli_fetch_array($sql2)) {
  echo "<tr>
   <td>$no</td>
   <td>".getNama($dt2['idCalon'])."</td>
   <td>".round($dt2['Kriteria1']/$max['maxK1'],2)."</td>
   <td>".round($dt2['Kriteria2']/$max['maxK2'],2)."</td>
   <td>".round($dt2['Kriteria3']/$max['maxK3'],2)."</td>
   <td>".round($dt2['Kriteria4']/$max['maxK4'],2)."</td>
  </tr>";
 $no++;
 }
 echo "</table>";

 //Proses perangkingan dengan rumus langkah 3
 $sql3 = mysqli_query($con,"SELECT * FROM tbmatrik");
 //Buat tabel untuk menampilkan hasil
 echo "<H3>Perangkingan</H3>
 <table width=500 style='border:1px; #ddd; solid; border-collapse:collapse' border=1>
  <tr>
   <td>no</td>
   <td>Nama</td>
   <td>total poin</td>
   <td>SAW</td>
   <td>ket</td>
  </tr>
  ";

//Kita gunakan rumus (Normalisasi x bobot)
 while ($dt3 = mysqli_fetch_array($sql3)) {
  $jumlah= ($dt3['Kriteria1'])+($dt3['Kriteria2'])+($dt3['Kriteria3'])+($dt3['Kriteria4']);
  $poin= round(
   (($dt3['Kriteria1']/$max['maxK1'])*$bobot[0])+
   (($dt3['Kriteria2']/$max['maxK2'])*$bobot[1])+
   (($dt3['Kriteria3']/$max['maxK3'])*$bobot[2])+
   (($dt3['Kriteria4']/$max['maxK4'])*$bobot[3]),3);

  $data[]=array('nama'=>getNama($dt3['idCalon']),
      'jumlah'=>$jumlah,
      'poin'=>$poin);

 }


//mengurutkan data
   foreach ($data as $key => $isi) {
    $nama[$key]=$isi['nama'];
    $jlh[$key]=$isi['jumlah'];
    $poin1[$key]=$isi['poin'];
   }
   array_multisort($poin1,SORT_ASC,$jlh,SORT_ASC,$data);
   $no=1;
   $h="prioritas";
   $prioritas=1;
   $hr=1;

   foreach ($data as $item) { ?>
   <tr>
   <td><?php echo $no ?></td>
   <td><?php echo$item['nama'] ?></td>
   <td><?php echo$item['jumlah'] ?></td>
   <td><?php echo$item['poin'] ?></td>
   <td><?php echo"$h $prioritas" ?></td>
   </tr>
   <?php
   $no++;
   if ($no>=50) {
    $h="  ";
    $prioritas=" ";
   }else{
    $prioritas++;
   }

   }
   echo "</table>";
?>

