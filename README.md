Grudge Tracker

A PHP/MySQL web app for logging, tracking, and settling interpersonal grudges — because sometimes an apology isn't enough, and you need a jury.

Grudge Tracker turns everyday beef into something structured: log a grudge against someone, take it to the Dispute Courtroom, let a jury of your peers vote on it, and watch your trust score and XP shift based on the outcome. It's built with a grunge/graffiti aesthetic — evidence-board layouts, hand-drawn SVG icons, drip marks — because grudges deserve a bit of drama.

The Problem

Small conflicts and unresolved grievances rarely get tracked or resolved fairly — they just get remembered, misremembered, or forgotten. Grudge Tracker gives people a lightweight, semi-serious system to document a grievance, put it in front of an impartial "jury," and get closure through a transparent voting process instead of it festering indefinitely.

User Flow
Register / Login — create an account and land on your Dashboard.
Dashboard — see your trust score (SVG donut chart), XP progress, and a snapshot of active grudges.
Log a Grudge — file a new grudge against another user, pick an emoji/mood tag, and attach evidence (file upload).
All Grudges — browse every grudge in the system with live filtering (by status, user, category).
Jury & Judge Invitations — once a grudge is disputed, eligible users (anyone uninvolved in the case) are sent an invitation to serve as jury or judge. If a user declines, the invitation moves to the next eligible user until the panel is filled. This keeps selection fair and impartial — nobody appoints themselves, and no one connected to the grudge decides its outcome.
Dispute Courtroom — invited jurors and a judge review the case: prosecution and defense each present their side, and the jury panel votes on the outcome.
Timeline — track how a specific grudge evolved from filing to resolution, laid out chronologically.
Profile — view your trust score history, XP level, earned achievement badges, and past grudges (both filed and received).
Success Page — confirmation screen after key actions (filing a grudge, casting a jury vote, etc.).

Every user operates under one role: they can file grudges, get accused in others' grudges, and — when invited — serve as jury or judge on disputes that aren't their own. Trust score and XP are earned (or lost) based on how those disputes play out.

Core Features
Authentication — secure login/register with session-based auth
CRUD on grudges — create, read, update (dispute status), and resolve grudges
Dispute Courtroom — structured voting system with jury logic
Fair jury/judge selection — eligible users are invited to serve as jury or judge on a per-case basis, keeping the process impartial
Trust score & XP system — calculated from grudge outcomes and jury participation
Achievement badges — unlocked through app engagement and milestones
Live filtering — JavaScript-powered search/filter on the All Grudges page
Custom SVG icon set — grunge/graffiti-styled icons in place of standard emoji

Tech Stack
Backend: PHP + MySQL
Frontend: Plain CSS (single main.css), vanilla JavaScript
Environment: XAMPP (Windows)

Project Structure
Grudge-Tracker/
├── Public/
│   ├── includes/
│   │   └── sidebar.php       # shared nav with active-state highlighting
│   ├── css/
│   │   └── main.css
│   ├── login.php
│   ├── register.php
│   ├── dashboard.php
│   ├── all-grudges.php
│   ├── log-grudge.php
│   ├── timeline.php
│   ├── courtroom.php
│   ├── profile.php
│   └── success.php
├── database/
│   └── grudge_tracker.sql
└── README.md

Setup
Clone this repo into C:\xampp\htdocs\Grudge-Tracker\
Start Apache and MySQL in XAMPP
Import database/grudge_tracker.sql into MySQL via phpMyAdmin
Visit 

http://localhost/Grudge-Tracker/Public/login.php
Status

Backend and database integration are complete — auth, grudge CRUD, jury voting, and trust score/XP logic are all live and wired to MySQL. Frontend polish is ongoing, primarily the rollout of a custom grunge-style SVG icon set across all pages to replace the current placeholder icons.

Demo video and ER diagram can be found from:https://drive.google.com/drive/folders/1AKYO9D0KJ2vEGeiVJlrj8NzC5xKPKB1M?usp=sharing
