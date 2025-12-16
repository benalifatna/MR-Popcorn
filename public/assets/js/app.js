// Si la page est chargée
document.addEventListener("DOMContentLoaded", () => {

    // Récupérons, le textarea et le small
   const textarea = document.getElementById('comment');
   const counter = document.getElementById('comment-counter');
   const maxLength = 1000;

   // Si y a un changement au niveau de la valeur du textarea,
   textarea.addEventListener('input', () => {

    // alors, récupérons la longueur de la valeur du textarea.
        const length = textarea.valjue.length;

    // Préparons le texte dynamique du compteur pour remplacer le texte statique
        counter.textContent = `${length} / ${maxLength} caractères`;

        // Si la longueur de la valeur du textarea est strictement supérieur à la longueur totale prévue
        if (length > maxLength) {
            
            // Alors rajoutons ces classes ci-dessous
            textarea.classList.add('is-invalid');
            counter.classList.add('text-invalid');
        } else {
            // Sinon retirons ces clasees
            textarea.classList.remove('is-invalid');
            counter.classList.remove('text-invalid');
        }
        
        
   })
 });