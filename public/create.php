<?php

// On doit déclarer session_start pour l'utiliser plus tard
session_start();

    require_once __DIR__ . "/../functions/db.php";

    
// var_dump($_SERVER);
// die();

    /*
    *------------------------------------------------------
    *   Traitement des données provenant du formulaire
    *------------------------------------------------------
    */
    // 1. Si les données du formulaire sont envoyer via la méthode POST;
    if ($_SERVER ['REQUEST_METHOD'] === "POST") {

    // Alors, 
    // 2. Protéger le serveur contre les failles de sécurité
    // 2a. Les failles de type csrf (s'assurer que les donnéées vient bien de ce formulaire en donnant un jeton token)
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) ||
        empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) ||
        $_POST['csrf_token'] !== $_SESSION['csrf_token']
    ) {
        // Effectuer une redirection vers la page de laquelle proviennent les informations
        // puis arrêter l'exécusion du script.
        header('Location: create.php');
        die();

    }
    // unset permet de supprimer les données de la session faire ménage
    unset($_SESSION['csrf_token']);
    unset($_POST['csrf_token']);
   // die('Continuer la partie');

    // 2b. Les robots spameurs (pot de miel)
    //Si le pot de miel n'existe pas ou qu'il n'est pas vide
    if (!isset($_POST['honey_pot']) || !empty($_POST['honey_pot'])) {
        // Effectuer une redirection vers la page de laquelle proviennent les informations
        // puis arrêter l'exécusion du script. attention au espace dans header
        header('Location: create.php');
        die();
    }
    unset($_POST['honey_pot']);
    
    // 3. procéder à la validation des données du formulaire
    $formErrors = [];

    if (isset($_POST['title'])) {// si le titre  est déclarer  et different de null
        $title = trim($_POST['title']); // trim supprime les espaces en début et en fin 
        if (empty($title)) { // si cest vide
            $formErrors['title'] = "Le titre est obligatoire.";

        } else if (mb_strlen($title) > 255){ // mb_strlen évite de compter les accents 
            $formErrors['title'] = "Le titre ne doit pas dépasser 255 caractères.";
        }
    }
   // var_dump($_POST);die();
//    la note peut être null
    if (isset($_POST['rating']) && $_POST['rating'] !== "") {
        $rating = trim($_POST['rating']);// trim supprime les espaces en début et en fin 

        if (!is_numeric($rating)) {// verifie si c'est un nombre
            $formErrors['rating'] = "La note doit être un nombre.";
        } else if (floatval($rating) < 0 || floatval($rating) > 5) {// rating est envoyer en chaine de caratère donc floatval le convertit
            $formErrors['rating'] = "La note doit être comprise entre 0 et 5.";
        }
    }
    
     if (isset($_POST['comment']) && $_POST['comment'] !== "") { // si il n'est pas déclarer et non null (pas vide)
        $comment = trim($_POST['comment']);

        if (mb_strlen($comment) > 1000) {
            $formErrors['comment'] = "Le commentaires ne doit pas dépasser 1000 caractères.";
        }
    }

    // 4. S'il existe au moins une erreur détecteée par le système
        if (count($formErrors) > 0) {
        //   Alors,
        // 4a. Sauvegarder les messages d'erreurs en session, pour affichage à l'écran de l'utilisateur
        $_SESSION['form_errors'] = $formErrors;

        // 4b. Sauvegarder les anciennes données provenant du formulaire en session
        // toutes les données du formulaire sont sauvegarder dans tableau $_SESSION avec clé old pendant 120 min
        $_SESSION['old'] = $_POST;
       // var_dump($_SESSION["old"]); die();

        // 4c. Effectuer une redirection vers la page de laquelle proviennent les informations
        // Puis arrêter l'exécution du script.
        header('Location: create.php');
        die();
     }
      //      die('Continuer la partie');


    // 5. Dans le cas contraire,
    // 5a. Arrondir la note à un chiffre apres la virgule,
    $ratingRounded = null;

    if (isset($_POST['rating']) && $_POST['rating'] !== "") {
        $ratingRounded = round($_POST['rating'], 1);
    }

    // 6. Etablir une connexion avec la base de données avec objet pdo
    // 7. Effectuer la requête d'insertion du nouveau film dans la table prévue (film)
    insertFilm($ratingRounded, $_POST);

    // 8. Générer le message flash de succès
    $_SESSION['sucess'] = "Le film a été ajouté à la liste avec succès.";

    // 9. Effectuer une redirection vers la page listant les films ajoutés (index.php)
    // Puis arrêter l'exécution du script.
    header("Location: index.php");
        die();
    }

// retour de chaine de caractere alétoirement 
// bin2hex convertit du binaire en hexadécimal
    // bin2hex(random_bytes(32));
    // die();

    // var_dump(bin2hex(random_bytes(32)));
    //  die();

    // Génerer et sauvegarder le jeton de sécurité en session
     $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

?>

<?php

// on déclare les variables avant pour qu'il soit utiliser dans les fichier
    $title="Nouveau film";
    $description = "Ajout d'un nouveau film";
    $keywords = "Cinéma, repertoire, ajout, nouveau, film, dwwm22";
?>

<!-- Au lieu include_once on peut utiliser require_once -->
<?php include_once __DIR__ . "/../partials/head.php";?>

    <?php include_once __DIR__ . "/../partials/nav.php";?>


        <!-- Main -->
        <main class="container">
            <h1 class="text-center my-3 display-5">Nouveau film</h1>

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
                                 value="<?= isset($_SESSION['old']['title']) && !empty($_SESSION['old']['title']) ? htmlspecialchars($_SESSION['old']['title']) : '';
                                unset($_SESSION['old']['title']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="rating">Note / 5</label>
                                <input type="number" min="0" max="5" step=".5" inputmode="decimal" name="rating" id="rating" class="form-control"
                                 value="<?= isset($_SESSION['old']['rating']) && ($_SESSION['old']['rating']) != ""? htmlspecialchars($_SESSION['old']['rating']) : '';
                                unset($_SESSION['old']['rating']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="comment">Laissez un commentaire</label>
                                <textarea name="comment" id="comment" class="form-control" rows="4">
                                <?= isset($_SESSION['old']['comment']) && !empty($_SESSION['old']['comment']) ? htmlspecialchars($_SESSION['old']['comment']) : '';
                                unset($_SESSION['old']['comment']) ?></textarea>
                            <small id="comment-counter"> 0 / 1000 caractères </small>
                        </div>
                            <!-- hidden pour le cacher  attention au espace dans value!!! -->
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'];?>">
                            <!-- hidden pour le cacher  attention si c'est remplie alors c'est un robot qui la rempli!!! -->
                            <input type="hidden" name="honey_pot" value="">

                            <div>
                                <!-- formnovalidate  -->
                                <input formnovalidate type="submit" value="Ajouter" class="w-100 btn btn-primary shadow">
                            </div>
    
                        </form>
                    </div>

                </div>
            </div>
                       
        </main>

    <?php include_once __DIR__ . "/../partials/footer.php";?>

 <?php include_once __DIR__ . "/../partials/foot.php";?>
    
