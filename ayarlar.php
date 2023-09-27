<?php
$server = $_POST["server"];
$database = $_POST["database"];
$sa = $_POST["sa"];
$sifre = $_POST["sifre"];
$Fdeger = $_POST["Fdeger"] ;

$yeniVeri = array(
    "Sunucu"=> "$server",
    "Database"=> "$database",
    "User"=> "$sa",
    "Sifre"=> "$sifre",
    "Fdeger"=> "$Fdeger"

);

// JSON dosyasını oku
$dosyaAdi = 'ayarlar.json';
$mevcutVeriler = json_decode(file_get_contents($dosyaAdi), true);

// Yeni veriyi mevcut verilere ekle
$mevcutVeriler[] = $yeniVeri;

// JSON dosyasına yeniden yaz
file_put_contents($dosyaAdi, json_encode($yeniVeri, JSON_PRETTY_PRINT));



$jSON = file_get_contents("ayarlar.json");
$data = json_decode($jSON,true);
$serverName = $data['Sunucu'];
$baglantiayari = array(
"Database" => $data['Database'],
"Uid" => $data['User'],
"PWD" => $data['Sifre']
);

$baglanti = sqlsrv_connect($serverName,$baglantiayari);


if (!$baglanti) {
    // Hata kodunu ve hata iletisini al
    $errorCode = sqlsrv_errors()[0]['code'];
    $errorMessage = sqlsrv_errors()[0]['message'];

    // Hata koduna göre kullanıcıya giriş hatası mesajını göster
    if ($errorCode == 18456) {
        $response['DATA'] ="SBB";
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
  // İşlemi sonlandır
    exit();
}



$sql = "select * from STOK_BARKOD";


$params = array();
$query = sqlsrv_query($baglanti, $sql, $params);

// if ($query === false) {
//     die(print_r(sqlsrv_errors(), true)); 
// }

if (sqlsrv_has_rows($query)) {
    $response['DATA'] ="VGB";
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} 
// else {
//     $response['DATA'] ="SBB";
//     echo json_encode($response, JSON_UNESCAPED_UNICODE);
// }





?>
