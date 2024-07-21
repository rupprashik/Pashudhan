<?php
// Database connection settings
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "veterinary_hospital";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// Fetch the record ID of the last record
$last_record_sql = "SELECT MAX(record_id) AS last_record_id FROM pet_records";
$result = $conn->query($last_record_sql);
$last_record_id = $result->fetch_assoc()['last_record_id'];

$current_record_id = isset($_GET['record_id']) ? intval($_GET['record_id']) : NULL;



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner_name = $_POST['owner_name'];
    $profession = $_POST['profession'];
    $address = $_POST['address'];
    $pet_name = $_POST['pet_name'];
    $animal_type = $_POST['animal_type'];
    $disease_description = $_POST['disease_description'];
    $fees = $_POST['fees'];
    $date = date('Y-m-d');

    if (empty($current_record_id) || isset($_POST['new_record'])) {
        // Calculate monthly and yearly serial numbers
        $year = date('Y');
        $month = date('m');

        $monthly_serial_sql = "SELECT COUNT(*) + 1 AS monthly_serial FROM pet_records WHERE YEAR(date) = '$year' AND MONTH(date) = '$month'";
        $result = $conn->query($monthly_serial_sql);
        $monthly_serial = $result->fetch_assoc()['monthly_serial'];

        $yearly_serial_sql = "SELECT COUNT(*) + 1 AS yearly_serial FROM pet_records WHERE YEAR(date) = '$year'";
        $result = $conn->query($yearly_serial_sql);
        $yearly_serial = $result->fetch_assoc()['yearly_serial'];

        // Insert data into database
        $sql = "INSERT INTO pet_records (owner_name, profession, address, pet_name, animal_type, disease_description, fees, date, monthly_serial, yearly_serial)
                VALUES ('$owner_name', '$profession', '$address', '$pet_name', '$animal_type', '$disease_description', '$fees', '$date', '$monthly_serial', '$yearly_serial')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('New record created successfully');</script>";
            $current_record_id = $conn->insert_id;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        // Update existing record
        $sql = "UPDATE pet_records SET owner_name='$owner_name', profession='$profession', address='$address', pet_name='$pet_name', animal_type='$animal_type', disease_description='$disease_description', fees='$fees' WHERE record_id=$current_record_id";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Record updated successfully');</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Fetch the current record if a record ID is provided
$current_record = null;
if (!empty($current_record_id)) {
    $sql = "SELECT * FROM pet_records WHERE record_id = $current_record_id";
    $result = $conn->query($sql);
    $current_record = $result->fetch_assoc();
}

// Determine the next and previous record IDs
$next_record_id = null;
$prev_record_id = null;
if (!empty($current_record_id)) {
    // Get the next record ID
    $sql = "SELECT record_id FROM pet_records WHERE record_id > $current_record_id ORDER BY record_id ASC LIMIT 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $next_record_id = $result->fetch_assoc()['record_id'];
    }

    // Get the previous record ID
    $sql = "SELECT record_id FROM pet_records WHERE record_id < $current_record_id ORDER BY record_id DESC LIMIT 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $prev_record_id = $result->fetch_assoc()['record_id'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinary Hospital Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e0f7fa;
        }
        .container {
            width: 90%;
            margin: auto;
            overflow: hidden;
        }
        header {
            background: #f0f0f0;
            color: #35424a;
            padding-top: 10px;
            min-height: 70px;
            border-bottom: #e8491d 3px solid;
        }
        header a {
            color: #35424a;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 20px;
        }
        header ul {
            padding: 0;
            list-style: none;
        }
        header li {
            float: left;
            display: inline;
            padding: 0 10px 0 10px;
        }
        header #branding {
            float: left;
        }
        header #branding h1 {
            margin: 0;
        }
        header nav {
            float: right;
            margin-top: 5px;
        }
        header .highlight, header .current a {
            color: #e8491d;
            font-weight: bold;
        }
        header .logo-branding {
            display: flex;
            align-items: center;
            gap: 190px;
        }
        .showcase {
            min-height: 300px;
            background: url('showcase.jpg') no-repeat 0 -400px;
            text-align: center;
            color: #35424a;
        }
        .showcase h1 {
            margin-top: 50px;
            font-size: 50px;
            margin-bottom: 10px;
        }
        .showcase p {
            font-size: 20px;
        }
        .form-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            max-width: 100%;
            margin: 10px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        .form-group {
            width: 48%;
        }
        .form-group-full {
            width: 100%;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .record-display {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 5px 10px;
            background-color: #35424a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .button:hover {
            background-color: #2e3b4a;
        }
        .navbar-button {
            display: inline-block;
            padding: 5px 5px;
            background-color: #35424a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            margin: 0 0px; /* Adjust margin to align with other links */
        }
        .navbar-button:hover {
            background-color: #2e3b4a;
        }

        .record-info {
            display: flex;
            justify-content: space-between; /* Adjusts the space between items */
            margin-bottom: 10px; /* Optional: Adds some space below the row */
        }
        .record-info label {
            flex: 1; /* Distributes the available space evenly */
            text-align: center; /* Centers the text in each label */
            margin: 0 10px; /* Optional: Adds some horizontal spacing between labels */
        }

        .floating-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background-color: #35424a;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            font-size: 24px;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }
        .floating-button:hover {
            background-color: #2e3b4a;
        }
        .floating-button-left {
            position: fixed;
            bottom: 20px;
            left: 20px;
            width: 50px;
            height: 50px;
            background-color: #35424a;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            font-size: 24px;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }
        .floating-button-left:hover {
            background-color: #2e3b4a;
        }


        header .search-bar {
            display: inline-block;
            margin-top: 0px;
        }
        header .search-bar input[type="text"] {
            padding: 5px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            margin-right: 5px;
        }
        header .search-bar button {
            padding: 0px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
        }
        header .search-bar button img {
            width: 16px;
            height: 16px;
        }

    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo-branding">
                <div>
                    <img src="dahd-logo.png" alt="Gov sign" width="300" height="80">
                </div>
                <div id="branding">
                    <h1>PASHUDHAN</h1>
                </div>
                <nav>
                    <ul>
                        <li class="current"><a href="#">Home</a></li>
                        <li><a href="analytics.php">Analytics</a></li>
                        <li class="search-bar">
                        <form action="vet_hospital.php" method="get" name="search">
                            <input type="text" name="record_id" placeholder="Search by Record ID">
                            <button type="submit">
                            </button>
                        </form>
                    </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

            <a href="?new_record=1" class="floating-button">+</a>
    
            <?php if (!$prev_record_id): ?>
                <a href="?record_id=<?php echo $last_record_id; ?>" class="floating-button-left">-</a>
            <?php endif; ?>
            <?php if ($prev_record_id): ?>
                <a href="?record_id=<?php echo $prev_record_id; ?>" class="floating-button-left">-</a>
            <?php endif; ?>


    <div class="container">
        <div class="navigation-buttons">
            <?php if ($next_record_id): ?>
                <a href="?record_id=<?php echo $next_record_id; ?>" class="button">Next Record</a>
            <?php endif; ?>
        </div>

        <form method="post" action="" class="form-container">
            <?php if ($current_record): ?>
                <div class="form-group-full record-info">
                    <label>Record Number: <?php echo htmlspecialchars($current_record['record_id']); ?></label>
                    <label>Monthly Serial: <?php echo htmlspecialchars($current_record['monthly_serial']); ?></label>
                    <label>Yearly Serial: <?php echo htmlspecialchars($current_record['yearly_serial']); ?></label>
                    <label>Date: <?php echo htmlspecialchars($current_record['date']); ?></label>
                </div>
                <div class="form-group">
                    <label for="owner_name">Owner's Name</label>
                    <input type="text" id="owner_name" name="owner_name" value="<?php echo htmlspecialchars($current_record['owner_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="profession">Owner's Profession</label>
                    <input type="text" id="profession" name="profession" value="<?php echo htmlspecialchars($current_record['profession']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Owner's Address</label>
                    <textarea id="address" name="address" required><?php echo htmlspecialchars($current_record['address']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="pet_name">Pet's Name</label>
                    <input type="text" id="pet_name" name="pet_name" value="<?php echo htmlspecialchars($current_record['pet_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="animal_type">Type of Animal</label>
                    <select id="animal_type" name="animal_type" required>
                        <option value="Dog" <?php echo $current_record['animal_type'] == 'Dog' ? 'selected' : ''; ?>>Dog</option>
                        <option value="Cat" <?php echo $current_record['animal_type'] == 'Cat' ? 'selected' : ''; ?>>Cat</option>
                        <option value="Bird" <?php echo $current_record['animal_type'] == 'Bird' ? 'selected' : ''; ?>>Bird</option>
                        <option value="Other" <?php echo $current_record['animal_type'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                        <option value="Rabbit" <?php echo $current_record['animal_type'] == 'Rabbit' ? 'selected' : ''; ?>>Rabbit</option>
                        <option value="Hamster" <?php echo $current_record['animal_type'] == 'Hamster' ? 'selected' : ''; ?>>Hamster</option>
                        <option value="Guinea Pig" <?php echo $current_record['animal_type'] == 'Guinea Pig' ? 'selected' : ''; ?>>Guinea Pig</option>
                        <option value="Fish" <?php echo $current_record['animal_type'] == 'Fish' ? 'selected' : ''; ?>>Fish</option>
                        <option value="Horse" <?php echo $current_record['animal_type'] == 'Horse' ? 'selected' : ''; ?>>Horse</option>
                        <option value="Turtle" <?php echo $current_record['animal_type'] == 'Turtle' ? 'selected' : ''; ?>>Turtle</option>
                        <option value="Lizard" <?php echo $current_record['animal_type'] == 'Lizard' ? 'selected' : ''; ?>>Lizard</option>
                        <option value="Snake" <?php echo $current_record['animal_type'] == 'Snake' ? 'selected' : ''; ?>>Snake</option>
                        <option value="Frog" <?php echo $current_record['animal_type'] == 'Frog' ? 'selected' : ''; ?>>Frog</option>
                        <option value="Parrot" <?php echo $current_record['animal_type'] == 'Parrot' ? 'selected' : ''; ?>>Parrot</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="disease_description">Disease Description</label>
                    <textarea id="disease_description" name="disease_description" required><?php echo htmlspecialchars($current_record['disease_description']); ?></textarea>
                </div>
                <div class="form-group-full">
                    <label for="fees">Fees</label>
                    <input type="number" id="fees" name="fees" step="0.01" value="<?php echo htmlspecialchars($current_record['fees']); ?>" required>
                </div>
                <div class="form-group-full">
                    <button type="submit">Save</button>
                </div>
            <?php else: ?>
                <div class="form-group-full record-info">
                    <label>Record Number: <?php echo $last_record_id+1; ?></label>
                </div>
                <div class="form-group">
                    <label for="owner_name">Owner's Name</label>
                    <input type="text" id="owner_name" name="owner_name" required>
                </div>
                <div class="form-group">
                    <label for="profession">Owner's Profession</label>
                    <input type="text" id="profession" name="profession" required>
                </div>
                <div class="form-group">
                    <label for="address">Owner's Address</label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                <div class="form-group">
                    <label for="pet_name">Pet's Name</label>
                    <input type="text" id="pet_name" name="pet_name" required>
                </div>
                <div class="form-group">
                    <label for="animal_type">Type of Animal</label>
                    <select id="animal_type" name="animal_type" required>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        <option value="Bird">Bird</option>
                        <option value="Other">Other</option>
                        <option value="Rabbit">Rabbit</option>
                        <option value="Hamster">Hamster</option>
                        <option value="Guinea Pig">Guinea Pig</option>
                        <option value="Fish">Fish</option>
                        <option value="Horse">Horse</option>
                        <option value="Turtle">Turtle</option>
                        <option value="Lizard">Lizard</option>
                        <option value="Snake">Snake</option>
                        <option value="Frog">Frog</option>
                        <option value="Parrot">Parrot</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="disease_description">Disease Description</label>
                    <textarea id="disease_description" name="disease_description" required></textarea>
                </div>
                <div class="form-group-full">
                    <label for="fees">Fees</label>
                    <input type="number" id="fees" name="fees" step="1" required>
                </div>
                <div class="form-group-full">
                    <button type="submit" name="new_record" value="1">Submit</button>
                </div>
            <?php endif; ?>
        </form>
    </div>

</body>
</html>
