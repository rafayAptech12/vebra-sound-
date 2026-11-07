<?php
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'All Tracks — VEBRA';
$activePage = 'music';
$activeSub = 'all-music';
$songs = [];
$filteredSongs = [];
$errorMessage = '';
$search = trim((string)($_GET['search'] ?? ''));

/* Escape output */
if (!function_exists('vebra_music_escape')) {
    function vebra_music_escape($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

/* Get first available field */
if (!function_exists('vebra_music_value')) {
    function vebra_music_value(array $row, array $fields, string $fallback = ''): string
    {
        foreach ($fields as $field) {
            if (isset($row[$field]) && trim((string)$row[$field]) !== '') {
                return trim((string)$row[$field]);
            }
        }
        return $fallback;
    }
}

/* Detect artist/genre name columns */
if (!function_exists('vebraFindNameColumn')) {
    function vebraFindNameColumn(mysqli $conn, string $table): ?string
    {
        if (!in_array($table, ['artists', 'genres'], true)) {
            return null;
        }

        $result = $conn->query("SHOW COLUMNS FROM `$table`");
        if (!$result) {
            return null;
        }

        $columns = [];
        while ($column = $result->fetch_assoc()) {
            $columns[] = $column['Field'];
        }
        $result->free();

        foreach (['name', 'artist_name', 'genre_name', 'title', 'username'] as $name) {
            if (in_array($name, $columns, true)) {
                return $name;
            }
        }

        return null;
    }
}

/* Database */
if (!isset($conn) || !($conn instanceof mysqli)) {
    http_response_code(500);
    exit('Database connection error. Check includes/db.php.');
}

$artistColumn = vebraFindNameColumn($conn, 'artists');
$genreColumn = vebraFindNameColumn($conn, 'genres');

$artistJoin = '';
$genreJoin = '';
$artistSelect = "'' AS artist_name";
$genreSelect = "'' AS genre_name";

if ($artistColumn !== null) {
    $artistJoin = 'LEFT JOIN artists a ON a.id = s.artist_id';
    $artistSelect = 'a.`' . $artistColumn . '` AS artist_name';
}

if ($genreColumn !== null) {
    $genreJoin = 'LEFT JOIN genres g ON g.id = s.genre_id';
    $genreSelect = 'g.`' . $genreColumn . '` AS genre_name';
}

$sql = "
    SELECT s.*, $artistSelect, $genreSelect
    FROM songs s
    $artistJoin
    $genreJoin
    WHERE s.status = 'approved'
    ORDER BY s.id DESC
";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }
    $result->free();
} else {
    $errorMessage = 'Songs could not be loaded. Please check your database.';
}

/* Search */
foreach ($songs as $song) {
    $title = vebra_music_value($song, ['title', 'song_title', 'track_title'], 'Untitled Song');
    $artist = vebra_music_value($song, ['artist_name'], 'Unknown Artist');
    $genre = vebra_music_value($song, ['genre_name'], 'Music');

    $searchable = strtolower($title . ' ' . $artist . ' ' . $genre);

    if ($search === '' || strpos($searchable, strtolower($search)) !== false) {
        $filteredSongs[] = $song;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<style>

/* ==========================================
   VEBRA — COMPACT ALL MUSIC
========================================== */

.vebra-library,
.vebra-library * {
    box-sizing: border-box;
}

.vebra-library {
    --vm-pink: #ff2e93;
    --vm-bg: #09090d;
    --vm-card: #111117;
    --vm-border: #26232e;
    width: min(1500px, calc(100% - 64px));
    margin: 0 auto;
    padding: 110px 0 65px;
    color: #f7f4fa;
}

/* Compact heading */
.vebra-library-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 25px;
}

.vebra-library-heading {
    margin: 0;
    font-size: clamp(30px, 3.5vw, 43px);
    font-weight: 850;
    letter-spacing: -1.8px;
    line-height: 1.1;
}

.vebra-library-heading span {
    color: var(--vm-pink);
    font-family: Georgia, serif;
    font-weight: 400;
    font-style: italic;
}

.vebra-library-total {
    flex-shrink: 0;
    color: #a8a1ae;
    font-size: 12px;
}

.vebra-library-total strong {
    color: var(--vm-pink);
    font-size: 15px;
}

/* Search */
.vebra-library-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 24px;
}

.vebra-library-search {
    display: flex;
    gap: 9px;
    width: min(100%, 430px);
}

.vebra-library-search input {
    width: 100%;
    min-width: 0;
    height: 43px;
    padding: 0 14px;
    border: 1px solid #39323f;
    border-radius: 9px;
    outline: none;
    background: #15121a;
    color: #fff;
    font: inherit;
    font-size: 13px;
}

.vebra-library-search input:focus {
    border-color: var(--vm-pink);
    box-shadow: 0 0 0 3px #ff2e9318;
}

.vebra-library-search input::placeholder {
    color: #77717f;
}

.vebra-library-search button {
    flex-shrink: 0;
    height: 43px;
    padding: 0 19px;
    border: 0;
    border-radius: 9px;
    background: var(--vm-pink);
    color: #fff;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    transition: .2s ease;
}

.vebra-library-search button:hover {
    background: #ff4fc3;
    transform: translateY(-2px);
}

.vebra-library-reset {
    color: var(--vm-pink);
    font-size: 12px;
    text-decoration: none;
}

/* Compact grid */
.vebra-music-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 19px;
}

/* Card */
.vebra-music-card {
    min-width: 0;
    overflow: hidden;
    border: 1px solid var(--vm-border);
    border-radius: 12px;
    background: var(--vm-card);
    transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
}

.vebra-music-card:hover {
    transform: translateY(-4px);
    border-color: #77345f;
    box-shadow: 0 12px 28px #0005;
}

/* Artwork */
.vebra-music-artwork {
    position: relative;
    display: block;
    width: 100%;
    aspect-ratio: 1 / 1;
    overflow: hidden;
    background: linear-gradient(145deg, #24152b, #09090d);
}

.vebra-music-artwork img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .4s ease;
}

.vebra-music-card:hover .vebra-music-artwork img {
    transform: scale(1.055);
}

.vebra-music-placeholder {
    display: flex;
    width: 100%;
    height: 100%;
    align-items: center;
    justify-content: center;
    color: var(--vm-pink);
    font-size: 55px;
}

.vebra-music-number {
    position: absolute;
    top: 9px;
    left: 9px;
    padding: 5px 8px;
    border: 1px solid #ffffff25;
    border-radius: 6px;
    background: #100d17d9;
    color: #fff;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .6px;
    pointer-events: none;
}

/* Artwork play button */
.vebra-music-artwork-play {
    position: absolute;
    right: 10px;
    bottom: 10px;
    z-index: 2;
    display: flex;
    width: 40px;
    height: 40px;
    align-items: center;
    justify-content: center;
    border: 0;
    border-radius: 50%;
    background: var(--vm-pink);
    color: #fff;
    font-size: 19px;
    cursor: pointer;
    box-shadow: 0 4px 16px #ff2e9360;
    opacity: 0;
    transform: translateY(5px) scale(.9);
    transition: .2s ease;
}

.vebra-music-card:hover .vebra-music-artwork-play,
.vebra-music-artwork-play:focus-visible {
    opacity: 1;
    transform: translateY(0) scale(1);
}

/* Details */
.vebra-music-info {
    padding: 12px 12px 13px;
}

.vebra-music-meta {
    overflow: hidden;
    margin: 0 0 5px;
    color: var(--vm-pink);
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .7px;
    text-overflow: ellipsis;
    text-transform: uppercase;
    white-space: nowrap;
}

.vebra-music-title {
    display: block;
    overflow: hidden;
    margin: 0 0 4px;
    color: #f8f5fa;
    font-size: 14px;
    font-weight: 800;
    line-height: 1.4;
    text-decoration: none;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.vebra-music-title:hover {
    color: var(--vm-pink);
}

.vebra-music-artist {
    overflow: hidden;
    margin: 0;
    color: #aaa4b0;
    font-size: 10px;
    line-height: 1.4;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Actions */
.vebra-music-actions {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-top: 11px;
}
/* TRACK PLAYBACK PROGRESS */
.vebra-track-progress {
    width: 100%;
    height: 3px;
    margin-top: 10px;
    overflow: hidden;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.12);
}

.vebra-track-progress-fill {
    display: block;
    width: 0%;
    height: 100%;
    border-radius: inherit;
    background: var(--vm-pink);
    transition: width 0.15s linear;
}

.vebra-music-button {
    display: inline-flex;
    min-width: 0;
    min-height: 33px;
    align-items: center;
    justify-content: center;
    border: 1px solid #393440;
    border-radius: 30px;
    background: transparent;
    color: #f8f4fa;
    font-family: inherit;
    font-size: 10px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    transition: .2s ease;
}

.vebra-music-button-primary {
    flex: 1;
    gap: 5px;
    border-color: var(--vm-pink);
    background: var(--vm-pink);
    color: #fff;
}

.vebra-music-button-primary:hover {
    border-color: #ff4fc3;
    background: #ff4fc3;
    transform: translateY(-1px);
}

.vebra-music-button-secondary {
    width: 34px;
    flex-shrink: 0;
    font-size: 14px;
}

.vebra-music-button-secondary:hover {
    border-color: var(--vm-pink);
    color: var(--vm-pink);
}

/* Empty state */
.vebra-library-message {
    grid-column: 1 / -1;
    padding: 35px 20px;
    border: 1px solid var(--vm-border);
    border-radius: 12px;
    color: #aaa4b0;
    text-align: center;
}

/* Responsive */
@media (max-width: 1250px) {
    .vebra-music-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }
}

@media (max-width: 950px) {
    .vebra-music-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 650px) {
    .vebra-library {
        width: calc(100% - 28px);
        padding: 90px 0 45px;
    }

    .vebra-library-header {
        margin-bottom: 19px;
    }

    .vebra-library-heading {
        font-size: 34px;
    }

    .vebra-library-toolbar {
        align-items: flex-start;
        flex-direction: column;
    }

    .vebra-library-search {
        width: 100%;
    }

    .vebra-music-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .vebra-music-info {
        padding: 10px;
    }

    .vebra-music-title {
        font-size: 13px;
    }

    .vebra-music-artist {
        font-size: 9px;
    }

    .vebra-music-artwork-play {
        width: 35px;
        height: 35px;
        opacity: 1;
        transform: none;
    }
}

@media (max-width: 350px) {
    .vebra-library {
        width: calc(100% - 20px);
    }

    .vebra-music-grid {
        gap: 8px;
    }

    .vebra-music-info {
        padding: 8px;
    }

    .vebra-music-title {
        font-size: 12px;
    }
}
</style>

<main id="main">
    <section class="vebra-library">

        <header class="vebra-library-header">
            <h1 class="vebra-library-heading">All <span>Tracks</span></h1>

            <div class="vebra-library-total">
                <strong><?= count($filteredSongs) ?></strong>
                <?= count($filteredSongs) === 1 ? 'TRACK' : 'TRACKS' ?>
            </div>
        </header>

        <div class="vebra-library-toolbar">
            <form class="vebra-library-search" action="all-music.php" method="get" role="search">
                <input
                    type="search"
                    name="search"
                    placeholder="Search songs, artists or genres..."
                    value="<?= vebra_music_escape($search) ?>"
                    aria-label="Search songs, artists or genres"
                >
                <button type="submit">SEARCH</button>
            </form>

            <?php if ($search !== ''): ?>
                <a class="vebra-library-reset" href="all-music.php">Clear search ×</a>
            <?php endif; ?>
        </div>

        <div class="vebra-music-grid">

            <?php if ($errorMessage !== ''): ?>

                <div class="vebra-library-message">
                    <?= vebra_music_escape($errorMessage) ?>
                </div>

            <?php elseif (empty($filteredSongs)): ?>

                <div class="vebra-library-message">
                    No tracks found.
                </div>

            <?php else: ?>

                <?php foreach ($filteredSongs as $index => $song): ?>

                    <?php
                    $songId = (int)($song['id'] ?? 0);

                    $songTitle = vebra_music_value(
                        $song,
                        ['title', 'song_title', 'track_title'],
                        'Untitled Song'
                    );

                    $artistName = vebra_music_value(
                        $song,
                        ['artist_name'],
                        'Unknown Artist'
                    );

                    $genreName = vebra_music_value(
                        $song,
                        ['genre_name'],
                        'Music'
                    );

                    $artwork = vebra_music_value(
                        $song,
                        ['artwork', 'cover_art', 'image', 'thumbnail'],
                        ''
                    );

                    $audioPath = vebra_music_value(
                        $song,
                        ['audio_path', 'audio_url', 'file_path', 'music_file'],
                        ''
                    );

                    $releaseYear = vebra_music_value(
                        $song,
                        ['release_year', 'year'],
                        ''
                    );

                    $audioAvailable = false;

                    if ($audioPath !== '') {
                        $scheme = strtolower((string)parse_url($audioPath, PHP_URL_SCHEME));

                        if (
                            filter_var($audioPath, FILTER_VALIDATE_URL) &&
                            in_array($scheme, ['http', 'https'], true)
                        ) {
                            $audioAvailable = true;
                        } else {
                            $localAudioPath = ltrim($audioPath, '/');
                            $audioAvailable = is_file(__DIR__ . '/' . $localAudioPath);
                        }
                    }

                    $trackUrl = 'track.php?id=' . $songId;

                    $coverForPlayer = $artwork !== ''
                        ? $artwork
                        : 'assets/images/album-1.jpg';
                    ?>

                    <article class="vebra-music-card">

                        <div class="vebra-music-artwork">

                            <a
                                href="<?= vebra_music_escape($trackUrl) ?>"
                                aria-label="Open <?= vebra_music_escape($songTitle) ?>"
                                style="display:block;width:100%;height:100%;"
                            >
                                <?php if ($artwork !== ''): ?>
                                    <img
                                        src="<?= vebra_music_escape($artwork) ?>"
                                        alt="<?= vebra_music_escape($songTitle) ?> artwork"
                                        loading="lazy"
                                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                                    >
                                    <span class="vebra-music-placeholder" style="display:none;">♪</span>
                                <?php else: ?>
                                    <span class="vebra-music-placeholder">♪</span>
                                <?php endif; ?>
                            </a>

                            <span class="vebra-music-number">
                                <?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?>
                            </span>

                            <?php if ($audioAvailable): ?>
                                <button
                                    type="button"
                                    class="vebra-music-artwork-play vebra-play-card-btn"
                                    data-url="<?= vebra_music_escape($audioPath) ?>"
                                    data-title="<?= vebra_music_escape($songTitle) ?>"
                                    data-artist="<?= vebra_music_escape($artistName) ?>"
                                    data-cover="<?= vebra_music_escape($coverForPlayer) ?>"
                                    data-id="<?= $songId ?>"
                                    aria-label="Play <?= vebra_music_escape($songTitle) ?>"
                                >
                                    <i class="ri-play-fill"></i>
                                </button>
                            <?php endif; ?>

                        </div>

                        <div class="vebra-music-info">

                            <p class="vebra-music-meta">
                                <?= vebra_music_escape($genreName) ?>
                                <?php if ($releaseYear !== ''): ?>
                                    · <?= vebra_music_escape($releaseYear) ?>
                                <?php endif; ?>
                            </p>

                            <a
                                class="vebra-music-title"
                                href="<?= vebra_music_escape($trackUrl) ?>"
                                title="<?= vebra_music_escape($songTitle) ?>"
                            >
                                <?= vebra_music_escape($songTitle) ?>
                            </a>

                            <p class="vebra-music-artist">
                                <?= vebra_music_escape($artistName) ?>
                            </p>

                            <div class="vebra-music-actions">

                                <?php if ($audioAvailable): ?>
                                    <button
                                        type="button"
                                        class="vebra-music-button vebra-music-button-primary vebra-play-card-btn"
                                        data-url="<?= vebra_music_escape($audioPath) ?>"
                                        data-title="<?= vebra_music_escape($songTitle) ?>"
                                        data-artist="<?= vebra_music_escape($artistName) ?>"
                                        data-cover="<?= vebra_music_escape($coverForPlayer) ?>"
                                        data-id="<?= $songId ?>"
                                        aria-label="Play <?= vebra_music_escape($songTitle) ?>"
                                    >
                                        <i class="ri-play-fill"></i> PLAY
                                    </button>
                                <?php else: ?>
                                    <a
                                        class="vebra-music-button vebra-music-button-primary"
                                        href="<?= vebra_music_escape($trackUrl) ?>"
                                    >
                                        DETAILS
                                    </a>
                                <?php endif; ?>

                                <a
                                    class="vebra-music-button vebra-music-button-secondary"
                                    href="<?= vebra_music_escape($trackUrl) ?>#reviews"
                                    aria-label="Reviews for <?= vebra_music_escape($songTitle) ?>"
                                    title="Reviews"
                                >
                                    <i class="ri-chat-smile-2-line"></i>
                                </a>

                            </div>
                            <div class="vebra-track-progress">
                                 <span class="vebra-track-progress-fill"></span>
                           </div>
                         </div>

                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
