<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Database connection function
function conn(){
    $DB_USER = "root";
    $DB_PASSWORD = "";
    $DB_NAME = "social_media";
    $DB_HOST = "localhost";
    $conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

// Table creation
function createTables() {
    $table_query = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_name VARCHAR(64) NOT NULL UNIQUE,
        f_name VARCHAR(64) NOT NULL,
        l_name VARCHAR(64) NOT NULL,
        bio TEXT,
        profile_img VARCHAR(124) DEFAULT 'default.png',
        password VARCHAR(255) NOT NULL,
        email VARCHAR(124) NOT NULL UNIQUE,
        dob DATETIME NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS session (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        is_active BOOLEAN DEFAULT TRUE,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS post (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_post VARCHAR(255),
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        user_id INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        user_id INT NOT NULL,
        post_id INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE(user_id, post_id)
    );
    ";

    $conn = conn();

    if (mysqli_multi_query($conn, $table_query)) {
        do {
            if ($result = mysqli_store_result($conn)) {
                mysqli_free_result($result);
            }
        } while (mysqli_more_results($conn) && mysqli_next_result($conn));

        mysqli_close($conn);
        return true;
    } else {
        echo "Error creating tables: " . mysqli_error($conn);
        mysqli_close($conn);
        return false;
    }
}

// Call to create tables if not already created
createTables();

// Get user input
$fn = $_POST['f_name'] ?? '';
$ln = $_POST['l_name'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$bio = $_POST['bio'] ?? '';
$email = $_POST['email'] ?? '';
$dob = $_POST['dob'] ?? '';
$file_url = $_POST['file'] ?? 'default.png';

// Validate required fields
if (empty($fn) || empty($ln) || empty($username) || empty($password) || empty($email) || empty($dob)) {
    echo "Error: All fields are required.";
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Error: Invalid email format.";
    exit;
}

// Validate date format (YYYY-MM-DD)
if (!DateTime::createFromFormat('Y-m-d', $dob)) {
    echo "Error: Invalid date format. Use YYYY-MM-DD.";
    exit;
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user function
function insert($fn, $ln, $username, $hashed_password, $bio, $file_url, $email, $dob) {
    $conn = conn();

    $stmt = $conn->prepare("INSERT INTO users (f_name, l_name, user_name, password, bio, profile_img, email, dob) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $fn, $ln, $username, $hashed_password, $bio, $file_url, $email, $dob);

    if ($stmt->execute()) {
        echo "User registered successfully.";
    } else {
        echo "Failed to insert data: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

// Insert the user
insert($fn, $ln, $username, $hashed_password, $bio, $file_url, $email, $dob);
?>
