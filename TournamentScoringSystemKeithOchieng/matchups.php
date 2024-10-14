<?php
include('db_connection.php');

// Fetch teams and individual participants
$teams_result = $conn->query("SELECT * FROM teams");
$individuals_result = $conn->query("SELECT * FROM participants WHERE type='individual'");

// Fetch events from the database
$events_result = $conn->query("SELECT * FROM events");
$team_events = [];
$individual_events = [];

// Separate events into team and individual categories
while ($event = $events_result->fetch_assoc()) {
    if ($event['event_type'] == 'team') {
        $team_events[] = $event['event_name'];
    } else {
        $individual_events[] = $event['event_name'];
    }
}

// Prepare matchups
$team_matchups = [];
$individual_matchups = [];

// Function to create matchups
function create_matchups($participants) {
    $matchups = [];
    shuffle($participants); // Shuffle to randomize matchups
    for ($i = 0; $i < count($participants); $i += 2) {
        if (isset($participants[$i]) && isset($participants[$i + 1])) {
            $matchups[] = [$participants[$i]['team_name'] ?? $participants[$i]['name'], 
                           $participants[$i + 1]['team_name'] ?? $participants[$i + 1]['name']];
        }
    }
    return $matchups;
}

// Generate matchups for team events
if ($teams_result->num_rows >= 4) {
    $teams = $teams_result->fetch_all(MYSQLI_ASSOC);
    foreach ($team_events as $event) {
        $team_matchups[$event] = create_matchups($teams);
    }
} else {
    foreach ($team_events as $event) {
        $team_matchups[$event] = "Not enough teams to create matchups.";
    }
}

// Generate matchups for individual events
if ($individuals_result->num_rows >= 2) {
    $individuals = $individuals_result->fetch_all(MYSQLI_ASSOC);
    foreach ($individual_events as $event) {
        $individual_matchups[$event] = create_matchups($individuals);
    }
} else {
    foreach ($individual_events as $event) {
        $individual_matchups[$event] = "Not enough individuals to create matchups.";
    }
}

// Handle point submissions and rounds logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event = $_POST['event'];
    $participant1 = $_POST['participant1'];
    $participant2 = $_POST['participant2'];
    $result = $_POST['result'];
    $round = $_POST['round'];

    // Point system: win = 3 points, draw = 1 point, loss = 0 points
    $points = [0, 0]; // [points for participant 1, points for participant 2]

    if ($result == 'win') {
        $points[0] = 3; // Participant 1 wins
    } elseif ($result == 'draw') {
        $points[0] = 1; // Draw
        $points[1] = 1;
    } elseif ($result == 'loss') {
        $points[1] = 3; // Participant 2 wins
    }

    // Insert or update scores for both participants/teams
    $conn->query("INSERT INTO scores (event, participant_name, round, points) VALUES ('$event', '$participant1', '$round', {$points[0]}) 
                  ON DUPLICATE KEY UPDATE points = points + {$points[0]}");

    $conn->query("INSERT INTO scores (event, participant_name, round, points) VALUES ('$event', '$participant2', '$round', {$points[1]}) 
                  ON DUPLICATE KEY UPDATE points = points + {$points[1]}");
}

// Function to display the current standings
function get_standings($conn, $event) {
    $result = $conn->query("SELECT participant_name, SUM(points) AS total_points FROM scores WHERE event='$event' GROUP BY participant_name ORDER BY total_points DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matchups</title>
    <link rel="stylesheet" href="style/styles_matchups.css">
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
        <h1>Team Matchups</h1>
        <?php foreach ($team_matchups as $event => $matchup): ?>
            <h2><?php echo htmlspecialchars($event); ?> Matchups</h2>
            <?php if (is_array($matchup)): ?>
                <ul>
                    <?php foreach ($matchup as $m): ?>
                        <li>
                            <?php echo htmlspecialchars(implode(' vs ', $m)); ?>
                            <form method="POST" action="">
                                <input type="hidden" name="event" value="<?php echo htmlspecialchars($event); ?>">
                                <input type="hidden" name="participant1" value="<?php echo htmlspecialchars($m[0]); ?>">
                                <input type="hidden" name="participant2" value="<?php echo htmlspecialchars($m[1]); ?>">
                                <input type="number" name="round" placeholder="Round" required min="1" max="4">
                                <select name="result" required>
                                    <option value="">Select Result</option>
                                    <option value="win">Team 1 Wins</option>
                                    <option value="draw">Draw</option>
                                    <option value="loss">Team 2 Wins</option>
                                </select>
                                <button type="submit">Submit Result</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><?php echo htmlspecialchars($matchup); ?></p>
            <?php endif; ?>
        <?php endforeach; ?>

        <h1>Individual Matchups</h1>
        <?php foreach ($individual_matchups as $event => $matchup): ?>
            <h2><?php echo htmlspecialchars($event); ?> Matchups</h2>
            <?php if (is_array($matchup)): ?>
                <ul>
                    <?php foreach ($matchup as $m): ?>
                        <li>
                            <?php echo htmlspecialchars(implode(' vs ', $m)); ?>
                            <form method="POST" action="">
                                <input type="hidden" name="event" value="<?php echo htmlspecialchars($event); ?>">
                                <input type="hidden" name="participant1" value="<?php echo htmlspecialchars($m[0]); ?>">
                                <input type="hidden" name="participant2" value="<?php echo htmlspecialchars($m[1]); ?>">
                                <input type="number" name="round" placeholder="Round" required min="1" max="20">
                                <select name="result" required>
                                    <option value="">Select Result</option>
                                    <option value="win">Participant 1 Wins</option>
                                    <option value="draw">Draw</option>
                                    <option value="loss">Participant 2 Wins</option>
                                </select>
                                <button type="submit">Submit Result</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><?php echo htmlspecialchars($matchup); ?></p>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</body>
</html>










