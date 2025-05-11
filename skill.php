<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password is empty
$dbname = "technician_system"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $skill_name = $_POST['skill_name'];
    $description = $_POST['description'];

    // Insert data into skills table
    $sql = "INSERT INTO skills (skill_name, description) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $skill_name, $description);
    
    if ($stmt->execute()) {
        $message = "New skill added successfully";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Skill</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="background">
        <div class="container">
            <h1>Add New Skill</h1>
            
            <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="skill_name">Skill Name:</label>
                    <input type="text" id="skill_name" name="skill_name" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                
                <div class="form-group">
                    <input type="submit" value="Add Skill">
                </div>
            </form>
            
            <p><a href="index.html">Back to Home</a></p>
        </div>
    </div>
</body>
</html>