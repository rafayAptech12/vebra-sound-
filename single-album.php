<?php
require_once __DIR__ . '/includes/db.php';

/*
|--------------------------------------------------------------------------
| VEBRA — DATABASE-DRIVEN SINGLE ALBUM
|--------------------------------------------------------------------------
| Album details come from albums.
| Tracks come from songs using album_id.
| Playback uses the existing global VEBRA bottom player.
|--------------------------------------------------------------------------
*/

function vebraSingleEscape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$albumKey = trim($_GET['album'] ?? 'nightform');

if ($albumKey === '') {
    $albumKey = 'nightform';
}

/*
|--------------------------------------------------------------------------
| Load selected album
|--------------------------------------------------------------------------
*/

$albumStmt = $conn->prepare("
    SELECT
        id,
        name,
        slug,
        description,
        cover,
        release_year,
        genre
    FROM albums
    WHERE slug = ?
    LIMIT 1
");

if (!$albumStmt) {
    die('Unable to load album information.');
}

$albumStmt->bind_param('s', $albumKey);
$albumStmt->execute();

$albumResult = $albumStmt->get_result();
$album = $albumResult->fetch_assoc();

$albumStmt->close();

if (!$album) {
    header('Location: albums.php');
    exit;
}

$albumId = (int)$album['id'];

/*
|--------------------------------------------------------------------------
| Load ONLY songs assigned to this album
|--------------------------------------------------------------------------
*/

$tracks = [];

$trackStmt = $conn->prepare("
    SELECT
        s.id,
        s.title,
        s.audio_path,
        s.artwork,
        s.release_year,
        ar.name AS artist_name
    FROM songs s
    LEFT JOIN artists ar
        ON ar.id = s.artist_id
    WHERE s.album_id = ?
      AND s.status = 'approved'
    ORDER BY s.id DESC
");

if (!$trackStmt) {
    die('Unable to load album tracks.');
}

$trackStmt->bind_param('i', $albumId);
$trackStmt->execute();

$trackResult = $trackStmt->get_result();

while ($row = $trackResult->fetch_assoc()) {
    $tracks[] = $row;
}

$trackStmt->close();

/*
|--------------------------------------------------------------------------
| Album presentation data
|--------------------------------------------------------------------------
*/

$albumTitle = $album['name'];
$albumCover = !empty($album['cover'])
    ? $album['cover']
    : 'assets/images/placeholder.svg';

$albumYear = !empty($album['release_year'])
    ? $album['release_year']
    : 'VEBRA';

$albumGenre = !empty($album['genre'])
    ? $album['genre']
    : 'Various genres';

$albumDescription = !empty($album['description'])
    ? $album['description']
    : 'Explore the music and tracks from this VEBRA release.';

$totalTracks = count($tracks);

$artists = [];

foreach ($tracks as $track) {
    $artistName = trim($track['artist_name'] ?? '');

    if ($artistName !== '' && !in_array($artistName, $artists, true)) {
        $artists[] = $artistName;
    }
}

$pageTitle = $albumTitle . ' — Album — VEBRA';
$activePage = 'music';
$activeSub = 'single-album';
$bodyClass = 'theme-magenta';

/*
|--------------------------------------------------------------------------
| Load other albums for sidebar
|--------------------------------------------------------------------------
*/

$otherAlbums = [];

$otherStmt = $conn->prepare("
    SELECT id, name, slug, cover, release_year
    FROM albums
    WHERE id != ?
    ORDER BY id ASC
    LIMIT 3
");

if ($otherStmt) {
    $otherStmt->bind_param('i', $albumId);
    $otherStmt->execute();

    $otherResult = $otherStmt->get_result();

    while ($row = $otherResult->fetch_assoc()) {
        $otherAlbums[] = $row;
    }

    $otherStmt->close();
}

require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <section class="section section--below-header section--glow">

        <div class="container-vebra">

            <div class="stack stack-7">

                <!-- BREADCRUMB + ALBUM HERO -->
                <div class="stack stack-6">

                    <nav class="breadcrumb-vebra" aria-label="Breadcrumb" data-reveal="up">

                        <a href="index.php">Home</a>

                        <span class="breadcrumb-vebra__sep">/</span>

                        <a href="discography.php">Discography</a>

                        <span class="breadcrumb-vebra__sep">/</span>

                        <a href="albums.php">Albums</a>

                        <span class="breadcrumb-vebra__sep">/</span>

                        <span aria-current="page">
                            <?php echo vebraSingleEscape($albumTitle); ?>
                        </span>

                    </nav>


                    <div class="album-hero">

                        <!-- COVER -->
                        <div class="album-hero__cover"
                             data-reveal="mask"
                             data-tilt="8"
                             data-cursor="Listen">

                            <div class="album-hero__vinyl" aria-hidden="true"></div>

                            <img
                                src="<?php echo vebraSingleEscape($albumCover); ?>"
                                alt="<?php echo vebraSingleEscape($albumTitle); ?> album artwork"
                            >

                        </div>


                        <!-- DETAILS -->
                        <div class="album-hero__body">

                            <span class="section-label">
                                Album &middot;
                                <?php echo vebraSingleEscape($albumYear); ?>
                            </span>

                            <h1 class="album-hero__title"
                                data-split="chars"
                                data-split-hero>
                                <?php echo vebraSingleEscape($albumTitle); ?>
                            </h1>

                            <p class="section-head__text">
                                <?php echo vebraSingleEscape($albumDescription); ?>
                            </p>


                            <div class="album-hero__platforms">

                                <span class="pill pill--solid">
                                    <i class="ri-album-fill"></i>
                                    <?php echo $totalTracks; ?> Tracks
                                </span>

                                <span class="pill">
                                    <i class="ri-calendar-line"></i>
                                    <?php echo vebraSingleEscape($albumYear); ?>
                                </span>

                                <span class="pill">
                                    <i class="ri-music-2-line"></i>
                                    <?php echo vebraSingleEscape($albumGenre); ?>
                                </span>

                            </div>


                            <div style="display:flex;gap:1rem;align-items:center;margin-top:2rem;flex-wrap:wrap;">

                                <a href="#album-tracklist"
                                   class="btn btn--solid btn--lg"
                                   data-magnetic="0.3">

                                    <span class="btn__label">
                                        <i class="ri-music-fill"></i>
                                        Explore tracks
                                    </span>

                                </a>

                                <a href="albums.php"
                                   class="btn btn--lg"
                                   data-magnetic="0.3">

                                    <span class="btn__label">
                                        <i class="ri-arrow-left-line"></i>
                                        All albums
                                    </span>

                                </a>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- CONTENT + SIDEBAR -->
                <div class="content-layout">

                    <div class="stack stack-4" data-reveal="up">

                        <h2 class="section-head__title" data-split="lines">
                            About <em><?php echo vebraSingleEscape($albumTitle); ?></em>
                        </h2>

                        <p class="section-head__text">
                            <?php echo vebraSingleEscape($albumDescription); ?>
                        </p>

                        <p class="section-head__text">
                            This release contains <?php echo $totalTracks; ?>
                            approved tracks in the VEBRA music collection.
                            Select any track below to start listening.
                        </p>

                        <?php if (!empty($artists)): ?>

                            <div class="stack stack-2">

                                <span class="section-label">Featured artists</span>

                                <p class="section-head__text">
                                    <?php
                                    echo vebraSingleEscape(
                                        implode(' · ', $artists)
                                    );
                                    ?>
                                </p>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- SIDEBAR -->
                    <aside class="sidebar" aria-label="Album information">

                        <div class="sidebar__block">

                            <h3 class="sidebar__title">Album info</h3>

                            <div class="info-list">

                                <div class="info-list__row">
                                    <span class="info-list__label">Released</span>
                                    <span class="info-list__value">
                                        <?php echo vebraSingleEscape($albumYear); ?>
                                    </span>
                                </div>

                                <div class="info-list__row">
                                    <span class="info-list__label">Genre</span>
                                    <span class="info-list__value">
                                        <?php echo vebraSingleEscape($albumGenre); ?>
                                    </span>
                                </div>

                                <div class="info-list__row">
                                    <span class="info-list__label">Tracks</span>
                                    <span class="info-list__value">
                                        <?php echo $totalTracks; ?>
                                    </span>
                                </div>

                                <div class="info-list__row">
                                    <span class="info-list__label">Artists</span>
                                    <span class="info-list__value">
                                        <?php echo count($artists); ?>
                                    </span>
                                </div>

                            </div>

                        </div>


                        <!-- RELATED ALBUMS -->
                        <div class="sidebar__block">

                            <h3 class="sidebar__title">More from VEBRA</h3>

                            <div class="mini-list">

                                <?php if (!empty($otherAlbums)): ?>

                                    <?php foreach ($otherAlbums as $other): ?>

                                        <a
                                            href="single-album.php?album=<?php echo urlencode($other['slug']); ?>"
                                            class="mini-item"
                                        >

                                            <img
                                                src="<?php echo vebraSingleEscape(
                                                    $other['cover'] ?: 'assets/images/placeholder.svg'
                                                ); ?>"
                                                alt="<?php echo vebraSingleEscape($other['name']); ?>"
                                                loading="lazy"
                                            >

                                            <div>

                                                <h4>
                                                    <?php echo vebraSingleEscape($other['name']); ?>
                                                </h4>

                                                <span>
                                                    <?php echo vebraSingleEscape($other['release_year']); ?>
                                                    &middot; Album
                                                </span>

                                            </div>

                                        </a>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <p class="section-head__text">
                                        No other albums available.
                                    </p>

                                <?php endif; ?>

                            </div>

                        </div>

                    </aside>

                </div>

            </div>

        </div>

    </section>


    <!-- ALBUM TRACKLIST -->
    <section class="section player-section" id="album-tracklist">

        <div class="player-section__bg" aria-hidden="true">

            <img
                src="<?php echo vebraSingleEscape($albumCover); ?>"
                alt=""
            >

        </div>


        <div class="container-vebra">

            <div class="section-head">

                <div>

                    <span class="section-label">VEBRA collection</span>

                    <h2 class="section-head__title" data-split="lines">
                        Listen to <em><?php echo vebraSingleEscape($albumTitle); ?></em>
                    </h2>

                </div>

                <span class="link-arrow">
                    <?php echo $totalTracks; ?> tracks
                    <i class="ri-music-2-line"></i>
                </span>

            </div>


            <!-- TRACKLIST -->
            <div class="stack stack-3" data-reveal="up">

                <?php if (!empty($tracks)): ?>

                    <?php foreach ($tracks as $index => $track): ?>

                        <?php
                        $trackId = (int)$track['id'];

                        $trackTitle = $track['title'];

                        $trackArtist = !empty($track['artist_name'])
                            ? $track['artist_name']
                            : 'Unknown artist';

                        $trackAudio = trim($track['audio_path'] ?? '');

                        $trackCover = !empty($track['artwork'])
                            ? $track['artwork']
                            : $albumCover;

                        $playable = ($trackAudio !== '');
                        ?>

                        <div
                            class="list-song vebra-music-card <?php echo $playable ? 'vebra-play-card-btn' : ''; ?>"
                            <?php if ($playable): ?>
                                data-id="<?php echo $trackId; ?>"
                                data-title="<?php echo vebraSingleEscape($trackTitle); ?>"
                                data-artist="<?php echo vebraSingleEscape($trackArtist); ?>"
                                data-album="<?php echo vebraSingleEscape($albumTitle); ?>"
                                data-url="<?php echo vebraSingleEscape($trackAudio); ?>"
                                data-cover="<?php echo vebraSingleEscape($trackCover); ?>"
                                data-play-song
                                role="button"
                                tabindex="0"
                                aria-label="Play <?php echo vebraSingleEscape($trackTitle); ?>"
                            <?php endif; ?>
                            style="cursor:<?php echo $playable ? 'pointer' : 'default'; ?>;display:flex;align-items:center;justify-content:space-between;position:relative;"
                        >

                            <div style="display:flex;align-items:center;gap:12px;min-width:0;">

                                <span class="list-song__index">
                                    <?php
                                    echo str_pad(
                                        (string)($index + 1),
                                        2,
                                        '0',
                                        STR_PAD_LEFT
                                    );
                                    ?>
                                </span>

                                <span class="list-song__eq" aria-hidden="true">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </span>

                                <span class="list-song__title">

                                    <?php echo vebraSingleEscape($trackTitle); ?>

                                    <span class="list-song__artist"
                                          style="display:block;font-size:0.8rem;opacity:0.7;">

                                        <?php echo vebraSingleEscape($trackArtist); ?>
                                        &bull;
                                        <?php echo vebraSingleEscape($albumTitle); ?>

                                    </span>

                                </span>

                            </div>


                            <div style="display:flex;align-items:center;gap:12px;">

                                <?php if ($playable): ?>

                                    <span class="btn-play btn-play--xs btn-play--glass"
                                          aria-hidden="true">
                                        <i class="ri-play-fill"></i>
                                    </span>

                                <?php else: ?>

                                    <span class="list-song__duration">
                                        Unavailable
                                    </span>

                                <?php endif; ?>

                            </div>


                            <div class="vebra-track-progress"
                                 style="position:absolute;bottom:0;left:0;right:0;height:2px;">

                                <span class="vebra-track-progress-fill"></span>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="section-head">

                        <h3 class="section-head__title">
                            No tracks available
                        </h3>

                        <p class="section-head__text">
                            This album currently has no approved songs assigned to it.
                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>

</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
