
<section class="grid_layout">
    <div class="container-xl">
        <div class="row gx-lg-5 justify-content-center">
            <div class="col-xl-4 col-lg-6 col-md-12 row justify-content-center">
                <div class="content row flex-column gy-4">
                    <div class="green_box bg-success p-5 rounded-3">
                        <h2 class="text-white">Wir Fördern Familienfreundliche Erlebnisse</h2>
                    </div>
                    <div class="white_box p-5 rounded-3 bg-light">
                        <p>Willkommen bei unserer Initiative für ein familienfreundliches Gastronomieerlebnis. Entdecken
                            Sie eine ausgewählte Auswahl an Restaurants, Cafés und Bars, die eine einladende Atmosphäre
                            für Familien mit Kindern schaffen</p>
                    </div>
                    <div class="image p-0">
                        <img src="<?php echo $image; ?>image1.png" alt="" class="rounded-1 img-fluid" height="290">
                    </div>
                    <div
                        class="white_box bg-light rounded-3 p-5 align-items-center d-flex align-items-center flex-column">
                        <img src="<?php echo $image; ?>image2.svg" alt="" class="img-fluid mb-4" width="64" height="64">
                        <h3 class="text-center mb-3">Zusammen für mehr Inklusion</h3>
                        <p class="text-center">Wir wollen eine Welt schaffen in der Jung und Alt miteinander statt
                            nebeneinander existieren.</p>
                    </div>
                    <div class="white_box bg-light rounded-3 p-5 d-flex align-items-center justify-content-center">
                        <img src="<?php echo $image; ?>handlogo.svg" alt="" width="238" height="238">
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-12 row justify-content-center">
                <div class="content row flex-column gy-4">
                    <div class="image p-0">
                        <img src="<?php echo $image; ?>image3.svg" alt="" class="rounded-1 img-fluid w-100"
                            height="410">
                    </div>
                    <div class="green_box bg-success p-5 rounded-3 d-flex aligm-items-center">
                        <h2 class="text-white">Wir Überprüfen jedes Café, Restaurant, Bistrot und co. Individuell. Bei
                            Bestehen werden Sie mit dem <span style="color: #5E487E">Kidsfriendly Label</span>
                            Ausgezeichnet!</h2>
                    </div>
                    <div
                        class="white_box bg-light rounded-3 p-5 align-items-center d-flex align-items-center flex-column">
                        <img src="<?php echo $image; ?>image4.svg" alt="" class="img-fluid mb-4" width="64" height="64">
                        <h3 class="text-center mb-3">Menus an Denen Auch Die Kleinsten Freude haben</h3>
                        <p class="text-center">Wir engagieren uns für nachhaltige und gesunde Kindermenus. Dabei stehen
                            wir unseren LabelpartnerInnen eng beratend zur Seite.</p>
                    </div>
                    <div class="green_box bg-info p-5 rounded-3 d-flex aligm-items-center">
                        <h2 class="text-white">Get the Family Friendly Badge</h2>
                    </div>
                    <div class="white_box bg-light rounded-3 p-0">
                        <img src="<?php echo $image; ?>image5.svg" alt="" class="img-fluid w-100" height="300">
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-12 row justify-content-center">
                <div class="content row flex-column gy-4">
                    <div class="white_box bg-light rounded-3 p-0">
                        <img src="<?php echo $image; ?>image6.svg" alt="" class="img-fluid w-100" height="455">
                    </div>
                    <div class="white_box bg-light rounded-3 p-0">
                        <img src="<?php echo $image; ?>image7.svg" alt="" class="img-fluid w-100" height="271">
                    </div>
                    <div
                        class="white_box bg-light rounded-3 p-5 align-items-center d-flex align-items-center flex-column">
                        <img src="<?php echo $image; ?>image4.svg" alt="" class="img-fluid mb-4" width="64" height="64">
                        <h3 class="text-center mb-3">Unvergessliche Erlebnisse</h3>
                        <p class="text-center">Bei unseren LabelpartnerInnen gibt es keine Langeweile. Wir wollen bei
                            unseren Jüngsten Ausgehkultur mit positiven Erlebnissen fördern.</p>
                    </div>
                    <div class="white_box p-5 rounded-3 bg-light d-flex flex-column align-items-center">
                        <p class="text-center">Unsere LabelpartnerInnen müssen folgende Mindeststandards erfüllen um
                            ausgezeichnet zu werden.</p>
                        <ol>
                            <li>Ich komme mit meinen Kinderwagen in den Gastraum hinein</li>
                            <li>Hochstühle werden zur bereitgestellt</li>
                            <li>Es gibt ein Kindermenu, dass den Kindern gefällt</li>
                        </ol>
                        <p>Zudem beraten und ermutigen wir unsere LabelpartnerInnen stetig über die Mindeststadards
                            hinaus zu wachsen</p>
                        <button class="btn bg-success" style="width: fit-content"><a href="http://" target="_blank"
                                rel="noopener noreferrer" class="text-white text-decoration-none">Get It
                                Now</a></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="most_popular">
    <div class="container-xl">
        <div class="row">
            <div class="header">
                <h2 class="text-center mb-5 text-white">Speicher dir deine Lieblingsrestaurants hier ab</h2>
            </div>
            <?php $custom_args = array(
                'post_type' => 'post',
                'posts_per_page' => 3,
                'post_status' => 'publish',
                'order' => 'DESC',
            );
            $results = new WP_QUERY($custom_args);
            $excluded_posts = array();
            $first = true;
            ?>
            <?php if ($results->have_posts()): ?>
                <div class="row">
                    <?php while ($results->have_posts()):
                        $results->the_post();
                        $blog_id = get_the_ID();
                        $excluded_posts[] = $blog_id; // Store the post ID in the array
                        ?>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="card rounded-4 p-4">
                                <img src="<?php echo get_the_post_thumbnail_url($blog_id); ?>" alt="" class="rounded-3 mb-3">
                                <h3 class="mb-3 text-white">
                                    <?php echo get_the_title(); ?>
                                </h3>
                                <p class="text-white">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                </p>
                                <div class="address text-white">Winchester St 14:00 - 20:00</div>
                                <a href="<?php echo get_permalink(); ?>'" target="_blank" rel="noopener noreferrer"
                                    class="readmore bg-success bg-gradient px-2 py-3 text-center text-decoration-none text-white rounded-3 mt-3"
                                    class="mt-3">Read More</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php get_footer(); ?>
<script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD9kzmCY9-5cEA3vcUYZPWLSwGlgT5aHmI&libraries=places&callback=initMap&language=en"></script>
<script>
    var map;
    var service;
    var infowindow;
    var autocomplete;

    function initMap() {
        var europeCenter = new google.maps.LatLng(51.1657, 10.4515); // Centered on Europe

        infowindow = new google.maps.InfoWindow();

        map = new google.maps.Map(
            document.getElementById('map'), { center: europeCenter, zoom: 5 }); // Zoom level adjusted for Europe

        autocomplete = new google.maps.places.Autocomplete(document.getElementById('location-input'));

        document.getElementById('search-form').addEventListener('submit', function (e) {
            e.preventDefault();
            var location = document.getElementById('location-input').value;
            searchRestaurants(location);
        });

        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }
            searchRestaurants(place.formatted_address);
        });
    }

    function searchRestaurants(location) {
        var europeBounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(35.173, -25.104),
            new google.maps.LatLng(71.538, 42.869)
        ); // Define bounding box for Europe

        var request = {
            query: 'restaurant ' + location,
            fields: ['name', 'geometry'],
            bounds: europeBounds // Apply bounding box to restrict search to Europe
        };

        service = new google.maps.places.PlacesService(map);

        service.findPlaceFromQuery(request, function (results, status) {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                for (var i = 0; i < results.length; i++) {
                    createMarker(results[i]);
                }
                map.setCenter(results[0].geometry.location);
            } else {
                alert('No restaurants found');
            }
        });
    }


    function createMarker(place) {
        var marker = new google.maps.Marker({
            map: map,
            position: place.geometry.location
        });

        google.maps.event.addListener(marker, 'click', function () {
            infowindow.setContent(place.name);
            infowindow.open(map, this);
        });
    }


</script>