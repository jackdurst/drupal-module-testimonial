(function ($) {
  var offset = 0;
  var limit = 3;

  $(document).ready(function () {
    // Lorsque l'utilisateur clique sur le bouton "Afficher plus"
    $('#load-more-testimonials').click(function () {
      offset += limit;
      $.ajax({
        url: '/testimonials/load-more-testimonials',
        type: 'GET',
        data: { offset: offset, limit: limit },
        dataType: 'html',
        success: function (data) {
          // Ajouter les témoignages supplémentaires à la fin de la liste existante
          $('.testimonial-wrapper').append(data);
        },
        error: function (xhr, status, error) {
          // Gérer les erreurs éventuelles
          console.error('Une erreur s\'est produite lors du chargement des témoignages :', error);
        }
      });
    });
  });
})(jQuery);
