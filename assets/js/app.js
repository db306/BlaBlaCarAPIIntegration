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
                console.log(data);

                displayTripOptions(data.trips);
                updateCursor(data.next_cursor);

            }).fail( err => {
            console.log('Woops', err);
        });
    }

    function displayTripOptions(trips){
        trips.forEach(trip => {
            $('.results').append('<div class="trip-entry"><a href="'+trip.link+'"><div>Price: <span class="price">'+ trip.price.amount + ' ' + trip.price.currency +'</span><br>Distance: <span class="distance">' + trip.distance_in_meters + ' Km</span><br>Duration: <span class="time">' + trip.duration_in_seconds + '</span></div></a></div>');
        });
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

