<?php
$pageTitle = 'Typography & Style Guide — vebra';
$pageDesc = 'The vebra type scale, colour tokens and component reference.';
$bodyClass = 'theme-magenta';
$activePage = 'more';
$activeSub = 'typography';
require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <!-- PAGE CAPTION
    ============================================= -->
    <section class="page-caption">
        <div class="container-vebra">
            <div class="page-caption__inner">
                <ol class="breadcrumb-vebra">
                    <li><a href="index.php">Home</a></li>
                    <li class="breadcrumb-vebra__sep"><i class="ri-arrow-right-s-line"></i></li>
                    <li aria-current="page">Typography</li>
                </ol>
                <h1 class="page-caption__title" data-split="lines" data-split-hero>
                    Typography
                    <span class="page-caption__ghost" aria-hidden="true">Typography</span>
                </h1>
                <p class="page-caption__text">The vebra type scale, colour tokens and core components in one place — the reference used to build every page of this template.</p>
            </div>
        </div>
    </section>


    <!-- TYPE SCALE
    ============================================= -->
    <section class="section">
        <div class="container-narrow">
            <div class="section-head">
                <div>
                    <span class="section-label">Type scale</span>
                    <h2 class="section-head__title" data-split="lines">Unbounded, Instrument Serif &amp; <em>Inter</em></h2>
                </div>
                <p class="section-head__text">Display and headings use Unbounded; italic accent words use Instrument Serif; body copy and UI use Inter. Every size is fluid.</p>
            </div>

            <div class="type-specimen" data-reveal-group data-stagger="0.05">
                <div class="type-row">
                    <span class="type-row__label">Display</span>
                    <div><span class="display">Nightform</span></div>
                </div>
                <div class="type-row">
                    <span class="type-row__label">Heading 1</span>
                    <div><h1>Every room is a studio</h1></div>
                </div>
                <div class="type-row">
                    <span class="type-row__label">Heading 2</span>
                    <div><h2>Six people, one <em>room</em></h2></div>
                </div>
                <div class="type-row">
                    <span class="type-row__label">Heading 3</span>
                    <div><h3>On the road in 2026</h3></div>
                </div>
                <div class="type-row">
                    <span class="type-row__label">Heading 4</span>
                    <div><h4>Upcoming gigs</h4></div>
                </div>
                <div class="type-row">
                    <span class="type-row__label">Heading 5</span>
                    <div><h5>Latest released</h5></div>
                </div>
                <div class="type-row">
                    <span class="type-row__label">Heading 6</span>
                    <div><h6>Track listing</h6></div>
                </div>
                <div class="type-row">
                    <span class="type-row__label">Lead</span>
                    <div><p class="lead">Eleven tracks recorded live to tape across one Brooklyn winter.</p></div>
                </div>
                <div class="type-row">
                    <span class="type-row__label">Body</span>
                    <div><p>vebra formed in a Bushwick basement in 2016 and never really left. Three albums later the method hasn&rsquo;t changed: everyone in one room, tape running, first or second take.</p></div>
                </div>
                <div class="type-row">
                    <span class="type-row__label">Small</span>
                    <div><p><small>Recorded at Rivington Street Studios, New York.</small></p></div>
                </div>
                <div class="type-row">
                    <span class="type-row__label">Label</span>
                    <div><span class="label">Section label</span></div>
                </div>
                <div class="type-row">
                    <span class="type-row__label">Serif accent</span>
                    <div><span class="accent-serif" style="font-size:2rem">Instrument Serif italic</span></div>
                </div>
            </div>
        </div>
    </section>


    <!-- COLOUR TOKENS
    ============================================= -->
    <section class="section section--alt">
        <div class="container-narrow">
            <div class="section-head">
                <div>
                    <span class="section-label">Colour</span>
                    <h2 class="section-head__title" data-split="lines">Dark-first <em>palette</em></h2>
                </div>
                <p class="section-head__text">Every colour is a CSS custom property. The core brand theme is <code>.theme-magenta</code> (Hot Pink/Magenta glow), fully unified across the platform.</p>
            </div>

            <div class="swatch-grid" data-reveal-group data-stagger="0.04">
                <div class="swatch-chip">
                    <span style="background:var(--bg-0)"></span>
                    <span>Base</span>
                </div>
                <div class="swatch-chip">
                    <span style="background:var(--bg-2)"></span>
                    <span>Surface</span>
                </div>
                <div class="swatch-chip">
                    <span style="background:var(--bg-3)"></span>
                    <span>Elevated</span>
                </div>
                <div class="swatch-chip">
                    <span style="background:var(--ink-100)"></span>
                    <span>Ink</span>
                </div>
                <div class="swatch-chip">
                    <span style="background:var(--ink-40)"></span>
                    <span>Muted</span>
                </div>
                <div class="swatch-chip">
                    <span style="background:var(--accent)"></span>
                    <span>Accent</span>
                </div>
                <div class="swatch-chip">
                    <span style="background:var(--accent-2)"></span>
                    <span>Accent 2</span>
                </div>
                <div class="swatch-chip">
                    <span style="background:var(--accent-warm)"></span>
                    <span>Warm</span>
                </div>
            </div>

            <h3 class="mt-8 mb-4">Accent themes</h3>
            <div class="swatch-grid" data-reveal-group data-stagger="0.04">
                <div class="swatch-chip theme-magenta">
                    <span style="background:var(--grad-accent)"></span>
                    <span>theme-magenta (Vebra Core)</span>
                </div>
                <div class="swatch-chip theme-violet">
                    <span style="background:var(--grad-accent)"></span>
                    <span>theme-violet</span>
                </div>
                <div class="swatch-chip theme-crimson">
                    <span style="background:var(--grad-accent)"></span>
                    <span>theme-crimson</span>
                </div>
                <div class="swatch-chip theme-amber">
                    <span style="background:var(--grad-accent)"></span>
                    <span>theme-amber</span>
                </div>
                <div class="swatch-chip theme-cyan">
                    <span style="background:var(--grad-accent)"></span>
                    <span>theme-cyan</span>
                </div>
                <div class="swatch-chip theme-chartreuse">
                    <span style="background:var(--grad-accent)"></span>
                    <span>theme-chartreuse</span>
                </div>
                <div class="swatch-chip theme-mono">
                    <span style="background:var(--grad-accent)"></span>
                    <span>theme-mono</span>
                </div>
                <div class="swatch-chip theme-gold">
                    <span style="background:var(--grad-accent)"></span>
                    <span>theme-gold</span>
                </div>
            </div>
        </div>
    </section>


    <!-- COMPONENTS
    ============================================= -->
    <section class="section">
        <div class="container-narrow">
            <div class="section-head">
                <div>
                    <span class="section-label">Components</span>
                    <h2 class="section-head__title" data-split="lines">Buttons, pills &amp; <em>fields</em></h2>
                </div>
            </div>

            <div class="stack stack-6">
                <div>
                    <h3 class="mb-4">Buttons</h3>
                    <div class="cluster cluster-4">
                        <a href="#" class="btn" data-magnetic="0.3"><span class="btn__label">Default</span></a>
                        <a href="#" class="btn btn--solid" data-magnetic="0.3"><span class="btn__label">Solid <i class="ri-arrow-right-up-line"></i></span></a>
                        <a href="#" class="btn btn--light" data-magnetic="0.3"><span class="btn__label">Light</span></a>
                        <a href="#" class="btn btn--glass" data-magnetic="0.3"><span class="btn__label">Glass</span></a>
                        <a href="#" class="btn btn--ghost"><span class="btn__label">Ghost</span></a>
                        <a href="#" class="link-arrow">Text link <i class="ri-arrow-right-up-line"></i></a>
                    </div>
                </div>

                <div>
                    <h3 class="mb-4">Circular &amp; play</h3>
                    <div class="cluster cluster-5">
                        <button type="button" class="btn-circle" aria-label="Arrow"><i class="ri-arrow-right-up-line"></i></button>
                        <button type="button" class="btn-circle btn-circle--solid" aria-label="Play"><i class="ri-play-fill"></i></button>
                        <button type="button" class="btn-circle btn-circle--glass" aria-label="Close"><i class="ri-close-line"></i></button>
                        <button type="button" class="btn-play" aria-label="Play video"><i class="ri-play-fill"></i></button>
                    </div>
                </div>

                <div>
                    <h3 class="mb-4">Pills &amp; socials</h3>
                    <div class="cluster cluster-4">
                        <span class="pill">Default</span>
                        <span class="pill pill--accent">Accent</span>
                        <span class="pill pill--solid">Solid</span>
                        <div class="socials">
                            <a href="#" aria-label="Facebook"><i class="ri-facebook-fill"></i></a>
                            <a href="#" aria-label="Instagram"><i class="ri-instagram-line"></i></a>
                            <a href="#" aria-label="Spotify"><i class="ri-spotify-fill"></i></a>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="mb-4">Form fields</h3>
                    <div class="contact-form">
                        <div class="row gx-4">
                            <div class="col-md-6">
                                <label class="field">
                                    <input type="text" class="field__control" name="name" placeholder=" " required>
                                    <span class="field__label">Your name</span>
                                    <span class="field__line"></span>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="field">
                                    <input type="email" class="field__control" name="email" placeholder=" " required>
                                    <span class="field__label">Email address</span>
                                    <span class="field__line"></span>
                                </label>
                            </div>
                        </div>
                        <label class="field">
                            <textarea class="field__control" name="message" placeholder=" " required></textarea>
                            <span class="field__label">Message</span>
                            <span class="field__line"></span>
                        </label>
                        <label class="check">
                            <input type="checkbox" required>
                            <span class="check__box" aria-hidden="true"></span>
                            <span>I agree to be contacted about this enquiry.</span>
                        </label>
                        <div class="mt-5">
                            <button type="button" class="btn btn--solid" data-magnetic="0.3"><span class="btn__label">Send message <i class="ri-arrow-right-up-line"></i></span></button>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="mb-4">Rich text</h3>
                    <div class="rich-text measure">
                        <p>Body copy sits at a comfortable measure with generous leading. <a href="#">Inline links</a> carry an accent underline that wipes away on hover.</p>
                        <ul>
                            <li>Unordered lists use an accent dot marker.</li>
                            <li>Ordered lists use leading-zero numerals.</li>
                        </ul>
                        <blockquote>Silence is the loudest instrument in the room.</blockquote>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
