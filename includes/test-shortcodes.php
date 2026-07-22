<?php
/**
 * Test shortcodes for SimpleCMP — real embeds that won't be stripped by WP filters.
 */

// Register shortcodes on init
add_action('init', function() {
    add_shortcode('simplecmp_test', 'simplecmp_test_embed');
    add_shortcode('simplecmp_tracker', 'simplecmp_test_tracker');
});

// Register shortcodes + inject responsive embed CSS
add_action('wp_head', function() {
    echo '<style>
    .simplecmp-embed-wrapper { max-width:800px; margin:1.5em 0; }
    .simplecmp-embed-wrapper iframe { display:block; width:100%; height:auto; aspect-ratio:16/9; border:0; }
    .simplecmp-embed-wrapper.maps iframe { aspect-ratio:4/3; }
    .simplecmp-embed-wrapper.spotify { max-width:400px; }
    .simplecmp-embed-wrapper.spotify iframe { aspect-ratio:auto; height:380px; border-radius:12px; }
    </style>';
});

function simplecmp_test_embed($atts) {
    $atts = shortcode_atts(['type' => 'youtube'], $atts);
    $type = $atts['type'];

    // CSS-based responsive embeds: wrapper handles all sizing via CSS classes.
    // No multi-property inline styles on iframes (engine truncates them).
    switch ($type) {
        case 'youtube':
            return '<div class="simplecmp-embed-wrapper"><iframe width="1280" height="720" '
                . 'src="https://www.youtube.com/embed/dQw4w9WgXcQ" '
                . 'title="YouTube video player" frameborder="0" '
                . 'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" '
                . 'allowfullscreen></iframe></div>';

        case 'vimeo':
            return '<div class="simplecmp-embed-wrapper"><iframe width="1280" height="720" '
                . 'src="https://player.vimeo.com/video/76979871" '
                . 'title="Vimeo video player" frameborder="0" '
                . 'allow="autoplay; fullscreen; picture-in-picture" '
                . 'allowfullscreen></iframe></div>';

        case 'googlemaps':
            return '<div class="simplecmp-embed-wrapper maps"><iframe width="600" height="450" '
                . 'src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2427.123456789!2d13.404954!3d52.520008!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sBerlin!5e0!3m2!1sde!2sde!4v1234567890" '
                . 'title="Google Maps" allowfullscreen="" loading="lazy"></iframe></div>';

        case 'spotify':
            return '<div class="simplecmp-embed-wrapper spotify"><iframe '
                . 'src="https://open.spotify.com/embed/track/4cOdK2wGLETKBW3PvgPWqT" '
                . 'width="100%" height="380" frameborder="0" '
                . 'allowtransparency="true" allow="encrypted-media"></iframe></div>';

        default:
            return '<p>Unknown embed type: ' . esc_html($type) . '</p>';
    }
}

function simplecmp_test_tracker($atts) {
    $atts = shortcode_atts(['type' => 'ga4'], $atts);
    $type = $atts['type'];

    switch ($type) {
        case 'ga4':
            // Wird vom Universal Blocker umgeschrieben → src="about:blank"
            return '<script src="https://www.googletagmanager.com/gtag/js?id=G-TEST123"></script>';

        case 'facebook':
            return '<script src="https://connect.facebook.net/en_US/fbevents.js"></script>';

        case 'hotjar':
            return '<script src="https://static.hotjar.com/c/hotjar-123456.js"></script>';

        default:
            return '<p>Unknown tracker: ' . esc_html($type) . '</p>';
    }
}
