// Snow Report Metric Toggle
//
// author: [Craig Davis](craig@there4development.com)
// since: 12/1/2012

(function ($) {

  // Click the metric toggle, set a cookie to remember them
  // and then change to the metric divs
  $(function(){

    $('.reportUnitsToggle button').on('click', function(e) {
      var unitPreference = $(e.target).data('units');
      $('.reportUnitsToggle button').toggleClass('active');
      $('.reportUnits').toggle();
      $.cookie('reportUnits', unitPreference, { expires: 365, path: '/'});
    });

  });

})(jQuery);

/* End of file view.js */