<?php
/**
 * Plugin Name: Members CPT
 * Plugin URI:  https://yourwebsite.com
 * Description: A custom post type for managing members.
 * Version: 1.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL2
 * Text Domain: members-cpt
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Register Members Custom Post Type
function create_members_cpt() {
    $labels = array(
        'name'               => _x('Members', 'Post Type General Name', 'members-cpt'),
        'singular_name'      => _x('Member', 'Post Type Singular Name', 'members-cpt'),
        'menu_name'          => __('Members', 'members-cpt'),
        'name_admin_bar'     => __('Member', 'members-cpt'),
        'add_new'            => __('Add New', 'members-cpt'),
        'add_new_item'       => __('Add New Member', 'members-cpt'),
        'new_item'           => __('New Member', 'members-cpt'),
        'edit_item'          => __('Edit Member', 'members-cpt'),
        'view_item'          => __('View Member', 'members-cpt'),
        'all_items'          => __('All Members', 'members-cpt'),
        'search_items'       => __('Search Members', 'members-cpt'),
        'not_found'          => __('No members found.', 'members-cpt'),
        'not_found_in_trash' => __('No members found in Trash.', 'members-cpt'),
    );

    $args = array(
        'label'               => __('Members', 'members-cpt'),
        'description'         => __('A custom post type for managing members.', 'members-cpt'),
        'labels'              => $labels,
        'public'              => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-groups',
        'supports'            => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'has_archive'         => true,
        'rewrite'             => array('slug' => 'members'),
        'show_in_rest'        => true, // Enables Gutenberg support
    );

    register_post_type('members', $args);
}
add_action('init', 'create_members_cpt');

// Register Custom Taxonomy for Members CPT
function members_register_taxonomy() {
    $labels = array(
        'name'              => 'Member Categories',
        'singular_name'     => 'Member Category',
        'search_items'      => 'Search Categories',
        'all_items'         => 'All Categories',
        'parent_item'       => 'Parent Category',
        'parent_item_colon' => 'Parent Category:',
        'edit_item'         => 'Edit Category',
        'update_item'       => 'Update Category',
        'add_new_item'      => 'Add New Category',
        'new_item_name'     => 'New Category Name',
        'menu_name'         => 'Categories',
    );

    $args = array(
        'hierarchical'      => true, // Works like normal categories
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'member-category'),
    );

    register_taxonomy('member_category', 'members', $args);
}
add_action('init', 'members_register_taxonomy');

// Flush rewrite rules on activation
function members_cpt_activate() {
    create_members_cpt();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'members_cpt_activate');

// Flush rewrite rules on deactivation
function members_cpt_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'members_cpt_deactivate');

// Add Custom Meta Box for Members CPT
function members_add_meta_box() {
    add_meta_box(
        'members_details_meta_box', 
        'Member Details', 
        'members_meta_box_callback', 
        'members', 
        'normal', 
        'high'
    );
}
add_action('add_meta_boxes', 'members_add_meta_box');

// Meta Box Callback Function
function members_meta_box_callback($post) {
    // Get saved values
    $bio = get_post_meta($post->ID, 'member_bio', true);
    $email = get_post_meta($post->ID, 'member_email', true);
    $phone = get_post_meta($post->ID, 'member_phone', true);

    // Security nonce
    wp_nonce_field('members_save_meta_box_data', 'members_meta_box_nonce');

    ?>
    <p>
        <label for="member_role">bio:</label>
        <textarea id="member_bio" name="member_bio" style="width:100%;"> <?php echo esc_attr($bio); ?> </textarea>
    </p>   
    <p>
        <label for="member_email">Email:</label>
        <input type="email" id="member_email" name="member_email" value="<?php echo esc_attr($email); ?>" style="width:100%;" />
    </p>   
    <p>
        <label for="member_phone">Phone</label>
        <input type="tel" id="member_phone" name="member_phone" value="<?php echo esc_attr($phone); ?>" style="width:100%;" />
    </p>
    <?php
}

// Save Custom Field Data
function members_save_meta_box_data($post_id) {
    // Verify nonce
    if (!isset($_POST['members_meta_box_nonce']) || !wp_verify_nonce($_POST['members_meta_box_nonce'], 'members_save_meta_box_data')) {
        return;
    }

    // Prevent autosave from overriding values
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save or update fields
    if (isset($_POST['member_bio'])) {
        update_post_meta($post_id, 'member_bio', sanitize_text_field($_POST['member_bio']));
    }   
    if (isset($_POST['member_email'])) {
        update_post_meta($post_id, 'member_email', sanitize_email($_POST['member_email']));
    } 
    if (isset($_POST['member_phone'])) {
        update_post_meta($post_id, 'member_phone', sanitize_text_field($_POST['member_phone']));
    }
}
add_action('save_post', 'members_save_meta_box_data');

// Shortcode to Display Members List
function members_display_shortcode($atts) {
    // Extract attributes (if any)
    $atts = shortcode_atts(array(
        'count' => -1, // Default: Show all members
    ), $atts, 'members_list');

    // Query Members CPT
    $args = array(
        'post_type'      => 'members',
        'posts_per_page' => intval($atts['count']),
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC'
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $output = '<div class="members-list">';
    
        while ($query->have_posts()) {
            $query->the_post();
            
            // Get member data
            $bio = get_post_meta(get_the_ID(), 'member_bio', true);
            $email = get_post_meta(get_the_ID(), 'member_email', true);
            $phone = get_post_meta(get_the_ID(), 'member_phone', true);
            $thumbnail = get_the_post_thumbnail(get_the_ID(), 'thumbnail', array('class' => 'member-thumbnail'));

            // Get Member Categories
            $categories = get_the_terms(get_the_ID(), 'member_category');
            $category_names = ($categories && !is_wp_error($categories)) ? wp_list_pluck($categories, 'name') : [];
            $category_list = !empty($category_names) ? implode(', ', $category_names) : 'No Category';

            //Assign CPT Terms
            if( $categories && !is_wp_error($categories)) {
                $category_link = [];

                foreach ($categories as $category) {
                    $category_links[] = '<a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a>';
                }

                $category_list = implode(', ', $category_links); 
            } else { 
                $category_list = 'Uncategorized';
            }

            // Start member item
            $output .= <<<HTML
            <div class="member-item">
    HTML;
    
            // Display Thumbnail if available
            if ($thumbnail) {
                $output .= <<<HTML
                <div class="member-photo">{$thumbnail}</div>
    HTML;
            }
    
            // Member Name
            $title = esc_html(get_the_title());
            $permalink = esc_url(get_permalink());
            $output .= <<<HTML
                <a href="{$permalink}"><h3>{$title}</h3></a>
    HTML;

            // Display Category
            // $catlink = esc_url(get_category_link());
            $output .= <<<HTML                
            <p>{$category_list}</p>
            HTML;        
                
            // Display Bio if available
            if (!empty($bio)) {
                $bio_escaped = esc_html($bio);
                $output .= <<<HTML
                <p>{$bio_escaped}</p>
    HTML;
            }
    
            // Display Email if available
            if (!empty($email)) {
                $email_escaped = esc_attr($email);
                $output .= <<<HTML
                <p><a href="mailto:{$email_escaped}">{$email_escaped}</a></p>
    HTML;
            }
    
            // Display Phone if available
            if (!empty($phone)) {
                $phone_escaped = esc_attr($phone);
                $output .= <<<HTML
                <p><a href="tel:{$phone_escaped}">{$phone_escaped}</a></p>
    HTML;
            }
    
            // Close member item
            $output .= <<<HTML
            </div>
    HTML;
        }
    
        $output .= '</div>'; // Close members list
        wp_reset_postdata(); // Reset post data
    } else {
        $output = '<p>No members found.</p>';
    }

    return $output;
}
add_shortcode('members_list', 'members_display_shortcode');

// Override Single Template for custom post type
function custom_members_single_template($single_template) {
    global $post;

    if ($post->post_type == 'members') {
        return plugin_dir_path(__FILE__) . 'templates/single-members.php';
    }

    return $single_template;
}
add_filter('single_template', 'custom_members_single_template');



?>


