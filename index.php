<?php

/**
 * Version : 1.0.0
 */
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <style>
        input[type=checkbox] {
            margin: 6px 5px 0;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1>Auto QRCode Covid-19 😷</h1>
        <p><strong>autoQR-19</strong> est un outil vous permettant, à chaque actualisation de la page, de regénérer automatiquement votre QR Code d'attestation de sortie. Le QR Code généré sera bien sûr <strong>identique</strong> à ceux générés par le site du gouvernement.</p>
        <p>Entrez vos information dans les champs ci-dessous pour générer votre QR Code. Pour le régénerer avec l'heure de sortie à l'heure actuelle, actualisez seulement le PDF.</p>
        <form action="qrcode.php" method="post">
            <div class="form-row">

                <?php

                $inputsFields = [
                    'lname' => [
                        'type' => 'text',
                        'label' => 'Votre nom',
                        'size' => 'col-6'
                    ],
                    'fname' => [
                        'type' => 'text',
                        'label' => 'Votre nom',
                        'size' => 'col-6'
                    ],
                    'birthday_date' => [
                        'type' => 'text',
                        'label' => 'Date de naissance',
                        'size' => 'col-6',
                        'args' => 'onfocus="(this.type=\'date\')" onblur="(this.type=\'text\')"'
                    ],
                    'birthday_place' => [
                        'type' => 'text',
                        'label' => 'Lieu de naissance',
                        'size' => 'col-6'
                    ],
                    'address' => [
                        'type' => 'text',
                        'label' => 'Adresse',
                        'size' => 'col-12'
                    ],
                    'city' => [
                        'type' => 'text',
                        'label' => 'Ville',
                        'size' => 'col-6'
                    ],
                    'postal_code' => [
                        'type' => 'text',
                        'label' => 'Code Postal',
                        'size' => 'col-6'
                    ],
                ];

                foreach ($inputsFields as $key => $input) : ?>
                    <div class="form-group <?= $input['size'] ?>">
                        <input class="form-control form-control-lg <?= isset($input['class']) ? $input['class'] : '' ?>" name="<?= $key ?>" id="<?= $key ?>" placeholder="<?= $input['label'] ?>" <?= isset($input['args']) ? $input['args'] : '' ?>">
                    </div>
                <?php endforeach; ?>
                <input type="hidden" name="nonce_form">
                <!--------- TYPE SORTIE ------->

                <div class="form-group">
                    <?php
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

                    foreach ($inputsSortie as $key => $input) : ?>
                        <div class="d-flex">
                            <input class="d-block w-10" type="checkbox" id="<?= $key ?>" name="type_sortie[]" value="<?= $key ?>">
                            <label class="d-block w-90" for="<?= $key ?>"><?= $input ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" class="w-100 mb-3 form-submit btn btn-primary">Envoyer</button>

        </form>
        <p><br><em>Ce site à recourt à l'utilisation de cookies utile à ce que vous n'ayez pas à retaper sans cesse vos informations !</em></p>
    </div>
</body>

</html>