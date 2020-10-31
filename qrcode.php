<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/phpqrcode/qrlib.php';


date_default_timezone_set('Europe/Paris');

if(isset($_POST['nonce_form'])) {
    setcookie('nonce_form', 'valid', time() + 365*24*3600, null, null, false, true);

    $fields['lname'] = htmlspecialchars(stripslashes(trim($_POST['lname'])));
    setcookie('lname', $fields['lname'], time() + 365*24*3600, null, null, false, true);

    $fields['fname'] = htmlspecialchars(stripslashes(trim($_POST['fname'])));
    setcookie('fname', $fields['fname'], time() + 365*24*3600, null, null, false, true);

    $birthdayDate = htmlspecialchars(stripslashes(trim($_POST['birthday_date'])));
    //Format date
    $date = new DateTime($birthdayDate);
    $fields['birthday_date'] = $date->format('d/m/Y');
    setcookie('birthday_date', $fields['birthday_date'], time() + 365*24*3600, null, null, false, true);

    $fields['birthday_place'] = htmlspecialchars(stripslashes(trim($_POST['birthday_place'])));
    setcookie('birthday_place', $fields['birthday_place'], time() + 365*24*3600, null, null, false, true);

    $fields['address'] = htmlspecialchars(stripslashes(trim($_POST['address'])));
    setcookie('address', $fields['address'], time() + 365*24*3600, null, null, false, true);

    $fields['city'] = htmlspecialchars(stripslashes(trim($_POST['city'])));
    setcookie('city', $fields['city'], time() + 365*24*3600, null, null, false, true); 
    
    $fields['postal_code'] = htmlspecialchars(stripslashes(trim($_POST['postal_code'])));
    setcookie('postal_code', $fields['postal_code'], time() + 365*24*3600, null, null, false, true); 

    $type_sortie = is_array($_POST['type_sortie']) ? implode(', ',$_POST['type_sortie']) : $_POST['type_sortie'];
    $fields['type_sortie'] = htmlspecialchars(stripslashes(trim($type_sortie)));
    setcookie('type_sortie', $fields['type_sortie'], time() + 365*24*3600, null, null, false, true); 



    createQRCode($fields);
    
} elseif ($_COOKIE['nonce_form']) {

    $fields['lname'] = $_COOKIE['lname'];
    $fields['fname'] = $_COOKIE['fname'];
    $fields['birthday_date'] = $_COOKIE['birthday_date'];
    $fields['birthday_place'] = $_COOKIE['birthday_place'];
    $fields['address'] = $_COOKIE['address'];
    $fields['city'] = $_COOKIE['city'];
    $fields['postal_code'] = $_COOKIE['postal_code'];
    $fields['type_sortie'] = $_COOKIE['type_sortie'];


    createQRCode($fields);

} else {
    header('Location: index.php');
    exit();
}

/**
 * createQRCode
 *
 * @param  array $fields
 * @return void
 */
function createQRCode(array $fields) {
    
    extract($fields);
    // Get current date
    $currentDate = date('d/m/Y');
    $currentTime = date('G\hi');

    $qrText = "Cree le: $currentDate a $currentTime;
    Nom: $lname;
    Prenom: $fname;
    Naissance: $birthday_date a $birthday_place;
    Adresse: $address $postal_code $city;
    Sortie: $currentDate a $currentTime;
    Motifs: $type_sortie";

    QRcode::png($qrText, 'qrcode.png'); // creates file
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML('<h1>Hello world!</h1>');
    $mpdf->Image('qrcode.png', 0, 0, 100, 100, 'png', '', true, false);
    $mpdf->Output();
}
