<?php
    // Fetch participants and teams from database
    include('db_connection.php');
    $individuals = $conn->query("SELECT * FROM participants WHERE type='individual'");
    $teams = $conn->query("SELECT * FROM teams");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participants</title>
    <link rel="stylesheet" href="style/participant.css">
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
        <h1>Participants</h1>

        <h2>Individual Participants</h2>
        <ul class="participant-list">
        <?php while ($row = $individuals->fetch_assoc()) { ?>
            <li><?php echo $row['name']; ?></li>
        <?php } ?>
        </ul>

        <h2>Teams</h2>
        <ul class="team-list">
        <?php while ($team = $teams->fetch_assoc()) { ?>
            <li><?php echo $team['team_name']; ?> (Team Members: <?php echo $team['members']; ?>)</li>
        <?php } ?>
        </ul>
    </div>
</body>
</html>
