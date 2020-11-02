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
function createPage(array $fields) {
    extract($fields);

    $currentDate = date('d/m/Y');
    $currentTime = date('G\hi');

    $inputsSortie = [
        'travail' => 'Déplacements entre le domicile et le lieu d’exercice de l’activité professionnelle ou un établissement d’enseignement ou de formation, déplacements professionnels ne pouvant être différés, déplacements pour un concours ou un examen;',
        'achats' => 'Déplacements pour effectuer des achats de fournitures nécessaires à l\'activité professionnelle, des achats de première nécessité dans des établissements dont les activités demeurent autorisées, le retrait de commande et les livraisons à domicile ;',
        'sante' => 'Consultations, examens et soins ne pouvant être assurés à distance et l’achat de médicaments ;',
        'famille' => 'Déplacements pour motif familial impérieux, pour l\'assistance aux personnes vulnérables et précaires ou la garde d\'enfants ;',
        'handicap' => 'Déplacement des personnes en situation de handicap et leur accompagnant ;',
        'animaux' => 'Déplacements brefs, dans la limite d\'une heure quotidienne et dans un rayon maximal d\'un kilomètre autour du domicile, liés soit à l\'activité physique individuelle des personnes, à l\'exclusion de toute pratique sportive collective et de toute proximité avec d\'autres personnes, soit à la promenade avec les seules personnes regroupées dans un même domicile, soit aux besoins des animaux de compagnie ;',
        'convocation' => 'Convocation judiciaire ou administrative et pour se rendre dans un service public ;',
        'missions' => 'Participation à des missions d\'intérêt général sur demande de l\'autorité administrative ;',
        'enfants' => 'Déplacement pour chercher les enfants à l’école et à l’occasion de leurs activités périscolaires ;'
    ];

    $page = "
    <h2 style='text-align:center;'>ATTESTATION DE DÉPLACEMENT DÉROGATOIRE </h2>
    
    <p style='text-align:center;'>
    En application du décret n°2020-1310 du 29 octobre 2020 prescrivant les mesures générales nécessaires pour faire face à l'épidémie de Covid19 dans le cadre de l'état d'urgence sanitaire 
    </p>
    
    <p>Je soussigné(e), </p>
    <p>Mme/M. : $lname $fname</p>
    <p>Né(e) le : $birthday_date &nbsp; &nbsp; &nbsp; &nbsp; à : $birthday_place</p>
    <p>Demeurant : $address $postal_code $city </p>
    <p>certifie que mon déplacement est lié au motif suivant (cocher la case) autorisé par le décretn°2020-1310 du 29 octobre 2020 prescrivant les mesures générales nécessaires pour faire face àl'épidémie de Covid19 dans le cadre de l'état d'urgence sanitaire <sup>1</sup> :</p>
    <table>
    ";

    foreach ($inputsSortie as $key => $input) : 
        $check = ($key!=$type_sortie)? "□": "x";
        $page .= "
            <tr><td>$check</td><td>$input</td></tr>
        ";
    endforeach;
    $page .= "
    </table>";

    $page .= "
    <p>Fait à : </p>
    <p>Le : $currentDate &nbsp; &nbsp; &nbsp; à: $currentTime </p>
    <p>(Date et heure de début de sortie à mentionner obligatoirement) </p>
    <p>Signature : </p>
    <hr>
    <div style='font-size:10px;'>
        <p>1 Les personnes souhaitant bénéficier de l'une de ces exceptions doivent se munir s'il y a lieu, lors de leurs déplacements hors de leur domicile, d'un document leur permettant de justifier que le déplacement considéré entre dans le champ de l'une de ces exceptions. </p>
        <p>2 A utiliser par les travailleurs non-salariés, lorsqu'ils ne peuvent disposer d'un justificatif de déplacement établi par leur employeur. </p>
        <p>3 Y compris les acquisitions à titre gratuit (distribution de denrées alimentaires...) et les déplacements liés à la perception de prestations sociales et au retrait d'espèces. toto toto01/01/1965paris999 av de foch 75001 paris</p>
    </div>

    <div style='clear:both;'></div>

    ";

    return $page;
    

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

    $file = 'qrcode'.date('-ymd-hi').'.png';

    $qrText = "Cree le: $currentDate a $currentTime;
    Nom: $lname;
    Prenom: $fname;
    Naissance: $birthday_date a $birthday_place;
    Adresse: $address $postal_code $city;
    Sortie: $currentDate a $currentTime;
    Motifs: $type_sortie";

    QRcode::png($qrText, $file); // creates file
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML( createPage( $fields ) , \Mpdf\HTMLParserMode::HTML_BODY);
    $mpdf->SetTitle('Mon attestation');

    $mpdf->Image($file, 0, 0, 100, 100, 'png', '', true, false);
    $mpdf->Output();

    
}

