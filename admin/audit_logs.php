<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$logs = mysqli_query($conn, "
    SELECT * FROM audit_logs
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Audit Logs</title>

<style>
/* ===== BACKGROUND ===== */
body{
    font-family:'Segoe UI',Arial,sans-serif;
    padding:20px;
    background:linear-gradient(-45deg,#e3f2fd,#fce4ec,#e8f5e9,#ede7f6);
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
    margin-bottom:20px;
}
.header h2{
    margin:0;
}
.header p{
    margin:6px 0 0;
    color:#555;
}

/* ===== TABLE CONTAINER ===== */
.table-wrap{
    background:rgba(255,255,255,0.7);
    backdrop-filter:blur(14px);
    border-radius:16px;
    box-shadow:0 12px 30px rgba(0,0,0,0.08);
    overflow:hidden;
    animation:floatIn 0.8s ease;
}
@keyframes floatIn{
    from{opacity:0;transform:translateY(15px)}
    to{opacity:1;transform:translateY(0)}
}

/* ===== TABLE ===== */
table{
    width:100%;
    border-collapse:collapse;
}
th{
    background:#212529;
    color:#fff;
    padding:14px;
    text-align:left;
}
td{
    padding:14px;
    border-bottom:1px solid #e6e6e6;
}
tr:hover{
    background:rgba(13,110,253,0.06);
}

/* ===== FOOTER ACTIONS ===== */
.actions{
    margin-top:20px;
}
.actions a{
    margin-right:12px;
    text-decoration:none;
    color:#0d6efd;
    font-weight:500;
}
.actions a:hover{
    text-decoration:underline;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <h2>📜 Audit Logs</h2>
    <p>Read-only log of academic and administrative actions</p>
</div>

<!-- TABLE -->
<div class="table-wrap">
<table>
<tr>
    <th>ID</th>
    <th>User</th>
    <th>Action</th>
    <th>Date & Time</th>
</tr>

<?php while($l=mysqli_fetch_assoc($logs)){ ?>
<tr>
    <td><?php echo $l['id']; ?></td>
    <td><?php echo htmlspecialchars($l['user_name']); ?></td>
    <td><?php echo htmlspecialchars($l['action']); ?></td>
    <td><?php echo $l['created_at']; ?></td>
</tr>
<?php } ?>

</table>
</div>

<!-- ACTIONS -->
<div class="actions">
    <a href="dashboard.php">⬅ Back to Dashboard</a>
    <a href="../auth/logout.php">Logout</a>
</div>

</body>
</html>
