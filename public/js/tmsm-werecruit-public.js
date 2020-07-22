(function( $ ) {
	'use strict';


  // Handle form submission
  jQuery(function($){

    if($('#tmsm-werecruit-container').length >0){

      var $skipPushState = false;

      var $load = function (){
        $('#tmsm-werecruit-filterform').submit(function(e){
          console.log('submitting');
          e.preventDefault();
          var filter = $('#tmsm-werecruit-filterform');
          var originalbutton = filter.find('button').text();
          $.ajax({
            url:filter.attr('action'),
            data:filter.serialize(), // form data
            type:filter.attr('method'), // POST
            beforeSend:function(xhr){
              console.log('beforeSend');
              $('#tmsm-werecruit-filterresponse').addClass('loading');
              $('#tmsm-werecruit-loadingexplaination').text(tmsm_werecruit_params.i18n.loadingexplaination);
              filter.find('button').text(tmsm_werecruit_params.i18n.loading); // changing the button label
            },
            success:function(data){
              $('#tmsm-werecruit-filterresponse').removeClass('loading');
              console.log('success');
              //console.log(data);
              filter.find('button').text(originalbutton); // changing the button label back
              $('#tmsm-werecruit-filterresponse').empty().html(data); // insert data

              if($skipPushState !== false){
                history.pushState({}, "", window.location.pathname + "#" + filter.serialize());﻿
              }

            },
            error:function(data){
              console.log('error');
              //console.log(data);
              $('#tmsm-werecruit-filterresponse').removeClass('loading');
            },

          });
          return false;
        });
      }



      var $reloadQueryString = function(){
        // Browser supports URLSearchParams, consider query string variables after hashtag
        if ('URLSearchParams' in window) {
          var parsedHash = new URLSearchParams(
            window.location.hash.substr(1) // skip the first char (#)
          );
          const filters = ['type', 'contract'];
          var count_changes = 0;
          filters.forEach(function(item){

            if(parsedHash.get(item) !== null){
              count_changes++;
            }
            $('#tmsm-werecruit-'+item).val(parsedHash.get(item));
          });

          if(count_changes > 0 ){
            $('#tmsm-werecruit-filterform').submit();
          }
        }
      };
      $load();
      $reloadQueryString();

      window.onpopstate = function(event) {
        $skipPushState = true;
        $reloadQueryString();
      };
    }

  });



})( jQuery );
