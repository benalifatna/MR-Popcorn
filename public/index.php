<?php
    session_start();

    require_once __DIR__ . "/../functions/db.php";

    // 1. Etablir une connexion avec la base de donnée
    // 2. Effectuer la requête de sélection de les films de la base de données
    $films = getFilms();
   
    //var_dump($films); die () ;

?>
<?php
// on le déclare avant partials.head pour que title s'affiche
    $title=" Liste des films";
    $description = "Découvrez la liste complète de mes films : notes, commentaires et fiches détaillées. Répertoire cinéma mis à jour régulièrement.";
    $keywords = "Cinéma, repertoire, film, dwwm22";
?>
<!-- Au lieu include_once on peut utiliser require_once -->
<?php include_once __DIR__ . "/../partials/head.php";?>

    <?php include_once __DIR__ . "/../partials/nav.php";?>


        <!-- Main -->
        <main class="container">
            <h1 class="text-center my-3 display-5">Liste des films</h1>

             <!-- d-flex : display flex ; justify-content-end : a droite -->
            <div class="d-flex justify-content-end align-items-center my-3">
                <a href="/create.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    Ajouter film</a>
            </div>

        <?php if(isset($_SESSION['success']) && !empty($_SESSION['success'])) : ?>   
                             <!-- Affichage du message flash -->
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
               <?php  unset($_SESSION['success']); ?>
            <?php endif ?>

            <?php if(count($films) > 0) : ?>
            <div class="container">
                <div class="row">
                    <div class="col-md-6 mx-auto">
                        <?php foreach($films as $film) : ?>
                        <article class="film-card bg-white p-4 rounded shadow mb-4">
                             
                            <!-- <h2>Titre : Tarzan</h2>
                            <p>Note : 4.5</p>
                            <br> -->
                            <h2>Titre: <?= htmlspecialchars($film['title']); ?></h2>
                            <p>Note: <?= isset($film['rating']) && $film['rating'] !== "" ? htmlspecialchars((float) $film['rating']) : 'Non renseignée'; ?></p>
                            <hr>
                            <div class="d-flex justify-content-start align-items-center gap-2">
                                <a href="" class="btn btn-sm btn-dark">Voir détails</a>
                                <a href="" class="btn btn-sm btn-secondary">Modifier</a>
                                <a href="" class="btn btn-sm btn-danger">Supprimer</a>

                            </div>

                        </article>
                       <?php endforeach  ?>
                    </div>

                </div>
            </div>
        <?php else :  ?>
                <p class="mt-5">Aucun film ajouté à la liste.</p>
            <?php endif ?>

        </main>

  <?php include_once __DIR__ . "/../partials/footer.php";?>

 <?php include_once __DIR__ . "/../partials/foot.php";?>
    
