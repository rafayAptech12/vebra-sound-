<?php
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Discography — VEBRA';
$activePage = 'music';
$activeSub = 'discography';
$bodyClass = 'theme-magenta';

/*
|--------------------------------------------------------------------------
| Language normalization
|--------------------------------------------------------------------------
*/

function discographyLanguageKey($value) {
    $value = strtolower(trim((string)$value));

    if (in_array($value, ['hindi', 'hindhi'], true)) {
        return 'hindi';
    }

    if (in_array($value, ['punjabi', 'panjabi'], true)) {
        return 'punjabi';
    }

    return $value;
}

function discographyLanguageLabel($value) {
    $key = discographyLanguageKey($value);

    $labels = [
        'hindi' => 'Hindi',
        'punjabi' => 'Punjabi',
        'urdu' => 'Urdu',
        'english' => 'English'
    ];

    return $labels[$key] ?? trim((string)$value);
}

function discographyYear($value) {
    $year = (int)$value;
    return $year > 0 ? (string)$year : '';
}

/*
|--------------------------------------------------------------------------
| Load approved music
|--------------------------------------------------------------------------
*/

$songs = [];

$sql = "
    SELECT
        s.id,
        s.title,
        s.artist_id,
        s.genre_id,
        s.language,
        s.release_year,
        s.artwork,
        s.audio_path,
        a.name AS artist_name,
        g.name AS genre_name
    FROM songs s
    LEFT JOIN artists a ON a.id = s.artist_id
    LEFT JOIN genres g ON g.id = s.genre_id
    WHERE s.status = 'approved'
      AND s.audio_path IS NOT NULL
      AND TRIM(s.audio_path) <> ''
    ORDER BY s.created_at DESC, s.id DESC
";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }
}

$totalSongs = count($songs);

/*
|--------------------------------------------------------------------------
| Build real filter options
|--------------------------------------------------------------------------
*/

$languages = [];
$years = [];
$genres = [];
$artistIds = [];

foreach ($songs as $song) {
    if (!empty($song['language'])) {
        $key = discographyLanguageKey($song['language']);
        $languages[$key] = discographyLanguageLabel($song['language']);
    }

    $year = discographyYear($song['release_year']);

    if ($year !== '') {
        $years[$year] = $year;
    }

    if (!empty($song['genre_name'])) {
        $key = strtolower(trim($song['genre_name']));
        $genres[$key] = trim($song['genre_name']);
    }

    if (!empty($song['artist_id'])) {
        $artistIds[(int)$song['artist_id']] = true;
    }
}

asort($languages);
krsort($years);
asort($genres);

$totalArtists = count($artistIds);
$totalGenres = count($genres);

require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <!-- PAGE HEADING -->
    <section class="page-caption discography-caption">
        <div class="container-vebra">

            <div class="page-caption__inner">

                <nav class="breadcrumb-vebra" aria-label="Breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="breadcrumb-vebra__sep">/</span>
                    <span aria-current="page">Discography</span>
                </nav>

                <h1 class="page-caption__title">
                    The Discography
                </h1>

                <p class="page-caption__text">
                    Explore the music. Discover the artists.
                    Every track has its own story.
                </p>

                <div class="page-caption__meta">

                    <div class="page-caption__stat">
                        <strong><?php echo $totalSongs; ?></strong>
                        <span>Available tracks</span>
                    </div>

                    <div class="page-caption__stat">
                        <strong><?php echo $totalArtists; ?></strong>
                        <span>Artists</span>
                    </div>

                    <div class="page-caption__stat">
                        <strong><?php echo $totalGenres; ?></strong>
                        <span>Genres</span>
                    </div>

                </div>

            </div>
        </div>
    </section>


    <!-- DISCOGRAPHY -->
    <section class="section discography-page">
        <div class="container-vebra">

            <div class="section-head">

                <div>
                    <span class="section-label">
                        VEBRA MUSIC LIBRARY
                    </span>

                    <h2 class="section-head__title">
                        Find your <em>frequency</em>
                    </h2>
                </div>

                <div class="stack stack-3">
                    <p class="section-head__text">
                        Discover real music from the VEBRA collection.
                        Search tracks and explore artists, genres,
                        languages and release years.
                    </p>
                </div>

            </div>


            <!-- SEARCH AND FILTERS -->
            <div class="discography-tools">

                <div class="discography-search">
                    <i class="ri-search-line"></i>

                    <input
                        type="search"
                        id="discographySearch"
                        placeholder="Search songs, artists, genres..."
                        aria-label="Search music"
                        autocomplete="off"
                    >
                </div>


                <select
                    id="discographyLanguage"
                    aria-label="Filter by language"
                >
                    <option value="">All languages</option>

                    <?php foreach ($languages as $key => $label): ?>
                        <option value="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>

                </select>


                <select
                    id="discographyYear"
                    aria-label="Filter by year"
                >
                    <option value="">All years</option>

                    <?php foreach ($years as $year): ?>
                        <option value="<?php echo htmlspecialchars($year, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($year, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>

                </select>


                <select
                    id="discographyGenre"
                    aria-label="Filter by genre"
                >
                    <option value="">All genres</option>

                    <?php foreach ($genres as $key => $label): ?>
                        <option value="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>

                </select>

            </div>


            <!-- RESULTS -->
            <div class="discography-results">

                <div class="discography-results__heading">
                    <span>THE COLLECTION</span>

                    <span id="discographyResultCount">
                        <?php echo $totalSongs; ?> tracks
                    </span>
                </div>


                <div
                    class="filter-grid discography-4__list"
                    id="discographyGrid"
                >

                    <?php foreach ($songs as $song):

                        $id = (int)$song['id'];

                        $title = trim((string)$song['title']);

                        $artist = $song['artist_name']
                            ?: 'Unknown Artist';

                        $genre = $song['genre_name']
                            ?: 'Unclassified';

                        $language = $song['language']
                            ?: 'Unknown';

                        $languageKey = discographyLanguageKey($language);

                        $languageLabel = discographyLanguageLabel($language);

                        $year = discographyYear($song['release_year']);

                        $artwork = trim((string)$song['artwork']);

                        if ($artwork === '') {
                            $artwork = 'assets/images/placeholder.svg';
                        }

                        $audio = trim((string)$song['audio_path']);

                        $searchText = strtolower(
                            $title . ' ' .
                            $artist . ' ' .
                            $genre . ' ' .
                            $languageLabel . ' ' .
                            $year . ' ' .
                            $id
                        );

                    ?>

                    <article
                        class="filter-grid__item album-row vebra-music-card discography-track"

                        data-search="<?php echo htmlspecialchars($searchText, ENT_QUOTES, 'UTF-8'); ?>"

                        data-language="<?php echo htmlspecialchars($languageKey, ENT_QUOTES, 'UTF-8'); ?>"

                        data-year="<?php echo htmlspecialchars($year, ENT_QUOTES, 'UTF-8'); ?>"

                        data-genre="<?php echo htmlspecialchars(strtolower(trim($genre)), ENT_QUOTES, 'UTF-8'); ?>"
                    >

                        <div class="album-row__thumb">

                            <img
                                src="<?php echo htmlspecialchars($artwork, ENT_QUOTES, 'UTF-8'); ?>"
                                alt=""
                                loading="lazy"
                                onerror="this.onerror=null;this.src='assets/images/placeholder.svg';"
                            >

                        </div>


                        <div class="discography-track__info">

                            <h3 class="album-row__title">
                                <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
                            </h3>

                            <div class="album-row__meta">

                                <span>
                                    <?php echo htmlspecialchars($artist, ENT_QUOTES, 'UTF-8'); ?>
                                </span>

                                <span>·</span>

                                <span>
                                    <?php echo htmlspecialchars($genre, ENT_QUOTES, 'UTF-8'); ?>
                                </span>

                                <span>·</span>

                                <span>
                                    <?php echo htmlspecialchars($languageLabel, ENT_QUOTES, 'UTF-8'); ?>
                                </span>

                            </div>

                        </div>


                        <div class="album-row__tracks">

                            <span>
                                <?php echo $year !== '' ? htmlspecialchars($year) : 'Year unknown'; ?>
                            </span>

                            <span>·</span>

                            <span>
                                Track #<?php echo $id; ?>
                            </span>

                        </div>


                        <div class="discography-track__actions">

                            <button
                                type="button"
                                class="btn btn--sm btn--solid vebra-play-card-btn"

                                data-url="<?php echo htmlspecialchars($audio, ENT_QUOTES, 'UTF-8'); ?>"

                                data-title="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>"

                                data-artist="<?php echo htmlspecialchars($artist, ENT_QUOTES, 'UTF-8'); ?>"

                                data-cover="<?php echo htmlspecialchars($artwork, ENT_QUOTES, 'UTF-8'); ?>"

                                data-id="<?php echo $id; ?>"

                                aria-label="Play <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>"
                            >

                                <span class="btn__label">
                                    <i class="ri-play-fill"></i>
                                    Play
                                </span>

                            </button>

                        </div>


                        <div class="vebra-track-progress">
                            <span class="vebra-track-progress-fill"></span>
                        </div>

                    </article>

                    <?php endforeach; ?>


                    <?php if (!$songs): ?>

                        <div class="discography-empty">
                            <i class="ri-music-2-line"></i>

                            <h3>No approved tracks yet</h3>

                            <p>
                                Approved music will appear here when available.
                            </p>
                        </div>

                    <?php endif; ?>

                </div>


                <!-- NO RESULTS -->
                <div
                    class="discography-no-results"
                    id="discographyNoResults"
                    hidden
                >
                    <h3>No matching tracks</h3>

                    <p>
                        Try another song, artist, genre or year.
                    </p>

                </div>

            </div>

        </div>
    </section>

</main>


<style>

/* FILTER CONTROLS */

.discography-tools {
    display: grid;
    grid-template-columns: minmax(220px, 1fr) repeat(3, minmax(130px, 180px));
    gap: 12px;
    margin: 30px 0;
}

.discography-search {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    padding: 0 16px;
    border: 1px solid var(--line);
    border-radius: 10px;
    background: rgba(255,255,255,.035);
}

.discography-search input {
    width: 100%;
    min-width: 0;
    padding: 15px 0;
    color: inherit;
    background: transparent;
    border: 0;
    outline: 0;
}

.discography-tools select {
    width: 100%;
    min-width: 0;
    padding: 14px;
    color: inherit;
    background: #15131a;
    border: 1px solid var(--line);
    border-radius: 10px;
    cursor: pointer;
}


/* RESULTS HEADING */

.discography-results__heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 18px;
    font-size: 12px;
    letter-spacing: 2px;
}


/* TRACK CARDS */

.discography-track__info {
    min-width: 0;
}

.discography-track__info .album-row__title {
    overflow-wrap: anywhere;
}

.discography-track__actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
}


/* FILTER VISIBILITY — IMPORTANT */

.discography-track[hidden] {
    display: none !important;
}

.discography-no-results[hidden] {
    display: none !important;
}


/* EMPTY STATE */

.discography-no-results,
.discography-empty {
    padding: 45px 20px;
    text-align: center;
}


/* RESPONSIVE */

@media (max-width: 991px) {

    .discography-tools {
        grid-template-columns: 1fr 1fr;
    }

    .discography-search {
        grid-column: 1 / -1;
    }

}

@media (max-width: 575px) {

    .discography-tools {
        grid-template-columns: 1fr;
    }

    .discography-search {
        grid-column: auto;
    }

    .discography-results__heading {
        font-size: 10px;
        letter-spacing: 1px;
    }

}

</style>


<script>
(function () {

    const search = document.getElementById('discographySearch');
    const language = document.getElementById('discographyLanguage');
    const year = document.getElementById('discographyYear');
    const genre = document.getElementById('discographyGenre');

    const cards = Array.from(
        document.querySelectorAll('.discography-track')
    );

    const count = document.getElementById('discographyResultCount');
    const empty = document.getElementById('discographyNoResults');

    if (!search || !language || !year || !genre || !count) {
        return;
    }


    function normalize(value) {
        return String(value || '').trim().toLowerCase();
    }


    function filterTracks() {

        const q = normalize(search.value);
        const lang = normalize(language.value);
        const yr = normalize(year.value);
        const gen = normalize(genre.value);

        let visible = 0;


        cards.forEach(function (card) {

            const text = normalize(card.dataset.search);
            const cardLang = normalize(card.dataset.language);
            const cardYear = normalize(card.dataset.year);
            const cardGenre = normalize(card.dataset.genre);


            const matchesSearch =
                !q || text.includes(q);

            const matchesLanguage =
                !lang || cardLang === lang;

            const matchesYear =
                !yr || cardYear === yr;

            const matchesGenre =
                !gen || cardGenre === gen;


            const show =
                matchesSearch &&
                matchesLanguage &&
                matchesYear &&
                matchesGenre;


            card.hidden = !show;

            /* Force visibility even if another CSS rule overrides hidden */
            card.style.display = show ? '' : 'none';


            if (show) {
                visible++;
            }

        });


        count.textContent =
            visible + (visible === 1 ? ' track' : ' tracks');


        if (empty) {
            empty.hidden = visible !== 0;
            empty.style.display = visible === 0 ? '' : 'none';
        }

    }


    search.addEventListener('input', filterTracks);

    language.addEventListener('change', filterTracks);
    year.addEventListener('change', filterTracks);
    genre.addEventListener('change', filterTracks);


    /* Initial count */
    filterTracks();

})();
</script>


<?php require_once __DIR__ . '/includes/footer.php'; ?>
