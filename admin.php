<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin CabsOnline</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <section class="main-wrapper">
        <div class="booking-form-wrapper">
            <h3>Admin Dashboard (CabsOnline)</h3>
            <h6 class="span-message">Click the button to list all unassigned booking requests with a pick-up time within
                3 hours.
            </h6>

            <form method="POST">
                <div class="form-group">
                    <button class="submit-button" type="submit" name="list">List Bookings</button>
                </div>
            </form>


            <?php
            if (isset($_POST['list'])) {
                $DBConnect = mysqli_connect("localhost", "root", "", "cabsonline");
                if (!$DBConnect) {
                    die("<p class='error-message'>Unable to connect to the database server.</p>");
                }

                date_default_timezone_set('Australia/Sydney');
                // Fetch unassigned bookings with pick-up time within 3 hours
                $query = "SELECT b.*, c.customer_name
                FROM bookings b
                JOIN customers c ON b.customer_email = c.email
                WHERE b.status = 'unassigned'
                AND b.pickup_date = DATE(NOW())
                AND TIME(b.pickup_time) BETWEEN TIME(NOW()) AND TIME(DATE_ADD(NOW(), INTERVAL 3 HOUR))";

                $result = mysqli_query($DBConnect, $query);

                if (mysqli_num_rows($result) > 0) {
                    echo "<table style='border=5'>";
                    echo "<tr><th>Booking Reference</th><th>Customer Name</th><th>Passenger Name</th><th>Contact Phone</th><th>Pick-up Address</th><th>Destination Suburb</th><th>Pick-up Date/Time</th></tr>";

                    while ($row = mysqli_fetch_assoc($result)) {
                        if ($row['pickup_unit_number'] !== "") {
                            $pickupAddress = $row['pickup_unit_number'] . '/' . $row['pickup_street_number'] . ' ' . $row['pickup_street_name'] . ', ' . $row['pickup_suburb'];
                        } else {
                            $pickupAddress = $row['pickup_street_number'] . ' ' . $row['pickup_street_name'] . ', ' . $row['pickup_suburb'];
                        }

                        $pickupDate = new DateTime($row['pickup_date']);
                        // Format the pickup date as "15th March 2024"
                        $pickupDateString = $pickupDate->format('jS F Y');

                        // Convert pickup time to AM/PM format
                        $pickupTime = date('g:i a', strtotime($row['pickup_time']));

                        echo "<tr>";
                        echo "<td>{$row['booking_number']}</td>";
                        echo "<td>{$row['customer_name']}</td>";
                        echo "<td>{$row['passenger_name']}</td>";
                        echo "<td>{$row['passenger_contact_phone']}</td>";
                        echo "<td>{$pickupAddress}</td>";
                        echo "<td>{$row['destination_suburb']}</td>";
                        echo "<td>{$pickupDateString} {$pickupTime}</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p class='error-message'>No unassigned bookings found with pick-up time within 3 hours.</p>";
                }
                mysqli_close($DBConnect);
            }
            ?>

            <h3 style="margin-top: 30px;">Assign taxi to a request</h3>
            <h6 class="span-message">
                Input a booking reference number and click update to assign taxi.
            </h6>

            <?php
            if (isset($_POST['update'])) {
                $DBConnect = mysqli_connect("localhost", "root", "", "cabsonline");
                if (!$DBConnect) {
                    die("<p class='error-message'>Unable to connect to the database server.</p>");
                }

                $booking_number = $_POST['booking_number'];

                $query = "UPDATE bookings SET status = 'assigned' WHERE booking_number = '$booking_number' AND status = 'unassigned'";
                $result = mysqli_query($DBConnect, $query);

                if ($result) {
                    if (mysqli_affected_rows($DBConnect) > 0) {
                        echo "<p class='confirmation-message'>The booking request <b>{$booking_number}</b> has been properly assigned.</p>";
                    } else {
                        echo "<p class='error-message'>Error: No unassigned booking request matches the provided booking reference number i.e. {$booking_number}.</p>";
                    }
                } else {
                    echo "<p class='error-message'>Error: No unassigned booking request matches the provided booking reference number.</p>";
                }
                mysqli_close($DBConnect);
            }
            ?>

            <form method="POST">
                <div class="form-group">
                    <label for="Booking Ref Number">Booking Reference Number :</label>
                    <input type="text" placeholder="Enter booking reference number" name="booking_number" required>
                </div>

                <div class="form-group">
                    <button class="submit-button" type="submit" name="update">Update</button>
                </div>
            </form>

        </div>
    </section>
</body>

</html>