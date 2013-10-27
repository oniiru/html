<?php
/*
Plugin Name: Username Changer
Description: Lets you <a href='users.php?page=username_changer'>change usernames</a>. 
Version: 1.4
Author: Daniel J Griffiths
Author URI: http://www.ghost1227.com
*/

// Check if class already exists
if (!class_exists('wp_username_changer')) {

    // Add link to users.php    
    add_filter( 'user_row_actions', 'username_changer_link', 10, 2 );
    function username_changer_link( $actions, $user ) {
        if(current_user_can('edit_users'))
            $actions[] = '<a href="' . admin_url('users.php?page=username_changer&id=' . $user->ID) . '">Change Username</a>';
        return $actions;
    }

    class wp_username_changer {
        // Add admin menu item
        function wp_username_changer() {
            add_action('admin_menu', array(&$this, 'add_admin_menus'));
        }
    
        function add_admin_menus() {
            //only admin-level users with the edit_users capability can change their username
            add_submenu_page('users.php', 'Username Changer', 'Username Changer', 'edit_users', 'username_changer', array(&$this, 'username_changer_page'));
        }

        // Display page
        function username_changer_page() {
            global $wpdb, $userdata, $current_user;
            get_currentuserinfo();

            // Update loop
            if (!empty($_POST['action']) && ($_POST['action']=='update') && !empty($_POST['new_username']) && !empty($_POST['current_username']) ){
                $new_username = $wpdb->escape($_POST['new_username']);
                $current_username = $wpdb->escape($_POST['current_username']);

                // Make sure username exists, just in case
                if (username_exists($current_username) && ($new_username == $current_username)) {
                    echo "<div id='message' class='error'><p><strong>'Current Username' and 'New Username' cannot both be '$new_username'!</strong></p></div>";
                // Make sure new username doesn't exist
                } elseif (username_exists($current_username) && (username_exists($new_username))) {
                    echo "<div id='message' class='error'><p><strong>'$current_username' cannot be changed to '$new_username', '$new_username' already exists!</strong></p></div>";
                } elseif (username_exists($current_username) && ($new_username != $current_username)) {
					$q = "UPDATE $wpdb->users SET user_login='" . esc_attr($new_username) . "' WHERE user_login='" . esc_attr($current_username) . "'";
					$qnn = "UPDATE $wpdb->users SET user_nicename='" . esc_attr($new_username) . "' WHERE user_login='" . esc_attr($new_username) . "'";

                    // Check if display name is the same as username
                    $usersql = "SELECT * from $wpdb->users WHERE user_login=\"" . esc_attr($current_username) . "\"";
                    $userinfo = $wpdb->get_row($usersql);

                    // If display name is the same as username, update both
                    if ($current_username == $userinfo->display_name) {
                        $qdn = "UPDATE $wpdb->users SET display_name='" . esc_attr($new_username) . "' WHERE user_login='" . esc_attr($new_username) . "'";
                    }

					if (false !== $wpdb->query($q)){
						$wpdb->query($qnn);
                        if(isset($qdn)){
                            $wpdb->query($qdn);
                        }

                        // If changing own username, display link to re-login
                        if($current_user->user_login == $current_username) {
                        echo "<div id='message' class='updated fade'><p><strong>Username '$current_username' was changed to '$new_username'.&nbsp;&nbsp;Click <a href='" . wp_login_url() . "'>here</a> to log back in.</strong></p></div>";
                        } else {
                            echo "<div id='message' class='updated fade'><p><strong>Username '$current_username' was changed to '$new_username'.</strong></p></div>";
                        }
                    } else {
                        // If database error occurred, display it
                        echo "<div id='message' class='error'><p><strong>A database error occured : $wpdb->last_error</strong></p></div>";
                    }
                } else {
                    // Warn if username doesn't exist (this should never happen)
                    echo "<div id='message' class='error'><p><strong>Username '$current_username' doesn't exist!</strong></p></div>";
                }
                // All fields are required
            } elseif (($_POST['action']=='update') && (empty($_POST['new_username']) || empty($_POST['current_username']))) {
                echo "<div id='message' class='error'><p><strong>Both 'Current Username' and 'New Username' fields are required!</strong></p></div>";            
            } ?>

            <div class="wrap">
                <h2>Username Changer</h2>
                <br />
                <form name="username_changer" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=username_changer">
                    <input type='hidden' name='action' value='update' />
                    <table style="width: 759px" cellpadding="5" class="widefat post fixed">
                        <thead>
                            <tr>
                                <?php if($_REQUEST['id']!='') { ?>
                                    <?php $usersql = "SELECT * from $wpdb->users where ID=" . $_REQUEST['id'];
                                        $userinfo = $wpdb->get_row($usersql); ?>
                                    <th><strong>Rename user to what?</strong></th>
                                <?php } else { ?>
                                    <th><strong>Edit which user?</strong></th>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td>
                                    <label for="current_username">
                                        <strong>Current Username</strong>
                                        <?php if($_REQUEST['id']!='') { ?>
                                            <?php $usersql = "SELECT * from $wpdb->users where ID=" . $_REQUEST['id'];
                                                $userinfo = $wpdb->get_row($usersql); ?>
                                            <input name="current_username" id="current_username" type="text" value="<?php echo $userinfo->user_login; ?>" size="30" readonly />
                                        <?php } else { ?>
                                            <select name="current_username" id="current_username">
                                                <option value=""></option>
                                                <?php $usersql = "SELECT * from $wpdb->users order by user_login asc";
                                                    $userinfo = $wpdb->get_results($usersql);
                                                    if($userinfo) {
                                                        foreach($userinfo as $userinfoObj) { ?>
                                                            <option value="<?php echo $userinfoObj->user_login; ?>"><?php echo $userinfoObj->user_login; ?> (<?php echo $userinfoObj->user_email; ?>)</option>
                                                        <?php }
                                                    } ?>
                                            </select>
                                        <?php } ?>
                                    </label>
                                    <br />
                                    <label for="new_username">
                                        <strong style="padding-right: 18px;">New Username</strong>
                                        <input name="new_username" id="new_username" type="text" value="" size="30" />
                                    </label>
                                    <div style="float: right;">
                                        <input type="submit" name="submit" class="button-secondary action" value="Save Changes" />
                                    </div>
                                </td>
                            </tr>
                        </thead>
                    </table>
                </form>
            </div>
        <?php }
    }
}

$wp_cau = new wp_username_changer;

?>
