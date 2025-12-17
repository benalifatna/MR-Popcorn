<?php
/**
 * Cette fonction permet d'afficher les étoiles en fonction de la note
 * @param float|null $rating
 * @return void
 */

function displayStars(float|null $rating) : string {
        // Note sur 5, peut être 0.5, 1, 1.5 ...
        
        // étoile pleine récuperer sur frontonsome on met étoile
        $fullStar = '<i class="fas fa-star" style="color: gold;"></i>';    
        
        // demi-étoile
        $halfStar = '<i class="fas fa-star-half-alt" style="color: gold;"></i>'; 
        
        // étoile vide
        $emptyStar = '<i class="far fa-star" style="color: gold;"></i>';   

        $stars = "";
        
        // Arrondir à la demi-étoile la plus proche
        $rating = round($rating * 2) / 2;

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= floor($rating)) { // floor pour arrondir
                $stars .= $fullStar; // pleine ".=" c"est une concatenation
            } elseif ($i - 0.5 == $rating) {
                $stars .= $halfStar; // demi-étoile
            } else {
                $stars .= $emptyStar; // vide
            }
        }

        return $stars;
    }
?>