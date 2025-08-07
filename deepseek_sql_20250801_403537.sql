-- Players table
CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(50) NOT NULL,
    team_id INT NOT NULL,
    jersey_number INT NOT NULL,
    date_of_birth DATE NOT NULL,
    nationality VARCHAR(50) NOT NULL,
    FOREIGN KEY (team_id) REFERENCES teams(id)
);

-- Staff table
CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL
);

-- Teams table
CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    coach VARCHAR(100) NOT NULL,
    home_ground VARCHAR(100) NOT NULL,
    founded_year INT NOT NULL
);

-- Matches table
CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_date DATETIME NOT NULL,
    home_team_id INT NOT NULL,
    away_team_id INT NOT NULL,
    venue VARCHAR(100) NOT NULL,
    home_team_score INT,
    away_team_score INT,
    FOREIGN KEY (home_team_id) REFERENCES teams(id),
    FOREIGN KEY (away_team_id) REFERENCES teams(id)
);