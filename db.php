<?php
function conn(){
    $DB_USER="root";
    $DB_PASSWORD="";
    $DB_NAME="social_media";
    $DB_HOST="localhost";
    $conn = mysqli_connect($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
    return $conn;
}
function createTables(){
    $table_query = `
    CREATE TABLE IF NOT EXISTS users{
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
};
CREATE TABLE IF NOT EXISTS session{
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
};
CREATE TABLE IF NOT EXISTS post{
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_post VARCHAR(255),
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
};
CREATE TABLE IF NOT EXISTS likes{
    id INT AUTO_INCREMENT PRIMARY KEY,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES post(id) ON DELETE CASCADE,
    UNIQUE(user_id, post_id)
};
    `;
    $DB_USER="root";
    $DB_PASSWORD="";
    $DB_NAME="login";
    $DB_HOST="localhost";
    $conn = mysqli_connect($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
    if (mysqli_query($conn, $table_query)){
        mysqli_close($conn);
        return true;
    }
    else{
        mysqli_close($conn);
        return false;
    };
}
createTables() ;
?>