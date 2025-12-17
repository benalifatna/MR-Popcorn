<?php
session_start();

require_once __DIR__ . "/../functions/db.php";
require_once __DIR__ . "/../functions/helpers.php";

// 1. Si l'identifiant du film à envoyé dans la barre d'URL n'existe pas
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
// tenter de récuperer le film
 $film = getFilm($filmId);

// 4. Si le film n'exite pas,
if (false === $film) {
    // Alors effectuer une redirection vers la page index
    // Puis, arrêter l'execution du script.
    header("Location: index.php");
    die();
}

// 7. Traitement du formulaire

    /*
     * ----------------------------------------------------------------
     * Traitement des données provenant du formulaire 
     * ---------------------------------------------------------------- 
     */

    //  7. Si les données du formulaire sont envoyées via la méthode POST,
    if ( $_SERVER['REQUEST_METHOD'] === "POST" ) {
    
        // Alors,
        // 8. Protéger le serveur contre les failles de sécurité
        // 8a. Les failles de type csrf
        if ( 
            !isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) ||
            empty($_POST['csrf_token'])  || empty($_SESSION['csrf_token'])  ||
            $_POST['csrf_token'] !== $_SESSION['csrf_token']
        ) {
            // Effectuer une redirection vers la page de laquelle proviennent les informations
            // Puis arrêter l'exécution du script.
            header('Location: edit.php');
            die();
        }
        unset($_SESSION['csrf_token']);
        unset($_POST['csrf_token']);

        
        // 8b. Les robots spameurs
        // Si le pot de miel n'existe pas ou qu'il n'est pas vide,
        if ( !isset($_POST['honey_pot']) || !empty($_POST['honey_pot']) ) {
            // Effectuer une redirection vers la page de laquelle proviennent les informations
            // Puis arrêter l'exécution du script.
            header('Location: edit.php');
            die();
        }
        unset($_POST['honey_pot']);
        
        // 9. Procéder à la validation des données du formulaire
        $formErrors = [];

        // Si le titre est déclaré et différent de null
        if (isset($_POST['title'])) {
            $title = trim($_POST['title']);

            if ("" === $title) {
                $formErrors['title'] = "Le titre est obligatoire.";
            } else if( mb_strlen($title) > 255 ) {
                $formErrors['title'] = "Le titre ne doit pas dépasser 255 caractères.";
            }
        }

        // Note
        if (isset($_POST['rating']) && $_POST['rating'] !== "") {
            $rating = trim($_POST['rating']); 

            if ( !is_numeric($rating) ) {
                $formErrors['rating'] = "La note doit être un nombre.";
            } else if ( floatval($rating) < 0 || floatval($rating) > 5 ) {
                $formErrors['rating'] = "La note doit être comprise entre 0 et 5.";
            }
        }

        // Comment
        if (isset($_POST['comment']) && $_POST['comment'] !== "") {
            $comment = trim($_POST['comment']); 

            if ( mb_strlen($comment) > 1000 ) {
                $formErrors['comment'] = "Le commentaire ne doit pas dépasser 1000 caractères.";
            }
        }

        
        // 10. S'il existe au moins une erreur détectée par le système,
        if ( count($formErrors) > 0 ) {
            // Alors,
            // 4a. Sauvegarder les messages d'erreurs en session, pour affichage à l'écran de l'utilisateur
            $_SESSION['form_errors'] = $formErrors;
            
            // 4b. Sauvegarder les anciennes données provenant du formulaire en session
            $_SESSION['old'] = $_POST;

            // 4c. Effectuer une redirection vers la page de laquelle proviennent les informations
            // Puis arrêter l'exécution du script.
            header('Location: edit.php');
            die();
        }

        // 11. Dans le cas contraire,
        // 11a. Arrondir la note à un chiffre après la virgule,
        $ratingRounded = null;

        if ( isset($_POST['rating']) && $_POST['rating'] !== "" ) {
            $ratingRounded = round($_POST['rating'], 1);
        }

        // 12. Etablir une connexion avec la base de données
        // 13. Effectuer la requête d'insertion du nouveau film dans la table prévue (film)
        updateFilm($ratingRounded, $film['id'], $_POST);

        // 14. Générer le message flash de succès
        $_SESSION['success'] = "Le film a été modifié avec succès.";

        // 15. Effectuer une redirection vers la page listant les films ajoutés (index.php)
        // Puis arrêter l'exécution du script.
        header("Location: index.php");
        die();
    }


// 6. Générer et sauvegarder en session le jeton de sécurité pour se protèger contre les faille s de type CSRF
$_SESSION['csrf_token']= bin2hex(random_bytes(32));

// 5. Dans le cas contraire,
// nous pouvons afficher les informations du film dans le formulaire de modification
?>
<?php

// on déclare les variables avant pour qu'il soit utiliser dans les fichier
    $title="Modifier le film : {$film['title']}";
    $description = "Modification du film : : {$film['title']}";
    $keywords = "Cinéma, repertoire, modification, film, dwwm22";
?>

<!-- Au lieu include_once on peut utiliser require_once -->
<?php include_once __DIR__ . "/../partials/head.php";?>

    <?php include_once __DIR__ . "/../partials/nav.php";?>


        <!-- Main -->
        <main class="container">
            <h1 class="text-center my-3 display-5">Modifier ce film</h1>

            <!-- Formulaire d'ajout d'un nouveau film -->
            <div class="contenaire">
                <div class="row">
                    <div class="col-md-8 col-lg-4 mx-auto p-4 bg-white shadow rounded">
                        <!-- si existe et pas vide -->
                        <?php if (isset($_SESSION['form_errors']) && !empty($_SESSION['form_errors'])) : ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach($_SESSION['form_errors'] as $error) : ?>
                                        <li><?= $error; ?></li>
                                    <?php endforeach ?>
                                    <!-- pour enlever le message d'erreur -->
                                    <?php unset($_SESSION['form_errors']); ?> 
                                </ul>
                            </div>
                        <?php endif ?> 


                        <!-- on enlève le action car symfony ne l'utilise pas -->
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="title">Titre <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" autofocus required
                                 value="<?= isset($_SESSION['old']['title']) && !empty($_SESSION['old']['title']) ? htmlspecialchars($_SESSION['old']['title']) : htmlspecialchars($film['title']);
                                unset($_SESSION['old']['title']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="rating">Note / 5</label>
                                <input type="number" min="0" max="5" step=".5" inputmode="decimal" name="rating" id="rating" class="form-control"
                                 value="<?= isset($_SESSION['old']['rating']) && ($_SESSION['old']['rating']) != ""? htmlspecialchars($_SESSION['old']['rating']) : htmlspecialchars((string) $film['title']);
                                unset($_SESSION['old']['rating']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="comment">Laissez un commentaire</label>
                                <textarea name="comment" id="comment" class="form-control" rows="4">
                                <?= isset($_SESSION['old']['comment']) && !empty($_SESSION['old']['comment']) ? htmlspecialchars($_SESSION['old']['comment']) : htmlspecialchars($film['comment']);
                                unset($_SESSION['old']['comment']) ?></textarea>
                            <small id="comment-counter"> 0 / 1000 caractères </small>
                        </div>
                            <!-- hidden pour le cacher  attention au espace dans value!!! -->
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'];?>">
                            <!-- hidden pour le cacher  attention si c'est remplie alors c'est un robot qui la rempli!!! -->
                            <input type="hidden" name="honey_pot" value="">

                            <div>
                                <!-- formnovalidate  -->
                                <input formnovalidate type="submit" value="Modifier" class="w-100 btn btn-primary shadow">
                            </div>
    
                        </form>
                    </div>

                </div>
            </div>
                       
        </main>

    <?php include_once __DIR__ . "/../partials/footer.php";?>

 <?php include_once __DIR__ . "/../partials/foot.php";?>
    
