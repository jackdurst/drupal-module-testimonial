(function ($) {
  var offset = 0;
  var limit = 1;
  var currentNodeId = drupalSettings.mynid;

  $(document).ready(function () {
    $('#load-more-testimonials').click(function () {
      console.log(currentNodeId)
      offset += limit;
      $.ajax({
        url: '/testimonials/load-more-testimonials',
        type: 'GET',
        data: { offset: offset, limit: limit, currentNodeId: currentNodeId},
        dataType: 'html',
        success: function (data) {
          var responseData = JSON.parse(data);
          console.log(data)

          responseData.testimonials.forEach(function(testimonial) {
            var testimonialHtml = '<div class="testimonial">';
            testimonialHtml += '<p>' + testimonial.testimonial + '</p>';

            // Date format :
            const date = new Date(testimonial.created * 1000)
            const formattedDate = date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });

            testimonialHtml += '<p>By ' + testimonial.name + ' on ' + formattedDate + '</p>';
            testimonialHtml += '</div>';

            $('.testimonial-wrapper').append(testimonialHtml);
          });
          console.log(responseData.has_more_testimonials)
          if (responseData.has_more_testimonials) {
            $('#load-more-testimonials').show();
          } else {
            $('#load-more-testimonials').hide();
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          console.error('Une erreur s\'est produite lors du chargement des témoignages :', error);
        }
      });
    });
  });
})(jQuery);
