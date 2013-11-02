<?php
require_once 'includes/ce/Library.php';

function motopressCEAddTools() {
    require_once 'includes/ce/Access.php';
    $ceAccess = new MPCEAccess();

    new MPCELibrary();

    $postType = get_post_type();
    $postTypes = get_option('motopress-ce-options');
    if (!$postTypes) $postTypes = array();

    if (in_array($postType, $postTypes) && post_type_supports($postType, 'editor') && $ceAccess->hasAccess()) {
        global $motopressCESettings;
        wp_localize_script('jquery', 'motopress', $motopressCESettings['motopress_localize']);
        wp_localize_script('jquery', 'motopressCE',
            array(
                'postID' => get_the_ID(),
//                'postPreviewUrl' => post_preview(),
                'nonces' => array(
                    'motopress_ce_get_wp_settings' => wp_create_nonce('wp_ajax_motopress_ce_get_wp_settings'),
                    'motopress_ce_render_content' => wp_create_nonce('wp_ajax_motopress_ce_render_content'),
                    'motopress_ce_remove_temporary_post' => wp_create_nonce('wp_ajax_motopress_ce_remove_temporary_post'),
                    'motopress_ce_get_library' => wp_create_nonce('wp_ajax_motopress_ce_get_library'),
                    'motopress_ce_render_shortcode' => wp_create_nonce('wp_ajax_motopress_ce_render_shortcode'),
                    'motopress_ce_get_attachment_thumbnail' => wp_create_nonce('wp_ajax_motopress_ce_get_attachment_thumbnail')
                )
            )
        );
        add_action('admin_head', 'motopressCEAddCEBtn');
        add_action('admin_head', 'motopressCEHTML');

        wp_register_style('mpce-style', plugin_dir_url(__FILE__) . 'includes/css/style.css', null, $motopressCESettings['plugin_version']);
        wp_enqueue_style('mpce-style');

        wp_register_style('mpce', plugin_dir_url(__FILE__) . 'mp/ce/css/ce.css', null, $motopressCESettings['plugin_version']);
        wp_enqueue_style('mpce');
    }
}

function motopressCEHTML() {
    global $motopressCESettings;
    global $motopressCELang;
    
//    global $post;
//    $nonce = wp_create_nonce('post_preview_' . $post->ID);
//    $url = add_query_arg( array( 'preview' => 'true', 'preview_id' => $post->ID, 'preview_nonce' => $nonce ), get_permalink($post->ID) );
//    echo '<a href="' . $url . '" target="wp-preview" title="' . esc_attr(sprintf(__('Preview “%s”'), $title)) . '" rel="permalink">' . __('Preview') . '</a>';
//    echo '<a href="' . post_preview() . '" target="wp-preview" title="' . esc_attr(sprintf(__('Preview “%s”'), $title)) . '" rel="permalink">' . __('Preview') . '</a>';
    
//    echo '<br/>';
//    echo $url;
//    echo '<br/>';
//    echo post_preview();
    
?>
        <div id="motopress-content-editor" style="display: none;">
            <div class="motopress-content-editor-navbar">
                <div class="navbar-inner">
                    <div id="motopress-logo">
                        <img src="<?php echo $motopressCESettings['plugin_root_url'].'/'.$motopressCESettings['plugin_name'].'/images/logo.png?ver='.$motopressCESettings['plugin_version']; ?>">
                    </div>
                    <div class="motopress-page-name">
                        <?php echo get_post_type() == 'page' ? $motopressCELang->CEPage : $motopressCELang->CEPost ;?>:
                        <span>
                            <?php
//                            $postTitle = get_the_title();
//                            $length = strlen($postTitle);
//                            if ($length > 50) {
//                                echo substr($postTitle, 0, 50);
//                            } elseif ($length === 0) {
//                                echo '<i>' . $motopressCELang->CEEmptyPostTitle . '</i>';
//                            } else {
//                                echo $postTitle;
//                            }
                            ?>
                        </span>
                    </div>
                    <div class="pull-left motopress-object-control-btns">
                        <!--<button class="btn-default" id="motopress-content-editor-duplicate"><?php // echo $motopressCELang->CEDuplicateBtnText; ?></button>-->
                        <button class="btn-default" id="motopress-content-editor-delete"><?php echo $motopressCELang->CEDeleteBtnText; ?></button>
                    </div>
                    <div class="pull-right navbar-btns">
                        <button class="btn-blue" id="motopress-content-editor-publish"><?php echo $motopressCELang->CEPublishBtnText; ?></button>
                        <button class="btn-default" id="motopress-content-editor-save"><?php echo $motopressCELang->CESaveBtnText; ?></button>
                        <button class="btn-default" id="motopress-content-editor-preview"><?php echo $motopressCELang->CEPreviewBtnText; ?></button>
                        <button class="btn-default" id="motopress-content-editor-close"><?php echo $motopressCELang->CECloseBtnText; ?></button>
                    </div>
                </div>
            </div>

            <div id="motopress-flash"></div>

            <div id="motopress-content-editor-scene-wrapper">
                <iframe id="motopress-content-editor-scene" class="motorpess-content-editor-scene" name="motopress-content-editor-scene"></iframe>
            </div>

            <!-- Code editor -->
            <div id="motopress-code-editor-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="codeModalLabel" aria-hidden="true">
                <div class="modal-header">
                    <p id="codeModalLabel"><?php echo $motopressCELang->edit . ' ' . $motopressCELang->CECodeObjName; ?></p>
                </div>
                <div class="modal-body">
                    <div id="motopress-code-editor-wrapper">
                        <?php
                            wp_editor('', 'motopress-code-content', array(
                                'textarea_rows' => false,
                                'tinymce' => array(
                                    'remove_linebreaks' => false,
                                    'schema' => 'html5',
                                    'theme_advanced_resizing' => false
                                )
                            ));
                        ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="motopress-save-code-content" class="btn-blue"><?php echo $motopressCELang->save; ?></button>
                    <button class="btn-default" data-dismiss="modal" aria-hidden="true"><?php echo $motopressCELang->cancel; ?></button>
                </div>
            </div>

            <div id="motopress-confirm-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true"></div>
        </div>
        <div id="motopress-preload"></div>
<?php
}

function motopressCEAddCEBtn() {
    global $motopressCESettings;
    global $motopressCELang;
    global $post;
    global $motopressCEIsjQueryVer;
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            if (!Browser.IE && !Browser.Opera) {
                var motopressCEButton = $('<input />', {
                    type: 'button',
                    id: 'motopress-ce-btn',
                    'class': 'wp-core-ui button-primary',
                    value: '<?php echo $motopressCELang->CEBtn; ?>',
                    'data-post-id' : '<?php echo $post->ID?>',
                    disabled: 'disabled'
                }).insertAfter($('div#titlediv'));
                <?php if ($motopressCEIsjQueryVer) { ?>
                    var preloader = $('#motopress-preload');
                    motopressCEButton.on('click', function() {
                        preloader.show();

                        if (typeof CE === 'undefined') {
                            var head = $('head')[0];
                            var stealVerScript = $('<script />', {
                                text: 'var steal = { production: "mp/ce/production.js" + motopress.pluginVersionParam };'
                            })[0];
                            head.appendChild(stealVerScript);
                            var script = $('<script />', {
                                src: '<?php echo $motopressCESettings["plugin_root_url"]; ?>' + '/' + '<?php echo $motopressCESettings["plugin_name"]; ?>' + '/steal/steal.production.js?mp/ce'
                            })[0];
                            head.appendChild(script);
                        }
                    });

                    function mpceOnEditorInit() {
                        motopressCEButton.removeAttr('disabled');
                        if (pluginAutoOpen) {
                            sessionStorage.setItem('pluginAutoOpen', false);
                            motopressCEButton.click();
                        }
                    }

                    var editorState = "<?php echo get_user_setting('editor', 'html'); ?>";
                    var pluginAutoOpen = sessionStorage.getItem('pluginAutoOpen');
                    pluginAutoOpen = (pluginAutoOpen && pluginAutoOpen === 'true') ? true : false;
                    if (pluginAutoOpen) preloader.show();

                    if (editorState === 'tinymce') {
                        tinyMCE.onAddEditor.add(function(mce, ed) {
                            if (ed.editorId === 'content') {
                                ed.onInit.add(function(ed) {
                                    mpceOnEditorInit();
                                });
                            }
                        });
                    } else { mpceOnEditorInit(); }
                    
                <?php } ?>
            }
        });
    </script>
    <?php
}

require_once $motopressCESettings['plugin_root'].'/'.$motopressCESettings['plugin_name'].'/includes/getWpSettings.php';
add_action('wp_ajax_motopress_ce_get_wp_settings', 'motopressCEGetWpSettings');
require_once $motopressCESettings['plugin_root'].'/'.$motopressCESettings['plugin_name'].'/includes/ce/renderContent.php';
add_action('wp_ajax_motopress_ce_render_content', 'motopressCERenderContent');
require_once $motopressCESettings['plugin_root'].'/'.$motopressCESettings['plugin_name'].'/includes/ce/removeTemporaryPost.php';
add_action('wp_ajax_motopress_ce_remove_temporary_post', 'motopressCERemoveTemporaryPost');
require_once $motopressCESettings['plugin_root'].'/'.$motopressCESettings['plugin_name'].'/includes/ce/getLibrary.php';
add_action('wp_ajax_motopress_ce_get_library', 'motopressCEGetLibrary');
require_once $motopressCESettings['plugin_root'].'/'.$motopressCESettings['plugin_name'].'/includes/ce/renderShortcode.php';
add_action('wp_ajax_motopress_ce_render_shortcode', 'motopressCERenderShortcode');
require_once $motopressCESettings['plugin_root'].'/'.$motopressCESettings['plugin_name'].'/includes/ce/getAttachmentThumbnail.php';
add_action('wp_ajax_motopress_ce_get_attachment_thumbnail', 'motopressCEGetAttachmentThumbnail');
