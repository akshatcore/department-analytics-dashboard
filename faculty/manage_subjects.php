<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../auth/login.php");
    exit;
}

/* Add subject */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = trim($_POST['subject']);
    if ($subject != '') {
        mysqli_query($conn, "INSERT INTO subjects (name) VALUES ('$subject')");
    }
}

/* Fetch subjects */
$subjects = mysqli_query($conn, "SELECT * FROM subjects ORDER BY name");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Subjects</title>

<style>
/* ===== BACKGROUND ===== */
body{
    font-family:'Segoe UI',Arial,sans-serif;
    padding:20px;
    background:linear-gradient(-45deg,#e3f2fd,#e8f5e9,#fce4ec,#ede7f6);
    background-size:400% 400%;
    animation:bgMove 14s ease infinite;
}
@keyframes bgMove{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

/* ===== HEADER ===== */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}
.header h2{margin:0;}
.header .actions a,
.header .actions button{margin-left:10px}

/* ===== CARD ===== */
.card{
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(14px);
    padding:25px;
    border-radius:16px;
    width:420px;
    box-shadow:0 12px 30px rgba(0,0,0,0.1);
    animation:floatIn 0.8s ease;
}
@keyframes floatIn{
    from{opacity:0;transform:translateY(15px)}
    to{opacity:1;transform:translateY(0)}
}

/* ===== FORM ===== */
input,button{
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:10px;
    border:1px solid #ccc;
    font-size:14px;
}
button{
    background:#0d6efd;
    color:#fff;
    border:none;
    cursor:pointer;
    transition:0.3s;
}
button:hover{background:#084298}

/* ===== TABLE ===== */
table{
    width:100%;
    margin-top:25px;
    border-collapse:collapse;
    background:rgba(255,255,255,0.85);
    backdrop-filter:blur(10px);
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}
th,td{
    padding:12px;
    border-bottom:1px solid #ddd;
    text-align:left;
}
th{
    background:#0d6efd;
    color:#fff;
}
tr:hover{
    background:#f1f5ff;
}

/* ===== ACTIONS ===== */
a.delete-btn{
    color:#dc3545;
    text-decoration:none;
    font-weight:600;
}
a.delete-btn:hover{text-decoration:underline}

.btn{
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    font-size:14px;
    color:#fff;
}
.btn-secondary{background:#6c757d}
.btn-primary{background:#198754}
.btn-danger{background:#dc3545}
.btn:hover{opacity:0.9}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <h2>📘 Manage Subjects</h2>
    <div class="actions">
        <a class="btn btn-primary" href="dashboard.php">🏠 Dashboard</a>
        <button class="btn btn-secondary" onclick="history.back()">⬅ Back</button>
        <a class="btn btn-danger" href="../auth/logout.php">Logout</a>
    </div>
</div>

<!-- ADD SUBJECT -->
<div class="card">
<form method="POST">
    <input name="subject" placeholder="Enter Subject Name" required>
    <button>➕ Add Subject</button>
</form>
</div>

<!-- SUBJECT LIST -->
<table>
<tr>
    <th>ID</th>
    <th>Subject Name</th>
    <th>Action</th>
</tr>

<?php while($s=mysqli_fetch_assoc($subjects)){ ?>
<tr>
    <td><?php echo $s['id']; ?></td>
    <td><?php echo htmlspecialchars($s['name']); ?></td>
    <td>
        <a class="delete-btn"
           href="delete_subject.php?id=<?php echo $s['id']; ?>"
           onclick="return confirm('Delete this subject? Related marks will also be removed.');">
           Delete
        </a>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>
