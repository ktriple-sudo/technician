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
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Insert data into clients table
    $sql = "INSERT INTO clients (name, email, phone, address) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $phone, $address);
    
    if ($stmt->execute()) {
        $message = "New client added successfully";
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
    <title>Client</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="background">
        <div class="container">
            <h1>Add New Client</h1>
            
            <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="name">Client Name:</label>
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
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                
                <div class="form-group">
                    <input type="submit" value="Add Client">
                </div>
            </form>
            
            <p><a href="index.html">Back to Home</a></p>
        </div>
    </div>
</body>
</html>