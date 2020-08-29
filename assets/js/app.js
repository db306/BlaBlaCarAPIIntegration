const $ = require('jquery');
import '../css/app.css';

$( document ).ready( () => {
    'use strict';

    $('button[name="submit"]').click( e => {
        e.preventDefault();
        $('.results').empty();
        $('input[name="cursor"]').val('');
        getTrips();
    });

    $('button[name="load-more"]').click( e => {
        e.preventDefault();
        getTrips();
    });

    function getTrips(){
        $.ajax(window.location + 'search?' + $('form').serialize())
            .done(data => {
                if(data.trips.length === 0){
                    alert('Woops ❌ No trips were foud');
                }
                displayTripOptions(data.trips);
                updateCursor(data.next_cursor);

            }).fail( (jqXHR,status, err) => {
                alert('⚠️ status ' + status + ': ' + err + '\n\n' + jqXHR.responseJSON.detail);
        });
    }

    function displayTripOptions(trips){
        trips.forEach(trip => {
            $('.results').append('<div class="trip-entry"><a href="'+trip.link+'"><div>Price: <span class="price">'+ trip.price.amount + ' ' + trip.price.currency +'</span><br>Distance: <span class="distance">' + metersToKilometers(trip.distance_in_meters) + ' Km</span><br>Duration: <span class="time">' + formatSeconds(trip.duration_in_seconds) + '</span></div></a></div>');
        });
    }

    function metersToKilometers(meters){
        return Math.round(meters / 1000);
    }

    function formatSeconds(timeInSeconds){
            const sec_num = parseInt(timeInSeconds, 10);
            let hours   = Math.floor(sec_num / 3600);
            let minutes = Math.floor((sec_num - (hours * 3600)) / 60);
            let seconds = sec_num - (hours * 3600) - (minutes * 60);

            if (hours   < 10) {hours   = "0"+hours;}
            if (minutes < 10) {minutes = "0"+minutes;}
            if (seconds < 10) {seconds = "0"+seconds;}
            return hours+':'+minutes+':'+seconds;
    }

    function updateCursor(cursor){
        if(cursor){
            $('input[name="cursor"]').val(cursor);
            $('#load-more').show();
        }else{
            $('#load-more').hide();
            $('input[name="cursor"]').val(null);
        }
    }
});

