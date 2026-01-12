<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'faculty') {
    header("Location: ../auth/login.php");
    exit;
}

$subjects = mysqli_query($conn, "SELECT * FROM subjects");

/* ===============================
   HANDLE FORM SUBMIT
   =============================== */
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $dept    = mysqli_real_escape_string($conn, $_POST['department']);
    $subject = intval($_POST['subject']);
    $type    = mysqli_real_escape_string($conn, $_POST['type']);
    $title   = mysqli_real_escape_string($conn, $_POST['title']);
    $desc    = mysqli_real_escape_string($conn, $_POST['description']);
    $link    = mysqli_real_escape_string($conn, $_POST['link']);
    $parent  = $_POST['parent_id'] ?? null;

    /* ===============================
       VERSIONING (SAFE)
       =============================== */
    if (!empty($parent)) {
        $vRow = mysqli_fetch_assoc(mysqli_query(
            $conn,
            "SELECT MAX(version) v FROM lab_resources WHERE parent_id=$parent"
        ));
        $version = ($vRow['v'] ?? 0) + 1;
        $parentSql = intval($parent);
    } else {
        $version = 1;
        $parentSql = "NULL";
    }

    /* ===============================
       FILE UPLOAD (SAFE)
       =============================== */
    $filePath = null;
    if (!empty($_FILES['file']['name'])) {
        $dir = "../uploads/lab_resources/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES['file']['name']);
        $filePath = $dir . $fileName;
        move_uploaded_file($_FILES['file']['tmp_name'], $filePath);
    }

    /* ===============================
       INSERT (FIXED)
       =============================== */
    $sql = "
        INSERT INTO lab_resources
        (
            department,
            subject_id,
            type,
            title,
            description,
            resource_link,
            file_path,
            version,
            parent_id,
            added_by
        )
        VALUES
        (
            '$dept',
            $subject,
            '$type',
            '$title',
            '$desc',
            '$link',
            '$filePath',
            $version,
            $parentSql,
            '{$_SESSION['name']}'
        )
    ";

    mysqli_query($conn, $sql) or die(mysqli_error($conn));

    /* OPTIONAL SUCCESS REDIRECT (keeps workflow same) */
    header("Location: add_lab_resource.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Lab / Resource</title>

<style>
body{
    font-family:'Segoe UI',Arial,sans-serif;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(-45deg,#e3f2fd,#e8f5e9,#fce4ec,#ede7f6);
    background-size:400% 400%;
    animation:bgMove 14s ease infinite;
}
@keyframes bgMove{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

.card{
    background:rgba(255,255,255,0.7);
    backdrop-filter:blur(14px);
    padding:30px;
    width:460px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(0,0,0,0.12);
}

h2{text-align:center;margin-bottom:20px;}

input,select,textarea,button{
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:10px;
    border:1px solid #ccc;
}

textarea{min-height:90px;}
button{
    background:#0d6efd;
    color:#fff;
    border:none;
    cursor:pointer;
}
button:hover{background:#084298;}

.actions{
    display:flex;
    justify-content:space-between;
    margin-top:15px;
}
.btn{
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    color:#fff;
}
.btn-secondary{background:#6c757d}
.btn-primary{background:#198754}
</style>
</head>

<body>

<div class="card">

<h2>🧪 Add / Update Lab & Resource</h2>

<?php if(isset($_GET['success'])){ ?>
<p style="color:green;text-align:center;">✔ Resource added successfully</p>
<?php } ?>

<form method="POST" enctype="multipart/form-data">

    <select name="department" required>
        <option value="">Department</option>
        <option>IT</option>
        <option>CM</option>
        <option>ME</option>
        <option>CE</option>
    </select>

    <select name="subject" required>
        <option value="">Subject</option>
        <?php while($s=mysqli_fetch_assoc($subjects)){ ?>
            <option value="<?php echo $s['id']; ?>">
                <?php echo htmlspecialchars($s['name']); ?>
            </option>
        <?php } ?>
    </select>

    <select name="type" required>
        <option value="">Type</option>
        <option>Lab</option>
        <option>Resource</option>
    </select>

    <input name="title" placeholder="Title" required>
    <textarea name="description" placeholder="Notes / Description" required></textarea>

    <input name="link" placeholder="External Link (optional)">
    <input type="file" name="file">

    <input type="hidden" name="parent_id">

    <button>💾 Save Resource</button>
</form>

<div class="actions">
    <button class="btn btn-secondary" onclick="history.back()">⬅ Back</button>
    <a class="btn btn-primary" href="dashboard.php">🏠 Dashboard</a>
</div>

</div>

</body>
</html>
