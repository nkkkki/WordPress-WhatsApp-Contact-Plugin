<?php
/*
Plugin Name: WhatsApp Contact Button
Description: Adds a WhatsApp icon to the website that allows users to contact the admin via WhatsApp.
Version: 1.2
Author: Nika Nemsitsveridze
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add settings for WhatsApp number in the admin panel
add_action('admin_menu', 'wp_whatsapp_contact_menu');
function wp_whatsapp_contact_menu() {
    add_menu_page(
        'WhatsApp Contact Settings',
        'WhatsApp Contact',
        'manage_options',
        'wp-whatsapp-contact-settings',
        'wp_whatsapp_contact_settings_page',
        'dashicons-whatsapp', // Icon in admin menu
        100
    );
}

// Create the settings page content
function wp_whatsapp_contact_settings_page() {
    ?>
    <div class="wrap">
        <h1>WhatsApp Contact Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wp_whatsapp_contact_settings_group');
            do_settings_sections('wp-whatsapp-contact-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings
add_action('admin_init', 'wp_whatsapp_contact_register_settings');
function wp_whatsapp_contact_register_settings() {
    register_setting('wp_whatsapp_contact_settings_group', 'wp_whatsapp_contact_number');

    add_settings_section(
        'wp_whatsapp_contact_section',
        'WhatsApp Contact Settings',
        null,
        'wp-whatsapp-contact-settings'
    );

    add_settings_field(
        'wp_whatsapp_contact_number',
        'WhatsApp Number',
        'wp_whatsapp_contact_number_field_callback',
        'wp-whatsapp-contact-settings',
        'wp_whatsapp_contact_section'
    );
}

function wp_whatsapp_contact_number_field_callback() {
    $whatsapp_number = esc_attr(get_option('wp_whatsapp_contact_number', ''));
    echo '<input type="text" name="wp_whatsapp_contact_number" value="' . $whatsapp_number . '" placeholder="+123456789" />';
}

// Add the WhatsApp button to all pages
add_action('wp_footer', 'wp_add_whatsapp_button');
function wp_add_whatsapp_button() {
    $whatsapp_number = get_option('wp_whatsapp_contact_number', '');
    if (empty($whatsapp_number)) {
        return;
    }

    // WhatsApp URL
    $whatsapp_url = 'https://wa.me/' . esc_attr($whatsapp_number);

    // HTML for the WhatsApp button
    echo '
    <style>
        #wp-whatsapp-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #25d366;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.3);
            z-index: 999;
            cursor: pointer;
        }
        #wp-whatsapp-btn img {
            width: 35px;
            height: 35px;
        }
    </style>

    <a href="' . $whatsapp_url . '" id="wp-whatsapp-btn" target="_blank">
        <img src="' . plugin_dir_url(__FILE__) . 'assets/whatsapp-icon.png" alt="WhatsApp">
    </a>
    ';
}

// Ensure the button shows up on all page types
add_action('template_redirect', 'wp_enqueue_whatsapp_button');
function wp_enqueue_whatsapp_button() {
    if (is_home() || is_front_page()) {
        add_action('wp_footer', 'wp_add_whatsapp_button');
    }
}

// Add assets (icon) for the button
add_action('plugins_loaded', 'wp_whatsapp_contact_load_assets');
function wp_whatsapp_contact_load_assets() {
    if (!file_exists(plugin_dir_path(__FILE__) . 'assets')) {
        mkdir(plugin_dir_path(__FILE__) . 'assets', 0755, true);
    }

    // WhatsApp icon (you can replace it with any icon of your choice)
    $icon_url = 'https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg';
    $icon_path = plugin_dir_path(__FILE__) . 'assets/whatsapp-icon.png';

    if (!file_exists($icon_path)) {
        $icon_data = file_get_contents($icon_url);
        file_put_contents($icon_path, $icon_data);
    }
}
