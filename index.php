<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Galerie PHP</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <?php
      if (isset($_GET['success']) && $_GET['success'] === 'true') {
          echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  Les données IPTC ont été modifiées avec succès.
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>';
      } elseif (isset($_GET['error']) && $_GET['error'] == 'true') {
          echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                  Erreur: les données IPTC n\'ont pas pu être incorporées dans l\'image.
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>';
      }
    ?>
      <div class="container mt-5">
        <h1 class="text-center">Galerie d'images</h1>
        <hr>
        <div class="row">
          <?php

            // Chemin d'accès au dossier contenant les images
            $directory = 'img';

    // Récupération de tous les fichiers du dossier
    try {
        // Récupération de tous les fichiers du dossier
        $files = scandir($directory);
    } catch (Exception $e) {
        // Journalisation de l'erreur
        error_log('Erreur lors de la récupération des fichiers du dossier : ' . $e->getMessage());
        echo '<div class="alert alert-danger" role="alert">
        Une erreur est survenue lors de la récupération des fichiers.
      </div>';
        exit();
    }

      // Boucle sur tous les fichiers du dossier
      foreach ($files as $file) {
          // Vérification que le fichier est une image
          $extension = pathinfo($file, PATHINFO_EXTENSION);
          if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
              // Chemin d'accès complet au fichier
              $image_path = $directory . '/' . $file;

              // Affichage des informations sur l'image
              $size = getimagesize($image_path, $info);
              if ($size !== false) {
                  // Nouvelle variable pour stocker les données IPTC
                  $iptc = iptcparse($info['APP13']);

                  // Stockage des données IPTC dans des variables
                  $keyword = isset($iptc['2#025']) ? $iptc['2#025'][0] : '';
                  $comment = isset($iptc['2#120']) ? $iptc['2#120'][0] : '';
                  $category = isset($iptc['2#055']) ? $iptc['2#055'][0] : '';
                  $country = isset($iptc['2#101']) ? $iptc['2#101'][0] : '';
                  $city = isset($iptc['2#090']) ? $iptc['2#090'][0] : '';
                  $category = isset($iptc['2#015']) ? $iptc['2#015'][0] : '';

                  echo '<div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">';
                  echo '<a href="#" data-toggle="modal" data-target="#exampleModal" data-file="' . $image_path . '">';
                  echo '<img src="' . $image_path . '" alt="' . $file . '" class="image">';
                  echo '</a>';
                  // Modification pour stocker les valeurs dans les champs du formulaire avec des data attributes
                  echo '<div class="d-none">';
                  echo '<div data-keyword="' . $keyword . '" data-comment="' . $comment. ' " data-country="' . $country . '" data-category="' . $category . '" data-city="' . $city . '" data-category="' . $category . '"></div>';
                  echo '</div>';
                  echo '</div>';
              }
          }
      }
    ?>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Informations sur l'image</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <img src="" alt="" class="img-fluid" id="modal-image">
            <form method="post" action="modifier_iptc.php">
              <div class="form-group">
                <label for="keyword-input">Mot-clé :</label>
                <input type="text" class="form-control" id="keyword-input" name="keyword-input">
              </div>
              <div class="form-group">
                <label for="comment-input">Commentaire :</label>
                <textarea class="form-control" id="comment-input" name="comment-input" rows="3"></textarea>
              </div>
              <div class="form-group">
                <label for="category-input">Catégorie :</label>
                <input type="text" class="form-control" id="category-input" name="category-input">
              </div>
              <div class="form-group">
                <label for="country-input">Pays :</label>
                <input type="text" class="form-control" id="country-input" name="country-input">
              </div>
              <div class="form-group">
                <label for="city-input">Ville :</label>
                <input type="text" class="form-control" id="city-input" name="city-input">
              </div>
              <input type="hidden" name="image_path" id="image-path-input" value="">
              <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
              <button type="submit" class="btn btn-primary">Enregistrer</button>
              </div>
            </form>


        </div>
      </div>
    </div>


      <script>
        $(document).ready(function() {
            $('#exampleModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var file = button.data('file');
                var modalImage = $(this).find('#modal-image');
                modalImage.attr('src', file);
                var imageInput = $(this).find('#image-path-input');
                imageInput.val(file);

                // Récupération des données IPTC correspondant à l'image cliquée
                var iptcDiv = button.parent().find('div[data-keyword][data-comment][data-category][data-country][data-city]');
            
            
                var keyword = iptcDiv.data('keyword');
                var comment = iptcDiv.data('comment');
                var category = iptcDiv.data('category');
                var country = iptcDiv.data('country');
                var city = iptcDiv.data('city');

                // Remplissage des champs du formulaire avec les données IPTC
                $(this).find('#keyword-input').val(keyword);
                $(this).find('#comment-input').val(comment);
                $(this).find('#category-input').val(category);
                $(this).find('#country-input').val(country);
                $(this).find('#city-input').val(city);
            });
        });



      </script>
  </body>
</html>
