<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

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
        FOREIGN KEY (post_id) REFERENCES post(id) ON DELETE CASCADE,
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

// Call createTables explicitly once to create the tables
createTables();

function insertTable() {
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $conn = conn();
        $f_name = $_POST['firstName'] ?? '';
        $l_name = $_POST['lastName'] ?? '';
        $username = $_POST['userName'] ?? '';
        $bio = $_POST['bio'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $dob = $_POST['dob'] ?? '';

        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        $profile_img_name = 'default.png';
        if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $tmp_name = $_FILES['profile_img']['tmp_name'];
            $profile_img_name = basename($_FILES['profile_img']['name']);
            $target_file = $upload_dir . $profile_img_name;

            if (!move_uploaded_file($tmp_name, $target_file)) {
                $profile_img_name = 'default.png';
            }
        }

        $stmt = $conn->prepare("INSERT INTO users (user_name, f_name, l_name, bio, profile_img, password, email, dob) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $username, $f_name, $l_name, $bio, $profile_img_name, $password_hashed, $email, $dob);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'User inserted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    }
}

// To handle the insert request, call insertTable() only when POST is received:
insertTable();
?>
