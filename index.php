<!doctype html>
<html lang="ja">
    <header>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        -->
        <link rel="stylesheet" href="./main.css">
        <!--<script src="http://maps.googleapis.com/maps/api/js?sensor=true&key=AIzaSyDymcMD7E0irE6FM1uGqzEhVmU5LTJDh-0"></script>-->
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDymcMD7E0irE6FM1uGqzEhVmU5LTJDh-0&libraries=places"></script>
        <script type="text/javascript">
            let currentLat, currentLng, latlng, map, service;
            let markers = [];
            let options ={
                    enableHighAccuracy: true,
                    timeout: 30000,
                    maximumAge: 0
                };
            
            /*function currentLocation(){
                getPosition();
            }*/
            
            function getPosition(){
                if(navigator.geolocation){
                    navigator.geolocation.getCurrentPosition(success, onError, options);
                }else{
                    document.getElementById("result").innerHTML = "Your browser does not support geolocation."
                }
            }
            
            function success(position){
                currentLat = position.coords.latitude;
                currentLng = position.coords.longitude;
                
                setLatLng(currentLat, currentLng);

                console.log(currentLat+", "+currentLng);
                
                let mapOptions = {
                    center: latlng,
                    zoom: 10,
                    mapTypeId: google.maps.MapTypeId.HYBRID
                };
                
                map = new google.maps.Map(document.getElementById("map"), mapOptions);
                service = new google.maps.places.PlacesService(map);
                findNearbyPlaces();
                
                /*let info = new google.maps.InfoWindow({
                    content: "my info window"
                });

                let currentMarker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    title: "my marker"
                });

                google.maps.event.addListener(currentMarker, "click", function(){
                    info.open(map, currentMarker);
                });*/

            }

            function setLatLng(latitude, longitude){
                let lat = latitude;
                let lng = longitude;
                latlng = new google.maps.LatLng(lat,lng);
            };
            
            function findNearbyPlaces(){
                let request = {
                    location: latlng,
                    radius: "500",
                    type: ["restaurant"]
                };
                //let service = new google.maps.places.PlacesService(map);
                service.nearbySearch(request, createMarker);
            }

            function createMarker(response, status){
                let latlngbounds = new google.maps.LatLngBounds();
                if(status == google.maps.places.PlacesServiceStatus.OK){
                    clearMarkers();
                    for(let i=0;i<response.length;i++){
                        drawMarker(response[i]);
                        latlngbounds.extend(response[i].geometry.location);
                    }
                    map.fitBounds(latlngbounds);
                }else if(status == google.maps.places.PlacesServiceStatus.ZERO_RESULTS){
                    console.log("no result");
                }else{
                    console.log("error");
                }
            }

            function drawMarker(response){
                let marker = new google.maps.Marker({
                    position: response.geometry.location,
                    map: map
                });
                markers.push(marker);
            }
            
            function clearMarkers(){
                if(markers){
                    for(i in markers){
                        markers[i].setMap(null);
                    }
                    markers = [];
                }
            }
            
            function onError(error){
                console.log(error.code);
                console.log(error.message);
                
                switch(error.code){
                    case PERMISSION_DENIED:
                        alert("User denied permission");
                        break;

                    case TIMEOUT:
                        alert("Geolocation timed out");
                        break;

                    case POSITION_UNAVAILABLE:
                        alert("Geolocation information is not available");
                        break;

                    default:
                        alert("Unknown error");
                        break;
                }
            }
            
        </script>
    </header>
    <body>
        <div class="main">
                <button onclick="test_instagram()">instagram</button>
                <div id="test_instagram_result"></div>
                <form action="">
                    <input type="text" id="address">
                    <button type="button" onclick="getLatLng()">get lat lng</button>
                    <button type="button" onclick="test_program()">test program</button>
                </form>
                <button onclick="getPosition()">getPosition</button>
                <div id="result"></div>
                <div id="map" style="height:500px; width:600px"></div>
        </div>
        <footer>
            
        </footer>
        <script>
            let inputLat, inputLng;
            function test_instagram(){
                $.ajax({
                    type: "GET",
                    dataType: "jsonp",
                    cache: false,
                    url: "https://api.instagram.com/v1/locations/search?lat="+ inputLat +"&lng="+ inputLng +"&access_token=8415320219.d67168c.7646405fbd2147169257dcd5ff05ccad",
                    success: function(response) {
                        /*for(let data in response.data){
                            console.log(data);
                        }*/
                    }
                });
            }

            function getLatLng(){
                let address = $("#address").val();
                address = address.replace(" ","+");
                $.ajax({
                    type: "GET",
                    //contentType:"application/json; charset=utf-8",
                    dataType:"json",
                    cache: false,
                    url: "https://maps.googleapis.com/maps/api/geocode/json?components=locality:"+ address +"&key=AIzaSyDymcMD7E0irE6FM1uGqzEhVmU5LTJDh-0",
                    success: function(response){
                        inputLat = response.results[0].geometry.location.lat;
                        inputLng = response.results[0].geometry.location.lng;
                        console.log(inputLat);
                        console.log(inputLng);
                    }
                })
            }

            function test_program(){
                
                getLatLng();

                test_instagram();
            }
        </script>
        <script src="./js/main.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    </body>
</html>