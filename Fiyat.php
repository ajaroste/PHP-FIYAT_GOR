<?php

$jSON = file_get_contents("ayarlar.json");
$data = json_decode($jSON,true);
$fytdeger = $data['Fdeger'];
$serverName = $data['Sunucu'];
$baglantiayari = array(
"Database" => $data['Database'],
"Uid" => $data['User'],
"PWD" => $data['Sifre']
);

$baglanti = sqlsrv_connect($serverName,$baglantiayari);

$barcode = $_POST['barcode'];
$fytdata ='';
$ind1fytdata ='';
$ind2fytdata ='';
if($fytdeger == "F1")
{
    $fytdata ='FIYAT1';
    $ind1fytdata ='F1IND1';
    $ind2fytdata ='F1IND2';
}
elseif($fytdeger == "F2") {

    $fytdata ='FIYAT2';
    $ind1fytdata ='F2IND1';
    $ind2fytdata ='F2IND2';
}
elseif($fytdeger =="F3") {

    $fytdata ='FIYAT3';
    $ind1fytdata ='F3IND1';
    $ind2fytdata ='F3IND2';
}
elseif($fytdeger == "F4") {
    $fytdata ='FIYAT4';
    $ind1fytdata ='F4IND1';
    $ind2fytdata ='F4IND2';
}
elseif($fytdeger == "F5") {
    $fytdata ='FIYAT5';
    $ind1fytdata ='F5IND1';
    $ind2fytdata ='F5IND2';
}
else{
    $fytdata ='FIYAT1';
};




$txt ="or STOK ='$barcode'";
$sql = "SELECT KODU,ADI, ADI2, SATIS_KDV1,ALIS_KDV1, DTURU, BIRIM1,
ANAGRUP, (SELECT ADI FROM STOK_ANA_GRUP SG WHERE SG.KODU=STOK.ANAGRUP) ANAGRUP_ADI,
ALTGRUP, (SELECT ADI FROM STOK_ALT_GRUP SG WHERE SG.KODU=STOK.ALTGRUP) ALTGRUP_ADI,
$fytdata FIYAT , DTURU1 FIYAT_DOVIZ, cast( ROUND( ($fytdata-(($fytdata/100*$ind1fytdata))) - (($fytdata-(($fytdata/100*$ind1fytdata))) /100*$ind2fytdata) ,2) as decimal(17,2))   YENIFIYAT, 
ISNULL((SELECT SUM(MIKTAR) FROM STOK_HAR WHERE STOK_HAR.KODU=STOK.KODU AND STOK_HAR.GDEPO='01'),0) - 
ISNULL((SELECT SUM(MIKTAR) FROM STOK_HAR WHERE STOK_HAR.KODU=STOK.KODU AND STOK_HAR.CDEPO='01'),0) MIKTAR 
,SKDV_SEKLI1,SKDV_SEKLI2,SKDV_SEKLI3,SKDV_SEKLI4,SKDV_SEKLI5,AKDV_SEKLI1 FROM STOK
WHERE KODU IN (SELECT STOK FROM STOK_BARKOD WHERE [BARKODNO]='$barcode')";



$params = array();
$query = sqlsrv_query($baglanti, $sql, $params);

if ($query === false) {
    die(print_r(sqlsrv_errors(), true)); 
}

if (sqlsrv_has_rows($query)) {
    $row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);

    $response['ADI'] = iconv('ISO-8859-9', 'UTF-8', $row['ADI']);
	$response['FIYAT'] = $row['FIYAT'];
	$response['INDFIYAT'] = $row['YENIFIYAT'];
    $response['DOVIZ'] = $row['FIYAT_DOVIZ'];
    $response['MIKTAR'] = $row['MIKTAR'];
    $response['BIRIM'] = $row['BIRIM1'];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} 
else {
    $response['HATA'] ="HATA";
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

?>
