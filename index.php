<?php

$host = "localhost";
$db = "php-task";
$charset = "utf8mb4";
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$user = "root";
$pass = "";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_CASE => PDO::CASE_NATURAL,
    PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    echo $e;
}

// Handle the form submission for adding new student records
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'submit') {
        $name = htmlspecialchars($_POST['name']);
        $college = htmlspecialchars($_POST['college']);
        $department = htmlspecialchars($_POST['department']);
        $gpa = htmlspecialchars($_POST['gpa']);

        $sql = "INSERT INTO `student` (`name`, `college`, `department`, `gpa`) VALUES (:name, :college, :department, :gpa)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':college', $college);
        $stmt->bindParam(':department', $department);
        $stmt->bindParam(':gpa', $gpa);
        $stmt->execute();
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        $sql = "DELETE FROM student WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

// Fetch student records for display
$rows = $pdo->query('SELECT * FROM student;');

?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Information Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Student Information Form</h2>
            <form id="studentForm" method="POST" action="">
                <input type="hidden" name="action" value="submit">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="college">College:</label>
                <input type="text" id="college" name="college" required>

                <label for="department">Department:</label>
                <input type="text" id="department" name="department" required>

                <label for="gpa">GPA:</label>
                <input type="text" id="gpa" name="gpa" required pattern="\d+(\.\d{1,2})?" title="Please enter a valid GPA (e.g., 3.75)">

                <input type="submit" value="Submit">
            </form>
        </div>

        <div class="table-container">
            <h2>Entered Student Information</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>College</th>
                        <th>Department</th>
                        <th>GPA</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody">
                    <?php
                        while ($row = $rows->fetch()) {
                            $id = $row['id'];
                            $name = trim($row['name'], "[]");
                            $college = trim($row['college'], "[]");
                            $department = trim($row['department'], "[]");
                            $gpa = trim($row['gpa'], "[]");
                            echo "<tr>";
                            echo "<td>$name</td>";
                            echo "<td>$college</td>";
                            echo "<td>$department</td>";
                            echo "<td>$gpa</td>";
                            echo "<td>
                                    <form method='POST' action='' style='display:inline;'>
                                        <input type='hidden' name='action' value='delete'>
                                        <input type='hidden' name='id' value='$id'>
                                        <input type='submit' value='Delete' onclick='return confirm(\"Are you sure you want to delete this record?\")'>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
