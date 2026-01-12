<?php
session_start();
include("../config/db.php");

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$faculty = mysqli_query($conn,"SELECT * FROM users WHERE role='faculty'");
$subjects = mysqli_query($conn,"SELECT * FROM subjects");

if($_SERVER["REQUEST_METHOD"]=="POST"){
    mysqli_query($conn,"
        INSERT INTO faculty_subjects (faculty_id, subject_id)
        VALUES ($_POST[faculty], $_POST[subject])
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Assign Subject</title>

<style>
/* ===== BACKGROUND ===== */
body{
    font-family:'Segoe UI',Arial,sans-serif;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(-45deg,#e3f2fd,#fce4ec,#e8f5e9,#ede7f6);
    background-size:400% 400%;
    animation:bgMove 12s ease infinite;
}
@keyframes bgMove{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

/* ===== CARD ===== */
.card{
    background:rgba(255,255,255,0.7);
    backdrop-filter:blur(14px);
    padding:30px;
    width:420px;
    border-radius:16px;
    box-shadow:0 20px 40px rgba(0,0,0,0.12);
    animation:slideUp 0.8s ease;
}
@keyframes slideUp{
    from{opacity:0;transform:translateY(20px)}
    to{opacity:1;transform:translateY(0)}
}

h2{
    text-align:center;
    margin-bottom:20px;
}

/* ===== FORM ===== */
select,button{
    width:100%;
    padding:12px;
    margin:12px 0;
    border-radius:10px;
    border:1px solid #ccc;
    font-size:15px;
}
select{
    background:#fff;
    cursor:pointer;
}
button{
    background:#007bff;
    color:#fff;
    border:none;
    cursor:pointer;
    transition:0.3s;
}
button:hover{
    background:#0056b3;
}

/* ===== ACTIONS ===== */
.actions{
    display:flex;
    justify-content:space-between;
    margin-top:15px;
}
.btn{
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    font-size:14px;
}
.btn-secondary{background:#6c757d;color:#fff;}
.btn-primary{background:#28a745;color:#fff;}
.btn:hover{opacity:0.9}
</style>
</head>

<body>

<div class="card">

<h2>📘 Assign Subject to Faculty</h2>

<form method="POST">
    <select name="faculty" required>
        <option value="">Select Faculty</option>
        <?php while($f=mysqli_fetch_assoc($faculty)){ ?>
        <option value="<?php echo $f['id']; ?>">
            <?php echo $f['name']; ?>
        </option>
        <?php } ?>
    </select>

    <select name="subject" required>
        <option value="">Select Subject</option>
        <?php while($s=mysqli_fetch_assoc($subjects)){ ?>
        <option value="<?php echo $s['id']; ?>">
            <?php echo $s['name']; ?>
        </option>
        <?php } ?>
    </select>

    <button>✅ Assign Subject</button>
</form>

<div class="actions">
    <button class="btn btn-secondary" onclick="history.back()">⬅ Back</button>
    <a class="btn btn-primary" href="../admin/dashboard.php">🏠 Dashboard</a>
</div>

</div>

</body>
</html>
