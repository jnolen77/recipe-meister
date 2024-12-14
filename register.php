<?php
session_start();
// Change this to your connection info.
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';
// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if ( !isset($_POST['username'], $_POST['password']) ) {
	// Could not get the data that should have been sent.
	exit('Please fill both the username and password fields!');
}


// Check if registration data was submitted
if (!isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    exit('Please fill all the fields!');
}

// check if the username already exists
if ($stmt = $con->prepare('SELECT id FROM accounts WHERE username = ?')) {
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();

    // Check if  username already exists
    if ($stmt->num_rows > 0) {
        // if username already exists
        echo 'Username exists, please choose another!';
        $stmt->close();
    } else {
        // if username doesn't exist, insert the new account
        $stmt->close();

        // insert new account
        if ($stmt = $con->prepare('INSERT INTO accounts (username, email, password) VALUES (?, ?, ?)')) {
            // Hash the password before storing it
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $stmt->bind_param('sss', $_POST['username'], $_POST['email'], $password);
            $stmt->execute();
            $stmt->close();

            // Redirect to the login page
            header('Location: index.html');
            exit();
        } else {
            echo 'Could not prepare statement!';
        }
    }
} else {
    echo 'Could not prepare statement!';
}

$con->close();
?>
