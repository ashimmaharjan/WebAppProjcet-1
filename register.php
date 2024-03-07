<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <section class="main-wrapper">
        <div class="form-wrapper">
            <h3>Create an account</h3>
            <h6 class="span-message">Please enter your details to create account.</h6>

            <?php
            session_start();

            if (isset($_GET['customer_name']) && isset($_GET['email']) && isset($_GET['password']) && isset($_GET['confirm_password']) && isset($_GET['contact_phone']) && isset($_GET['submit'])) {
                $customer_name = $_GET['customer_name'];
                $email = $_GET['email'];
                $password = $_GET['password'];
                $confirm_password = $_GET['confirm_password'];
                $contact_phone = $_GET['contact_phone'];

                if ($password !== $confirm_password) {
                    echo "<p class='error-message'>Sorry, Passwords do not match.</p>";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "<p class='error-message'>Invalid email format.</p>";
                } else {

                    $DBConnect = @mysqli_connect("localhost", "root", "", "cabsonline")
                        or die("<p class='error-message'>Unable to connect to the database server.</p>" . "<p class='error-message'>Error code " . mysqli_connect_errno() . ": " . mysqli_connect_error()) . "</p>";

                    $queryEmail = "SELECT * FROM customers WHERE email = '{$email}'";
                    $resultEmail = mysqli_query($DBConnect, $queryEmail);

                    if (mysqli_num_rows($resultEmail) > 0) {
                        echo "<p class='error-message'>Email already in use.</p>";
                    } else {

                        $SQLstring = "INSERT INTO customers (email, customer_name, password, contact_phone) VALUES ('{$email}', '{$customer_name}', '{$password}', '{$contact_phone}')";
                        $queryResult = @mysqli_query($DBConnect, $SQLstring);
                        if (!$queryResult) {
                            die("<p class='error-message'>Unable to insert into customers table.</p><p class='error-message'>Error code " . mysqli_errno($DBConnect) . ": " . mysqli_error($DBConnect) . "</p>");
                        } else {
                            $_SESSION['email'] = $email;
                            header("Location: booking.php");
                            exit();
                        }
                    }
                }
            }
            ?>

            <form>
                <div class="form-group">
                    <label for="Name">Name:</label>
                    <input type="text" placeholder="Enter you name" name="customer_name" required>
                </div>

                <div class="form-group">
                    <label for="Email">Email:</label>
                    <input type="text" placeholder="Enter you email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="Phone">Phone Number:</label>
                    <input type="text" placeholder="Enter you phone number" name="contact_phone" required>
                </div>

                <div class="form-group">
                    <label for="Password">Password:</label>
                    <input type="password" placeholder="Enter a password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="ConfirmPassword">Confirm Password:</label>
                    <input type="password" placeholder="Re-enter you password" name="confirm_password" required>
                </div>

                <div class="form-group">
                    <button class="submit-button" type="submit" name="submit">Register</button>
                </div>

                <p style="text-align: center;">Already registered? <a href="./login.php">Login Here</a> </p>
            </form>
        </div>
    </section>
</body>

</html>