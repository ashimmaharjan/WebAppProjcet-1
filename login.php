<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>
</head>

<body>
    <section class="main-wrapper">
        <div class="grid-wrapper">
            <div class="animation-wrapper">
                <h1>CabsOnline</h1>
                <div class="animation-holder">
                    <dotlottie-player src="https://lottie.host/08f756ed-e9b7-48ca-bf2c-38f6455b5765/DSrn2FjxOR.json" background="transparent" speed="1" style="width: 250px; height: 250px;" loop autoplay>
                    </dotlottie-player>
                </div>
                <p>Book a cab with ease.</p>
            </div>
            <div class="login-form-wrapper">
                <h3>Login</h3>
                <h6 class="span-message">Please enter your login credentials.</h6>

                <?php
                session_start();

                if (isset($_POST['submit'])) {
                    $email = $_POST['email'];
                    $password = $_POST['password'];

                    $DBConnect = mysqli_connect("localhost", "root", "", "cabsonline");
                    if (!$DBConnect) {
                        die("<p class='error-message'>Unable to connect to the database server.</p>");
                    }

                    $query = "SELECT * FROM customers WHERE email = '$email' AND password = '$password'";
                    $result = mysqli_query($DBConnect, $query);

                    if (mysqli_num_rows($result) == 1) {
                        $_SESSION['email'] = $email;
                        header("Location: booking.php");
                        exit();
                    } else {
                        echo "<p class='error-message'>Invalid email or password.</p>";
                    }

                    mysqli_close($DBConnect);
                }
                ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="Email">Email:</label>
                        <input type="text" placeholder="Enter your email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="Password">Password:</label>
                        <input type="password" placeholder="Enter a password" name="password" required>
                    </div>

                    <div class="form-group">
                        <button class="submit-button" type="submit" name="submit">Login</button>
                    </div>

                    <p style="text-align: center;">New Member? <a href="./register.php">Register now</a> </p>
                </form>
            </div>
        </div>
    </section>
</body>

</html>