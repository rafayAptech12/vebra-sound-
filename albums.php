<?php
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Albums — VEBRA';
$activePage = 'music';
$activeSub = 'albums';
$bodyClass = 'theme-magenta';

/*
|--------------------------------------------------------------------------
| Load albums and their real approved songs
|--------------------------------------------------------------------------
*/

$albums = [];
$tracksByAlbum = [];

$albumSql = "
    SELECT
        a.id,
        a.name,
        a.slug,
        a.description,
        a.cover,
        a.release_year,
        a.genre,
        COUNT(s.id) AS total_tracks
    FROM albums a
    LEFT JOIN songs s
        ON s.album_id = a.id
        AND s.status = 'approved'
    GROUP BY
        a.id,
        a.name,
        a.slug,
        a.description,
        a.cover,
        a.release_year,
        a.genre
    ORDER BY a.id ASC
";

$albumResult = $conn->query($albumSql);

if ($albumResult) {
    while ($album = $albumResult->fetch_assoc()) {
        $albums[] = $album;
        $tracksByAlbum[(int)$album['id']] = [];
    }
}

/*
|--------------------------------------------------------------------------
| Load actual track information
|--------------------------------------------------------------------------
*/

$trackSql = "
    SELECT
        s.id,
        s.album_id,
        s.title,
        s.audio_path,
        s.artwork,
        ar.name AS artist_name
    FROM songs s
    LEFT JOIN artists ar
        ON ar.id = s.artist_id
    WHERE s.status = 'approved'
      AND s.album_id IS NOT NULL
    ORDER BY s.id DESC
";

$trackResult = $conn->query($trackSql);

if ($trackResult) {
    while ($track = $trackResult->fetch_assoc()) {
        $albumId = (int)$track['album_id'];

        if (isset($tracksByAlbum[$albumId])) {
            $tracksByAlbum[$albumId][] = $track;
        }
    }
}

function albumEscape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <!-- PAGE CAPTION -->
    <section class="page-caption">
        <div class="container-vebra">
            <div class="page-caption__inner">

                <nav class="breadcrumb-vebra" aria-label="Breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="breadcrumb-vebra__sep">/</span>
                    <a href="discography.php">Music</a>
                    <span class="breadcrumb-vebra__sep">/</span>
                    <span aria-current="page">Albums</span>
                </nav>

                <h1 class="page-caption__title"
                    data-split="chars"
                    data-split-hero>
                    Albums
                    <span class="page-caption__ghost" aria-hidden="true">LPs</span>
                </h1>

                <p class="page-caption__text"
                   data-reveal="up"
                   data-delay="0.25">
                    Explore the VEBRA collection — complete records,
                    carefully selected tracks and sounds worth returning to.
                </p>

            </div>
        </div>
    </section>


    <!-- ALBUMS GRID -->
    <section class="section discography-page">
        <div class="container-vebra">

            <div class="filter-grid filter-grid--rows">

                <?php if (!empty($albums)): ?>

                    <?php foreach ($albums as $index => $album): ?>

                        <?php
                        $albumId = (int)$album['id'];
                        $slug = $album['slug'];
                        $albumTracks = $tracksByAlbum[$albumId] ?? [];

                        $cover = !empty($album['cover'])
                            ? $album['cover']
                            : 'assets/images/placeholder.svg';

                        $year = !empty($album['release_year'])
                            ? $album['release_year']
                            : 'VEBRA';

                        $firstTrack = $albumTracks[0] ?? null;

                        $albumNumber = str_pad(
                            (string)($index + 1),
                            2,
                            '0',
                            STR_PAD_LEFT
                        );
                        ?>

                        <article class="album-feature">

                            <!-- ALBUM ARTWORK -->
                            <a href="single-album.php?album=<?php echo urlencode($slug); ?>"
                               class="album-feature__media"
                               data-reveal="mask"
                               data-cursor="Listen">

                                <span class="album-feature__index">
                                    <?php echo $albumNumber; ?>
                                </span>

                                <img
                                    src="<?php echo albumEscape($cover); ?>"
                                    alt="<?php echo albumEscape($album['name']); ?> album artwork"
                                    loading="lazy"
                                >

                            </a>


                            <!-- ALBUM INFORMATION -->
                            <div class="album-feature__body">

                                <span class="section-label">
                                    <?php echo albumEscape($year); ?>
                                    &middot;
                                    LP
                                    &middot;
                                    <?php echo (int)$album['total_tracks']; ?>
                                    tracks
                                </span>

                                <h3 class="album-feature__title"
                                    data-split="lines">

                                    <a href="single-album.php?album=<?php echo urlencode($slug); ?>">
                                        <?php echo albumEscape($album['name']); ?>
                                    </a>

                                </h3>


                                <p class="section-head__text">
                                    <?php
                                    echo !empty($album['description'])
                                        ? albumEscape($album['description'])
                                        : 'Discover the sounds and tracks in this VEBRA album.';
                                    ?>
                                </p>


                                <!-- REAL DATABASE TRACKS -->
                                <div class="album-feature__tracks">

                                    <?php if (!empty($albumTracks)): ?>

                                        <?php
                                        $previewTracks = array_slice(
                                            $albumTracks,
                                            0,
                                            5
                                        );
                                        ?>

                                        <?php foreach ($previewTracks as $trackIndex => $track): ?>

                                            <div class="album-feature__track">

                                                <a
                                                    href="track.php?id=<?php echo (int)$track['id']; ?>"
                                                    style="display:flex;justify-content:space-between;width:100%;color:inherit;"
                                                >
                                                    <span>
                                                        <?php echo str_pad((string)($trackIndex + 1), 2, '0', STR_PAD_LEFT); ?>
                                                        <?php echo albumEscape($track['title']); ?>
                                                    </span>

                                                    <span>
                                                        <?php echo albumEscape($track['artist_name'] ?: 'Unknown artist'); ?>
                                                    </span>
                                                </a>

                                            </div>

                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <div class="album-feature__track">
                                            No approved tracks available yet.
                                        </div>

                                    <?php endif; ?>

                                </div>


                                <!-- ALBUM ACTIONS -->
                                <div style="display:flex;gap:1rem;align-items:center;margin-top:1.5rem;flex-wrap:wrap;">

                                    <a
                                        href="single-album.php?album=<?php echo urlencode($slug); ?>"
                                        class="btn btn--solid"
                                        data-magnetic="0.3"
                                    >
                                        <span class="btn__label">
                                            Open album
                                            <i class="ri-arrow-right-up-line"></i>
                                        </span>
                                    </a>


                                    <?php if (
                                        $firstTrack &&
                                        !empty($firstTrack['audio_path'])
                                    ): ?>

                                        <button
                                            type="button"
                                            class="btn vebra-play-card-btn"
                                            data-magnetic="0.3"
                                            data-url="<?php echo albumEscape($firstTrack['audio_path']); ?>"
                                            data-title="<?php echo albumEscape($firstTrack['title']); ?>"
                                            data-artist="<?php echo albumEscape($firstTrack['artist_name'] ?: 'Unknown artist'); ?>"
                                            data-cover="<?php echo albumEscape($firstTrack['artwork'] ?: $cover); ?>"
                                            data-id="<?php echo (int)$firstTrack['id']; ?>"
                                        >
                                            <span class="btn__label">
                                                <i class="ri-play-fill"></i>
                                                Play track 1
                                            </span>
                                        </button>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="section-head">
                        <h2>No albums available</h2>
                        <p class="section-head__text">
                            Albums will appear here when they are added to VEBRA.
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
