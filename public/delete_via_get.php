<?php
session_start();

require_once __DIR__ . "/../functions/db.php";
//require_once __DIR__ . "/../functions/helpers.php";

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

// 5. Dans le cas contraire,
// Effectuer la requete de suppression
deleteFilm((int) $film['id']);

// 6. Générer le message flash de succès
 $_SESSION['success'] = "Le film a été retiré de la liste.";

 // 7. Rediriger vers la page d'acceuil puis arrêter l'exécution du script.
 header("Location: index.php");
 die();


