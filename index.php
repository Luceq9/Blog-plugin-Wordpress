<?php
/*
Plugin Name: Blog Plugin
Description: Wtyczka dodająca funkcje bloga, takie jak shortcode’y, meta pola i ustawienia.
Author: LuceQ
Version: 1.0
*/

add_action('admin_menu', 'blog_plugin_add_settings_page');
add_action('admin_init', 'blog_plugin_register_settings');
add_shortcode('blog_posts', 'blog_plugin_display_posts_shortcode');


function blog_plugin_add_settings_page() {
    add_options_page(
        'Ustawienia Bloga',       // Tytuł strony
        'Blog Plugin',            // Tytuł, który pojawi się w menu
        'manage_options',         // Wymagane uprawnienia
        'blog_plugin',            // Unikalny identyfikator slug
        'blog_plugin_render_settings_page' // Funkcja wyświetlająca stronę
    );
}

function blog_plugin_render_settings_page() {
    ?>
    <div class="wrap">
        <h2>Ustawienia Bloga</h2>
        <form action="options.php" method="post">
            <?php
            settings_fields('blog_plugin_options');
            do_settings_sections('blog_plugin');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function blog_plugin_register_settings() {
    register_setting('blog_plugin_options_group', 'blog_plugin_post_count');
    
    add_settings_section(
        'blog_plugin_main_section',   // ID sekcji
        'Główne ustawienia',          // Tytuł sekcji
        null,                         // Funkcja wyświetlająca sekcję (tutaj zostawiamy puste)
        'blog_plugin'                 // Slug strony, na której sekcja ma być wyświetlona
    );
    
    add_settings_field(
        'blog_plugin_post_count',     // ID pola
        'Liczba postów na stronie',   // Etykieta wyświetlana dla pola
        'blog_plugin_post_count_callback', // Funkcja, która generuje pole
        'blog_plugin',                // Slug strony, na której pole jest wyświetlane
        'blog_plugin_main_section'    // Sekcja, do której należy pole
    );
}


function blog_plugin_post_count_callback() {
    $post_count = get_option('blog_plugin_post_count', 5);
    echo "<input type='number' name='blog_plugin_post_count' value='" . esc_attr($post_count) . "' />";
}


function blog_plugin_display_posts_shortcode($atts) {
    $post_count = get_option('blog_plugin_post_count', 5);

    $query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => intval($post_count),
    ));

    $output = '<div class="blog-posts">';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $output .= '<div class="post-item">';
            $output .= '<h2><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';
            $output .= '<p>' . get_the_excerpt() . '</p>';
            $output .= '</div>';
        }
    } else {
        $output .= '<p>Brak postów do wyświetlenia.</p>';
    }

    $output .= '</div>';

    wp_reset_postdata();

    return $output;
}
