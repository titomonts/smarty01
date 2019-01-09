jQuery(function () {
    jQuery(document).ready(function() {
        jQuery('.availability_calendar_checker').each(function(index) {
            if (typeof jQuery().datepicker === 'function') { 
                /* Datepicker */
                var aday = 86400000;
                var today = new Date();
                var tomorrow = new Date(today.getTime() + aday);

                var minDate = false;
                var maxDate = false;
                
                var $this = jQuery(this).find('form');

                //var txtArrivalDate[index] = function () {
                    $this.find(".availability_calendar_input:first input").datepicker({
                        dateFormat: 'dd M yy',
                        changeMonth: false,
                        changeYear: false,
                        minDate: today,
                        onSelect: function (date) {
                            
                            var dt2 = $this.find(".availability_calendar_input:last input");
                            minDate = $(this).datepicker('getDate');
                            maxDate = dt2.datepicker('getDate');

                            var nextDate = new Date(minDate.getTime() + aday);

                            if (!maxDate || maxDate < minDate) {
                                dt2.datepicker('setDate', nextDate);
                            }
                            dt2.datepicker('option', 'minDate', nextDate);
                        },
                        onClose: function() {
                            var dt2 = $this.find(".availability_calendar_input:last input");
                            if (!maxDate && minDate) {
                                dt2.datepicker("show");
                            }
                        }
                    });        
                //}
                //txtArrivalDate[index]();

                var hasDate = $this.find(".availability_calendar_input:first input").val();
                if (hasDate) {
                    tomorrow = $this.find(".availability_calendar_input:first input").datepicker('getDate');
                }   

                //var txtDepartDate[index] = function () {
                    $this.find(".availability_calendar_input:last input").datepicker({
                        dateFormat: 'dd M yy',
                        changeMonth: false,
                        changeYear: false,
                        minDate: tomorrow
                    });
                //}
                //txtDepartDate[index]();
            } else {
                console.log('Availability calendar requires jQuery and jQuery UI datepicker');
            }
        });
        
        var redirection;
        var url;
        var res_url;
        jQuery('.availability_calendar_checker form').bind('submit', function(e) {
            var $this = jQuery(this);
            
            if($this.find(".availability_calendar_input:first input").val() && $this.find(".availability_calendar_input:last input").val()) {            
                e.preventDefault();

                jQuery('.availability_calendar_popup').prependTo('body').find('.availability_calendar_popup_content, .availability_calendar_overlay').fadeIn();
                $.ajax({
                    url:      '/',
                    data:     {},
                    success:  function(result) {
                        redirection = setTimeout(function() {
                            url = 'https://booking.elitehavens.com/booking.aspx?' + $this.serialize();
                            //var win = window.open(url, 'bookingTarget');
                            jQuery('.availability_calendar_popup .availability_calendar_overlay, .availability_calendar_popup .availability_calendar_popup_content').fadeOut();
                            
                             window.location.href = url;
                             /*
                            if(!win || win.closed || typeof win.closed=='undefined') {
                                //Browser has allowed it to be opened
                                var params = [
                                    'height='+screen.height,
                                    'width='+screen.width,
                                    'fullscreen=yes' // only works in IE, but here for completeness
                                ].join(',');
                                     // and any other options from
                                     // https://developer.mozilla.org/en/DOM/window.open

                                var popup = window.open(url, '', params); 

                                if(!popup || popup.closed || typeof popup.closed=='undefined') {
                                    window.location.href = url;
                                }
                            } else {
                                //Browser has blocked it
                                win.focus();
                            }
                            */
                        }, 2000); 
                    }
                });
                
            } else {
                e.preventDefault();
                $this.find(".availability_calendar_input input").after('<div class="error-message">Please fill out this field.</div>');                    
            }
            
        });
        jQuery('a[href*=".html?villaid="]').bind('click', function(e) {
            e.preventDefault();
            var $this = jQuery(this);
            var popup = jQuery('.availability_calendar_popup').clone();
            
            var parameters = $this.attr('href').split('?');
            var query = parameters[1];
            var qs = parse_query_string(query);
            
            var villaid = qs.villaid.split('|');
            for (var i = 0; i < villaid.length; i++) {
                villaid[i] = '<a href="http://booking.elitehavens.com/booking.aspx?pid=' + villaid[i] + '&curl=' + qs.curl + '" class="availability_calendar_popup_btn">Book now</a>';
            }
            var villaids = villaid.join('');
            
            if (!$('.availability_calendar_popup_options').size()) {
                popup.find('.availability_calendar_popup_content').html(villaids);
                popup.removeClass('availability_calendar_redirect_popup').addClass('availability_calendar_popup_options').prependTo('body').find('.availability_calendar_popup_content, .availability_calendar_overlay').fadeIn();
            } else {
                $('.availability_calendar_popup_options').find('.availability_calendar_popup_content, .availability_calendar_overlay').fadeIn();
            }
        });
        jQuery('body').on('click', 'a[href^="http://booking.elitehavens.com/booking.aspx"]', function(e) {
            e.preventDefault();
            var $this = jQuery(this);

            jQuery('.availability_calendar_popup_options').find('.availability_calendar_popup_content, .availability_calendar_overlay').fadeOut();
            jQuery('.availability_calendar_popup.availability_calendar_redirect_popup').prependTo('body').find('.availability_calendar_popup_content, .availability_calendar_overlay').fadeIn();
            $.ajax({
                url:      '/',
                data:     {},
                success:  function(result) {
                    redirection = setTimeout(function() {
                        res_url = $this.attr('href');
                        url = res_url.replace("http:", "https:");
                        //var win = window.open(url, 'bookingTarget');
                        jQuery('.availability_calendar_popup .availability_calendar_overlay, .availability_calendar_popup .availability_calendar_popup_content').fadeOut();
                        
                        window.location.href = url;
                        /* if(!win || win.closed || typeof win.closed=='undefined') {
                            //Browser has allowed it to be opened
                            var params = [
                                'height='+screen.height,
                                'width='+screen.width,
                                'fullscreen=yes' // only works in IE, but here for completeness
                            ].join(',');
                                 // and any other options from
                                 // https://developer.mozilla.org/en/DOM/window.open
                            
                            var popup = window.open(url, '', params); 
                            
                            if(!popup || popup.closed || typeof popup.closed=='undefined') {
                                window.location.href = url;
                            }
                        } else {
                            //Browser has blocked it
                            win.focus();
                        } */
                    }, 2000); 
                }
            });
            
        });
        
        jQuery('body').on('click', '.availability_calendar_popup .availability_calendar_overlay', function(e) {
            clearTimeout(redirection);
            jQuery('.availability_calendar_popup .availability_calendar_overlay, .availability_calendar_popup .availability_calendar_popup_content').fadeOut();
        });
        
        jQuery('.availability_link').each(function(e) {
            days = 3;
            var ci = new Date();
            var co = new Date();
            co.setDate(co.getDate() + days);
            
            insertParam('ci', formatDate(ci), '.availability_link a');
            insertParam('co', formatDate(co), '.availability_link a');
        });
        
        function replaceValidationUI( form ) {
            // Suppress the default bubbles
            form.addEventListener( "invalid", function( event ) {
                event.preventDefault();
            }, true );

            // Support Safari, iOS Safari, and the Android browser—each of which do not prevent
            // form submissions by default
            form.addEventListener( "submit", function( event ) {
                if ( !this.checkValidity() ) {
                    event.preventDefault();
                }
            });

            var submitButton = form.querySelector( "button:not([type=button]), input[type=submit]" );
            submitButton.addEventListener( "click", function( event ) {
                var invalidFields = form.querySelectorAll( ":invalid" ),
                    errorMessages = form.querySelectorAll( ".error-message" ),
                    parent;

                // Remove any existing messages
                for ( var i = 0; i < errorMessages.length; i++ ) {
                    errorMessages[ i ].parentNode.removeChild( errorMessages[ i ] );
                }

                for ( var i = 0; i < invalidFields.length; i++ ) {
                    parent = invalidFields[ i ].parentNode;
                    parent.insertAdjacentHTML( "beforeend", "<div class='error-message'>" + 
                        invalidFields[ i ].validationMessage +
                        "</div>" );
                }

                // If there are errors, give focus to the first invalid field
                if ( invalidFields.length > 0 ) {
                    invalidFields[ 0 ].focus();
                }
            });
        }

        // Replace the validation UI for all forms
        var forms = document.querySelectorAll( "form" );
        for ( var i = 0; i < forms.length; i++ ) {
            replaceValidationUI( forms[ i ] );
        }
        
        function insertParam(key, value, param) {
            key = encodeURI(key); value = encodeURI(value);
            
            //var kvp = document.location.search.substr(1).split('&');
            var url = jQuery(param).attr('href').split('?');
            
            if (url.length > 1) {
                var kvp = url[1].split('&');

                var i=kvp.length; var x; while(i--) 
                {
                    x = kvp[i].split('=');

                    if (x[0]==key)
                    {
                        x[1] = value;
                        kvp[i] = x.join('=');
                        break;
                    }
                }

                if(i<0) {kvp[kvp.length] = [key,value].join('=');}

                //this will reload the page, it's likely better to store this until finished
                url[1] = kvp.join('&'); 
                jQuery(param).attr('href', url.join('?'));
            }
        }
        
        function formatDate(date) {
          var monthNames = [
            "Jan", "Feb", "Mar",
            "Apr", "May", "Jun", "Jul",
            "Aug", "Sep", "Oct",
            "Nov", "Dec"
          ];

          var day = date.getDate();
          var monthIndex = date.getMonth();
          var year = date.getFullYear();

          return day + '+' + monthNames[monthIndex] + '+' + year;
        }
        
        function parse_query_string(query) {
            var vars = query.split("&");
            var query_string = {};
            for (var i = 0; i < vars.length; i++) {
                var pair = vars[i].split("=");
                var key = decodeURIComponent(pair[0]);
                var value = decodeURIComponent(pair[1]);
                // If first entry with this name
                if (typeof query_string[key] === "undefined") {
                    query_string[key] = decodeURIComponent(value);
                // If second entry with this name
                } else if (typeof query_string[key] === "string") {
                    var arr = [query_string[key], decodeURIComponent(value)];
                    query_string[key] = arr;
                // If third or later entry with this name
                } else {
                    query_string[key].push(decodeURIComponent(value));
                }
            }
            return query_string;
        }
    });
});