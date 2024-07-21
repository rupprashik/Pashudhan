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

// Initialize variables for month and year input
$selected_month = isset($_POST['month']) ? $_POST['month'] : date('m');
$selected_year = isset($_POST['year']) ? $_POST['year'] : date('Y');

// Fetch total fees collected
$total_fees_sql = "SELECT SUM(fees) AS total_fees FROM pet_records WHERE MONTH(date) = $selected_month AND YEAR(date) = $selected_year";
$result = $conn->query($total_fees_sql);
$total_fees = $result->fetch_assoc()['total_fees'];

// Fetch total monthly entries
$total_monthly_entries_sql = "SELECT COUNT(*) AS total_monthly_entries FROM pet_records WHERE MONTH(date) = $selected_month AND YEAR(date) = $selected_year";
$result = $conn->query($total_monthly_entries_sql);
$total_monthly_entries = $result->fetch_assoc()['total_monthly_entries'];

// Fetch total yearly entries
$total_yearly_entries_sql = "SELECT COUNT(*) AS total_yearly_entries FROM pet_records WHERE YEAR(date) = $selected_year";
$result = $conn->query($total_yearly_entries_sql);
$total_yearly_entries = $result->fetch_assoc()['total_yearly_entries'];

// Fetch entries per animal type
$animal_type_sql = "SELECT animal_type, COUNT(*) AS count FROM pet_records GROUP BY animal_type";
$animal_type_result = $conn->query($animal_type_sql);
$animal_type_data = [];
while ($row = $animal_type_result->fetch_assoc()) {
    $animal_type_data[] = $row;
}

// Fetch entries per month
$monthly_entries_sql = "SELECT MONTH(date) AS month, COUNT(*) AS count FROM pet_records WHERE YEAR(date) = $selected_year GROUP BY MONTH(date)";
$monthly_entries_result = $conn->query($monthly_entries_sql);
$monthly_entries_data = [];
while ($row = $monthly_entries_result->fetch_assoc()) {
    $monthly_entries_data[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .container {
            width: 90%;
            margin: auto;
            overflow: hidden;
        }
        header {
            background: #35424a;
            color: #ffffff;
            padding-top: 30px;
            min-height: 70px;
            border-bottom: #e8491d 3px solid;
        }
        header a {
            color: #ffffff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 16px;
        }
        header ul {
            padding: 0;
            list-style: none;
        }
        header li {
            float: left;
            display: inline;
            padding: 0 20px 0 20px;
        }
        header #branding {
            float: left;
        }
        header #branding h1 {
            margin: 0;
        }
        header nav {
            float: right;
            margin-top: 10px;
        }
        header .highlight, header .current a {
            color: #e8491d;
            font-weight: bold;
        }
        .card {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .card h2 {
            margin-top: 0;
        }
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }
        .form-container {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container label {
            display: block;
            margin-bottom: 5px;
        }
        .form-container input, .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .form-container button {
            background: #35424a;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1>Analytics</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="vet_hospital.php">Home</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="form-container">
            <form method="post" action="">
                <label for="month">Select Month:</label>
                <select id="month" name="month">
                    <?php
                    for ($m=1; $m<=12; $m++) {
                        $month = date('F', mktime(0, 0, 0, $m, 10));
                        echo "<option value='$m' ".($m == $selected_month ? "selected" : "").">$month</option>";
                    }
                    ?>
                </select>
                <label for="year">Select Year:</label>
                <select id="year" name="year">
                    <?php
                    $current_year = date('Y');
                    for ($y = 2020; $y <= $current_year; $y++) {
                        echo "<option value='$y' ".($y == $selected_year ? "selected" : "").">$y</option>";
                    }
                    ?>
                </select>
                <button type="submit">Submit</button>
            </form>
        </div>

        <div class="card">
            <h2>Total Fees Collected</h2>
            <p><?php echo number_format($total_fees, 2); ?></p>
        </div>
        <div class="card">
            <h2>Total Monthly Entries</h2>
            <p><?php echo $total_monthly_entries; ?></p>
        </div>
        <div class="card">
            <h2>Total Yearly Entries</h2>
            <p><?php echo $total_yearly_entries; ?></p>
        </div>
        <div class="card">
            <h2>Entries by Animal Type</h2>
            <div class="chart-container">
                <canvas id="animalTypeChart"></canvas>
            </div>
        </div>
        <div class="card">
            <h2>Entries per Month</h2>
            <div class="chart-container">
                <canvas id="monthlyEntriesChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Animal Type Chart
        var animalTypeCtx = document.getElementById('animalTypeChart').getContext('2d');
        var animalTypeChart = new Chart(animalTypeCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($animal_type_data, 'animal_type')); ?>,
                datasets: [{
                    label: '# of Entries',
                    data: <?php echo json_encode(array_column($animal_type_data, 'count')); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Monthly Entries Chart
        var monthlyEntriesCtx = document.getElementById('monthlyEntriesChart').getContext('2d');
        var monthlyEntriesChart = new Chart(monthlyEntriesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthly_entries_data, 'month')); ?>,
                datasets: [{
                    label: '# of Entries',
                    data: <?php echo json_encode(array_column($monthly_entries_data, 'count')); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth:1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
</script>
</body>
</html>

    
