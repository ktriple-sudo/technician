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

// Get skills for dropdown
$skills = array();
$sql = "SELECT id, skill_name FROM skills ORDER BY skill_name";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $skills[] = $row;
    }
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $years_experience = $_POST['years_experience'];
    $selected_skills = isset($_POST['skills']) ? $_POST['skills'] : array();

    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Insert technician
        $sql = "INSERT INTO technicians (name, email, phone, years_experience) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $phone, $years_experience);
        $stmt->execute();
        
        $technician_id = $stmt->insert_id;
        
        // Insert technician skills
        if (!empty($selected_skills)) {
            $skill_sql = "INSERT INTO technician_skills (technician_id, skill_id) VALUES (?, ?)";
            $skill_stmt = $conn->prepare($skill_sql);
            
            foreach ($selected_skills as $skill_id) {
                $skill_stmt->bind_param("ii", $technician_id, $skill_id);
                $skill_stmt->execute();
            }
            
            $skill_stmt->close();
        }
        
        // Commit transaction
        $conn->commit();
        $message = "New technician added successfully";
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $message = "Error: " . $e->getMessage();
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Technician</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="background">
        <div class="container">
            <h1>Add New Technician</h1>
            
            <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="name">Technician Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="years_experience">Years of Experience:</label>
                    <input type="number" id="years_experience" name="years_experience" min="0" required>
                </div>
                
                <div class="form-group">
                    <label>Skills:</label>
                    <div class="skills-checkboxes">
                        <?php foreach ($skills as $skill): ?>
                        <div>
                            <input type="checkbox" id="skill_<?php echo $skill['id']; ?>" name="skills[]" value="<?php echo $skill['id']; ?>">
                            <label for="skill_<?php echo $skill['id']; ?>"><?php echo $skill['skill_name']; ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <input type="submit" value="Add Technician">
                </div>
            </form>
            
            <p><a href="index.html">Back to Home</a></p>
        </div>
    </div>
</body>
</html>