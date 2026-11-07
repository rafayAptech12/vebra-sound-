
<?php
require_once __DIR__ . '/includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$songId = max(1, (int)($_GET['id'] ?? 1));

function track_escape($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// --------------------------------------------------
// REVIEW SUBMISSION
// --------------------------------------------------
$reviewNotice = '';

if (
    ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST'
    && ($_POST['action'] ?? '') === 'submit_review'
) {
    if (!$isLoggedIn) {
        header('Location: auth/login.php?ref=' . urlencode('track.php?id=' . $songId));
        exit;
    }

    $userId = (int)$_SESSION['user_id'];
    $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    $reviewText = trim((string)($_POST['review'] ?? ''));

    if ($reviewText !== '') {
        $stmt = mysqli_prepare($conn, "
            INSERT INTO reviews (user_id, song_id, rating, review, status)
             VALUES (?, ?, ?, ?, 'pending')
        ");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iiis", $userId, $songId, $rating, $reviewText);

            if (mysqli_stmt_execute($stmt)) {
                $reviewNotice = 'Thank you! Your rating and review have been published.';
            } else {
                $reviewNotice = 'Your review could not be submitted. Please try again.';
            }

            mysqli_stmt_close($stmt);
        }
    } else {
        $reviewNotice = 'Please write a brief review before submitting.';
    }
}

// --------------------------------------------------
// FETCH SELECTED SONG
// --------------------------------------------------
$stmt = mysqli_prepare($conn, "
    SELECT
        s.*,
        a.name AS artist_name,
        a.image AS artist_image,
        a.bio AS artist_bio,
        g.name AS genre_name
    FROM songs s
    LEFT JOIN artists a ON a.id = s.artist_id
    LEFT JOIN genres g ON g.id = s.genre_id
    WHERE s.id = ? AND s.status = 'approved'
    LIMIT 1
");

$song = null;

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $songId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $song = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);
}

// Fallback only if requested song does not exist
if (!$song) {
    $fallbackRes = mysqli_query($conn, "
        SELECT
            s.*,
            a.name AS artist_name,
            a.image AS artist_image,
            a.bio AS artist_bio,
            g.name AS genre_name
        FROM songs s
        LEFT JOIN artists a ON a.id = s.artist_id
        LEFT JOIN genres g ON g.id = s.genre_id
        WHERE s.status = 'approved'
        ORDER BY s.id DESC
        LIMIT 1
    ");

    if ($fallbackRes && ($row = mysqli_fetch_assoc($fallbackRes))) {
        $song = $row;
        $songId = (int)$song['id'];
    }
}

if (!$song) {
    exit('No approved songs are currently available.');
}

// --------------------------------------------------
// SONG DATA
// --------------------------------------------------
$title = (string)($song['title'] ?? 'Untitled Song');
$artistName = (string)($song['artist_name'] ?? 'VEBRA Artist');
$artistId = (int)($song['artist_id'] ?? 0);
$genre = (string)($song['genre_name'] ?? 'Music');
$language = (string)($song['language'] ?? 'English');
$year = (int)($song['release_year'] ?? 0);
$artwork = !empty($song['artwork'])
    ? (string)$song['artwork']
    : 'assets/images/album-1.jpg';
$audioPath = trim((string)($song['audio_path'] ?? ''));

$pageTitle = $title . ' — VEBRA';
$activePage = 'music';
$activeSub = 'discography';
$bodyClass = 'theme-magenta';

// --------------------------------------------------
// REVIEW STATISTICS
// --------------------------------------------------
$avgRating = 0;
$reviewCount = 0;

$statStmt = mysqli_prepare($conn, "
    SELECT AVG(rating) AS avg_r, COUNT(*) AS cnt
    FROM reviews
    WHERE song_id = ? AND status = 'approved'
");

if ($statStmt) {
    mysqli_stmt_bind_param($statStmt, "i", $songId);
    mysqli_stmt_execute($statStmt);
    $statResult = mysqli_stmt_get_result($statStmt);

    if ($statResult && ($row = mysqli_fetch_assoc($statResult))) {
        $avgRating = round((float)($row['avg_r'] ?? 0), 1);
        $reviewCount = (int)($row['cnt'] ?? 0);
    }

    mysqli_stmt_close($statStmt);
}

// --------------------------------------------------
// FETCH REVIEWS
// --------------------------------------------------
$reviews = [];

$revStmt = mysqli_prepare($conn, "
    SELECT r.*, u.username, u.name AS user_name
    FROM reviews r
    JOIN users u ON u.id = r.user_id
    WHERE r.song_id = ? AND r.status = 'approved'
    ORDER BY r.created_at DESC
");

if ($revStmt) {
    mysqli_stmt_bind_param($revStmt, "i", $songId);
    mysqli_stmt_execute($revStmt);
    $revResult = mysqli_stmt_get_result($revStmt);

    if ($revResult) {
        while ($row = mysqli_fetch_assoc($revResult)) {
            $reviews[] = $row;
        }
    }

    mysqli_stmt_close($revStmt);
}

// --------------------------------------------------
// RELATED SONGS
// --------------------------------------------------
$relatedSongs = [];

$relStmt = mysqli_prepare($conn, "
    SELECT s.*, a.name AS artist_name
    FROM songs s
    LEFT JOIN artists a ON a.id = s.artist_id
    WHERE s.id != ? AND s.status = 'approved'
    ORDER BY s.id DESC
    LIMIT 4
");

if ($relStmt) {
    mysqli_stmt_bind_param($relStmt, "i", $songId);
    mysqli_stmt_execute($relStmt);
    $relResult = mysqli_stmt_get_result($relStmt);

    if ($relResult) {
        while ($row = mysqli_fetch_assoc($relResult)) {
            $relatedSongs[] = $row;
        }
    }

    mysqli_stmt_close($relStmt);
}

require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <!-- TRACK HERO -->
    <section class="section section--below-header section--glow">
        <div class="container-vebra">

            <nav class="breadcrumb-vebra" aria-label="Breadcrumb">
                <a href="index.php">Home</a>
                <span class="breadcrumb-vebra__sep">/</span>
                <a href="all-music.php">Music</a>
                <span class="breadcrumb-vebra__sep">/</span>
                <span aria-current="page"><?= track_escape($title) ?></span>
            </nav>

            <div class="track-hero">

                <div class="track-hero__art" data-reveal="mask">
                    <img
                        src="<?= track_escape($artwork) ?>"
                        alt="<?= track_escape($title) ?> artwork"
                        id="track-cover-img"
                    >
                </div>

                <div class="track-hero__content">

                    <div class="track-meta-badges">
                        <span class="track-badge track-badge--accent">
                            <i class="ri-disc-line"></i>
                            <?= track_escape($genre) ?>
                        </span>

                        <span class="track-badge">
                            <i class="ri-global-line"></i>
                            <?= track_escape($language) ?>
                        </span>

                        <?php if ($year > 0): ?>
                            <span class="track-badge">
                                <i class="ri-calendar-line"></i>
                                <?= $year ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($avgRating > 0): ?>
                            <span class="track-badge" style="color:#ffc107;">
                                <i class="ri-star-fill"></i>
                                <?= $avgRating ?> (<?= $reviewCount ?>)
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1 class="display" data-split="lines">
                        <?= track_escape($title) ?>
                    </h1>

                    <p class="section-head__text" style="font-size:1.25rem;margin-top:.5rem;">
                        By
                        <?php if ($artistId > 0): ?>
                            <a
                                href="single-artist.php?id=<?= $artistId ?>"
                                style="color:var(--accent);font-weight:600;text-decoration:underline;text-underline-offset:4px;"
                            >
                                <?= track_escape($artistName) ?>
                            </a>
                        <?php else: ?>
                            <strong><?= track_escape($artistName) ?></strong>
                        <?php endif; ?>
                    </p>

                    <!-- DEDICATED PLAYER: CONNECTED TO GLOBAL AMPLITUDE PLAYER -->
                    <?php if ($audioPath !== ''): ?>

                        <div
                            class="vebra-track-player"
                            id="vebra-track-player"
                            data-track-id="<?= $songId ?>"
                            data-track-url="<?= track_escape($audioPath) ?>"
                            style="margin-top:2rem;padding:1.5rem;background:var(--glass-bg-strong);border:1px solid var(--glass-brd);border-radius:var(--radius-lg);backdrop-filter:blur(16px);box-shadow:var(--shadow-md);"
                        >

                            <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">

                                <button
                                    type="button"
                                    id="vebra-play-btn"
                                    class="btn-play"
                                    style="width:58px;height:58px;flex-shrink:0;background:var(--accent);color:var(--accent-ink);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;transition:transform .2s ease,box-shadow .2s ease;box-shadow:0 4px 20px rgba(var(--accent-rgb),.4);"
                                    aria-label="Play track"
                                >
                                    <i class="ri-play-fill" id="play-icon"></i>
                                </button>

                                <div style="flex:1;min-width:220px;">

                                    <div style="display:flex;justify-content:space-between;font-size:var(--fs-xs);color:var(--ink-70);margin-bottom:8px;">
                                        <span id="current-time">0:00</span>
                                        <span id="total-duration">--:--</span>
                                    </div>

                                    <input
                                        type="range"
                                        id="seek-bar"
                                        value="0"
                                        min="0"
                                        max="100"
                                        step="0.1"
                                        aria-label="Track progress"
                                        style="width:100%;height:6px;border-radius:3px;accent-color:var(--accent);cursor:pointer;"
                                    >

                                </div>

                                <div style="display:flex;align-items:center;gap:8px;color:var(--ink-70);">
                                    <i class="ri-volume-up-line" style="font-size:1.25rem;"></i>
                                    <input
                                        type="range"
                                        id="volume-bar"
                                        value="70"
                                        min="0"
                                        max="100"
                                        aria-label="Volume"
                                        style="width:80px;height:4px;accent-color:var(--accent);cursor:pointer;"
                                    >
                                </div>

                            </div>

                            <p
                                id="track-player-status"
                                style="font-size:var(--fs-xs);color:var(--ink-60);margin:12px 0 0;"
                                aria-live="polite"
                            >
                                Ready to play
                            </p>

                        </div>

                        <!-- Metadata only. Playback is handled by the global player. -->
                        <audio
                            id="vebra-track-metadata"
                            src="<?= track_escape($audioPath) ?>"
                            preload="metadata"
                            style="display:none;"
                        ></audio>

                    <?php else: ?>

                        <div class="auth-alert auth-alert--danger" style="margin-top:1.5rem;">
                            <i class="ri-information-line"></i>
                            Audio preview is currently unavailable for this track.
                        </div>

                    <?php endif; ?>

                    <div style="display:flex;gap:1rem;margin-top:2rem;flex-wrap:wrap;">
                        <a href="all-music.php" class="btn btn--solid" data-magnetic="0.2">
                            <span class="btn__label">
                                <i class="ri-arrow-left-line"></i> All Releases
                            </span>
                        </a>

                        <a href="#reviews" class="btn" data-magnetic="0.2">
                            <span class="btn__label">
                                <i class="ri-chat-smile-2-line"></i>
                                Reviews (<?= $reviewCount ?>)
                            </span>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- REVIEWS AND DETAILS -->
    <section class="section" id="reviews">
        <div class="container-vebra">
            <div class="content-layout">

                <div class="stack stack-5">

                    <div class="section-head">
                        <div>
                            <span class="section-label">Listener feedback</span>
                            <h2 class="section-head__title" data-split="lines">
                                Ratings &amp; <em>Reviews</em>
                            </h2>
                        </div>

                        <?php if ($avgRating > 0): ?>
                            <div style="text-align:right;">
                                <div class="rating-stars" style="font-size:1.5rem;">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="ri-star-fill<?= $i > round($avgRating) ? ' empty' : '' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p style="font-size:var(--fs-sm);color:var(--ink-70);margin-top:4px;">
                                    <?= $avgRating ?> out of 5 stars (<?= $reviewCount ?> reviews)
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($reviewNotice !== ''): ?>
    <div class="auth-alert auth-alert--success">
        <?= track_escape($reviewNotice) ?>
    </div>
<?php endif; ?>

                    <div style="padding:2rem;background:var(--glass-bg-strong);border:1px solid var(--glass-brd);border-radius:var(--radius-lg);backdrop-filter:blur(16px);">

                        <h3 style="font-family:var(--font-display);font-size:var(--fs-lg);margin-bottom:1rem;color:var(--ink-100);">
                            Leave a Review for &ldquo;<?= track_escape($title) ?>&rdquo;
                        </h3>

                        <?php if ($isLoggedIn): ?>

                            <form method="POST" action="track.php?id=<?= $songId ?>#reviews">
                                <input type="hidden" name="action" value="submit_review">

                                <div style="margin-bottom:1.25rem;">
                                    <label style="display:block;font-size:var(--fs-xs);text-transform:uppercase;letter-spacing:.08em;color:var(--ink-70);margin-bottom:8px;">
                                        Your Star Rating:
                                    </label>

                                    <div class="star-rating-input">
                                        <input type="radio" id="star5" name="rating" value="5" checked>
                                        <label for="star5" title="5 stars"><i class="ri-star-fill"></i></label>

                                        <input type="radio" id="star4" name="rating" value="4">
                                        <label for="star4" title="4 stars"><i class="ri-star-fill"></i></label>

                                        <input type="radio" id="star3" name="rating" value="3">
                                        <label for="star3" title="3 stars"><i class="ri-star-fill"></i></label>

                                        <input type="radio" id="star2" name="rating" value="2">
                                        <label for="star2" title="2 stars"><i class="ri-star-fill"></i></label>

                                        <input type="radio" id="star1" name="rating" value="1">
                                        <label for="star1" title="1 star"><i class="ri-star-fill"></i></label>
                                    </div>
                                </div>

                                <div class="auth-field">
                                    <label for="review-text">Your Comments / Thoughts:</label>
                                    <textarea
                                        id="review-text"
                                        name="review"
                                        rows="4"
                                        placeholder="What makes this track stand out to you?"
                                        required
                                    ></textarea>
                                </div>

                                <button type="submit" class="btn btn--solid" data-magnetic="0.2">
                                    <span class="btn__label">
                                        <i class="ri-send-plane-fill"></i> Submit Review
                                    </span>
                                </button>
                            </form>

                        <?php else: ?>

                            <p style="color:var(--ink-70);margin-bottom:1rem;">
                                You must be logged in to leave a review and rating for this track.
                            </p>

                            <a
                                href="auth/login.php?ref=<?= urlencode('track.php?id=' . $songId) ?>"
                                class="btn btn--solid"
                                data-magnetic="0.2"
                            >
                                <span class="btn__label">
                                    <i class="ri-login-box-line"></i> Login to Review
                                </span>
                            </a>

                        <?php endif; ?>

                    </div>

                    <div class="reviews-list" style="margin-top:2rem;">

                        <?php if (!empty($reviews)): ?>

                            <?php foreach ($reviews as $rev):
                                $rRating = (int)$rev['rating'];
                                $rUser = $rev['user_name'] ?? $rev['username'] ?? 'Listener';
                                $rDate = !empty($rev['created_at'])
                                    ? date('M d, Y', strtotime($rev['created_at']))
                                    : '';
                                $rText = (string)($rev['review'] ?? '');
                            ?>

                                <div style="padding:1.5rem;background:rgba(255,255,255,.03);border:1px solid var(--line);border-radius:var(--radius-md);margin-bottom:1rem;">

                                    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:.5rem;flex-wrap:wrap;">
                                        <div>
                                            <strong style="color:var(--ink-100);font-size:1.05rem;">
                                                <?= track_escape($rUser) ?>
                                            </strong>
                                            <span style="font-size:var(--fs-xs);color:var(--ink-60);margin-left:8px;">
                                                <?= track_escape($rDate) ?>
                                            </span>
                                        </div>

                                        <div class="rating-stars" style="font-size:1rem;">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="ri-star-fill<?= $i > $rRating ? ' empty' : '' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>

                                    <p style="color:var(--ink-80);line-height:1.6;margin:0;">
                                        <?= nl2br(track_escape($rText)) ?>
                                    </p>

                                </div>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <p style="color:var(--ink-60);font-style:italic;">
                                No reviews have been written for this track yet. Be the first to share your thoughts!
                            </p>

                        <?php endif; ?>

                    </div>
                </div>

                <!-- SIDEBAR -->
                <aside class="sidebar" aria-label="Track details">

                    <div class="sidebar__block">
                        <h3 class="sidebar__title">Track Information</h3>

                        <div class="info-list">
                            <div class="info-list__row">
                                <span class="info-list__label">Title</span>
                                <span class="info-list__value"><?= track_escape($title) ?></span>
                            </div>

                            <div class="info-list__row">
                                <span class="info-list__label">Artist</span>
                                <span class="info-list__value"><?= track_escape($artistName) ?></span>
                            </div>

                            <div class="info-list__row">
                                <span class="info-list__label">Genre</span>
                                <span class="info-list__value"><?= track_escape($genre) ?></span>
                            </div>

                            <div class="info-list__row">
                                <span class="info-list__label">Language</span>
                                <span class="info-list__value"><?= track_escape($language) ?></span>
                            </div>

                            <div class="info-list__row">
                                <span class="info-list__label">Year</span>
                                <span class="info-list__value"><?= $year ?: '—' ?></span>
                            </div>

                            <div class="info-list__row">
                                <span class="info-list__label">Format</span>
                                <span class="info-list__value">Digital Audio</span>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($song['artist_bio'])): ?>

                        <div class="sidebar__block">
                            <h3 class="sidebar__title">About the Artist</h3>

                            <p style="color:var(--ink-70);font-size:var(--fs-sm);line-height:1.6;">
                                <?= track_escape($song['artist_bio']) ?>
                            </p>

                            <?php if ($artistId > 0): ?>
                                <a
                                    href="single-artist.php?id=<?= $artistId ?>"
                                    class="link-arrow"
                                    style="margin-top:1rem;"
                                >
                                    View Artist Profile
                                    <i class="ri-arrow-right-up-line"></i>
                                </a>
                            <?php endif; ?>
                        </div>

                    <?php endif; ?>

                    <?php if (!empty($relatedSongs)): ?>

                        <div class="sidebar__block">
                            <h3 class="sidebar__title">More from VEBRA</h3>

                            <div class="mini-list">

                                <?php foreach ($relatedSongs as $rel):
                                    $relId = (int)$rel['id'];
                                    $relTitle = (string)($rel['title'] ?? 'Untitled');
                                    $relArt = !empty($rel['artwork'])
                                        ? $rel['artwork']
                                        : 'assets/images/album-1.jpg';
                                    $relYear = (int)($rel['release_year'] ?? 0);
                                ?>

                                    <a href="track.php?id=<?= $relId ?>" class="mini-item">
                                        <img
                                            src="<?= track_escape($relArt) ?>"
                                            alt="<?= track_escape($relTitle) ?>"
                                        >
                                        <div>
                                            <h4><?= track_escape($relTitle) ?></h4>
                                            <span><?= $relYear ?: 'Music' ?> &middot; Song</span>
                                        </div>
                                    </a>

                                <?php endforeach; ?>

                            </div>
                        </div>

                    <?php endif; ?>

                </aside>

            </div>
        </div>
    </section>

</main>

<?php
// --------------------------------------------------
// DEDICATED PLAYER SYNC
// Uses the same Amplitude instance as the bottom player.
// No second audio playback is started.
// --------------------------------------------------

$extraScripts = <<<'EOD'
<script>
(function () {
    function initVebraTrackPlayer() {
        const player = document.getElementById('vebra-track-player');
        if (!player || player.dataset.initialized === '1') return;

        player.dataset.initialized = '1';

        const trackId = String(player.dataset.trackId || '');
        const trackUrl = String(player.dataset.trackUrl || '');

        const playBtn = document.getElementById('vebra-play-btn');
        const playIcon = document.getElementById('play-icon');
        const seekBar = document.getElementById('seek-bar');
        const volumeBar = document.getElementById('volume-bar');
        const currentTimeEl = document.getElementById('current-time');
        const durationEl = document.getElementById('total-duration');
        const statusEl = document.getElementById('track-player-status');
        const metadataAudio = document.getElementById('vebra-track-metadata');

        if (!playBtn || !seekBar || !currentTimeEl || !durationEl) return;

        let seeking = false;

        function getPlayer() {
            return window.Amplitude || null;
        }

        function formatTime(seconds) {
            seconds = Number(seconds) || 0;
            seconds = Math.max(0, seconds);

            return Math.floor(seconds / 60) + ':' +
                String(Math.floor(seconds % 60)).padStart(2, '0');
        }

        function findTrackIndex(A) {
            if (!A || typeof A.getSongs !== 'function') return -1;

            const songs = A.getSongs() || [];

            let index = songs.findIndex(function (song) {
                return String(song.id) === trackId;
            });

            if (index !== -1) return index;

            return songs.findIndex(function (song) {
                return String(song.url || '').replace(/^\.\//, '') ===
                    trackUrl.replace(/^\.\//, '');
            });
        }

        function isSelectedTrack(A) {
            return A && A.getActiveIndex() === findTrackIndex(A);
        }

        function updatePlayer() {
            const A = getPlayer();

            if (!A) {
                statusEl.textContent = 'Global player is not ready.';
                return;
            }

            const selectedIndex = findTrackIndex(A);
            const activeIndex = A.getActiveIndex();
            const isActive = selectedIndex !== -1 &&
                activeIndex === selectedIndex;

            if (!isActive) {
                playIcon.className = 'ri-play-fill';
                playBtn.setAttribute('aria-label', 'Play track');
                currentTimeEl.textContent = '0:00';
                seekBar.value = 0;

                const metadataDuration = metadataAudio
                    ? metadataAudio.duration
                    : 0;

                durationEl.textContent = metadataDuration > 0
                    ? formatTime(metadataDuration)
                    : '--:--';

                statusEl.textContent = selectedIndex === -1
                    ? 'Track not found in global playlist.'
                    : 'Ready to play';

                return;
            }

            const playing = A.getPlayerState() === 'playing';
            const played = Number(A.getSongPlayedSeconds()) || 0;
            const duration = Number(A.getSongDuration()) || 0;

            playIcon.className = playing
                ? 'ri-pause-fill'
                : 'ri-play-fill';

            playBtn.setAttribute(
                'aria-label',
                playing ? 'Pause track' : 'Play track'
            );

            currentTimeEl.textContent = formatTime(played);

            if (duration > 0) {
                durationEl.textContent = formatTime(duration);

                if (!seeking) {
                    seekBar.value = Math.min(
                        100,
                        (played / duration) * 100
                    );
                }
            }

            statusEl.textContent = playing ? 'Now playing' : 'Paused';
        }

        // Play / Pause using the existing global playlist.
        playBtn.addEventListener('click', function () {
            const A = getPlayer();

            if (!A) {
                statusEl.textContent = 'Global player is not initialized.';
                return;
            }

            const selectedIndex = findTrackIndex(A);

            if (selectedIndex === -1) {
                statusEl.textContent =
                    'This track is not available in the global playlist.';
                return;
            }

            const activeIndex = A.getActiveIndex();
            const playing = A.getPlayerState() === 'playing';

            if (activeIndex === selectedIndex) {
                if (playing) {
                    A.pause();
                } else {
                    A.play();
                }
            } else {
                A.playSongAtIndex(selectedIndex);
            }

            setTimeout(updatePlayer, 100);
        });

        // Seek control.
        seekBar.addEventListener('input', function () {
            seeking = true;

            const A = getPlayer();

            if (!isSelectedTrack(A)) return;

            const duration = Number(A.getSongDuration()) || 0;

            if (duration > 0) {
                const target = (Number(seekBar.value) / 100) * duration;
                currentTimeEl.textContent = formatTime(target);
            }
        });

 
seekBar.addEventListener('change', function () {
    const A = getPlayer();

    if (A) {
        const selectedIndex = findTrackIndex(A);
        const activeIndex = A.getActiveIndex();
        const duration = Number(A.getSongDuration()) || 0;

        if (
            selectedIndex !== -1 &&
            activeIndex === selectedIndex &&
            duration > 0 &&
            typeof A.skipTo === 'function'
        ) {
            const targetTime =
                (Number(seekBar.value) / 100) * duration;

            A.skipTo(targetTime, selectedIndex);
        }
    }

    seeking = false;
    setTimeout(updatePlayer, 150);
});


        // Shared volume control.
        volumeBar.addEventListener('input', function () {
            const A = getPlayer();

            if (A && typeof A.setVolume === 'function') {
                A.setVolume(Number(volumeBar.value));
            }
        });

        // Show duration from metadata without starting a second player.
        if (metadataAudio) {
            metadataAudio.addEventListener('loadedmetadata', function () {
                if (metadataAudio.duration > 0) {
                    durationEl.textContent =
                        formatTime(metadataAudio.duration);
                }
            });
        }

        const A = getPlayer();

        if (A && typeof A.getVolume === 'function') {
            volumeBar.value = A.getVolume();
        }

        updatePlayer();
        window.setInterval(updatePlayer, 300);
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            initVebraTrackPlayer
        );
    } else {
        initVebraTrackPlayer();
    }
})();
</script>
EOD;


require_once __DIR__ . '/includes/footer.php';
?>
