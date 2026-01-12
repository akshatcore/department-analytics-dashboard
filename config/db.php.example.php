<?php
$conn = mysqli_connect(
    "DB_HOST",
    "DB_USERNAME",
    "DB_PASSWORD",
    "DB_NAME"
);

if (!$conn) {
    die("Database connection failed");
}
