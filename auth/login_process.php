<?php
session_start();
include("../config/db.php");

/* ✅ Check request method */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* ✅ Safely fetch form data */
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : '';

    /* ✅ Validate empty fields */
    if ($email == '' || $password == '' || $role == '') {
        echo "All fields are required";
        exit;
    }

    /* ✅ Query */
    $query = "SELECT * FROM users 
              WHERE email='$email' 
              AND password='$password' 
              AND role='$role'";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        /* ✅ Role-based redirect */
        if ($role == 'admin') {
            header("Location: ../admin/dashboard.php");
        } elseif ($role == 'faculty') {
            header("Location: ../faculty/dashboard.php");
        } else {
            header("Location: ../student/dashboard.php");
        }
        exit;

    } else {
        echo "Invalid login credentials";
    }

} else {
    /* ✅ Prevent direct access */
    header("Location: login.php");
    exit;
}
?>
