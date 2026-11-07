<?php
require_once __DIR__ . '/includes/db.php';

/*
|--------------------------------------------------------------------------
| VEBRA homepage
|--------------------------------------------------------------------------
| Uses the existing MySQLi connection, $conn.
| Keep the current includes/header.php, includes/footer.php and main.js.
*/

$loadAmplitude = true;
$pageTitle = 'VEBRA — Music, Artists & Releases';
$activePage = 'home';
$activeSub = '';

$songs = [];
$videos = [];
$artists = [];
$playerSongs = [];
$reviews = [];

if (!function_exists('vebra_escape')) {
    function vebra_escape($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('vebra_field')) {
    function vebra_field(array $row, array $keys, string $fallback = ''): string
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && trim((string)$row[$key]) !== '') {
                return trim((string)$row[$key]);
            }
        }
        return $fallback;
    }
}

if (!function_exists('vebra_image_path')) {
    function vebra_image_path(string $path, string $fallback): string
    {
        return trim($path) !== '' ? trim($path) : $fallback;
    }
}

/* Latest five approved songs */
$result = mysqli_query($conn, "
    SELECT s.*, a.name AS artist_name, g.name AS genre_name
    FROM songs s
    LEFT JOIN artists a ON a.id = s.artist_id
    LEFT JOIN genres g ON g.id = s.genre_id
    WHERE s.status = 'approved'
    ORDER BY s.created_at DESC, s.id DESC
    LIMIT 5
");

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $songs[] = $row;
    }
}

/* Three approved songs for the listening-room player */
$result = mysqli_query($conn, "
    SELECT s.*, a.name AS artist_name, g.name AS genre_name
    FROM songs s
    LEFT JOIN artists a ON a.id = s.artist_id
    LEFT JOIN genres g ON g.id = s.genre_id
    WHERE s.status = 'approved' AND s.id IN (1, 2, 4)
    ORDER BY FIELD(s.id, 1, 2, 4)
");
if (!$result || mysqli_num_rows($result) < 3) {
    $result = mysqli_query($conn, "
        SELECT s.*, a.name AS artist_name, g.name AS genre_name
        FROM songs s
        LEFT JOIN artists a ON a.id = s.artist_id
        LEFT JOIN genres g ON g.id = s.genre_id
        WHERE s.status = 'approved'
        ORDER BY s.id ASC
        LIMIT 3
    ");
}

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $playerSongs[] = $row;
    }
}

/* Latest five approved videos */
$result = mysqli_query($conn, "
    SELECT v.*, a.name AS artist_name, g.name AS genre_name
    FROM videos v
    LEFT JOIN artists a ON a.id = v.artist_id
    LEFT JOIN genres g ON g.id = v.genre_id
    WHERE v.status = 'approved'
    ORDER BY v.created_at DESC, v.id DESC
    LIMIT 5
");

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $videos[] = $row;
    }
}

/* Artists with approved release counts */
$result = mysqli_query($conn, "
    SELECT a.*, COUNT(s.id) AS release_count
    FROM artists a
    LEFT JOIN songs s
        ON s.artist_id = a.id AND s.status = 'approved'
    GROUP BY a.id
    ORDER BY release_count DESC, a.id DESC
    LIMIT 6
");

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $artists[] = $row;
    }
}

/* Real listener reviews, when available */
$result = mysqli_query($conn, "
    SELECT r.*, s.title AS song_title, u.username, u.name AS user_name
    FROM reviews r
    JOIN songs s ON s.id = r.song_id
    JOIN users u ON u.id = r.user_id
    WHERE r.review IS NOT NULL AND TRIM(r.review) <> '' AND r.status = 'approved'
    ORDER BY r.created_at DESC, r.id DESC
    LIMIT 5
");

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reviews[] = [
            'quote' => $row['review'],
            'author' => !empty($row['user_name'])
                ? $row['user_name']
                : ($row['username'] ?? 'Listener'),
            'sub' => 'Listener review — ' . ($row['song_title'] ?? ''),
            'rating' => max(1, min(5, (int)($row['rating'] ?? 5)))
        ];
    }
}

/* Latest five approved listener reviews */
$result = mysqli_query($conn, "
    SELECT
        r.id,
        r.rating,
        r.review,
        r.created_at,
        s.title AS song_title,
        a.name AS artist_name,
        u.username,
        u.name AS user_name
    FROM reviews r
    INNER JOIN songs s ON s.id = r.song_id
    LEFT JOIN artists a ON a.id = s.artist_id
    INNER JOIN users u ON u.id = r.user_id
    WHERE r.status = 'approved'
      AND s.status = 'approved'
      AND r.review IS NOT NULL
      AND TRIM(r.review) <> ''
    ORDER BY r.created_at DESC, r.id DESC
    LIMIT 5
");

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reviews[] = [
            'quote' => $row['review'],
            'author' => !empty($row['user_name'])
                ? $row['user_name']
                : ($row['username'] ?? 'Listener'),
            'sub' => 'Review for ' . $row['song_title']
                . ' — ' . ($row['artist_name'] ?? 'VEBRA Artist'),
            'rating' => max(1, min(5, (int)$row['rating']))
        ];
    }
}
require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <!-- HERO -->
    <section class="hero hero-6">
        <div class="hero__bg" aria-hidden="true">
            <img
                id="vebra-hero-slide"
                src="assets/images/hero-slide-1.jpg"
                alt="VEBRA live music stage"
            >
        </div>

        <div class="hero__inner">
            <div class="hero-6__grid">
                <div>
                    <p class="hero__label" data-reveal="up">
                        VEBRA — music without limits
                    </p>

                    <h1 class="hero__title hero-6__title"
                        data-split="lines" data-split-hero>
                        Louder than <em>memory</em>
                    </h1>
                </div>

                <div class="hero-6__side" data-reveal="up" data-delay="0.35">
                    <p>
                        Discover new sounds, explore artists and find
                        the next track that stays with you.
                    </p>

                    <div class="hero__actions mt-0">
                        <a href="#featured-player" class="btn btn--solid"
                           data-magnetic="0.3">
                            <span class="btn__label">
                                Listen now <i class="ri-play-fill"></i>
                            </span>
                        </a>

                        <a href="events.php" class="btn" data-magnetic="0.3">
                            <span class="btn__label">Explore events</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero__rail">
            <span class="scroll-cue">Scroll to discover</span>
            <div class="hero__rail-socials">
                <a href="https://instagram.com/" target="_blank" rel="noopener">Instagram</a>
                <a href="https://youtube.com/" target="_blank" rel="noopener">YouTube</a>
                <a href="https://spotify.com/" target="_blank" rel="noopener">Spotify</a>
            </div>
        </div>
    </section>

    <!-- LISTENING ROOM -->
    <?php if ($playerSongs): ?>
    <section class="section vebra-player-section" id="featured-player">
        <div class="container-vebra">

            <div class="section-head" data-reveal="up">
                <div>
                    <span class="section-label">The listening room</span>
                    <h2 class="section-head__title" data-split="lines">
                        Press play. <em>Feel more.</em>
                    </h2>
                </div>
                <a href="discography.php" class="link-arrow">
                    All releases <i class="ri-arrow-right-up-line"></i>
                </a>
            </div>

            <div class="vebra-player-panel" data-reveal="up">
                <a href="song-detail.php?id=<?= (int)$playerSongs[0]['id'] ?>" class="vebra-player-art" id="vebra-player-art-link">
                    <img
                        id="vebra-player-cover"
                        src="<?= vebra_escape(vebra_image_path(
                            vebra_field($playerSongs[0], ['artwork', 'cover_image', 'cover']),
                            'assets/images/album-1.jpg'
                        )) ?>"
                        alt="Featured track artwork"
                    >
                    <span class="vebra-player-status">FEATURED TRACK</span>
                </a>

                <div class="vebra-player-content">
                    <div class="vebra-player-now">
                        <span class="section-label">Selected release</span>
                        <h3 id="vebra-player-title">
                            <a id="vebra-player-title-link" href="song-detail.php?id=<?= (int)$playerSongs[0]['id'] ?>" style="color: inherit; text-decoration: none;">
                                <?= vebra_escape($playerSongs[0]['title'] ?? 'Untitled') ?>
                            </a>
                        </h3>
                        <p id="vebra-player-artist">
                            <?= vebra_escape($playerSongs[0]['artist_name'] ?? 'Unknown artist') ?>
                        </p>
                    </div>

                    <div class="music-player vebra-player-list">
                        <?php foreach ($playerSongs as $index => $song):
                            $audio = vebra_field(
                                $song,
                                ['audio_path', 'file_path', 'audio', 'file']
                            );

                            $cover = vebra_image_path(
                                vebra_field($song, ['artwork', 'cover_image', 'cover']),
                                'assets/images/album-1.jpg'
                            );

                            $title = (string)($song['title'] ?? 'Untitled');
                            $artist = (string)($song['artist_name'] ?? 'Unknown artist');
                        ?>
                        <button
                            type="button"
                            class="list-song vebra-player-song vebra-music-card<?= $index === 0 ? ' is-active' : '' ?>"
                            data-title="<?= vebra_escape($title) ?>"
                            data-artist="<?= vebra_escape($artist) ?>"
                            data-album="<?= vebra_escape($song['album'] ?? '') ?>"
                            data-url="<?= vebra_escape($audio) ?>"
                            data-cover="<?= vebra_escape($cover) ?>"
                            data-id="<?= (int)$song['id'] ?>"
                            aria-label="Play <?= vebra_escape($title) ?>"
                        >
                            <span class="vebra-song-number">
                                <?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?>
                            </span>

                            <span class="vebra-song-info">
                                <strong><?= vebra_escape($title) ?></strong>
                                <small><?= vebra_escape($artist) ?></small>
                            </span>

                            <span class="vebra-song-play" aria-hidden="true">
                                <i class="ri-play-fill"></i>
                            </span>

                            <span class="list-song__duration"></span>
                            <div class="vebra-track-progress" style="width:100%;"><span class="vebra-track-progress-fill"></span></div>
                        </button>
                        <?php endforeach; ?>
                    </div>

                    <p class="vebra-player-note">
                        Choose a track from the list to start listening.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- LATEST RELEASES -->
    <section class="section section--alt discography-1" data-slider-scope>
        <div class="container-vebra">
            <div class="section-head">
                <div>
                    <span class="section-label">Fresh from the studio</span>
                    <h2 class="section-head__title">Latest <em>releases</em></h2>
                </div>
                <a href="discography.php" class="link-arrow">
                    All songs <i class="ri-arrow-right-up-line"></i>
                </a>
            </div>

            <div class="swiper swiper--clip" data-slider="disco">
                <div class="swiper-wrapper">
                    <?php if ($songs): ?>
                        <?php foreach ($songs as $song):
                            $id = (int)$song['id'];
                            $title = (string)($song['title'] ?? 'Untitled');
                            $artist = (string)($song['artist_name'] ?? 'Unknown artist');
                            $artwork = vebra_image_path(
                                vebra_field($song, ['artwork', 'cover_image', 'cover']),
                                'assets/images/album-1.jpg'
                            );
                            $year = (int)($song['release_year'] ?? 0);
                        ?>
                        <div class="swiper-slide">
                            <article class="album-card vebra-music-card" data-cursor="Listen">
                                <div class="album-card__cover" style="position:relative;">
                                    <a href="song-detail.php?id=<?= $id ?>" style="display:block; width:100%; height:100%;">
                                        <img
                                            src="<?= vebra_escape($artwork) ?>"
                                            alt="<?= vebra_escape($title) ?> artwork"
                                            loading="lazy"
                                        >
                                    </a>
                                    <span class="btn-circle btn-circle--glass album-card__play vebra-play-card-btn"
                                          data-url="<?= vebra_escape($song['audio_path'] ?? '') ?>"
                                          data-title="<?= vebra_escape($title) ?>"
                                          data-artist="<?= vebra_escape($artist) ?>"
                                          data-cover="<?= vebra_escape($artwork) ?>"
                                          data-id="<?= $id ?>"
                                          role="button"
                                          aria-label="Play <?= vebra_escape($title) ?>"
                                          style="cursor: pointer; z-index: 10;">
                                        <i class="ri-play-fill"></i>
                                    </span>
                                </div>
                                <div class="vebra-track-progress"><span class="vebra-track-progress-fill"></span></div>
                                <div class="album-card__body">
                                    <div>
                                        <h3 class="album-card__title">
                                            <a href="song-detail.php?id=<?= $id ?>"><?= vebra_escape($title) ?></a>
                                        </h3>
                                        <span class="album-card__meta">
                                            <?= vebra_escape($artist) ?>
                                        </span>
                                    </div>
                                    <span class="album-card__year">
                                        <?= $year ?: '—' ?>
                                    </span>
                                </div>
                            </article>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="swiper-slide">
                            <div class="album-card">
                                <div class="album-card__cover">
                                    <img src="assets/images/album-1.jpg" alt="VEBRA releases">
                                </div>
                                <div class="album-card__body">
                                    <h3 class="album-card__title">Releases coming soon</h3>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="slider-toolbar">
                <div class="swiper-pagination pagination-lines js-slider-pagination"></div>
                <div class="slider-nav">
                    <button type="button" class="btn-circle slider-nav__btn js-slider-prev"
                            aria-label="Previous releases">
                        <i class="ri-arrow-left-line"></i>
                    </button>
                    <button type="button" class="btn-circle slider-nav__btn js-slider-next"
                            aria-label="Next releases">
                        <i class="ri-arrow-right-line"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- ARTISTS -->
    <section class="section vebra-artists-section" data-slider-scope>
        <div class="container-vebra">
            <div class="section-head">
                <div>
                    <span class="section-label">Meet the creators</span>
                    <h2 class="section-head__title">Artists to <em>know</em></h2>
                </div>
                <a href="artists.php" class="link-arrow">
                    All artists <i class="ri-arrow-right-up-line"></i>
                </a>
            </div>

            <div class="swiper swiper--clip" data-slider="members">
                <div class="swiper-wrapper">
                    <?php if ($artists): ?>
                        <?php foreach ($artists as $artist):
                            $artistId = (int)$artist['id'];
                            $artistName = (string)($artist['name'] ?? 'Artist');
                            $artistImage = vebra_image_path(
                                vebra_field($artist, ['image', 'photo', 'profile_image', 'artist_image']),
                                'assets/images/team-1.jpg'
                            );
                        ?>
                        <div class="swiper-slide">
                            <a href="single-artist.php?id=<?= $artistId ?>"
                               class="vebra-artist-card" data-cursor="Explore">
                                <div class="vebra-artist-image">
                                    <img src="<?= vebra_escape($artistImage) ?>"
                                         alt="<?= vebra_escape($artistName) ?>"
                                         loading="lazy">
                                </div>
                                <div class="vebra-artist-info">
                                    <h3><?= vebra_escape($artistName) ?></h3>
                                    <span>
                                        <?= (int)($artist['release_count'] ?? 0) ?>
                                        approved releases
                                    </span>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="swiper-slide">
                            <p>Artists will appear here when profiles are added.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="slider-toolbar">
                <div class="swiper-pagination pagination-lines js-slider-pagination"></div>
                <div class="slider-nav">
                    <button type="button" class="btn-circle slider-nav__btn js-slider-prev"
                            aria-label="Previous artists">
                        <i class="ri-arrow-left-line"></i>
                    </button>
                    <button type="button" class="btn-circle slider-nav__btn js-slider-next"
                            aria-label="Next artists">
                        <i class="ri-arrow-right-line"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

        <!-- LATEST VIDEOS CAROUSEL -->
    <section class="section section--alt" data-slider-scope>
        <div class="container-vebra">

            <div class="section-head">
                <div>
                    <span class="section-label">Watch the latest</span>
                    <h2 class="section-head__title">
                        Sound meets <em>vision</em>
                    </h2>
                </div>

                <a href="video-gallery.php" class="link-arrow">
                    All videos <i class="ri-arrow-right-up-line"></i>
                </a>
            </div>

            <?php if (!empty($videos)): ?>

                <div class="swiper swiper--clip" data-slider="disco">
                    <div class="swiper-wrapper">

                        <?php foreach ($videos as $video):

                            $videoId = (int)($video['id'] ?? 0);

                            $videoTitle = (string)(
                                $video['title'] ?? 'Untitled video'
                            );

                            $videoArtist = (string)(
                                $video['artist_name'] ?? 'VEBRA'
                            );

                            $videoThumb = vebra_image_path(
                                vebra_field($video, ['thumbnail']),
                                'assets/images/video-cover-1.jpg'
                            );
                        ?>

                            <div class="swiper-slide">

                                <a
                                    href="single-video.php?id=<?= $videoId ?>"
                                    class="video-card"
                                    data-cursor="Play"
                                >

                                    <img
                                        src="<?= vebra_escape($videoThumb) ?>"
                                        alt="<?= vebra_escape($videoTitle) ?>"
                                        loading="lazy"
                                    >

                                    <span class="video-card__play btn-play btn-play--glass">
                                        <i class="ri-play-fill"></i>
                                    </span>

                                    <div class="video-card__body">

                                        <h3 class="video-card__title">
                                            <?= vebra_escape($videoTitle) ?>
                                        </h3>

                                        <span class="video-card__meta">
                                            <?= vebra_escape($videoArtist) ?>
                                        </span>

                                    </div>

                                </a>

                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>

                <div class="slider-toolbar">

                    <div class="swiper-pagination pagination-lines js-slider-pagination"></div>

                    <div class="slider-nav">

                        <button
                            type="button"
                            class="btn-circle slider-nav__btn js-slider-prev"
                            aria-label="Previous videos"
                        >
                            <i class="ri-arrow-left-line"></i>
                        </button>

                        <button
                            type="button"
                            class="btn-circle slider-nav__btn js-slider-next"
                            aria-label="Next videos"
                        >
                            <i class="ri-arrow-right-line"></i>
                        </button>

                    </div>

                </div>

            <?php else: ?>

                <p>No approved videos available yet.</p>

            <?php endif; ?>

        </div>
    </section>
    
    <!-- GALLERY -->
    <section class="section photo-gallery-2" data-filter-scope>
        <div class="container-vebra">
            <div class="gallery-toolbar">
                <div>
                    <span class="section-label">Behind the sound</span>
                    <h2 class="section-head__title mb-0">From the <em>pit</em></h2>
                </div>
                <a href="gallery.php" class="link-arrow">
                    Full gallery <i class="ri-arrow-right-up-line"></i>
                </a>
            </div>

            <div class="filter-grid gallery-grid gallery-grid--spans"
                 data-filter-grid data-reveal-group data-stagger="0.06">
                <?php for ($i = 1; $i <= 8; $i++):
                    $full = "assets/images/gallery-2-$i.jpg";
                    $thumb = "assets/images/gallery-2-$i-thumb.jpg";
                ?>
                <div class="filter-grid__item" data-category="live">
                    <a href="<?= vebra_escape($full) ?>" class="gallery-tile"
                       data-lightbox="image" data-gallery="home-vebra"
                       data-src="<?= vebra_escape($full) ?>"
                       data-caption="VEBRA live — frame <?= $i ?>"
                       data-cursor="View">
                        <img src="<?= vebra_escape($thumb) ?>"
                             alt="VEBRA live frame <?= $i ?>" loading="lazy">
                        <span class="gallery-tile__zoom">
                            <i class="ri-zoom-in-line"></i>
                        </span>
                        <span class="gallery-tile__caption">
                            <span class="gallery-tile__title">
                                Frame <?= str_pad((string)$i, 2, '0', STR_PAD_LEFT) ?>
                            </span>
                            <span class="gallery-tile__meta">Live / Studio</span>
                        </span>
                    </a>
                </div>
                <?php endfor; ?>
                <p class="filter-empty">No photos in this category yet.</p>
            </div>
        </div>
    </section>

    <!-- LISTENER REVIEWS -->
    <?php if ($reviews): ?>
    <section class="section section--alt vebra-press-section" data-slider-scope>
        <div class="container-vebra">
            <div class="section-head">
                <div>
                    <span class="section-label">Listener Reviews</span>
                    <h2 class="section-head__title">Words about the <em>music</em></h2>
                </div>
            </div>

            <div class="swiper vebra-press-slider" data-slider="testimonials">
                <div class="swiper-wrapper">
                    <?php foreach ($reviews as $review): ?>
                    <div class="swiper-slide">
                        <div class="vebra-quote-box">
                            <div class="vebra-quote-stars"
                                 aria-label="<?= (int)$review['rating'] ?> out of 5 stars">
                                <?php for ($i = 0; $i < (int)$review['rating']; $i++): ?>
                                    <i class="ri-star-fill"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="vebra-quote-text">
                                &ldquo;<?= vebra_escape($review['quote']) ?>&rdquo;
                            </p>
                            <cite class="vebra-quote-cite">
                                &mdash; <?= vebra_escape($review['author']) ?>
                                <?php if ($review['sub'] !== ''): ?>
                                    <span style="display:block;opacity:.7;font-weight:400;text-transform:none;font-size:.8rem;margin-top:4px">
                                        <?= vebra_escape($review['sub']) ?>
                                    </span>
                                <?php endif; ?>
                            </cite>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="slider-toolbar">
                <div class="swiper-pagination pagination-lines js-slider-pagination"></div>
                <div class="slider-nav">
                    <button type="button" class="btn-circle slider-nav__btn js-slider-prev"
                            aria-label="Previous review">
                        <i class="ri-arrow-left-line"></i>
                    </button>
                    <button type="button" class="btn-circle slider-nav__btn js-slider-next"
                            aria-label="Next review">
                        <i class="ri-arrow-right-line"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- JOURNAL -->
    <section class="section news-section" data-slider-scope>
        <div class="container-vebra">
            <div class="section-head">
                <div>
                    <span class="section-label">Explore VEBRA</span>
                    <h2 class="section-head__title">Inside the <em>music</em></h2>
                </div>
                <a href="news-page.php" class="link-arrow">
                    All news <i class="ri-arrow-right-up-line"></i>
                </a>
            </div>

            <div class="swiper swiper--clip" data-slider="news">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <article class="media-card news-card">
                            <a href="discography.php" class="media-card__media" data-cursor="Explore">
                                <img src="assets/images/news-1.jpg"
                                     alt="Latest music releases" loading="lazy">
                                <span class="card-icon"><i class="ri-arrow-right-up-line"></i></span>
                            </a>
                            <div class="media-card__body">
                                <div class="media-card__meta">
                                    <span class="news-card__category">Releases</span>
                                </div>
                                <h3 class="media-card__title">
                                    <a href="discography.php">Discover the latest releases</a>
                                </h3>
                                <p class="media-card__excerpt">
                                    Explore new music from the VEBRA catalogue.
                                </p>
                            </div>
                        </article>
                    </div>

                    <div class="swiper-slide">
                        <article class="media-card news-card">
                            <a href="video-gallery.php" class="media-card__media" data-cursor="Watch">
                                <img src="assets/images/news-2.jpg"
                                     alt="Music video collection" loading="lazy">
                                <span class="card-icon"><i class="ri-arrow-right-up-line"></i></span>
                            </a>
                            <div class="media-card__body">
                                <div class="media-card__meta">
                                    <span class="news-card__category">Video</span>
                                </div>
                                <h3 class="media-card__title">
                                    <a href="video-gallery.php">Watch the latest visuals</a>
                                </h3>
                                <p class="media-card__excerpt">
                                    Music videos and visual stories from the artists.
                                </p>
                            </div>
                        </article>
                    </div>

                    <div class="swiper-slide">
                        <article class="media-card news-card">
                            <a href="artists.php" class="media-card__media" data-cursor="Explore">
                                <img src="assets/images/news-3.jpg"
                                     alt="Featured artists" loading="lazy">
                                <span class="card-icon"><i class="ri-arrow-right-up-line"></i></span>
                            </a>
                            <div class="media-card__body">
                                <div class="media-card__meta">
                                    <span class="news-card__category">Artists</span>
                                </div>
                                <h3 class="media-card__title">
                                    <a href="artists.php">Meet the artists behind the sound</a>
                                </h3>
                                <p class="media-card__excerpt">
                                    Find artists and explore their releases.
                                </p>
                            </div>
                        </article>
                    </div>
                </div>
            </div>

            <div class="slider-toolbar">
                <div class="swiper-pagination pagination-lines js-slider-pagination"></div>
                <div class="slider-nav">
                    <button type="button" class="btn-circle slider-nav__btn js-slider-prev"
                            aria-label="Previous news">
                        <i class="ri-arrow-left-line"></i>
                    </button>
                    <button type="button" class="btn-circle slider-nav__btn js-slider-next"
                            aria-label="Next news">
                        <i class="ri-arrow-right-line"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
$extraScripts = <<<'HTML'
<script>
document.addEventListener('DOMContentLoaded', function () {
    /* Hero image rotation */
    const hero = document.getElementById('vebra-hero-slide');

    if (hero) {
        const images = [
            'assets/images/hero-slide-1.jpg',
            'assets/images/hero-slide-2.jpg',
            'assets/images/hero-slide-3.jpg'
        ];

        let current = 0;
        hero.style.transition = 'opacity 700ms ease';

        window.setInterval(function () {
            const next = (current + 1) % images.length;
            const preload = new Image();

            preload.onload = function () {
                hero.style.opacity = '0';

                window.setTimeout(function () {
                    hero.src = images[next];
                    hero.style.opacity = '1';
                    current = next;
                }, 700);
            };

            preload.onerror = function () {
                current = next;
            };

            preload.src = images[next];
        }, 5000);
    }

    /* Sync the listening-room display with the selected track */
    const rows = Array.from(document.querySelectorAll('.vebra-player-song'));
    const titleLink = document.getElementById('vebra-player-title-link') || (document.getElementById('vebra-player-title') ? document.getElementById('vebra-player-title').querySelector('a') : null);
    const artist = document.getElementById('vebra-player-artist');
    const cover = document.getElementById('vebra-player-cover');
    const artLink = document.getElementById('vebra-player-art-link');

    rows.forEach(function (row, index) {
        row.addEventListener('click', function () {
            rows.forEach(function (item) {
                item.classList.remove('is-active');
            });

            row.classList.add('is-active');

            const songId = row.dataset.id || '';
            const detailUrl = 'song-detail.php?id=' + encodeURIComponent(songId);

            if (titleLink) {
                titleLink.textContent = row.dataset.title || '';
                if (songId) titleLink.href = detailUrl;
            }
            if (artLink && songId) {
                artLink.href = detailUrl;
            }
            if (artist) artist.textContent = row.dataset.artist || '';

            if (cover && row.dataset.cover) {
                cover.style.opacity = '0.3';

                const nextCover = new Image();
                nextCover.onload = function () {
                    cover.src = row.dataset.cover;
                    cover.style.opacity = '1';
                };
                nextCover.onerror = function () {
                    cover.style.opacity = '1';
                };
                nextCover.src = row.dataset.cover;
            }

            var bottomPlayer = document.getElementById('vebra-docked-bottom-player');
            if (bottomPlayer) {
                bottomPlayer.classList.add('is-visible');
                document.body.classList.add('has-bottom-player');
            }

            if (window.vebraPlayTrack) {
                window.vebraPlayTrack({
                    id: songId,
                    name: row.dataset.title || '',
                    artist: row.dataset.artist || '',
                    album: row.dataset.album || '',
                    url: row.dataset.url || '',
                    cover_art_url: row.dataset.cover || ''
                });
            } else if (window.Amplitude &&
                typeof window.Amplitude.playSongAtIndex === 'function') {
                var allSongs = window.Amplitude.getSongs();
                var targetIdx = allSongs.findIndex(function (s) {
                    return (songId && String(s.id) === String(songId)) || s.url === row.dataset.url || (row.dataset.title && s.name === row.dataset.title);
                });
                if (targetIdx !== -1) {
                    window.Amplitude.playSongAtIndex(targetIdx);
                } else {
                    window.Amplitude.playSongAtIndex(index);
                }
            } else {
                console.error('AmplitudeJS is not loaded. Check $loadAmplitude and footer.php.');
            }
        });
    });
});
</script>
HTML;

require_once __DIR__ . '/includes/footer.php';
?>
