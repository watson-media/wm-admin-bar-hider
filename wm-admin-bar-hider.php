<?php
// WM Admin Bar Hider Admin CSS
function wm_admin_bar_hider_css() {
    ob_start();
    wp_enqueue_style( 'wm_admin_bar_hider_admin_css', plugin_dir_url( __FILE__ ) . 'assets/wm-admin-bar-hider.css', false, '1.0.0' );
}
add_action( 'admin_enqueue_scripts', 'wm_admin_bar_hider_css' );

function wm_admin_bar_hider_settings() {
    add_options_page(
        'WM Admin Bar Hider',
        'WM Admin Bar Hider',
        'manage_options',
        'wm-admin-bar-hider',
        'wm_admin_bar_hider_options'
    );
}
add_action('admin_menu', 'wm_admin_bar_hider_settings');

function wm_admin_bar_hider_options() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    if (isset($_POST['submit'])) {
        update_option('wm_hide_admin_bar', $_POST['wm_hide_admin_bar']);
        update_option('wm_hide_admin_bar_pages', $_POST['wm_hide_admin_bar_pages']);
        update_option('wm_hide_admin_bar_roles', $_POST['wm_hide_admin_bar_roles']);
        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
    }

    $hide_admin_bar = get_option('wm_hide_admin_bar', 'no');
    $selected_page_ids = get_option('wm_hide_admin_bar_pages', array());

    // Get all existing pages
    $pages = get_pages();

    global $wp_roles;
    $roles = $wp_roles->get_names();
    $selected_roles = get_option('wm_hide_admin_bar_roles', array());

    ?>

    <div class="wrap wm_magic_section">
        <h1 class="big-title">
            <span>WM Admin Bar Hider</span><span class="text-light text-subtitle">Version: 1.0.2</span>
        </h1>
        <form method="post">
            <div class="card">
                <h2 class="title">Hide Admin Bar for everyone & everywhere</h2>
                <table>
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="wm_hide_admin_bar">Hide Admin bar everywhere on the frontend for all users:</label>
                            </th>
                            <td>
                                <select name="wm_hide_admin_bar" id="wm_hide_admin_bar">
                                    <option value="yes" <?php selected($hide_admin_bar, 'yes'); ?>>Yes</option>
                                    <option value="no" <?php selected($hide_admin_bar, 'no'); ?>>No</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card">
                <h2 class="title">Hide Admin Bar for selected pages</h2>
                <table class="wm_full_width_table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="wm_hide_admin_bar_pages">Select the pages where admin bar should be hidden:</label>
                                <p class="wm_left_text">Note: If the Above option is selected to "Yes", this option will be ignored.</p>
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <select name="wm_hide_admin_bar_pages[]" id="wm_hide_admin_bar_pages" multiple>
                                    <?php foreach ($pages as $page) : ?>
                                    <?php if (is_array($selected_page_ids) && in_array($page->ID, $selected_page_ids)){
                                            $selected_attribute = 'selected';
                                        } else {
                                            $selected_attribute = '';
                                        }
                                    ?>
                                    <option value="<?php echo $page->ID; ?>" <?php echo $selected_attribute; ?>><?php echo $page->post_title; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card">
                <h2 class="title">Hide Admin Bar for selected user roles</h2>
                <table class="wm_full_width_table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="wm_hide_admin_bar_roles">Select the user roles where admin bar should be hidden:</label>
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <select name="wm_hide_admin_bar_roles[]" id="wm_hide_admin_bar_roles" multiple>
                                    <?php foreach ($roles as $role_key => $role_name) : ?>
                                        <?php $selected = in_array($role_key, $selected_roles) ? 'selected' : ''; ?>
                                <option value="<?php echo esc_attr($role_key); ?>" <?php echo $selected; ?>>        <?php echo esc_html($role_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
<?php
}

// Use 'wp' action hook instead of 'init'
add_action('wp', 'wm_hide_admin_bar_frontend');

function wm_hide_admin_bar_frontend() {
    $current_user = wp_get_current_user();
    $hide_admin_bar = get_option('wm_hide_admin_bar', 'no');
    $selected_page_ids = get_option('wm_hide_admin_bar_pages', array());
    $selected_roles = get_option('wm_hide_admin_bar_roles', array());

    if (in_array('administrator', $current_user->roles)) {
        if ($hide_admin_bar === 'yes') {
            add_filter('show_admin_bar', '__return_false');
        } else {
            $current_page_id = get_the_ID();
            if (is_array($selected_page_ids) && in_array($current_page_id, $selected_page_ids)) {
                add_filter('show_admin_bar', '__return_false');
            }
        }
    } elseif (count(array_intersect($current_user->roles, $selected_roles)) > 0) {
        add_filter('show_admin_bar', '__return_false');
    }
}