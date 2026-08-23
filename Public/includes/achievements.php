<?php
// Checks all achievement conditions for a user and unlocks any newly-earned ones.
// Call this after any action that could trigger a badge (logging a grudge, filing a dispute, etc).

function checkAchievements($pdo, $userId) {
    // Pull the user's current stats
    $userStmt = $pdo->prepare("SELECT level, trust_score, streak_count FROM users WHERE id = ?");
    $userStmt->execute([$userId]);
    $user = $userStmt->fetch();

    $totalGrudgesStmt = $pdo->prepare("SELECT COUNT(*) as count FROM grudges WHERE user_id = ?");
    $totalGrudgesStmt->execute([$userId]);
    $totalGrudges = $totalGrudgesStmt->fetch()['count'];

    $resolvedStmt = $pdo->prepare("SELECT COUNT(*) as count FROM grudges WHERE user_id = ? AND status = 'Resolved'");
    $resolvedStmt->execute([$userId]);
    $resolvedCount = $resolvedStmt->fetch()['count'];

    $disputesFiledStmt = $pdo->prepare("SELECT COUNT(*) as count FROM disputes WHERE filed_by = ?");
    $disputesFiledStmt->execute([$userId]);
    $disputesFiled = $disputesFiledStmt->fetch()['count'];

    // Each achievement's unlock condition, matched by its "code" from the achievements table
    $conditions = [
        'first_blood'   => $totalGrudges >= 1,
        'day_in_court'  => $disputesFiled >= 1,
        'on_a_streak'   => $user['streak_count'] >= 3,
        'let_it_go'     => $resolvedCount >= 10,
        'petty_royalty' => $user['level'] >= 40,
        'trusted'       => $user['trust_score'] >= 80,
    ];

    foreach ($conditions as $code => $isMet) {
        if (!$isMet) continue;

        // Look up the achievement's ID by its code
        $achStmt = $pdo->prepare("SELECT id FROM achievements WHERE code = ?");
        $achStmt->execute([$code]);
        $achievement = $achStmt->fetch();
        if (!$achievement) continue;

        // Insert only if not already unlocked — the UNIQUE key on (user_id, achievement_id)
        // means this is safe to attempt even if it's already unlocked
        $insertStmt = $pdo->prepare("INSERT IGNORE INTO user_achievements (user_id, achievement_id) VALUES (?, ?)");
        $insertStmt->execute([$userId, $achievement['id']]);
    }
}