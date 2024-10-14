<?php
// Include database connection
include('db_connection.php');

// Fetch points for teams
$teams_points = $conn->query("SELECT team_name, event_name, points FROM teams_points");

// Fetch points for individuals
$individuals_points = $conn->query("SELECT individual_name, event_name, points FROM individuals_points");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Points Table</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style/styles_points.css"> <!-- Link to the new CSS file -->
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
        <h1>Points Table</h1>

        <h2>Teams Points</h2>
        <table>
            <tr>
                <th>Team Name</th>
                <th>Event Name</th>
                <th>Points</th>
            </tr>
            <?php if ($teams_points->num_rows > 0): ?>
                <?php while ($row = $teams_points->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['team_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['points']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3">No points available for teams.</td></tr>
            <?php endif; ?>
        </table>

        <h2>Individuals Points</h2>
        <table>
            <tr>
                <th>Individual Name</th>
                <th>Event Name</th>
                <th>Points</th>
            </tr>
            <?php if ($individuals_points->num_rows > 0): ?>
                <?php while ($row = $individuals_points->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['individual_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['points']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3">No points available for individuals.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>


