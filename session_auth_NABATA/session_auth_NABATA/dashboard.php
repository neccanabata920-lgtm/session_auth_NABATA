<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$name = $_SESSION['user_name'] ?? 'User';
$userId = (int) $_SESSION['user_id'];
$sessionId = session_id();
$sessionFingerprint = hash('sha256', $sessionId . ($_SERVER['HTTP_USER_AGENT'] ?? ''));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your private dashboard</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 32px 20px; background: #f3f5f9; color: #172033; font: 16px/1.5 system-ui, sans-serif; }
        main { width: min(100%, 900px); margin: 0 auto; padding: 16px 0; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 32px; }
        .brand { color: #172033; font-size: 1.15rem; font-weight: 800; letter-spacing: -.03em; text-decoration: none; }
        .account { display: flex; align-items: center; gap: 14px; padding: 8px 10px 8px 8px; border: 1px solid #e8ebf2; border-radius: 12px; background: #fff; box-shadow: 0 6px 20px #1720330a; }
        .profile { display: flex; align-items: center; gap: 10px; min-width: 0; }
        .avatar { display: grid; width: 40px; height: 40px; flex: 0 0 40px; place-items: center; border-radius: 50%; background: #315ee8; }
        .avatar svg { width: 22px; height: 22px; fill: #fff; }
        .profile-name { max-width: 180px; overflow: hidden; color: #172033; font-weight: 650; text-overflow: ellipsis; white-space: nowrap; }
        .card { padding: clamp(28px, 7vw, 64px); border: 1px solid #e8ebf2; border-radius: 20px; background: #fff; box-shadow: 0 20px 60px #17203312; }
        .info-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; margin-top: 20px; }
        .info-card { padding: 24px; border: 1px solid #e8ebf2; border-radius: 16px; background: #fff; box-shadow: 0 12px 32px #1720330a; }
        .info-card h2 { margin: 0 0 14px; font-size: 1.05rem; }
        .info-card p { margin: 8px 0 0; color: #647084; overflow-wrap: anywhere; }
        .security-card { grid-column: 1 / -1; }
        .security-card code { display: inline-block; margin-top: 10px; padding: 8px 10px; border-radius: 7px; background: #f3f5f9; color: #172033; font: 600 .9rem/1.4 ui-monospace, SFMono-Regular, Consolas, monospace; }
        .detail-label { color: #172033; font-weight: 650; }
        .eyebrow { margin: 0 0 12px; color: #315ee8; font-size: .8rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        h1 { margin: 0; font-size: clamp(2rem, 6vw, 3.25rem); line-height: 1.15; letter-spacing: -.04em; }
        .description { max-width: 470px; margin: 18px 0 30px; color: #647084; font-size: 1.08rem; }
        .explore { display: inline-block; padding: 12px 20px; border-radius: 9px; background: #315ee8; color: #fff; font-weight: 700; text-decoration: none; transition: background .2s, transform .2s; }
        .explore:hover { transform: translateY(-2px); background: #254bc3; }
        .sign-out { padding: 10px 16px; border: 1px solid #d5dbe5; border-radius: 8px; background: #fff; color: #172033; font: inherit; font-weight: 600; cursor: pointer; }
        .sign-out:hover { background: #f8f9fc; }
        @media (max-width: 520px) {
            body { padding: 20px 16px; }
            .topbar { align-items: flex-start; flex-direction: column; }
            .account { max-width: 100%; }
            .profile-name { max-width: min(38vw, 180px); }
            .card { border-radius: 14px; }
            .info-grid { grid-template-columns: 1fr; }
            .security-card { grid-column: auto; }
        }
    </style>
</head>
<body>
<main>
    <div class="topbar">
        <a class="brand" href="dashboard.php">SessionAuthSystem Dashboard</a>
        <div class="account">
            <div class="profile">
                <span class="avatar" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <path d="M12 12a4.25 4.25 0 1 0 0-8.5 4.25 4.25 0 0 0 0 8.5Zm0 2c-4.1 0-7.5 2.15-7.5 4.75V21h15v-2.25C19.5 16.15 16.1 14 12 14Z"/>
                    </svg>
                </span>
                <span class="profile-name"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <form method="post" action="logout.php">
                <button class="sign-out" type="submit">Sign out</button>
            </form>
        </div>
    </div>
    <section class="card" aria-labelledby="welcome-heading">
        <p class="eyebrow">Your private space</p>
        <h1 id="welcome-heading">Welcome, <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>!</h1>
        <p class="description">This page is protected and only visible to logged-in users.</p>
        <a class="explore" href="#welcome-heading">Explore Dashboard</a>
    </section>
    <div class="info-grid">
        <section class="info-card security-card" aria-labelledby="session-security-heading">
            <h2 id="session-security-heading">Your session is active and protected against Session Fixation</h2>
            <p>Session regeneration is performed at login with:</p>
            <code>session_regenerate_id(true);</code>
        </section>
        <section class="info-card" aria-labelledby="user-details-heading">
            <h2 id="user-details-heading">User details</h2>
            <p><span class="detail-label">User ID:</span> <?= htmlspecialchars((string) $userId, ENT_QUOTES, 'UTF-8') ?></p>
            <p><span class="detail-label">Username:</span> <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></p>
        </section>
        <section class="info-card" aria-labelledby="active-session-heading">
            <h2 id="active-session-heading">Active Session ID</h2>
            <p><span class="detail-label">Session ID fingerprint:</span> <?= htmlspecialchars($sessionFingerprint, ENT_QUOTES, 'UTF-8') ?></p>
        </section>
    </div>
</main>
</body>
</html>
