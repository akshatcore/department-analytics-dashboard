<?php
session_start();
include("../config/db.php");

if ($_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$dept = $_GET['department'] ?? '';
$type = $_GET['type'] ?? '';

$q = "
SELECT lr.*, s.name subject
FROM lab_resources lr
LEFT JOIN subjects s ON lr.subject_id=s.id
WHERE lr.version = (
    SELECT MAX(version)
    FROM lab_resources
    WHERE parent_id = lr.parent_id OR id = lr.id
)
";

if ($dept!='') $q.=" AND lr.department='$dept'";
if ($type!='') $q.=" AND lr.type='$type'";

$data = mysqli_query($conn,$q);
?>

<!DOCTYPE html>
<html>
<head>
<title>Lab & Resources</title>

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

/* ===== FILTER CARD ===== */
.filter-card{
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(14px);
    padding:18px;
    border-radius:14px;
    margin-bottom:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    animation:floatIn 0.8s ease;
}
@keyframes floatIn{
    from{opacity:0;transform:translateY(15px)}
    to{opacity:1;transform:translateY(0)}
}

/* ===== FORM ===== */
select,button{
    padding:10px 12px;
    border-radius:10px;
    border:1px solid #ccc;
    margin-right:10px;
    font-size:14px;
}
button{
    background:#0d6efd;
    color:#fff;
    border:none;
    cursor:pointer;
}
button:hover{opacity:0.9}

/* ===== TABLE ===== */
table{
    width:100%;
    border-collapse:collapse;
    background:rgba(255,255,255,0.85);
    backdrop-filter:blur(10px);
    border-radius:14px;
    overflow:hidden;
    box-shadow:0 12px 30px rgba(0,0,0,0.08);
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

/* ===== LINKS ===== */
a.link{
    color:#0d6efd;
    text-decoration:none;
    font-weight:600;
}
a.link:hover{text-decoration:underline}

/* ===== BUTTONS ===== */
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
    <h2>🧪 Lab & Resource Notes</h2>
    <div class="actions">
        <a class="btn btn-primary" href="dashboard.php">🏠 Dashboard</a>
        <button class="btn btn-secondary" onclick="history.back()">⬅ Back</button>
        <a class="btn btn-danger" href="../auth/logout.php">Logout</a>
    </div>
</div>

<!-- FILTER -->
<div class="filter-card">
<form method="GET">
    <select name="department">
        <option value="">Department</option>
        <option>IT</option><option>CM</option><option>ME</option><option>CE</option>
    </select>

    <select name="type">
        <option value="">Type</option>
        <option>Lab</option>
        <option>Resource</option>
    </select>

    <button>🔍 Filter</button>
</form>
</div>

<!-- TABLE -->
<table>
<tr>
    <th>Dept</th>
    <th>Subject</th>
    <th>Type</th>
    <th>Title</th>
    <th>Notes</th>
    <th>File</th>
    <th>Link</th>
    <th>Version</th>
</tr>

<?php while($r=mysqli_fetch_assoc($data)){ ?>
<tr>
    <td><?php echo htmlspecialchars($r['department']); ?></td>
    <td><?php echo htmlspecialchars($r['subject']); ?></td>
    <td><?php echo htmlspecialchars($r['type']); ?></td>
    <td><?php echo htmlspecialchars($r['title']); ?></td>
    <td><?php echo htmlspecialchars($r['description']); ?></td>
    <td>
        <?php if($r['file_path']){ ?>
            <a class="link" href="<?php echo $r['file_path']; ?>">Download</a>
        <?php } ?>
    </td>
    <td>
        <?php if($r['resource_link']){ ?>
            <a class="link" href="<?php echo $r['resource_link']; ?>" target="_blank">Open</a>
        <?php } ?>
    </td>
    <td>v<?php echo $r['version']; ?></td>
</tr>
<?php } ?>

</table>

</body>
</html>
