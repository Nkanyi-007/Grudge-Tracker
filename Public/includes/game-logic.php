<?php
// Shared game-mechanic functions: streaks, trust score, and verdict finalization.

function updateStreak($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT streak_count, last_active_date FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    $today = date('Y-m-d');
    $lastActive = $user['last_active_date'];

    if ($lastActive === $today) {
        return; // Already logged something today — streak doesn't change twice in one day
    }

    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $newStreak = ($lastActive === $yesterday) ? $user['streak_count'] + 1 : 1;

    $update = $pdo->prepare("UPDATE users SET streak_count = ?, last_active_date = ? WHERE id = ?");
    $update->execute([$newStreak, $today, $userId]);
}

function adjustTrust($pdo, $userId, $amount) {
    $stmt = $pdo->prepare("SELECT trust_score FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $current = $stmt->fetch()['trust_score'];

    $new = $current + $amount;
    if ($new > 100) $new = 100;
    if ($new < 0) $new = 0;

    $update = $pdo->prepare("UPDATE users SET trust_score = ? WHERE id = ?");
    $update->execute([$new, $userId]);
}

function finalizeVerdictIfReady($pdo, $disputeId) {
    $disputeStmt = $pdo->prepare("SELECT * FROM disputes WHERE id = ?");
    $disputeStmt->execute([$disputeId]);
    $dispute = $disputeStmt->fetch();
    if (!$dispute || $dispute['status'] !== 'In Session') return;

    $totalJurorsStmt = $pdo->prepare("SELECT COUNT(*) as count FROM dispute_jurors WHERE dispute_id = ?");
    $totalJurorsStmt->execute([$disputeId]);
    $totalJurors = (int) $totalJurorsStmt->fetch()['count'];

    $votesStmt = $pdo->prepare("SELECT vote, COUNT(*) as count FROM jury_votes WHERE dispute_id = ? GROUP BY vote");
    $votesStmt->execute([$disputeId]);
    $voteCounts = ['Guilty' => 0, 'Innocent' => 0];
    foreach ($votesStmt->fetchAll() as $row) {
        $voteCounts[$row['vote']] = (int) $row['count'];
    }
    $totalVotes = $voteCounts['Guilty'] + $voteCounts['Innocent'];

    // Only finalize once every invited juror has actually voted
    if ($totalJurors === 0 || $totalVotes < $totalJurors) return;

    $verdict = $voteCounts['Guilty'] > $voteCounts['Innocent'] ? 'Guilty' : 'Innocent';

    $updateStmt = $pdo->prepare("UPDATE disputes SET verdict = ?, status = 'Ruled', resolved_at = NOW() WHERE id = ?");
    $updateStmt->execute([$verdict, $disputeId]);

    if ($dispute['defendant_id']) {
        adjustTrust($pdo, $dispute['defendant_id'], $verdict === 'Innocent' ? 5 : -5);
    }
}        