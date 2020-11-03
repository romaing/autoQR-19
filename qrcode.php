<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/phpqrcode/qrlib.php';


if(empty($_POST['type_sortie'])) {
    header('Location: index.php/?error=sortie');
    exit();
}

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
    setcookie('birthday_date_unformat', $birthdayDate, time() + 365*24*3600, null, null, false, true);
    setcookie('birthday_date', $fields['birthday_date'], time() + 365*24*3600, null, null, false, true);

    $fields['birthday_place'] = htmlspecialchars(stripslashes(trim($_POST['birthday_place'])));
    setcookie('birthday_place', $fields['birthday_place'], time() + 365*24*3600, null, null, false, true);

    $fields['address'] = htmlspecialchars(stripslashes(trim($_POST['address'])));
    setcookie('address', $fields['address'], time() + 365*24*3600, null, null, false, true);

    $fields['city'] = htmlspecialchars(stripslashes(trim($_POST['city'])));
    setcookie('city', $fields['city'], time() + 365*24*3600, null, null, false, true); 
    
    $fields['postal_code'] = htmlspecialchars(stripslashes(trim($_POST['postal_code'])));
    setcookie('postal_code', $fields['postal_code'], time() + 365*24*3600, null, null, false, true); 

    
    $type_sortie = $_POST['type_sortie'];
    $fields['type_sortie'] = is_array($type_sortie) ? implode(', ', $type_sortie) : $type_sortie;
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

function createPage(array $fields, $currentDate, $currentTime ,$file ) {
    extract($fields);


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
    
    <p style='text-align:center;font-family:arial;'>
    En application du décret n°2020-1310 du 29 octobre 2020 prescrivant les mesures générales nécessaires pour faire face à l'épidémie de Covid19 dans le cadre de l'état d'urgence sanitaire. 
    </p>
    <div style='font-family:arial;'>
    <p>Je soussigné(e), </p>
    <p>Mme/M. : $lname $fname</p>
    <p>Né(e) le : $birthday_date &nbsp; &nbsp; &nbsp; &nbsp; à : $birthday_place</p>
    <p>Demeurant : $address $postal_code $city </p>
    <p>certifie que mon déplacement est lié au motif suivant (cocher la case) autorisé par le décretn°2020-1310 du 29 octobre 2020 prescrivant les mesures générales nécessaires pour faire face àl'épidémie de Covid19 dans le cadre de l'état d'urgence sanitaire <sup>1</sup> :</p>
    <table style='font-family:arial;'>
    ";

    $type_sortie = explode(', ', $type_sortie);

    foreach ($inputsSortie as $key => $input) : 
        $check = ! in_array($key, $type_sortie  ) ? "□": "☒";
        $page .= "
            <tr><td style='font-size:24px; width:50px;'>$check</td><td>$input</td></tr>
        ";
    endforeach;

    
    $page .= "
    </table>";

    $page .= "
    <table width=100% border=0 style='font-family:arial;'>
    <tr>
    <td><p>Fait à : $city</p>
    <p>Le : $currentDate &nbsp; &nbsp; &nbsp; à: $currentTime </p>
    <p>(Date et heure de début de sortie à mentionner obligatoirement) </p>
    <p>Signature : </p></td>
    <td width=100><img src=\"$file\" width=100 ></td>
    </tr>
    </table>
    
    <div style='font-size:11px;'>
        <p><sup>1</sup> Les personnes souhaitant bénéficier de l'une de ces exceptions doivent se munir s'il y a lieu, lors de leurs déplacements hors de leur domicile, d'un document leur permettant de justifier que le déplacement considéré entre dans le champ de l'une de ces exceptions. </p>
        <p><sup>2</sup> A utiliser par les travailleurs non-salariés, lorsqu'ils ne peuvent disposer d'un justificatif de déplacement établi par leur employeur. </p>
        <p><sup>3</sup> Y compris les acquisitions à titre gratuit (distribution de denrées alimentaires...) et les déplacements liés à la perception de prestations sociales et au retrait d'espèces. </p>
    </div>
    </div>
    


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
    $decalage = time() - (30 * 60);
    $currentDate = date('d/m/Y', $decalage);
    $currentTime = date('G\hi', $decalage );

    $file = 'qrcode'.date('-ymd-hi').'.png';
    $type_sortie = $type_sortie ?? '';
    $qrText = "Cree le: $currentDate a $currentTime;
    Nom: $lname;
    Prenom: $fname;
    Naissance: $birthday_date a $birthday_place;
    Adresse: $address $postal_code $city;
    Sortie: $currentDate a $currentTime;
    Motifs: $type_sortie";

    QRcode::png($qrText, $file); // creates file
    $mpdf = new \Mpdf\Mpdf();

    $mpdf->WriteHTML( createPage( $fields, $currentDate, $currentTime ,$file  ) , \Mpdf\HTMLParserMode::HTML_BODY);

    $mpdf->SetTitle('Mon attestation');
    $mpdf->AddPage();
    $mpdf->Image($file, 0, 0, 100, 100, 'png', '', true, false);
    $mpdf->Output('mon_attestation.pdf', 'I');
    unlink ( $file );
    file_put_contents('273244'.date('ymd').'.txt', $fname. ' ' . $lname[0] .'. a généré une attestation !'.PHP_EOL);
}

