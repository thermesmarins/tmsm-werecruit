(function( $ ) {
	'use strict';

	// Availpro calendar for shortcode [tmsm-werecruit-calendar]
  var tmsm_werecruit_calendar = $('#tmsm-werecruit-calendar');

  if(tmsm_werecruit_calendar.length > 0){

    var tmsm_werecruit_calendar_today = moment().subtract(1, 'days');
    var tmsm_werecruit_calendar_startdate = moment();
    var tmsm_werecruit_calendar_enddate = moment().add(1, 'year');

    var tmsm_werecruit_calendar_selected_date;
    var tmsm_werecruit_calendar_selected_begin;
    var tmsm_werecruit_calendar_selected_end;
    var tmsm_werecruit_calendar_lastdateclicked;
    var tmsm_werecruit_calendar_current_year;
    var tmsm_werecruit_calendar_current_month;
    var tmsm_werecruit_calendar_nights = 1;
    var tmsm_werecruit_calendar_minstay = 0;

    /**
     * Set Calendar Events for Month
     *
     * @param month
     */
    var tmsm_werecruit_calendar_set_events = function(month){

      tmsm_werecruit_calendar_current_year = month.format('YYYY');
      tmsm_werecruit_calendar_current_month = month.format('MM');

      var events_toload = [];
      if (typeof tmsm_werecruit_params.data !== 'undefined') {

        if (typeof tmsm_werecruit_params.data[month.format('YYYY-MM')] !== 'undefined') {
          var days = tmsm_werecruit_params.data[month.format('YYYY-MM')];
          var events = [];
          var i = 0;
          var lowest_price = null;

          // Get lowest price
          $.each(days, function (index, value) {
            if (typeof value.Price !== 'undefined' && value.Status !=='NotAvailable' ) {
              if(lowest_price === null){
                lowest_price = Number(value.Price);
              }
              if(Number(value.Price) < lowest_price){
                lowest_price = Number(value.Price);
              }
            }
          });

          // Create Events
          $.each(days, function (index, value) {
            value.date = index;
            if (typeof value.Price !== 'undefined' && value.Status !=='NotAvailable') {
              value.PriceWithCurrency = Number(value.Price).toLocaleString(tmsm_werecruit_params.locale,
                {style: "currency", currency: tmsm_werecruit_params.options.currency, minimumFractionDigits: 0, maximumFractionDigits: 2});

              value.Test = tmsm_werecruit_params.locale;
              if(Number(value.Price) === lowest_price){
                value.LowestPrice=1;
              }
              events[i] = value;
              events_toload.push(events[i]);
              i++;
            }

          });
          tmsm_werecruit_calendar_clndr.addEvents(events_toload);
        }

      }


    }

    var setCalendarWidth = function(){

      if(tmsm_werecruit_calendar.width()> 600){
        tmsm_werecruit_calendar.addClass('calendar-large');
        tmsm_werecruit_calendar.removeClass('calendar-small');
      }
      else{
        tmsm_werecruit_calendar.addClass('calendar-small');
        tmsm_werecruit_calendar.removeClass('calendar-large');
      }
    };
    $( window ).resize(function() {
      setCalendarWidth();
    });
    setCalendarWidth();

    // Clndr
    var tmsm_werecruit_calendar_clndr = tmsm_werecruit_calendar.clndr({
      template: $('#tmsm-werecruit-calendar-template').html(),
      startWithMonth: tmsm_werecruit_calendar_startdate,
      constraints: {
        startDate: tmsm_werecruit_calendar_today,
        endDate: tmsm_werecruit_calendar_enddate
      },
      adjacentDaysChangeMonth: true,
      forceSixRows: false,
      trackSelectedDate: false,
      clickEvents: {
        click: function(target) {

          var reoderdates = false;

          $('.day.mouseover').removeClass('mouseover');

          //if(target.events.length && !$(target.element).hasClass('inactive') && !$(target.element).hasClass('last-month') && !$(target.element).hasClass('next-month')) {
          if(!$(target.element).hasClass('inactive') && !$(target.element).hasClass('last-month') && !$(target.element).hasClass('next-month')) {
            //$('.day').removeClass('selected').removeClass('selected-range').removeClass('active');

            tmsm_werecruit_calendar_lastdateclicked = target.date;


            // Reorder dates
            if(typeof tmsm_werecruit_calendar_selected_begin !== 'undefined'){
              if(tmsm_werecruit_calendar_selected_begin > tmsm_werecruit_calendar_lastdateclicked){

                $('.calendar-day-' + tmsm_werecruit_calendar_selected_begin.format('YYYY-MM-DD')).removeClass('selected').removeClass('selected-hover').removeClass('selected-begin').removeClass('selected-end').removeClass('active');

                reoderdates = true;
                tmsm_werecruit_calendar_selected_begin = undefined;
                tmsm_werecruit_calendar_selected_end = undefined;
              }
              else{
                reoderdates = false;
              }
            }
            else{
              reoderdates = false;
            }

            // Reinitialize selected days if begin and end have both been initialized
            if(typeof tmsm_werecruit_calendar_selected_begin !== 'undefined' && typeof tmsm_werecruit_calendar_selected_end !== 'undefined'){
              $('.day').removeClass('selected').removeClass('selected-hover').removeClass('selected-begin').removeClass('selected-end').removeClass('active');
              tmsm_werecruit_calendar_selected_begin = undefined;
              tmsm_werecruit_calendar_selected_end = undefined;
              $('#tmsm-werecruit-form-checkoutdateinfo').html('');
              $('#tmsm-werecruit-form-checkoutdate').val('');
            }

            // Begin date not initialized
            if(typeof tmsm_werecruit_calendar_selected_begin === 'undefined'){
              tmsm_werecruit_calendar_selected_begin = tmsm_werecruit_calendar_lastdateclicked;
              $('.calendar-day-' + tmsm_werecruit_calendar_selected_begin.format('YYYY-MM-DD')).addClass('selected selected-begin');
              $('#tmsm-werecruit-form-checkindateinfo').html(tmsm_werecruit_calendar_selected_begin.format('L'));
              $('#tmsm-werecruit-form-checkindate').val(tmsm_werecruit_calendar_selected_begin.format('YYYY-MM-DD'));
              $('#tmsm-werecruit-form-arrivaldate').val(tmsm_werecruit_calendar_selected_begin.format('YYYY-MM-DD'));

              tmsm_werecruit_calendar_minstay = $('.calendar-day-' + tmsm_werecruit_calendar_selected_begin.format('YYYY-MM-DD') +' .cell').data('minstay');
              if(!tmsm_werecruit_calendar_minstay){
                tmsm_werecruit_calendar_minstay = 0;
              }
              $('#tmsm-werecruit-form-minstay-message').attr('data-value', tmsm_werecruit_calendar_minstay);
              $('#tmsm-werecruit-form-minstay-number').html(tmsm_werecruit_calendar_minstay);

            }
            else{
              if(reoderdates === false){
                tmsm_werecruit_calendar_selected_end = tmsm_werecruit_calendar_lastdateclicked;

                // Check if dates respect minstay
                if (tmsm_werecruit_calendar_minstay > 0) {
                  var checkminstay = moment(tmsm_werecruit_calendar_selected_begin);
                  if (checkminstay.add(tmsm_werecruit_calendar_minstay, 'days') > tmsm_werecruit_calendar_selected_end) {
                    console.warn('doest not respect minstay');
                    tmsm_werecruit_calendar_selected_end = undefined;
                  }
                  else {

                  }
                }
                if( typeof tmsm_werecruit_calendar_selected_end !== 'undefined'){
                  $('.calendar-day-' + tmsm_werecruit_calendar_selected_end.format('YYYY-MM-DD')).addClass('selected selected-end');
                }
              }
            }

            // Calculate nights
            $('#tmsm-werecruit-form-dates-container').hide();
            if(typeof tmsm_werecruit_calendar_selected_begin !== 'undefined' && typeof tmsm_werecruit_calendar_selected_end !== 'undefined'){
              tmsm_werecruit_calendar_nights = tmsm_werecruit_calendar_selected_end.diff(tmsm_werecruit_calendar_selected_begin, "days");
              $('#tmsm-werecruit-form-checkoutdateinfo').html(tmsm_werecruit_calendar_selected_end.format('L'));
              $('#tmsm-werecruit-form-checkoutdate').val(tmsm_werecruit_calendar_selected_end.format('YYYY-MM-DD'));

              $('#tmsm-werecruit-form-dates-container').show();

              // Submit calculate total price
              $('#tmsm-werecruit-calculatetotal').submit();
            }
            else{
              tmsm_werecruit_calendar_nights = 0;
            }

            $('#tmsm-werecruit-form-nights-number').html(tmsm_werecruit_calendar_nights);
            $('#tmsm-werecruit-form-nights-message').attr('data-value', tmsm_werecruit_calendar_nights);
            $('#tmsm-werecruit-form-nights').val(tmsm_werecruit_calendar_nights);
            if(tmsm_werecruit_calendar_nights > 0){
              $('#tmsm-werecruit-form-minstay-message').attr('data-value', 0);
              $('#tmsm-werecruit-form-minstay-number').html('');
            }
            else{
              $('#tmsm-werecruit-calculatetotal-totalprice').hide();
              $('#tmsm-werecruit-calculatetotal-ota').hide();
            }

          }
        },

        onMonthChange: tmsm_werecruit_calendar_set_events

      },
      doneRendering: function() {
        var self = this;
        $(this.element).on('mouseover', '.day:not(.inactive)', function(e) {

          var target = self.buildTargetObject(e.currentTarget, true);
          var hover_begin = tmsm_werecruit_calendar_selected_begin;
          var hover_end = target.date;


          // Over Select
          // Begin date already initialized
          if(typeof tmsm_werecruit_calendar_selected_begin !== 'undefined' && typeof tmsm_werecruit_calendar_selected_end === 'undefined'){

            $('.day').removeClass('mouseover').removeClass('selected').removeClass('selected-hover').removeClass('selected-end');

            $('.calendar-day-' + hover_end.format('YYYY-MM-DD')).addClass('selected selected-end');

            // selection
            var i = 0;
            var selectedDate = hover_begin;
            if(hover_end > selectedDate){
              $('.calendar-day-' + selectedDate.format('YYYY-MM-DD')).addClass('selected selected-hover');
              while(hover_end.format('YYYY-MM-DD') != selectedDate.format('YYYY-MM-DD')) {
                i++;
                selectedDate = moment(hover_begin).add(i, 'days');
                $('.calendar-day-' + selectedDate.format('YYYY-MM-DD')).addClass('selected selected-hover');

              }

            }

          }
          // Begin date not initialized
          else{
            $('.day.mouseover').removeClass('mouseover');
            $('.calendar-day-' + hover_end.format('YYYY-MM-DD')).addClass('mouseover');
          }

        });
      }

    });

    tmsm_werecruit_calendar_set_events(tmsm_werecruit_calendar_startdate);

    // Calculate total price form
    $('#tmsm-werecruit-calculatetotal').on('submit', function(e){
      e.preventDefault();

      // Reset value
      $('#tmsm-werecruit-calculatetotal-totalprice-value').html('');
      $('#tmsm-werecruit-calculatetotal-ota').html('');
      $('#tmsm-werecruit-form-submit').prop('disabled', true);
      $('#tmsm-werecruit-calculatetotal-loading').show();
      $('#tmsm-werecruit-calculatetotal-errors').hide();
      $('#tmsm-werecruit-form').removeClass('tmsm-werecruit-form-has-ota-price');

      // Calculate date if begin and end are defined
      if(tmsm_werecruit_calendar_selected_begin && tmsm_werecruit_calendar_nights > 0){

        // Ajax call
        $.ajax({
          url: _wpUtilSettings.ajax.url,
          type: 'post',
          dataType: 'json',
          enctype: 'multipart/form-data',
          data: {
            action: 'tmsm-werecruit-calculatetotal',
            date_begin: tmsm_werecruit_calendar_selected_begin.format('YYYY-MM-DD'),
            date_end: tmsm_werecruit_calendar_selected_end.format('YYYY-MM-DD'),
            nights: tmsm_werecruit_calendar_nights,
            security: $('#tmsm-werecruit-calculatetotal-nonce').val(),
          },
          success: function (data) {
            $('#tmsm-werecruit-calculatetotal-loading').hide();

            if (data.success === true) {
              $('#tmsm-werecruit-form-submit').prop('disabled', false);
              $('#tmsm-werecruit-calculatetotal-errors').hide();

              if(data.data.accommodation && data.data.accommodation.totalprice){
                var Price = data.data.accommodation.totalprice;
                if(Price){
                  $('#tmsm-werecruit-calculatetotal-totalprice').show();
                  var PriceWithCurrency = Number(Price).toLocaleString(tmsm_werecruit_params.locale, {style: "currency", currency: tmsm_werecruit_params.options.currency, minimumFractionDigits: 0, maximumFractionDigits: 0});
                  if(PriceWithCurrency){
                    $('#tmsm-werecruit-calculatetotal-totalprice').html(_.unescape(tmsm_werecruit_params.i18n.selecteddatepricelabel.replace(/%/g,PriceWithCurrency)));
                  }
                }
              }

              if(data.data.ota && data.data.ota.totalprice){
                var Price = data.data.ota.totalprice;
                if(Price){
                  $('#tmsm-werecruit-form').addClass('tmsm-werecruit-form-has-ota-price');

                  $('#tmsm-werecruit-calculatetotal-ota').show();
                  var PriceWithCurrency = Number(Price).toLocaleString(tmsm_werecruit_params.locale, {style: "currency", currency: tmsm_werecruit_params.options.currency, minimumFractionDigits: 0, maximumFractionDigits: 0});
                  if(PriceWithCurrency){
                    $('#tmsm-werecruit-calculatetotal-ota').html(_.unescape(tmsm_werecruit_params.i18n.otacomparelabel.replace(/%/g,PriceWithCurrency)));
                  }
                }
                else{
                  $('#tmsm-werecruit-form').removeClass('tmsm-werecruit-form-has-ota-price');
                }
              }

            }
            else {
              $('#tmsm-werecruit-form-submit').prop('disabled', true);
              $('#tmsm-werecruit-calculatetotal-totalprice').hide();
              $('#tmsm-werecruit-calculatetotal-ota').hide();
              $('#tmsm-werecruit-calculatetotal-errors').show().html(data.errors);
              $('#tmsm-werecruit-form').removeClass('tmsm-werecruit-form-has-ota-price');
            }
          },
          error: function (jqXHR, textStatus) {
            console.log('error');
            console.log(jqXHR);
            console.log(textStatus);
          }
        });
      }

    });


  }

  // Display year best price in shortcode [tmsm-werecruit-bestprice-year]
  var tmsm_werecruit_bestprice_year = $('.tmsm-werecruit-bestprice-year');
  if(tmsm_werecruit_bestprice_year.length > 0){
    tmsm_werecruit_bestprice_year.each(function(e){
      if($(this).data('price')){
        $(this).html(_.unescape(tmsm_werecruit_params.i18n.yearbestpricelabel.replace('%',Number($(this).data('price')).toLocaleString(tmsm_werecruit_params.locale, {style: "currency", currency: tmsm_werecruit_params.options.currency, minimumFractionDigits: 0, maximumFractionDigits: 0}))));
      }
    });

  }

})( jQuery );
