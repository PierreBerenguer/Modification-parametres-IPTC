<?php

    $image_path = $_POST['image_path'];
    $keyword = $_POST['keyword-input'];
    $comment = $_POST['comment-input'];
    $category = $_POST['category-input'];
    $country = $_POST['country-input'];
    $city = $_POST['city-input'];

    // Récupération des données IPTC existantes
    try {
        $info = getimagesize($image_path, $iptc);
        $iptc = isset($iptc['APP13']) ? iptcparse($iptc['APP13']) : [];
    } catch (Exception $e) {
        // Journalisation de l'erreur
        error_log('Erreur lors de la récupération des données IPTC : ' . $e->getMessage());
    }
    // Mise à jour des données IPTC
    $iptc['2#025'] = $keyword;
    $iptc['2#120'] = $comment;
    $iptc['2#015'] = $category;
    $iptc['2#101'] = $country;
    $iptc['2#090'] = $city;

    // Ré-encodage des données IPTC dans l'image
    $iptc_data = '';
    foreach ($iptc as $tag => $data) {
        try {
            $iptc_data .= iptc_make_tag(2, substr($tag, 2), $data);
        } catch (Exception $e) {
            // Journalisation de l'erreur
            error_log('Erreur lors de la création des données IPTC : ' . $e->getMessage());
        }
    }

    $content = iptcembed($iptc_data, $image_path);

    if ($content !== false) {
        file_put_contents($image_path, $content);
        $_SESSION['success_message'] = "Les données IPTC ont été modifiées avec succès.";
    } else {
        // Journalisation de l'erreur
        error_log("Impossible d'incorporer les données IPTC dans l'image : " . error_get_last());
        $_SESSION['error_message'] = "Erreur: les données IPTC n'ont pas pu être incorporées dans l'image.";
    }


    // Redirection vers la page précédente
    header('Location: index.php'. '?success=true');
    exit();

    function iptc_make_tag($rec, $data, $value)
    {
        if (is_array($value)) {
            $value = implode('', $value);
        }

        $length = strlen($value);
        $retval = chr(0x1C) . chr($rec) . chr($data);

        if ($length < 0x8000) {
            $retval .= chr($length >> 8) . chr($length & 0xFF);
        } else {
            $retval .= chr(0x80) . chr(0x04);
            $retval .= chr(($length >> 24) & 0xFF);
            $retval .= chr(($length >> 16) & 0xFF);
            $retval .= chr(($length >> 8) & 0xFF);
            $retval .= chr($length & 0xFF);
        }

        return $retval . $value;
    }
