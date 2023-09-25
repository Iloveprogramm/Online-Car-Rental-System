<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Car Rental</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='main.css'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-item" id="left">Hertz-UTS</div>
            <h2><div class="nav-item" id="center">Car Rental Center</div></h2>
            <div class="nav-item" id="right"><button class="btn-reserve" onclick="window.location.href='reservation.php'">Car Reservation</button></div>
        </nav>
        <input type="text" id="search" placeholder="Search for car brands...">
    </header>
    <div class="dropdown-container">
    <select id="sort-options">
    <option value="none">Sort by Price</option>
    <option value="asc">Price: Low to High</option>
    <option value="desc">Price: High to Low</option>
</select>
</div>
<div class="category-container">
  <button class="category-btn" data-category="all">All</button>
  <button class="category-btn" data-category="Sedan">Sedan</button>
  <button class="category-btn" data-category="SUV">SUV</button>
  <button class="category-btn" data-category="Wagon">Wagon</button>
</div>
    <section class="car-container" id="car-container">
    </section>
    <script>
function generateCarCards(cars) {
    $.each(cars, function(index, car) {
        var car_card = $('<div></div>').addClass('car-card');
        var car_img = $('<img>').addClass('car-image').attr('src', './image/' + car['image']).attr('alt', car['Brand']);
        var car_title = $('<h2></h2>').addClass('car-title').text(car['Brand']);
        var car_specs = $('<div></div>').addClass('car-specs');
        var car_model = $('<p></p>').addClass('car-model').text('Model: ' + car['Model']);
        var car_year = $('<p></p>').addClass('car-year').text('Year: ' + car['Modelyear']);
        var car_mileage = $('<p></p>').addClass('car-mileage').text('Mileage: ' + car['Mileage']);
        var car_fuel = $('<p></p>').addClass('car-fuel').text('Fuel: ' + car['Fueltype']);
        var car_seats = $('<p></p>').addClass('car-seats').text('Seats: ' + car['Seats']);
        var car_price = $('<p></p>').addClass('car-price').text('Price per day: ' + car['PricePerDay']);
        var car_availability = $('<p></p>').addClass('car-availability').text('Available: ' + (car['Availability'] ? "Yes" : "No"));
        var car_button = $('<button></button>').addClass('btn-add-reserve').attr('data-car-id', car['Id']).attr('data-car-availability', car['Availability']).text(car['Availability'] ? 'Add to Reservation' : 'Unavailable');
        var car_description = $('<p></p>').addClass('car-description').text('Description: ' + car['Description']);
        
        car_specs.append(car_model, car_year, car_mileage, car_fuel, car_seats, car_price);
        car_card.append(car_img, car_title, car_specs, car_availability, car_button, car_description);
        $('#car-container').append(car_card);
    });
}

function bindSearch() {
    $('#search').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        $('.car-card').each(function() {
            var brand = $(this).find('.car-title').text().toLowerCase();
            if (brand.indexOf(searchTerm) === -1) {
                $(this).hide(); 
            } else {
                $(this).show(); 
            }
        });
    });
}

function bindSort() {
    $('#sort-options').change(function() {
        var sortOption = $(this).val();

        $.getJSON('cars.json', function(data) {
            var cars = data['cars'];
            if (sortOption === 'asc') {
                cars.sort((a, b) => a.PricePerDay - b.PricePerDay);
            } else if (sortOption === 'desc') {
                cars.sort((a, b) => b.PricePerDay - a.PricePerDay);
            }
            $('#car-container').empty();
            generateCarCards(cars);
        });
    });
}

function bindCategory() {
    $('.category-btn').click(function() {
        var category = $(this).data('category');
  
        $.getJSON('cars.json', function(data) {
            var cars = data['cars'];
            var filteredCars;

            if(category === "all") {
                filteredCars = cars;
            } else {
                filteredCars = cars.filter(function(car) {
                    return car['Category'] === category;
                });
            }

            $('#car-container').empty();
            generateCarCards(filteredCars);
        });

        $('.category-btn').removeClass('active');
        $(this).addClass('active');
    });
}

function fetchData() {
    $.getJSON('cars.json', function(data) {
        var cars = data['cars'];
        $('#car-container').empty();
        generateCarCards(cars);
    });
}

$(document).ready(function() {
    fetchData();
    setInterval(fetchData, 9000);
    bindSearch();
    bindSort();
    bindCategory();

    $(document).on('click', '.btn-add-reserve', function() {
        var carId = $(this).data('car-id');
        var carAvailability = $(this).data('car-availability');

        if(carAvailability == '1') {
            $.ajax({
                type: 'POST',
                url: 'add_to_cart.php',
                data: { id: carId },
                success: function(response) {
                    alert(response);
                }
            });
        } else {
            alert('Sorry, the car is not available now. Please try other cars.');
        }
    });
});

</script>


</body>
</html>
