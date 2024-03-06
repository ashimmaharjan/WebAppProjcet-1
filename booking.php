<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cab Booking</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body>
    <section class="main-wrapper">
        <div class="booking-form-wrapper">
            <h3>Book a Cab</h3>
            <h6 class="span-message">Please fill the fields below to book a cab.</h6>

            <?php
            session_start();
            $email = null;

            if (isset($_SESSION['email'])) {
                $email = $_SESSION['email'];
            } else {
                // If the session variable is not set, redirect to the login page
                header("Location: login.php");
                exit();
            }
            function generateBookingReference()
            {
                return uniqid();
            }

            function sendConfirmationEmail($email, $customerName, $bookingRefNumber, $pickupDate, $pickupTime)
            {
                $to = $email;
                $subject = "Your booking request with CabsOnline!";
                $message = "Dear $customerName,\n\nThanks for booking with CabsOnline! Your booking reference number is $bookingRefNumber. We will pick up the passengers in front of your provided address at $pickupTime on $pickupDate.";
                $headers = "From: booking@cabsonline.com.au";

                try {
                    mail($to, $subject, $message, $headers, "-r 104174420@student.swin.edu.au");
                } catch (Exception $e) {
                }
            }

            if (isset($_POST['submit'])) {
                $passengerName = $_POST['passenger_name'];
                $passengerContactPhone = $_POST['passenger_contact_phone'];
                $pickupUnitNumber = $_POST['pickup_unit_number'];
                $pickupStreetNumber = $_POST['pickup_street_number'];
                $pickupStreetName = $_POST['pickup_street_name'];
                $pickupSuburb = $_POST['pickup_suburb'];
                $destinationSuburb = $_POST['destination_suburb'];
                $pickupDate = $_POST['pickup_date'];
                $pickupTime = $_POST['pickup_time'];


                if (empty($passengerName) || empty($passengerContactPhone) || empty($pickupStreetNumber) || empty($pickupStreetName) || empty($pickupSuburb) || empty($destinationSuburb) || empty($pickupDate) || empty($pickupTime)) {
                    echo "<p class='error-message'>Please fill in all required fields.</p>";
                } else {
                    date_default_timezone_set('Australia/Sydney');
                    $currentDateTime = new DateTime();
                    $pickupDateTime = new DateTime("$pickupDate $pickupTime");

                    // Add 40 minutes to the current date and time
                    $currentDateTime->modify('+40 minutes');

                    if ($pickupDateTime <= $currentDateTime) {
                        echo "<p class='error-message'>Pick-up date/time must be at least 40 minutes after the current date/time.</p>";
                    } else {
                        $DBConnect = mysqli_connect("localhost", "root", "", "cabsonline");
                        if (!$DBConnect) {
                            die("<p class='error-message'>Unable to connect to the database server.</p>");
                        }

                        $bookingRefNumber = generateBookingReference();
                        $bookingDateTime = date('Y-m-d H:i:s');
                        $status = "unassigned";

                        $query = "INSERT INTO bookings (booking_number, customer_email, passenger_name, passenger_contact_phone, pickup_unit_number, pickup_street_number, pickup_street_name, pickup_suburb, destination_suburb, pickup_date, pickup_time, booking_datetime, status) VALUES ('$bookingRefNumber', '{$_SESSION['email']}', '$passengerName', '$passengerContactPhone', '$pickupUnitNumber', '$pickupStreetNumber', '$pickupStreetName', '$pickupSuburb', '$destinationSuburb', '$pickupDate', '$pickupTime', '$bookingDateTime', '$status')";
                        $result = mysqli_query($DBConnect, $query);

                        if ($result) {
                            sendConfirmationEmail($_SESSION['email'], $passengerName, $bookingRefNumber, $pickupDate, $pickupTime);
                            echo "<p class='confirmation-message'>Thank you! Your booking reference number is <b>$bookingRefNumber</b>. We will pick up the passengers in front of your provided address at $pickupTime on $pickupDate.</p>";
                        } else {
                            echo "<p class='error-message'>Error occurred while booking. Please try again later.</p>";
                        }
                        mysqli_close($DBConnect);
                    }
                }
            }
            ?>

            <form method="POST">
                <div class="booking-input-grid">
                    <div class="form-group">
                        <label for="Name">Passenger Name:</label>
                        <input type="text" placeholder="Enter passenger name" name="passenger_name" required>
                    </div>

                    <div class="form-group">
                        <label for="Phone">Passenger Contact Number:</label>
                        <input type="text" placeholder="Enter passenger contact number" name="passenger_contact_phone" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="Pickup address">Pickup Address:</label>
                    <div class="booking-input-grid">
                        <input type="text" placeholder="Enter unit number" name="pickup_unit_number">
                        <input type="text" placeholder="Enter street number" name="pickup_street_number" required>
                        <input type="text" placeholder="Enter street name" name="pickup_street_name" required>
                        <input type="text" placeholder="Enter suburb" name="pickup_suburb" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="Destination suburb">Destination Suburb:</label>
                    <input type="text" placeholder="Enter destination suburb" name="destination_suburb" required>
                </div>

                <div class="booking-input-grid">
                    <div class="form-group">
                        <label for="Pickup date">Pickup Date:</label>
                        <input type="date" placeholder="Select Pickup date" name="pickup_date" required>
                    </div>

                    <div class="form-group">
                        <label for="Pickup time">Pickup Time:</label>
                        <input type="time" placeholder="Select Pickup time" name="pickup_time" required>
                    </div>
                </div>

                <div class="form-group">
                    <button class="submit-button" type="submit" name="submit">Book a Cab</button>
                </div>
            </form>
        </div>
    </section>

</body>

</html>