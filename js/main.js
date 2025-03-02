(function ($) {
    "use strict";
    
    // Dropdown on mouse hover
    $(document).ready(function () {
        function toggleNavbarMethod() {
            if ($(window).width() > 992) {
                $('.navbar .dropdown').on('mouseover', function () {
                    $('.dropdown-toggle', this).trigger('click');
                }).on('mouseout', function () {
                    $('.dropdown-toggle', this).trigger('click').blur();
                });
            } else {
                $('.navbar .dropdown').off('mouseover').off('mouseout');
            }
        }
        toggleNavbarMethod();
        $(window).resize(toggleNavbarMethod);
    });


    // Date and time picker
    $('.date').datetimepicker({
        format: 'L'
    });
    $('.time').datetimepicker({
        format: 'LT'
    });
    
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    // Team carousel
    $(".team-carousel, .related-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        center: true,
        margin: 30,
        dots: false,
        loop: true,
        nav : true,
        navText : [
            '<i class="fa fa-angle-left" aria-hidden="true"></i>',
            '<i class="fa fa-angle-right" aria-hidden="true"></i>'
        ],
        responsive: {
            0:{
                items:1
            },
            576:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            }
        }
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        margin: 30,
        dots: true,
        loop: true,
        center: true,
        responsive: {
            0:{
                items:1
            },
            576:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            }
        }
    });


    // Vendor carousel
    $('.vendor-carousel').owlCarousel({
        loop: true,
        margin: 30,
        dots: true,
        loop: true,
        center: true,
        autoplay: true,
        smartSpeed: 1000,
        responsive: {
            0:{
                items:2
            },
            576:{
                items:3
            },
            768:{
                items:4
            },
            992:{
                items:5
            },
            1200:{
                items:6
            }
        }
    });
    
})(jQuery);

function initializeCompareFeature() {
    // Fetch cars from the database
    fetch("http://localhost:8888/car_dealership/get_cars.php")
        .then(response => response.json())
        .then(cars => {
            const car1Select = document.getElementById("car1");
            const car2Select = document.getElementById("car2");

            // Populate the dropdowns with car options
            cars.forEach(car => {
                const option = document.createElement("option");
                option.value = car.id;
                option.text = car.name;
                car1Select.appendChild(option.cloneNode(true));
                car2Select.appendChild(option);
            });
        })
        .catch(error => console.error("Error fetching cars:", error));

    // Handle Compare Button Click
    document.getElementById("compare-btn").addEventListener("click", function () {
        const car1Id = document.getElementById("car1").value;
        const car2Id = document.getElementById("car2").value;

        if (!car1Id || !car2Id) {
            alert("Please select 2 cars to compare.");
            return;
        }

        // Fetch car details for comparison
        Promise.all([
            fetch(`get_booking_details.php?car_id=${car1Id}`).then(response => response.json()),
            fetch(`get_booking_details.php?car_id=${car2Id}`).then(response => response.json())
        ])
        .then(([car1Data, car2Data]) => {
            if (car1Data.status === "success" && car2Data.status === "success") {
                const car1 = car1Data.data;
                const car2 = car2Data.data;

                // Update Car 1 Details
                document.getElementById("car1-name").innerText = car1.name;
                document.getElementById("car1-image").src = car1.image;
                document.getElementById("car1-details").innerHTML = `
                    <tr><th>Overview</th><td>${car1.overview}</td></tr>
                    <tr><th>Key Features</th><td>${car1.key_features}</td></tr>
                    <tr><th>Pricing</th><td>${car1.pricing}</td></tr>
                    <tr><th>Technical Specs</th><td>${car1.technical_specs}</td></tr>
                    <tr><th>Additional Features</th><td>${car1.additional_features}</td></tr>
                    <tr><th>Warranty & Maintenance</th><td>${car1.warranty_maintenance}</td></tr>
                    <tr><th>Purchase Options</th><td>${car1.purchase_options}</td></tr>
                    <tr><th>Insurance Options</th><td>${car1.insurance_options}</td></tr>
                    <tr><th>Test Drive</th><td>${car1.test_drive}</td></tr>
                `;

                // Update Car 2 Details
                document.getElementById("car2-name").innerText = car2.name;
                document.getElementById("car2-image").src = car2.image;
                document.getElementById("car2-details").innerHTML = `
                    <tr><th>Overview</th><td>${car2.overview}</td></tr>
                    <tr><th>Key Features</th><td>${car2.key_features}</td></tr>
                    <tr><th>Pricing</th><td>${car2.pricing}</td></tr>
                    <tr><th>Technical Specs</th><td>${car2.technical_specs}</td></tr>
                    <tr><th>Additional Features</th><td>${car2.additional_features}</td></tr>
                    <tr><th>Warranty & Maintenance</th><td>${car2.warranty_maintenance}</td></tr>
                    <tr><th>Purchase Options</th><td>${car2.purchase_options}</td></tr>
                    <tr><th>Insurance Options</th><td>${car2.insurance_options}</td></tr>
                    <tr><th>Test Drive</th><td>${car2.test_drive}</td></tr>
                `;

                // Show the comparison table
                document.getElementById("comparison-table").style.display = "flex";
            } else {
                alert("Failed to load car details for comparison.");
            }
        })
        .catch(error => console.error("Error fetching car details:", error));
    });
}

// Initialize the Compare Feature when the DOM is loaded
document.addEventListener("DOMContentLoaded", initializeCompareFeature);



