-- ============================================================
-- GRUDGE TRACKER — DATABASE SCHEMA
-- A social platform for logging, tracking, and disputing
-- interpersonal grievances.
-- ============================================================

CREATE DATABASE IF NOT EXISTS grudge_tracker;
USE grudge_tracker;

-- ============================================================
-- 1. USERS
-- Handles authentication, and tracks each user's trust score,
-- XP/level progress, streak, and one-time undo eligibility.
-- ============================================================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  avatar_emoji VARCHAR(10) DEFAULT '👑',

  -- Core game mechanics
  trust_score INT DEFAULT 50,          -- 0–100, "trust has to be earned"
  xp INT DEFAULT 0,
  level INT DEFAULT 1,
  streak_count INT DEFAULT 0,
  last_active_date DATE DEFAULT NULL,

  -- "One Undo, Ever"
  undo_used BOOLEAN DEFAULT FALSE,
  undo_used_at TIMESTAMP NULL DEFAULT NULL,

  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ============================================================
-- 2. GRUDGES (core "posts" — Create/View/Edit/Delete requirement)
-- ============================================================
CREATE TABLE grudges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,

  title VARCHAR(255) NOT NULL,
  person_involved VARCHAR(100) NOT NULL,
  category VARCHAR(50) NOT NULL,
  severity ENUM('Low', 'Medium', 'High', 'Critical') NOT NULL,
  status ENUM('Active', 'In Progress', 'Resolved', 'Archived') DEFAULT 'Active',
  emoji VARCHAR(10) DEFAULT NULL,       -- "how does it feel" picker
  notes TEXT DEFAULT NULL,
  date_occurred DATE NOT NULL,

  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- ============================================================
-- 3. GRUDGE EVIDENCE
-- File uploads (screenshots, receipts) attached to a grudge.
-- ============================================================
CREATE TABLE grudge_evidence (
  id INT AUTO_INCREMENT PRIMARY KEY,
  grudge_id INT NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (grudge_id) REFERENCES grudges(id) ON DELETE CASCADE
);


-- ============================================================
-- 4. COMMENTS ("Witness Statements")
-- ============================================================
CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  grudge_id INT NOT NULL,
  user_id INT NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (grudge_id) REFERENCES grudges(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- ============================================================
-- 5. LIKES ("I Relate")
-- ============================================================
CREATE TABLE likes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  grudge_id INT NOT NULL,
  user_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (grudge_id) REFERENCES grudges(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_like (grudge_id, user_id)   -- one like per user per grudge
);


-- ============================================================
-- 6. DISPUTES (Courtroom cases)
-- A dispute is opened against a specific grudge.
-- ============================================================
CREATE TABLE disputes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  grudge_id INT NOT NULL,
  filed_by INT NOT NULL,

  status ENUM('In Session', 'Ruled', 'Dismissed') DEFAULT 'In Session',
  verdict ENUM('Guilty', 'Innocent', 'Pending') DEFAULT 'Pending',
  verdict_reasoning TEXT DEFAULT NULL,

  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  resolved_at TIMESTAMP NULL DEFAULT NULL,

  FOREIGN KEY (grudge_id) REFERENCES grudges(id) ON DELETE CASCADE,
  FOREIGN KEY (filed_by) REFERENCES users(id) ON DELETE CASCADE
);


-- ============================================================
-- 7. DISPUTE CLAIMS
-- Prosecution / Defense statements within a dispute.
-- ============================================================
CREATE TABLE dispute_claims (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dispute_id INT NOT NULL,
  submitted_by INT NOT NULL,
  side ENUM('Prosecution', 'Defense') NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (dispute_id) REFERENCES disputes(id) ON DELETE CASCADE,
  FOREIGN KEY (submitted_by) REFERENCES users(id) ON DELETE CASCADE
);


-- ============================================================
-- 8. DISPUTE JURORS
-- Only users explicitly invited to a case may vote on it.
-- ============================================================
CREATE TABLE dispute_jurors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dispute_id INT NOT NULL,
  user_id INT NOT NULL,
  invited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (dispute_id) REFERENCES disputes(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_invite (dispute_id, user_id)
);


-- ============================================================
-- 9. JURY VOTES
-- The application layer checks dispute_jurors before allowing
-- an insert here, since MySQL alone can't enforce
-- (must also exist in a different pair-table) as a constraint.
-- ============================================================
CREATE TABLE jury_votes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dispute_id INT NOT NULL,
  juror_id INT NOT NULL,
  vote ENUM('Guilty', 'Innocent') NOT NULL,
  reasoning TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (dispute_id) REFERENCES disputes(id) ON DELETE CASCADE,
  FOREIGN KEY (juror_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_vote (dispute_id, juror_id)   -- one vote per juror per case
);


-- ============================================================
-- 10. ACHIEVEMENTS (master badge list)
-- ============================================================
CREATE TABLE achievements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  title VARCHAR(100) NOT NULL,
  description VARCHAR(255) NOT NULL,
  icon VARCHAR(10) DEFAULT '🏆'
);


-- ============================================================
-- 11. USER ACHIEVEMENTS
-- Unlocked automatically by PHP application logic after
-- relevant actions (e.g. resolving a grudge checks whether the
-- "resolved 10 grudges" condition is now met).
-- ============================================================
CREATE TABLE user_achievements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  achievement_id INT NOT NULL,
  unlocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE,
  UNIQUE KEY unique_user_achievement (user_id, achievement_id)
);


-- ============================================================
-- 12. UNDO LOG
-- One Undo, Ever. Stores the previous state of the user's
-- most recent reversible action (as JSON) so it can be
-- restored exactly once, then never again.
-- ============================================================
CREATE TABLE undo_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  action_type ENUM('delete_grudge', 'delete_comment', 'cast_vote', 'file_dispute') NOT NULL,
  reference_id INT NOT NULL,
  previous_state JSON NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  restored BOOLEAN DEFAULT FALSE,

  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- ============================================================
-- 13. PASSWORD RESETS
-- helps with passwrd resets 
-- ============================================================
CREATE TABLE password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token VARCHAR(255) NOT NULL,
  expires_at TIMESTAMP NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- ============================================================
-- SEED DATA — default achievement definitions
-- ============================================================
INSERT INTO achievements (code, title, description, icon) VALUES
('first_blood',   'First Blood',    'Logged your first grudge',       '🏆'),
('day_in_court',  'Day in Court',   'Filed your first dispute',       '⚖️'),
('on_a_streak',   'On a Streak',    '3-day logging streak',           '🔥'),
('let_it_go',     'Let It Go',      'Resolved 10 grudges',            '🕊️'),
('petty_royalty', 'Petty Royalty',  'Reached Level 40',                '👑'),
('trusted',       'Trusted',        'Reached 80 trust score',          '🛡️');