<?php 
include('db_connection.php');

// Fetch all events from the database
$events_result = $conn->query("SELECT event_name, event_type FROM events");

$events = [];
if ($events_result->num_rows > 0) {
    while ($row = $events_result->fetch_assoc()) {
        $events[] = $row; // Store event data in an array
    }
}

// Function to get standings
function get_standings($conn, $event) {
    $sql = "SELECT participant_name, SUM(points) as total_points
            FROM scores
            WHERE event = '$event'
            GROUP BY participant_name
            ORDER BY total_points DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Rank</th><th>Participant</th><th>Total Points</th></tr>";
        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $rank . "</td><td>" . htmlspecialchars($row['participant_name']) . "</td><td>" . htmlspecialchars($row['total_points']) . "</td></tr>";
            $rank++;
        }
        echo "</table>";
    } else {
        echo "<p>No standings available for $event.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Standings</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style/standings.css"> <!-- Link to the new CSS file -->
</head>
<body>
    <div class="sidebar">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="participants.php">Participants</a></li>
            <li><a href="matchups.php">Matchups</a></li>
            <li><a href="rules.php">Rules</a></li>
            <li><a href="points.php">Points</a></li>
            <li><a href="standings.php">Standings</a></li>
            <li><a href="add_participant.php">Add Participants</a></li>
            <li><a href="manage_events.php">Events</a></li>
        </ul>
    </div>

    <div class="content">
        <?php foreach ($events as $event): ?>
            <h1><?php echo htmlspecialchars($event['event_name']); ?> Standings</h1>
            <?php get_standings($conn, $event['event_name']); ?>
        <?php endforeach; ?>
    </div>
</body>
</html>


