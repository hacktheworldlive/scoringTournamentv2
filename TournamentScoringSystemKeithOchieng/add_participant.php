<?php
// Include the database connection file
include('db_connection.php');

// Variables to hold the limits
$max_teams = 4;
$max_team_members = 5;
$max_individuals = 20;
$team_limit_reached = false;
$individual_limit_reached = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'];

    if ($type == 'team') {
        // Check how many teams are already in the database
        $team_count_query = "SELECT COUNT(*) as team_count FROM teams";
        $result = $conn->query($team_count_query);
        $row = $result->fetch_assoc();
        $team_count = $row['team_count'];

        if ($team_count >= $max_teams) {
            $message = "Team limit reached! No more teams can be entered.";
            $team_limit_reached = true;
        } else {
            // Check how many members the team has
            $team_name = $_POST['team_name'];
            $members = $_POST['members'];
            $member_count = count(explode(',', $members));

            if ($member_count != $max_team_members) {
                $message = "Each team must have exactly 5 members.";
            } else {
                // Insert the team if everything is valid
                $sql = "INSERT INTO teams (team_name, members) VALUES ('$team_name', '$members')";
                if ($conn->query($sql) === TRUE) {
                    $message = "Team added successfully!";
                } else {
                    $message = "Error: " . $sql . "<br>" . $conn->error;
                }
            }
        }
    } elseif ($type == 'individual') {
        // Check how many individuals are already in the database
        $individual_count_query = "SELECT COUNT(*) as individual_count FROM participants WHERE type = 'individual'";
        $result = $conn->query($individual_count_query);
        $row = $result->fetch_assoc();
        $individual_count = $row['individual_count'];

        if ($individual_count >= $max_individuals) {
            $message = "Individual participant limit reached! No more individuals can be entered.";
            $individual_limit_reached = true;
        } else {
            $name = $_POST['name'];
            // Insert individual if the limit has not been reached
            $sql = "INSERT INTO participants (name, type) VALUES ('$name', 'individual')";
            if ($conn->query($sql) === TRUE) {
                $message = "Individual added successfully!";
            } else {
                $message = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Participant</title>
    <link rel="stylesheet" href="style/add_participant.css"> <!-- Link to the new CSS file -->
</head>
<body>
    <div class="container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <h2>Menu</h2>
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

        <!-- Main Content Area -->
        <div class="content">
            <h1>Add Participant</h1>
            <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>

            <?php if (!$team_limit_reached && !$individual_limit_reached): ?>
                <form method="POST" action="">
                    <label for="type">Participant Type:</label>
                    <select name="type" id="typeSelect" required>
                        <option value="individual">Individual</option>
                        <option value="team">Team</option>
                    </select><br>

                    <!-- Individual Name Field (only visible for individuals) -->
                    <div id="individualFields">
                        <label for="name">Participant Name:</label>
                        <input type="text" name="name" required><br>
                    </div>

                    <!-- Team Fields (only visible for teams) -->
                    <div id="teamFields" style="display:none;">
                        <label for="team_name">Team Name:</label>
                        <input type="text" name="team_name" required><br>

                        <label for="members">Team Members (comma separated):</label>
                        <input type="text" name="members" required><br>
                    </div>

                    <input type="submit" value="Add Participant">
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const typeSelect = document.getElementById('typeSelect');
        const individualFields = document.getElementById('individualFields');
        const teamFields = document.getElementById('teamFields');

        typeSelect.addEventListener('change', function() {
            if (this.value === 'team') {
                teamFields.style.display = 'block';
                individualFields.style.display = 'none';
            } else {
                teamFields.style.display = 'none';
                individualFields.style.display = 'block';
            }
        });
    </script>
</body>
</html>

