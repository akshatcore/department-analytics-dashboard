<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

function performanceBadge($value) {
    if ($value >= 75) return "🟢 Good";
    if ($value >= 50) return "🟡 Average";
    return "🔴 Needs Improvement";
}

/* Summary cards */
$totalStudents = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM students"))['c'];
$avgMarks = round(mysqli_fetch_assoc(mysqli_query($conn,"SELECT AVG(marks) a FROM student_marks"))['a'],1);
$avgAttendance = round(mysqli_fetch_assoc(mysqli_query($conn,"SELECT AVG(attendance_percent) a FROM student_attendance"))['a'],1);
$totalSubjects = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM subjects"))['c'];

/* Filters */
$dept = $_GET['department'] ?? '';
$year = $_GET['year'] ?? '';
$sem  = $_GET['semester'] ?? '';

$query = "
SELECT s.*, AVG(m.marks) avg_marks
FROM students s
LEFT JOIN student_marks m ON s.id=m.student_id
WHERE 1=1
";
if ($dept!='') $query.=" AND s.department='$dept'";
if ($year!='') $query.=" AND s.year='$year'";
if ($sem!='')  $query.=" AND s.semester='$sem'";
$query.=" GROUP BY s.id";

$students = mysqli_query($conn,$query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Principal Dashboard</title>

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
    margin-bottom:30px;
}
.header h2{margin:0;}
.header .actions a,
.header .actions button{margin-left:10px}

/* ===== CARDS ===== */
.cards{
    display:flex;
    gap:20px;
    margin-bottom:35px;
    flex-wrap:wrap;
}
.card{
    background:rgba(255,255,255,0.7);
    backdrop-filter:blur(14px);
    padding:18px;
    border-radius:14px;
    width:220px;
    text-align:center;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    animation:floatIn 0.7s ease;
}
@keyframes floatIn{
    from{opacity:0;transform:translateY(15px)}
    to{opacity:1;transform:translateY(0)}
}

/* ===== ACTION ROW ===== */
.actions-row{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:30px;
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

/* ===== BUTTONS ===== */
.btn{
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    color:#fff;
    font-size:14px;
}
.btn-dark{background:#343a40}
.btn-info{background:#17a2b8}
.btn-secondary{background:#6c757d}
.btn-danger{background:#dc3545}
.btn:hover{opacity:0.9}

/* ===== TABLE ===== */
.table-wrap{
    background:rgba(255,255,255,0.7);
    backdrop-filter:blur(14px);
    border-radius:14px;
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
    padding:12px;
}
td{
    padding:12px;
    border-bottom:1px solid #e6e6e6;
}
tr:hover{
    background:rgba(13,110,253,0.05);
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div>
        <h2>Principal Dashboard</h2>
        <p>Welcome, Admin</p>
    </div>
    <div class="actions">
        <button class="btn btn-secondary" onclick="history.back()">⬅ Back</button>
        <a class="btn btn-danger" href="../auth/logout.php">Logout</a>
    </div>
</div>

<!-- SUMMARY CARDS -->
<div class="cards">
    <div class="card"><h3>Total Students</h3><p><?php echo $totalStudents; ?></p></div>
    <div class="card"><h3>Avg Marks</h3><p><?php echo $avgMarks; ?></p></div>
    <div class="card"><h3>Avg Attendance</h3><p><?php echo $avgAttendance; ?>%</p></div>
    <div class="card"><h3>Total Subjects</h3><p><?php echo $totalSubjects; ?></p></div>
</div>

<!-- ADMIN ACTIONS -->
<div class="actions-row">
    <a class="btn btn-dark" href="audit_logs.php">Audit Logs</a>
    <a class="btn btn-info" href="subject_difficulty.php">Subject Difficulty</a>
    <a class="btn btn-info" href="department_comparison.php">Department Comparison</a>
    <a class="btn btn-info" href="lab_resources.php">Lab & Resources</a>
</div>

<!-- FILTER BAR -->
<form method="GET" class="filter-box">
    <select name="department">
        <option value="">All Departments</option>
        <option>IT</option><option>CM</option><option>ME</option><option>CE</option>
    </select>
    <select name="year">
        <option value="">All Years</option>
        <option>1st</option><option>2nd</option><option>3rd</option>
    </select>
    <select name="semester">
        <option value="">All Semesters</option>
        <option>Semester 1</option><option>Semester 2</option><option>Semester 3</option>
        <option>Semester 4</option><option>Semester 5</option><option>Semester 6</option>
    </select>
    <button class="btn btn-secondary">Filter</button>
</form>

<!-- STUDENT TABLE -->
<div class="table-wrap">
<table>
<tr>
    <th>Name</th><th>Dept</th><th>Year</th><th>Semester</th><th>Performance</th>
</tr>

<?php while($s=mysqli_fetch_assoc($students)){ ?>
<tr>
<td>
    <a href="../reports/student_profile.php?id=<?php echo $s['id']; ?>">
        <?php echo htmlspecialchars($s['name']); ?>
    </a>
</td>
<td><?php echo $s['department']; ?></td>
<td><?php echo $s['year']; ?></td>
<td><?php echo $s['semester']; ?></td>
<td><?php echo performanceBadge($s['avg_marks'] ?? 0); ?></td>
</tr>
<?php } ?>
</table>
</div>

</body>
</html>
