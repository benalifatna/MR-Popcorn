// Si la page est chargée
document.addEventListener("DOMContentLoaded", () => {

    // Récupérons, le textarea et le small
   const textarea = document.getElementById('comment');
   const counter = document.getElementById('comment-counter');

   const maxLength = 1000;

 function editCounter() {
        // alors, récupérons la longueur de la valeur du textarea.
        const length = textarea.value.length;
    
        // Préparons le texte dynamique du compteur, pour remplacer le texte statique
        counter.textContent = `${length} / ${maxLength} caractères`;
    
        // Si la longueur de la valeur du textarea est strictement supérieur à la longueur totale prévue,
        if (length > maxLength) {
    
            // Alors, rajoutons ces classes ci-dessous
            textarea.classList.add('is-invalid');
            counter.classList.add('text-danger');
        } else {
            // Sinon, retirons ces classes.
            textarea.classList.remove('is-invalid');
            counter.classList.remove('text-danger');
        }
    }

    editCounter();
  
     // Si y a un changement au niveau de la valeur du textarea,
    textarea.addEventListener('input', editCounter);

         
   
 });