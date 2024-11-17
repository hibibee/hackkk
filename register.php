<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_management";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่าฟอร์มถูกส่งมา
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ตรวจสอบว่า Password และ Confirm-Password ตรงกัน
    if ($password !== $confirm_password) {
        die("Password และ Confirm-Password ไม่ตรงกัน!");
    }

    // เข้ารหัสรหัสผ่าน
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // ตรวจสอบว่าอีเมลนี้มีอยู่ในระบบหรือยัง
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "อีเมลนี้มีอยู่ในระบบแล้ว!";
    } else {
        // บันทึกข้อมูลผู้ใช้ใหม่
        $sql = "INSERT INTO users (email, name, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $email, $name, $hashed_password);
        if ($stmt->execute()) {
            echo "ลงทะเบียนสำเร็จ!";
        } else {
            echo "เกิดข้อผิดพลาด: " . $stmt->error;
        }
    }

    $stmt->close();
}

$conn->close();
?>
