<?php
require_once __DIR__ . "/../functions/db.php";
require_once __DIR__ . "/../functions/helpers.php";

// 1. Si l'identifiant du film envoyer dans la barre d'URL n'existe pas
    // si c'est pas déclarer et déclarer mais null ou vide
    if (!isset($_GET['film_id']) || empty($_GET['film_id'])) {
        // Alors effectuer une redirection vers la page d'index
        // Puis arrêter l'exécution du script
        header("Location: index.php");
        die();
    }

// Dans le cas contraire,
// 2. Récuperer l'identifiant du film depuis la barre d'URL ($_GET),
// Protéger le serveur contre les failles XSS
// Convertit l'identifiant en entier.
$filmId = (int) htmlspecialchars($_GET['film_id']); // (int) convertit le 'string' en 'int'
            // htmlspecialchars permet de contrer les intru
// $filmId = strip_tags($_GET['film_id']); // n'affiche pas les balise script
// echo $filmId; die();

// 3. Etablir une connexion avec la base de données
// Afin de vérifier si l'identifiant correspond à un film qui existe vraiment.
 $film = getFilm($filmId);

// 4. Si le film n'exite pas,
if (false === $film) {
    // Alors effectuer une redirection vers la page index
    // Puis, arrêter l'execution du script.
    header("Location: index.php");
    die();
}
?>



<?php

// 5. Dans le cas contraire donc film existe ,
// Récuperer le film pour affichage.

    $title = "Les détails de ce film";
    $description = "Les détails de ce film";
    $keywords = "Cinema, répertoire, lire, film, dwwm22";
?>
<?php include_once __DIR__ . "/../partials/head.php"; ?>

    <?php include_once __DIR__ . "/../partials/nav.php"; ?>

        <!-- Main: Le contenu spécifique à cette page -->
        <main class="container">
            <h1 class="text-center my-3 display-5">Les détails de ce film</h1>

            <p class="text-center my-4">
                <small>
                    Ajouté le <?= (new DateTime($film['created_at']))->format('d/m/Y \à H:i:s'); ?>
                </small>
                <br>
                <small>
                    <?php if(isset($film['updated_at']) && !empty($film['updated_at'])) : ?>
                        Modifié le <?= (new DateTime($film['updated_at']))->format('d/m/Y \à H:i:s'); ?>
                    <?php endif ?>
                </small>
            </p>

            <div class="container">
                <div class="row">
                    <div class="col-md-6 mx-auto">
                        <article class="film-card bg-white p-4 rounded shadow mb-4">
                            <h2>Titre: <?= htmlspecialchars($film['title']); ?></h2>
                            <p>Note: <?= isset($film['rating']) && $film['rating'] !== "" ? displayStars((float) htmlspecialchars($film['rating'])) : 'Non renseignée'; ?></p>
                            <p>Commentaire: <?= isset($film['comment']) && $film['comment'] !== "" ? htmlspecialchars($film['comment']) : 'Non renseigné'; ?></p>
                            <hr>
                            <div class="d-flex justify-content-start align-items-center gap-2">
                                <a href="edit.php" class="btn btn-sm btn-secondary">Modifier</a>
                                <a href="delete.php" class="btn btn-sm btn-danger">Supprimer</a>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </main>

    <?php include_once __DIR__ . "/../partials/footer.php"; ?>

<?php include_once __DIR__ . "/../partials/foot.php"; ?>
