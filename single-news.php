<?php
$articleParam = isset($_GET['article']) ? trim($_GET['article']) : 'making-of-nightform';

$articles = [
    'making-of-nightform' => [
        'title' => 'Inside the making of Nightform',
        'category' => 'Studio',
        'date' => 'March 04, 2026',
        'read_time' => '9 min',
        'author' => 'Mara Okonkwo',
        'type' => 'Studio diary',
        'cover' => 'assets/images/news-1.jpg',
        'hero' => 'assets/images/single-news.jpg',
        'p1' => 'The barn came with a leak, a wood stove and a resident barn owl that only ever appeared during quiet takes. We had booked three weeks upstate to demo four songs and ended up staying six, tracking an entire album live to two-inch tape with the doors propped open on whatever weather New York decided to send us.',
        'p2' => 'None of that was the plan. The plan was a clean, fast record cut in a proper room in the city, mixed in a fortnight and out before the summer festivals. What we got instead was Nightform — slower, stranger, and far more honest about what this band actually sounds like when nobody is watching the clock.',
        'h2' => 'Two tape machines, one survivor',
        'p3' => 'The Studer arrived on a Tuesday and died on a Thursday. Its replacement, an unloved MCI hauled up from a basement in Kingston, held together for the rest of the sessions on a diet of new capacitors and superstition. Every song on the finished record passed through it, which explains the slight wobble at the top of "Halogen" that we decided to keep.',
        'quote' => 'We stopped trying to make it sound expensive and started trying to make it sound true.',
        'quote_author' => 'Ivo Lang — guitar, production',
        'p4' => 'Sequencing took longer than tracking. Side C exists because of a rainstorm that arrived halfway through a take and never got edited out; you can hear it under the second verse if you listen on headphones. We tried removing it four separate times and the song got worse every time.',
        'tags' => ['Nightform', 'Studio', 'Analog', 'Catskills']
    ],
    'tour-routing' => [
        'title' => 'The 2026 routing is finally locked',
        'category' => 'Tour',
        'date' => 'Feb 21, 2026',
        'read_time' => '5 min',
        'author' => 'Elias Ward',
        'type' => 'Tour update',
        'cover' => 'assets/images/news-2.jpg',
        'hero' => 'assets/images/slider-3.jpg',
        'p1' => 'Fourteen countries, eighty-six rooms and one very long ferry crossing across the English Channel. Months of routing negotiation and venue availability checks have finally concluded.',
        'p2' => 'We are opening in London at Eventim Apollo and traveling through Manchester, Amsterdam, Vienna, Cologne, Paris, and Lisbon, before heading back across the Atlantic for our biggest headline show at Kings Theatre in Brooklyn.',
        'h2' => 'Full sound and lighting production traveling with us',
        'p3' => 'Rather than hiring local production at every venue, the full visual installation, quadraphonic surround sub-bass setup, and modular synth racks are being transported in two dedicated tour trucks.',
        'quote' => 'Every room deserves the exact same intensity that we created in the studio.',
        'quote_author' => 'Elias Ward — live director',
        'p4' => 'Pre-sale tickets are available immediately to newsletter subscribers, with general sales opening globally this Friday at 10:00 local time.',
        'tags' => ['Tour 2026', 'Europe', 'Brooklyn', 'Live Production']
    ],
    'halogen-video' => [
        'title' => 'Shooting "Halogen" in one take',
        'category' => 'Video',
        'date' => 'Feb 09, 2026',
        'read_time' => '6 min',
        'author' => 'Junie Okafor',
        'type' => 'Behind the scenes',
        'cover' => 'assets/images/news-3.jpg',
        'hero' => 'assets/images/video-cover-1.jpg',
        'p1' => 'Four minutes, one Steadicam operator running backward, and a cavernous disused tram depot in Rotterdam. Nobody fell over, and take six was the miracle take that made the final cut.',
        'p2' => 'Directed by Elena Rostov, the objective was to capture the claustrophobic momentum of the track without using artificial CGI or rapid editing cuts.',
        'h2' => 'Lighting through fog and structural steel',
        'p3' => 'We rigged over sixty warm sodium lamps suspended from the tram overhead wires. As the song builds to its crescendo, the lighting dimmer shifts gradually into pulsing violet strobes synchronized to the drum machine.',
        'quote' => 'One continuous shot creates a tension that cuts can never replicate.',
        'quote_author' => 'Elena Rostov — Director',
        'p4' => 'The complete music video is now streaming in 4K on our official video channel and discography hub.',
        'tags' => ['Music Video', 'Halogen', 'Rotterdam', 'Steadicam']
    ],
    'before-a-show' => [
        'title' => 'What actually happens before a show',
        'category' => 'Live',
        'date' => 'Jan 30, 2026',
        'read_time' => '7 min',
        'author' => 'Tomi Chen',
        'type' => 'Photo essay & tour diary',
        'cover' => 'assets/images/gallery-4.jpg',
        'hero' => 'assets/images/gallery-4.jpg',
        'p1' => 'Soundcheck at Eventim Apollo starts at 15:30. By 16:45, the room sounds like rolling thunder in an empty cathedral. Backstage, the dressing rooms are freezing, the tea is lukewarm, and someone has inevitably misplaced the second snare drum dampener.',
        'p2' => 'Music journalists often ask what bands do during the five hours between soundcheck and showtime. The truth is neither glamorous nor cinematic: three hours of silence, bad catering sandwiches, endless cable re-wrapping, and twenty minutes of collective pacing.',
        'h2' => 'The ninety minutes before curfew',
        'p3' => 'Around 19:45, the doors open and the air pressure in the building shifts. You can hear the low murmur of two thousand people filtering into the stalls. By the time our intro tape begins looping its drone, every trace of exhaustion vanishes into adrenaline.',
        'quote' => 'The stage is the only place in the building where things make complete sense.',
        'quote_author' => 'Soren Kjaer — drums',
        'p4' => 'We walked off stage at 22:52 drenched in sweat and deaf from sub-bass. Half an hour later, the flight cases were already rolling into the trucks bound for Manchester.',
        'tags' => ['Live', 'Tour Diary', 'London', 'Apollo', 'Backstage']
    ],
    'modular-rig' => [
        'title' => 'The modular rig that ate side B',
        'category' => 'Gear',
        'date' => 'Jan 12, 2026',
        'read_time' => '8 min',
        'author' => 'Ivo Lang',
        'type' => 'Studio technical notes',
        'cover' => 'assets/images/gallery-5.jpg',
        'hero' => 'assets/images/gallery-5.jpg',
        'p1' => 'Nine Eurorack modules, a pair of vintage tape delays, one dying Roland Space Echo, and a spring reverb unit salvaged from a 1974 combo amp. That was the instrument that ended up dominating the entire second half of Nightform.',
        'p2' => 'We did not intend to build a drone machine. It began as an experiment to generate subtle fluctuating harmonics beneath the acoustic piano tracks. But within forty-eight hours of patching voltage-controlled filters into feedback loops, the rig took over the control room.',
        'h2' => 'Unstable oscillators and happy accidents',
        'p3' => 'The core bassline on "Sub-Static" was generated by temperature drift in an analogue oscillator that refused to stay calibrated. Every time the barn heater kicked on, the pitch dropped a quarter tone. Instead of tuning it, we retuned the guitars to match it.',
        'quote' => 'Perfection in synthesis is sterile. The soul lives in the components that are about to fail.',
        'quote_author' => 'Ivo Lang — synthesizers',
        'p4' => 'We recorded thirty hours of raw improvised voltage signals directly to two-inch tape. Those tapes were chopped, looped, and sequenced into the ambient transitions that bridge every song on side B.',
        'tags' => ['Gear', 'Eurorack', 'Synthesizers', 'Analog', 'Tape Loop']
    ],
    'drawing-nightform' => [
        'title' => 'Drawing the Nightform sleeve',
        'category' => 'Design',
        'date' => 'Dec 18, 2025',
        'read_time' => '6 min',
        'author' => 'Katja Lind',
        'type' => 'Visual arts & packaging',
        'cover' => 'assets/images/album-3.jpg',
        'hero' => 'assets/images/album-3.jpg',
        'p1' => 'Forty rejected mockups, three ruined silkscreens, one heated midnight shouting match in the kitchen, and finally the stark geometric foil block that became the front sleeve of Nightform.',
        'p2' => 'Physical packaging has always been as essential to VEBRA as the mastering engineer. We wanted the vinyl sleeve to feel heavy, tactile, and devoid of unnecessary text or corporate logos on the outer gatefold.',
        'h2' => 'Heavyweight board and black-on-black foil',
        'p3' => 'The final print run used 400gsm unbleached Swedish cardstock with blind debossing and matte metallic foil. In low light, the typography disappears entirely, leaving only the embossed architectural grid caught in the reflections.',
        'quote' => 'A record sleeve should feel like an artifact discovered in an abandoned broadcasting station.',
        'quote_author' => 'Katja Lind — art director',
        'p4' => 'The limited deluxe edition includes a 28-page risograph booklet printed on translucent Japanese vellum featuring session photographs and tape box logs from the Catskills.',
        'tags' => ['Design', 'Vinyl', 'Packaging', 'Typography', 'Artwork']
    ],
    'ten-years' => [
        'title' => 'Ten years of Rivington Street',
        'category' => 'Band',
        'date' => 'Nov 27, 2025',
        'read_time' => '10 min',
        'author' => 'Marcus Sterling',
        'type' => 'Retrospective',
        'cover' => 'assets/images/gallery-1.jpg',
        'hero' => 'assets/images/gallery-1.jpg',
        'p1' => 'The lease on Rivington Street has officially been renewed for another ten years. In an era where every historic rehearsal room in Lower Manhattan has been turned into luxury condos or cold-brew bars, our basement remains standing.',
        'p2' => 'We moved our first drum kit down these narrow stairs in the winter of 2016. The walls were damp, the ceiling was low enough that Soren scraped his knuckles on the water pipes, but it had one irreplaceable virtue: we could make noise at 3:00 AM.',
        'h2' => 'Five albums on one worn rug',
        'p3' => 'Every single song in the VEBRA discography was written on that rug. From the earliest four-track cassette demos to the pre-production rehearsals for the 2026 world tour, the room has absorbed thousands of hours of music.',
        'quote' => 'Bands do not just need talent; they need a physical room they can ruin without consequence.',
        'quote_author' => 'Marcus Sterling — bass',
        'p4' => 'Here is to the next decade of broken cables, late-night master mixes, and the undeniable resonance of wood, wire, and voltage in Lower Manhattan.',
        'tags' => ['History', 'Studio', 'Rivington', 'Brooklyn', 'The Band']
    ]
];

$curArticle = isset($articles[$articleParam]) ? $articles[$articleParam] : $articles['making-of-nightform'];

$commentSuccess = false;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['comment_submit'])) {
    $commentName = trim($_POST['name'] ?? '');
    $commentBody = trim($_POST['comment'] ?? '');
    if ($commentName && $commentBody) {
        $commentSuccess = true;
    }
}

$pageTitle = $curArticle['title'] . ' — vebra';
$pageDesc = $curArticle['p1'];
$bodyClass = 'theme-magenta';
$activePage = 'news';
$activeSub = 'single-news';
require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <!-- SINGLE NEWS
    ============================================= -->
    <section class="page-caption" data-parallax-scope>
        <div class="page-caption__bg" aria-hidden="true">
            <img src="<?= htmlspecialchars($curArticle['cover']) ?>" alt="" data-parallax-speed="0.12">
        </div>

        <div class="container-vebra">
            <div class="page-caption__inner">
                <ul class="breadcrumb-vebra">
                    <li><a href="index.php">Home</a></li>
                    <li class="breadcrumb-vebra__sep" aria-hidden="true">/</li>
                    <li><a href="news-page.php">News</a></li>
                    <li class="breadcrumb-vebra__sep" aria-hidden="true">/</li>
                    <li aria-current="page"><?= htmlspecialchars($curArticle['title']) ?></li>
                </ul>

                <h1 class="page-caption__title" data-split="lines" data-split-hero>
                    <?= htmlspecialchars($curArticle['title']) ?>
                </h1>

                <div class="page-caption__meta" data-reveal="up" data-delay="0.25">
                    <div class="page-caption__stat"><strong><?= htmlspecialchars($curArticle['category']) ?></strong><span>Category</span></div>
                    <div class="page-caption__stat"><strong><?= htmlspecialchars($curArticle['date']) ?></strong><span>Published</span></div>
                    <div class="page-caption__stat"><strong><?= htmlspecialchars($curArticle['read_time']) ?></strong><span>Read time</span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container-vebra">
            <div class="content-layout">
                <article class="article">
                    <div class="article__hero" data-reveal="mask">
                        <img src="<?= htmlspecialchars($curArticle['hero']) ?>" alt="<?= htmlspecialchars($curArticle['title']) ?>">
                    </div>

                    <div class="article__meta">
                        <span>By <?= htmlspecialchars($curArticle['author']) ?></span>
                        <span><?= htmlspecialchars($curArticle['type']) ?></span>
                        <span><?= htmlspecialchars($curArticle['read_time']) ?> read</span>
                        <div class="article__share socials socials--bare">
                            <a href="https://facebook.com/" target="_blank" rel="noopener">Fb</a>
                            <a href="#">Tw</a>
                            <a href="#">Ig</a>
                        </div>
                    </div>

                    <div class="rich-text has-dropcap">
                        <p><?= htmlspecialchars($curArticle['p1']) ?></p>
                        <p><?= htmlspecialchars($curArticle['p2']) ?></p>

                        <h2><?= htmlspecialchars($curArticle['h2']) ?></h2>
                        <p><?= htmlspecialchars($curArticle['p3']) ?></p>

                        <div class="pull-quote" data-reveal="up">
                            <p>&ldquo;<?= htmlspecialchars($curArticle['quote']) ?>&rdquo;</p>
                            <cite><?= htmlspecialchars($curArticle['quote_author']) ?></cite>
                        </div>

                        <p><?= htmlspecialchars($curArticle['p4']) ?></p>
                    </div>

                    <div class="article__footer">
                        <div class="article__tags">
                            <?php foreach ($curArticle['tags'] as $tg): ?>
                                <span class="pill"><?= htmlspecialchars($tg) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="socials">
                            <a href="https://facebook.com/" target="_blank" rel="noopener" aria-label="Share on Facebook"><i class="ri-facebook-fill"></i></a>
                            <a href="https://x.com/" target="_blank" rel="noopener" aria-label="Share on X / Twitter"><i class="ri-twitter-fill"></i></a>
                            <a href="https://instagram.com/" target="_blank" rel="noopener" aria-label="Share on Instagram"><i class="ri-instagram-line"></i></a>
                            <a href="mailto:hello@vebra.com" aria-label="Share by email"><i class="ri-mail-line"></i></a>
                        </div>
                    </div>
                </article>

                <aside class="sidebar" aria-label="Related information">
                    <div class="sidebar__block">
                        <h2 class="sidebar__title">Search</h2>
                        <form class="form-inline" action="search.php" method="GET">
                            <input type="search" name="q" placeholder="Search the catalogue..." aria-label="Search" required>
                            <button type="submit" aria-label="Search"><i class="ri-search-line"></i></button>
                        </form>
                    </div>

                    <div class="sidebar__block">
                        <h2 class="sidebar__title">Categories</h2>
                        <div class="sidebar__links">
                            <a href="news-page.php">Studio</a>
                            <a href="news-page.php">Tour</a>
                            <a href="news-page.php">Video</a>
                            <a href="news-page.php">Gear</a>
                            <a href="news-page.php">Design</a>
                        </div>
                    </div>

                    <div class="sidebar__block">
                        <h2 class="sidebar__title">Recent posts</h2>
                        <div class="mini-list">
                            <a href="single-news.php?article=making-of-nightform" class="mini-item">
                                <img src="assets/images/news-1.jpg" alt="">
                                <div>
                                    <h4>Inside the making of Nightform</h4>
                                    <span>March 04, 2026</span>
                                </div>
                            </a>
                            <a href="single-news.php?article=tour-routing" class="mini-item">
                                <img src="assets/images/news-2.jpg" alt="">
                                <div>
                                    <h4>The 2026 routing is finally locked</h4>
                                    <span>Feb 21, 2026</span>
                                </div>
                            </a>
                            <a href="single-news.php?article=halogen-video" class="mini-item">
                                <img src="assets/images/news-3.jpg" alt="">
                                <div>
                                    <h4>Shooting &ldquo;Halogen&rdquo; in one take</h4>
                                    <span>Feb 09, 2026</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </aside>
            </div>

            <div class="comments-block">
                <div class="section-head">
                    <div>
                        <span class="section-label">Discussion</span>
                        <h2 class="section-head__title" data-split="lines">Community <em>replies</em></h2>
                    </div>
                </div>

                <?php if ($commentSuccess): ?>
                    <div style="background: rgba(var(--accent-rgb), 0.15); border: 1px solid var(--accent); padding: 16px 20px; border-radius: 6px; margin-bottom: 28px; color: #fff;">
                        <i class="ri-checkbox-circle-line" style="color:var(--accent); margin-right:8px;"></i> Thank you! Your comment has been received and added to moderation queue.
                    </div>
                <?php endif; ?>

                <div class="comment-list">
                    <article class="comment">
                        <div class="comment__avatar"><img src="assets/images/team-1.jpg" alt=""></div>
                        <div>
                            <div class="comment__head">
                                <h3 class="comment__name">Dana Whitfield</h3>
                                <span class="comment__date">March 05, 2026</span>
                                <a href="#" class="comment__reply">Reply</a>
                            </div>
                            <p>
                                The rain under the second verse of side C is the best production decision of the
                                year. Please never clean it up on a remaster.
                            </p>
                        </div>
                    </article>

                    <article class="comment comment--reply">
                        <div class="comment__avatar"><img src="assets/images/team-2.jpg" alt=""></div>
                        <div>
                            <div class="comment__head">
                                <h3 class="comment__name">Ivo Lang</h3>
                                <span class="comment__date">March 05, 2026</span>
                                <a href="#" class="comment__reply">Reply</a>
                            </div>
                            <p>
                                We tried four times, Dana. It always sounded worse. The rain stays.
                            </p>
                        </div>
                    </article>

                    <article class="comment">
                        <div class="comment__avatar"><img src="assets/images/team-3.jpg" alt=""></div>
                        <div>
                            <div class="comment__head">
                                <h3 class="comment__name">Sofia Brandt</h3>
                                <span class="comment__date">March 07, 2026</span>
                                <a href="#" class="comment__reply">Reply</a>
                            </div>
                            <p>
                                Saw the Rotterdam show and half of this record live before it came out. Hearing
                                the tape version now, it makes complete sense why you kept the room in.
                            </p>
                        </div>
                    </article>
                </div>

                <div class="comment-form">
                    <h2 class="section-head__title" data-split="lines">Write a <em>comment</em></h2>

                    <form method="POST" action="">
                        <input type="hidden" name="comment_submit" value="1">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="field">
                                    <input type="text" name="name" id="comment-name" class="field__control" placeholder=" " required>
                                    <label for="comment-name" class="field__label">Your name</label>
                                    <span class="field__line" aria-hidden="true"></span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="field">
                                    <input type="email" name="email" id="comment-email" class="field__control" placeholder=" " required>
                                    <label for="comment-email" class="field__label">Your email</label>
                                    <span class="field__line" aria-hidden="true"></span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="field">
                                    <textarea name="comment" id="comment-body" class="field__control" placeholder=" " rows="5" required></textarea>
                                    <label for="comment-body" class="field__label">Comment</label>
                                    <span class="field__line" aria-hidden="true"></span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn--solid" data-magnetic="0.3"><span class="btn__label">Post comment <i class="ri-arrow-right-up-line"></i></span></button>
                    </form>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
