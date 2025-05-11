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

// Get clients for dropdown
$clients = array();
$sql = "SELECT id, name FROM clients ORDER BY name";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }
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

// Used to store available technicians
$technicians = array();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['find_technicians'])) {
        // Find technicians with the selected skill
        $skill_id = $_POST['skill_id'];
        
        $sql = "SELECT t.id, t.name, t.years_experience 
                FROM technicians t 
                JOIN technician_skills ts ON t.id = ts.technician_id 
                WHERE ts.skill_id = ?
                ORDER BY t.years_experience DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $skill_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $technicians[] = $row;
            }
        }
        
        $stmt->close();
    } elseif (isset($_POST['book_appointment'])) {
        // Book the appointment
        $client_id = $_POST['client_id'];
        $technician_id = $_POST['technician_id'];
        $appointment_date = $_POST['appointment_date'];
        $description = $_POST['description'];
        
        $sql = "INSERT INTO appointments (client_id, technician_id, appointment_date, description) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $client_id, $technician_id, $appointment_date, $description);
        
        if ($stmt->execute()) {
            $message = "Appointment booked successfully";
            // Reset form
            unset($technicians);
            $technicians = array();
        } else {
            $message = "Error booking appointment: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="background">
        <div class="container">
            <h1>Book Appointment</h1>
            
            <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="client_id">Select Client:</label>
                    <select id="client_id" name="client_id" required>
                        <option value="">-- Select Client --</option>
                        <?php foreach ($clients as $client): ?>
                        <option value="<?php echo $client['id']; ?>"><?php echo $client['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="skill_id">Required Skill:</label>
                    <select id="skill_id" name="skill_id" required>
                        <option value="">-- Select Skill --</option>
                        <?php foreach ($skills as $skill): ?>
                        <option value="<?php echo $skill['id']; ?>"><?php echo $skill['skill_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <input type="submit" name="find_technicians" value="Find Technicians">
                </div>
                
                <?php if (!empty($technicians)): ?>
                <hr>
                <h2>Available Technicians</h2>
                
                <div class="form-group">
                    <label for="technician_id">Select Technician:</label>
                    <select id="technician_id" name="technician_id" required>
                        <option value="">-- Select Technician --</option>
                        <?php foreach ($technicians as $tech): ?>
                        <option value="<?php echo $tech['id']; ?>">
                            <?php echo $tech['name']; ?> (<?php echo $tech['years_experience']; ?> years experience)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="appointment_date">Appointment Date:</label>
                    <input type="datetime-local" id="appointment_date" name="appointment_date" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                
                <div class="form-group">
                    <input type="submit" name="book_appointment" value="Book Appointment">
                </div>
                <?php endif; ?>
            </form>
            
            <p><a href="index.html">Back to Home</a></p>
        </div>
    </div>
</body>
</html>