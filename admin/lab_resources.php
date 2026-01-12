<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$type = $_GET['type'] ?? '';
$dept = $_GET['department'] ?? '';

$query = "
SELECT lr.*, s.name AS subject
FROM lab_resources lr
LEFT JOIN subjects s ON lr.subject_id = s.id
WHERE 1=1
";

if ($type!='') $query.=" AND lr.type='$type'";
if ($dept!='') $query.=" AND lr.department='$dept'";

$data = mysqli_query($conn,$query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Lab & Resource Notes</title>

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
    margin-bottom:25px;
}
.header h2{margin:0;}
.header p{
    margin:6px 0 0;
    color:#555;
}

/* ===== FILTER BAR ===== */
.filter-box{
    background:rgba(255,255,255,0.65);
    backdrop-filter:blur(12px);
    padding:16px;
    border-radius:12px;
    margin-bottom:25px;
    box-shadow:0 8px 20px rgba(0,0,0,0.06);
    animation:floatIn 0.8s ease;
}
@keyframes floatIn{
    from{opacity:0;transform:translateY(15px)}
    to{opacity:1;transform:translateY(0)}
}

/* Animated dropdowns */
select{
    padding:8px 12px;
    border-radius:8px;
    border:1px solid #ccc;
    margin-right:10px;
    background:#fff;
    cursor:pointer;
    transition:all 0.25s ease;
}
select:hover{
    transform:translateY(-2px);
    box-shadow:0 4px 10px rgba(0,0,0,0.12);
}
select:focus{
    outline:none;
    border-color:#0d6efd;
    box-shadow:0 0 0 3px rgba(13,110,253,0.2);
}

button{
    padding:8px 14px;
    border-radius:8px;
    border:none;
    cursor:pointer;
}

/* ===== TABLE ===== */
.table-wrap{
    background:rgba(255,255,255,0.7);
    backdrop-filter:blur(14px);
    border-radius:16px;
    box-shadow:0 12px 30px rgba(0,0,0,0.08);
    overflow:hidden;
}
table{
    width:100%;
    border-collapse:collapse;
}
th{
    background:#212529;
    color:#fff;
    padding:14px;
}
td{
    padding:14px;
    border-bottom:1px solid #e6e6e6;
}
tr:hover{
    background:rgba(13,110,253,0.06);
}

/* ===== LINKS ===== */
a.resource-link{
    color:#0d6efd;
    text-decoration:none;
    font-weight:500;
}
a.resource-link:hover{
    text-decoration:underline;
}

/* ===== ACTIONS ===== */
.actions{
    margin-top:20px;
}
.actions a{
    margin-right:14px;
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
    <h2>🧪 Lab & Resource Notes</h2>
    <p>Centralized access to lab manuals and academic resources</p>
</div>

<!-- FILTER BAR -->
<form method="GET" class="filter-box">
    <select name="department">
        <option value="">All Departments</option>
        <option>IT</option>
        <option>CM</option>
        <option>ME</option>
        <option>CE</option>
    </select>

    <select name="type">
        <option value="">All Types</option>
        <option>Lab</option>
        <option>Resource</option>
    </select>

    <button>Filter</button>
</form>

<!-- TABLE -->
<div class="table-wrap">
<table>
<tr>
    <th>Department</th>
    <th>Subject</th>
    <th>Type</th>
    <th>Title</th>
    <th>Description</th>
    <th>Link</th>
    <th>Added By</th>
    <th>Date</th>
</tr>

<?php while($r=mysqli_fetch_assoc($data)){ ?>
<tr>
    <td><?php echo htmlspecialchars($r['department']); ?></td>
    <td><?php echo htmlspecialchars($r['subject']); ?></td>
    <td><?php echo htmlspecialchars($r['type']); ?></td>
    <td><?php echo htmlspecialchars($r['title']); ?></td>
    <td><?php echo htmlspecialchars($r['description']); ?></td>
    <td>
        <?php if($r['resource_link']){ ?>
            <a class="resource-link" href="<?php echo $r['resource_link']; ?>" target="_blank">Open</a>
        <?php } ?>
    </td>
    <td><?php echo htmlspecialchars($r['added_by']); ?></td>
    <td><?php echo $r['created_at']; ?></td>
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
