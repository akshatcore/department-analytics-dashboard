<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<style>
/* ===== BACKGROUND ===== */
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

/* ===== CARD ===== */
.card{
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(14px);
    padding:35px;
    width:360px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(0,0,0,0.12);
    animation:slideUp 0.8s ease;
}
@keyframes slideUp{
    from{opacity:0;transform:translateY(20px)}
    to{opacity:1;transform:translateY(0)}
}

h2{
    text-align:center;
    margin-bottom:25px;
}

/* ===== FORM ===== */
label{
    font-weight:600;
    font-size:14px;
}
input,select,button{
    width:100%;
    padding:12px;
    margin-top:6px;
    margin-bottom:16px;
    border-radius:10px;
    border:1px solid #ccc;
    font-size:14px;
}

button{
    background:#0d6efd;
    color:#fff;
    border:none;
    cursor:pointer;
    font-size:15px;
    transition:0.3s;
}
button:hover{
    background:#084298;
}

/* ===== FOOTER ===== */
.footer{
    text-align:center;
    margin-top:10px;
    font-size:13px;
    color:#555;
}
</style>
</head>

<body>

<div class="card">

<h2>🔐 Login</h2>

<form method="post" action="login_process.php">

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <label>Role</label>
    <select name="role">
        <option value="admin">Admin</option>
        <option value="faculty">Faculty</option>
        <option value="student">Student</option>
    </select>

    <button type="submit">Login</button>
</form>

<div class="footer">
    Department Analytics & Reporting System
</div>

</div>

</body>
</html>
