CREATE DATABASE tournament_db;

USE tournament_db;

-- Table for storing teams and participants
CREATE TABLE participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    team VARCHAR(255),
    score INT DEFAULT 0
);

-- Table for storing events
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Table for storing scores
CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participant_id INT,
    event_id INT,
    score INT,
    FOREIGN KEY (participant_id) REFERENCES participants(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);

--Table for matchups 
CREATE TABLE matchups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('team', 'individual'),
    team1 VARCHAR(255),
    team2 VARCHAR(255),
    participant1 VARCHAR(255),
    participant2 VARCHAR(255),
    winner VARCHAR(255) DEFAULT NULL
);


--Then later you can add points in both teams  and participants 
ALTER TABLE teams ADD points INT DEFAULT 0;
ALTER TABLE participants ADD points INT DEFAULT 0;


--Matchups tables
CREATE TABLE matchups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(255),
    type ENUM('team', 'individual'), 
    team1 VARCHAR(255),
    team2 VARCHAR(255),
    participant1 VARCHAR(255),
    participant2 VARCHAR(255),
    winner VARCHAR(255),
    result ENUM('win', 'draw', 'loss'),
    round INT DEFAULT 1
);

--For creating Scores 
CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event VARCHAR(100),
    team_name VARCHAR(100),
    round INT,
    points INT,
    UNIQUE (event, team_name, round) -- Ensures each team has unique points per round
);

