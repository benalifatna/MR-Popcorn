<?php

/**
 * Cette fonction permet d'établir une connexion avec la base de données.
 *
 * @return PDO
 */
    function connectToDb() : PDO {

        $dsnDb = 'mysql:dbname=mr-popcorn;host=127.0.0.1;port=3306';
        $userDb = 'root';
        $passwordDb = ''; // pas de mot de passe car sur windows 

       
        try {
            $db = new PDO($dsnDb, $userDb, $passwordDb);// c'est un objet
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// pour afficher les erreurs en sql
        } catch (\PDOException $exception) {
            die("Connection to database failed: " . $exception->getMessage());
        }

        return $db;
    }

    /**
     * Cette fonction permet d'inserer un nouveau film en base de données.
     *
     * @param float|null $ratingRounded
     * @param array $data
     * @return void
     */
    //function createFilm(?float $ratingRounded, array $data = []){ // ? note peut etre null ou float
    function insertFilm(null|float $ratingRounded, array $data = []) : void { //  peut s'écrire aussi null ou float

        //Etablissons une connexion à la base de données.
        $db = connectToDb();
        try {
             // Préparons la requete à executer
        $req = $db -> prepare("INSERT INTO film (title, rating, comment, created_at, updated_at)
                     VALUES (:title, :rating, :comment, now(), now() ) ");

        // Passons à la requete, les données necessaires
        $req->bindValue(":title", $data['title']);
        $req->bindValue(":rating", $ratingRounded);
        $req->bindValue(":comment", $data['comment']);

        //Exécutons la requête
        $req->execute();

        // Fermons le curseur, c'est à dire la connexion à la base de données.
        $req->closeCursor();
         } catch (\PDOException $exception) {
            throw $exception;
        }
    
    }
    
    /**
     * Cette fonction permet de récupérer tous les films de la base de données.
     *
     * @return array
     */
    function getFilms(): array {
        $db = connectToDb();

        try {
            $req = $db->prepare("SELECT * FROM film ORDER BY created_at DESC");
            $req->execute();
            $films = $req->fetchAll();
            $req->closeCursor(); // non obligatoire ferme le curseur à la base donnée
        } catch(\PDOException $exception) {
            throw $exception;
        }

        return $films;
    }
    
    /**
     * Cette fonction permet de récupérer un film de la base de données
     * en fonction de l'identifiant renseigné
     * @param integer $filmId
     * @return false|array
     */

     function getFilm(int $filmId): false|array {
        $db = connectToDb();

        try {
            $req = $db->prepare("SELECT * FROM film WHERE id=:id");
            $req ->bindValue(":id", $filmId);//considère comme un objet "->" comme "." dans document chez javascript

            $req->execute();
            $film = $req->fetch();// fetch pour récuperer 
            $req->closeCursor(); // non obligatoire ferme le curseur à la base donnée
        } catch(\PDOException $exception) {
            throw $exception;
        }

        return $film;
        
    }
  
    /**
     * Cette fonction permet de modifier un film de la base de données
     * en fonction de l'identifiant renseigné
     *
     * @param null|float $ratingRounded
     * @param integer $filmId
     * @param array $data
     * @return void
     */
    function updateFilm(null|float $ratingRounded, int $filmId, array $data = []): void {
        $db = connectToDb();

        try {
            $req = $db->prepare("UPDATE film SET title=:title, rating=:rating, comment=:comment, updated_at=now() WHERE id=:id");
    
            $req->bindValue(":title", $data['title']);
            $req->bindValue(":rating", $ratingRounded);
            $req->bindValue(":comment", $data['comment']);
            $req->bindValue(":id", $filmId);
    
            $req->execute();
            $req->closeCursor();
        } catch (\PDOException $exception) {
            throw $exception;
        }
    }
    
    /**
     * Cette fonction permet de supprimer le film dans la base de données
     *
     * @param integer $filmId
     * @return void
     */
    function deleteFilm(int $filmId): void { //void ne renvoie rien
        $db = connectToDb();

        try {
            $req = $db->prepare("DELETE FROM film WHERE id=:id");
            $req->bindValue(":id", $filmId);
            $req->execute();// execute le code SQL
            $req->closeCursor();
        } catch (\PDOException $exception) {
            throw $exception;
        }
    }
?>