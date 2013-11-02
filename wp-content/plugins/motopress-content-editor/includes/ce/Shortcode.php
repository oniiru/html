<?php
/**
 * Description of Shortcodes
 *
 * @author dmitry
 */
class MPCEShortcode {
    const PREFIX = 'mp_';

    public static $attributes = array(
        'closeType' => 'data-motopress-close-type',
        'shortcode' => 'data-motopress-shortcode',
        'group' => 'data-motopress-group',
        'parameters' => 'data-motopress-parameters',
        'content' => 'data-motopress-content',
        'unwrap' => 'data-motopress-unwrap'
    );

    public function register() {
        $this->addShortcode('row', 'motopressRow');
        $this->addShortcode('row_inner', 'motopressRowInner');
        $this->addShortcode('span', 'motopressSpan');
        $this->addShortcode('span_inner', 'motopressSpanInner');
        $this->addShortcode('text', 'motopressText');
        $this->addShortcode('heading', 'motopressTextHeading');
        $this->addShortcode('image', 'motopressImage');
        $this->addShortcode('image_slider', 'motopressImageSlider');
        $this->addShortcode('video', 'motopressVideo');
        $this->addShortcode('code', 'motopressCode');
        $this->addShortcode('space', 'motopressSpace');
        $this->addShortcode('button', 'motopressButton');
        $this->addShortcode('wp_archives', 'motopressWPWidgetArchives');
        $this->addShortcode('wp_calendar', 'motopressWPWidgetCalendar');
        $this->addShortcode('wp_categories', 'motopressWPWidgetCategories');
        $this->addShortcode('wp_navmenu', 'motopressWPNavMenu_Widget');
        $this->addShortcode('wp_meta', 'motopressWPWidgetMeta');
        $this->addShortcode('wp_pages', 'motopressWPWidgetPages');
        $this->addShortcode('wp_posts', 'motopressWPWidgetRecentPosts');
        $this->addShortcode('wp_comments', 'motopressWPWidgetRecentComments');
        $this->addShortcode('wp_rss', 'motopressWPWidgetRSS');
        $this->addShortcode('wp_search', 'motopressWPWidgetSearch');
        $this->addShortcode('wp_tagcloud', 'motopressWPWidgetTagCloud');
        $this->addShortcode('wp_widgets_area', 'motopressWPWidgetArea');
        $this->addShortcode('gmap', 'motopressGoogleMaps');
    }

    /**
     * @param string $tag
     * @param string $func
     */
    private function addShortcode($tag, $func) {
        add_shortcode(self::PREFIX . $tag, array($this, $func));
    }

    /**
     * @param string $content
     * @return string
     */
    public function cleanupShortcode($content) {
        return strtr($content, array(
            '<p>[' => '[',
            '</p>[' => '[',
            ']<p>' => ']',
            ']</p>' => ']',
            ']<br />' => ']'
        ));
    }

    /**
     * @param string $closeType
     * @param string $shortcode
     * @param stdClass $parameters
     * @param string $content
     * return string
     */
    public function toShortcode($closeType, $shortcode, $parameters, $content) {
        $str = '[' . $shortcode;
        if (!is_null($parameters)) {
            foreach ($parameters as $attr => $values) {
//                $value = (isset($values->value)) ? $values->value : '';
                if (isset($values->value))
                    $str .= ' ' . $attr . '="' . $values->value . '"';
            }
        }
        $str .= ']';
        if ($closeType === MPCEObject::ENCLOSED) {
            if (!is_null($content))
                $str .= $content;
            $str .= '[/' . $shortcode . ']';
        }
        return $str;
    }

    public function motopressRow($atts, $content = null) {
        return '<div class="mp-row-fluid motopress-row">' . do_shortcode($content) . '</div>';
    }

    public function motopressRowInner($atts, $content = null) {
        return '<div class="mp-row-fluid motopress-row">' . do_shortcode($content) . '</div>';
    }

    public function motopressSpan($atts, $content = null) {
        extract(shortcode_atts(array(
            'col' => 12,
            'classes' => '',
            'style' => ''
                        ), $atts));
        $style = empty($style) ? '' : 'style="' . $style . '"';
        return '<div class="mp-span' . $col . ' motopress-span ' . $classes . '" ' . $style . '>' . do_shortcode($content) . '</div>';
    }

    public function motopressSpanInner($atts, $content = null) {
        extract(shortcode_atts(array(
            'col' => 12,
            'classes' => '',
            'style' => ''
                        ), $atts));
        $style = empty($style) ? '' : 'style="' . $style . '"';
        return '<div class="mp-span' . $col . ' motopress-span ' . $classes . '" ' . $style . '>' . do_shortcode($content) . '</div>';
    }

    public function motopressText($atts, $content = null) {
        return '<div class="motopress-text-obj">' . $content . '</div>';
    }

    public function motopressTextHeading($atts, $content = null) {
        $result = empty($content) ? '<h2>' . $content . '</h2>' : $content;
        return '<div class="motopress-text-obj">' . $result . '</div>';
    }

    public function motopressImage($atts, $content = null) {
        extract(shortcode_atts(array(
            'id' => '',
            'link' => '#',
            'align' => 'left'
//            'scale' => 'crop',
//            'lightbox' => 'true'
        ), $atts));

        global $motopressCESettings;
        require_once $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/includes/getLanguageDict.php';
        $motopressCELang = motopressCEGetLanguageDict();
        $error = null;

        if (isset($id) && !empty($id)) {
            $id = (int) $id;
            $attachment = get_post($id);
            if (!empty($attachment) && $attachment->post_type === 'attachment') {
                if (wp_attachment_is_image($id)) {
                    $title = esc_attr($attachment->post_title);

                    $alt = trim(strip_tags(get_post_meta($id, '_wp_attachment_image_alt', true)));
                    if (empty($alt)) {
                        $alt = trim(strip_tags($attachment->post_excerpt));
                    }
                    if (empty($alt)) {
                        $alt = trim(strip_tags($attachment->post_title));
                    }

                    $img = '<img';
                    if (!empty($attachment->guid))
                        $img .= ' src="' . $attachment->guid . '"';
                    if (!empty($title))
                        $img .= ' title="' . $title . '"';
                    if (!empty($alt))
                        $img .= ' alt="' . $alt . '"';
                    $img .= ' />';

                    if (isset($link) && !empty($link) && $link !== '#') {
                        $img = '<a href="' . $link . '">' . $img . '</a>';
                    }
                } else {
                    $error = $motopressCELang->CEAttachmentNotImage;
                }
            } else {
                $error = $motopressCELang->CEAttachmentEmpty;
            }
        } else {
            $error = $motopressCELang->CEImageIdEmpty;
        }

        $imgHtml = '<div class="motopress-image-obj motopress-text-align-' . $align . '">';
        if (empty($error)) {
            $imgHtml .= $img;
        } else {
            $imgHtml .= $error;
        }
        $imgHtml .= '</div>';

        return $imgHtml;
    }

    public function motopressImageSlider($atts, $content = null) {
        extract(shortcode_atts(array(
            'ids' => ''
                        ), $atts));

        global $motopressCESettings;
        require_once $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/includes/getLanguageDict.php';
        $motopressCELang = motopressCEGetLanguageDict();
        $error = null;

        if (isset($ids) && !empty($ids)) {
            $ids = trim($ids);
            $ids = explode(',', $ids);
            $ids = array_filter($ids);

            if (!empty($ids)) {
                wp_enqueue_style('mpce-flexslider');
                wp_enqueue_script('mpce-flexslider');

                $images = array();
                $imageErrors = array();
                foreach ($ids as $id) {
                    $id = (int) trim($id);

                    $attachment = get_post($id);
                    if (!empty($attachment) && $attachment->post_type === 'attachment') {
                        if (wp_attachment_is_image($id)) {
                            $title = esc_attr($attachment->post_title);

                            $alt = trim(strip_tags(get_post_meta($id, '_wp_attachment_image_alt', true)));
                            if (empty($alt)) {
                                $alt = trim(strip_tags($attachment->post_excerpt));
                            }
                            if (empty($alt)) {
                                $alt = trim(strip_tags($attachment->post_title));
                            }

                            $img = '<img';
                            if (!empty($attachment->guid))
                                $img .= ' src="' . $attachment->guid . '"';
                            if (!empty($title))
                                $img .= ' title="' . $title . '"';
                            if (!empty($alt))
                                $img .= ' alt="' . $alt . '"';
                            $img .= ' />';

                            $images[] = $img;
                            unset($img);
                        } else {
                            $imageErrors[] = $motopressCELang->CEAttachmentNotImage;
                        }
                    } else {
                        $imageErrors[] = $motopressCELang->CEAttachmentEmpty;
                    }
                }
            } else {
                $error = $motopressCELang->CEImageSliderIdsEmpty;
            }
        } else {
            $error = $motopressCELang->CEImageSliderIdsEmpty;
        }

        $uniqid = uniqid();
        $sliderHtml = '<div class="motopress-image-slider-obj flexslider" id="' . $uniqid . '">';
        if (empty($error)) {
            if (!empty($images)) {
                $sliderHtml .= '<ul class="slides">';
                foreach ($images as $image) {
                    $sliderHtml .= '<li>' . $image . '</li>';
                }
                $sliderHtml .= '</ul>';
            } elseif (!empty($imageErrors)) {
                $sliderHtml .= '<ul>';
                foreach ($imageErrors as $imageError) {
                    $sliderHtml .= '<li>' . $imageError . '</li>';
                }
                $sliderHtml .= '</ul>';
            }
        } else {
            $sliderHtml .= $error;
        }
        $sliderHtml .= '</div>';

        $slideshow = (self::isContentEditor()) ? 'false' : 'true';

        $sliderHtml .= '<script>
            jQuery(document).ready(function($) {
                $(".motopress-image-slider-obj#' . $uniqid . '").flexslider({
                    slideshow: ' . $slideshow . '
                });
            });
            </script>';
        return $sliderHtml;
    }

    const DEFAULT_VIDEO = 'http://www.youtube.com/watch?v=t0jFJmTDqno';
    const YOUTUBE = 'youtube';
    const VIMEO = 'vimeo';

    public function motopressVideo($atts, $content = null) {
        extract(shortcode_atts(array(
            'src' => ''
                        ), $atts));

        global $motopressCESettings;
        require_once $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/includes/getLanguageDict.php';
        $motopressCELang = motopressCEGetLanguageDict();
        $error = null;

        if (!empty($src)) {
            $src = filter_var($src, FILTER_SANITIZE_URL);
            $src = str_replace('&amp;', '&', $src);
            $url = parse_url($src);
            if ($url) {
                if (!isset($url['scheme']) || empty($url['scheme'])) {
                    $src = 'http://' . $src;
                    $url = parse_url($src);
                }
            }

            if ($url) {
                if (isset($url['host']) && !empty($url['host']) && isset($url['path']) && !empty($url['path'])) {
                    $videoSite = self::getVideoSite($url);
                    if ($videoSite) {
                        $videoId = self::getVideoId($videoSite, $url);
                        if ($videoId) {
                            $src = self::getVideoSrc($videoSite, $videoId, $url['query']);
                        } else {
                            $error = $motopressCELang->CEVideoIdError;
                        }
                    } else {
                        $error = $motopressCELang->CEIncorrectVideoURL;
                    }
                } else {
                    $error = $motopressCELang->CEIncorrectVideoURL;
                }
            } else {
                $error = $motopressCELang->CEParseVideoURLError;
            }
        } else {
            $error = $motopressCELang->CEIncorrectVideoURL;
        }

        $videoHtml = '<div class="motopress-video-obj">';
        if (empty($error)) {
            $videoHtml .= '<iframe src="' . $src . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        } else {
            $videoHtml .= $error;
        }
        $videoHtml .= '</div>';

        return $videoHtml;
    }

    private static function getVideoSite($url) {
        $videoSite = false;

        $youtubeRegExp = '/youtube\.com|youtu\.be/is';
        $vimeoRegExp = '/vimeo\.com/is';
        if (preg_match($youtubeRegExp, $url['host'])) {
            $videoSite = self::YOUTUBE;
        } else if (preg_match($vimeoRegExp, $url['host'])) {
            $videoSite = self::VIMEO;
        }

        return $videoSite;
    }

    private static function getVideoId($videoSite, $url) {
        $videoId = false;

        switch ($videoSite) {
            case self::YOUTUBE:
                if (preg_match('/youtube\.com/is', $url['host'])) {
                    if (preg_match('/watch/is', $url['path']) && isset($url['query']) && !empty($url['query'])) {
                        parse_str($url['query'], $parameters);
                        if (isset($parameters['v']) && !empty($parameters['v'])) {
                            $videoId = $parameters['v'];
                        }
                    } else if (preg_match('/embed/is', $url['path'])) {
                        $path = explode('/', $url['path']);
                        if (isset($path[2]) && !empty($path[2])) {
                            $videoId = $path[2];
                        }
                    }
                } else if (preg_match('/youtu\.be/is', $url['host'])) {
                    $path = explode('/', $url['path']);
                    if (isset($path[1]) && !empty($path[1])) {
                        $videoId = $path[1];
                    }
                }
                break;
            case self::VIMEO:
                if (preg_match('/player\.vimeo\.com/is', $url['host']) && preg_match('/video/is', $url['path'])) {
                    $path = explode('/', $url['path']);
                    if (isset($path[2]) && !empty($path[2])) {
                        $videoId = $path[2];
                    }
                } else if (preg_match('/vimeo\.com/is', $url['host'])) {
                    $path = explode('/', $url['path']);
                    if (isset($path[1]) && !empty($path[1])) {
                        $videoId = $path[1];
                    }
                }
                break;
        }

        return $videoId;
    }

    private static function getVideoSrc($videoSite, $videoId, $query) {
        $youtubeSrc = 'http://www.youtube.com/embed/';
        $vimeoSrc = 'http://player.vimeo.com/video/';
        $videoQuery = '';
        $wmode = 'wmode=opaque';

        if (!empty($query)) {
            parse_str($query, $parameters);
            if (self::isContentEditor()) {
                if (isset($parameters['autoplay']) && !empty($parameters['autoplay'])) {
                    unset($parameters['autoplay']);
                }
            }
        }

        switch ($videoSite) {
            case self::YOUTUBE:
                $videoSrc = $youtubeSrc;
                if (isset($parameters['v']) && !empty($parameters['v'])) {
                    unset($parameters['v']);
                }
                break;
            case self::VIMEO:
                $videoSrc = $vimeoSrc;
                break;
        }

        $videoSrc .= $videoId;

        if (!empty($parameters)) {
            $videoQuery = http_build_query($parameters);
        }

        if (!empty($videoQuery)) {
            $videoSrc .= '?' . $videoQuery . '&' . $wmode;
        } else {
            $videoSrc .= '?' . $wmode;
        }

        return $videoSrc;
    }

    private static function isContentEditor() {
        if (
            (isset($_GET['motopress-ce']) && $_GET['motopress-ce'] === '1') ||
            (isset($_POST['action']) && $_POST['action'] === 'motopress_ce_render_shortcode')
        ) {
            return true;
        }
        return false;
    }

    public function motopressCode($atts, $content = null) {
        return '<div class="motopress-code-obj">' . do_shortcode($content) . '</div>';
    }

    public function motopressSpace($atts, $content = null) {
        extract(shortcode_atts(array(), $atts));
        return '<div class="motopress-space-obj"></div>';
    }

    public function motopressButton($atts, $content = null) {
        extract(shortcode_atts(array(
            'text' => '',
            'link' => '#',
            'target' => 'false',
            'color' => 'default',
            'size' => 'default',
            'align' => 'left',
            'custom_class' => ''
                        ), $atts));
        $classes = array(
            'motopress-btn-color-' . $color,
            'motopress-btn-size-' . $size
        );
        $classes = implode(' ', $classes);
        $align = 'motopress-text-align-' . $align;
        $target = ($target == 'true') ? '_blank' : '_self';
        return '<div class="motopress-button-obj ' . $align . '">
                    <a href="' . $link . '" class="motopress-btn ' . $classes . ' ' . $custom_class . '" target="' . $target . '">' . $text . '</a>
                </div>';
    }

    public function motopressWPWidgetArchives($attrs, $content = null) {
        $result = '';
        $title = '';
        extract(shortcode_atts(array(
            'title' => '',
            'dropdown' => '',
            'count' => ''
                        ), $attrs));

        ($dropdown == true) ? $attrs['dropdown'] = true : $attrs['dropdown'] = false;
        ($count == true) ? $attrs['count'] = true : $attrs['count'] = false;

        $result = '<div class="motopress-wp_archives">';
        $type = 'WP_Widget_Archives';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetCalendar($attrs, $content = null) {
        $result = '';
        $title = '';
        extract(shortcode_atts(array(
            'title' => '',
                        ), $attrs));

        $result = '<div class="motopress-wp_calendar">';
        $type = 'WP_Widget_Calendar';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetCategories($attrs, $content = null) {
        $result = '';
        $title = '';
        extract(shortcode_atts(array(
            'title' => '',
            'dropdown' => '',
            'count' => '',
            'hierarchical' => '',
        ), $attrs));

        ($dropdown == true) ? $attrs['dropdown'] = true : $attrs['dropdown'] = false;
        ($count == true) ? $attrs['count'] = true : $attrs['count'] = false;
        ($hierarchical == true) ? $attrs['hierarchical'] = true : $attrs['hierarchical'] = false;

        $result = '<div class="motopress-wp_categories">';
        $type = 'WP_Widget_Categories';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPNavMenu_Widget($attrs, $content = null) {
        $result = '';
        $title = '';
        $nav_menu = '';
        extract(shortcode_atts(array(
            'title' => '',
            'nav_menu' => '',
        ), $attrs));

        $result = '<div class="motopress-wp_custommenu">';
        $type = 'WP_Nav_Menu_Widget';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetMeta($attrs, $content = null) {
        $result = '';
        $title = '';
        extract(shortcode_atts(array(
            'title' => '',
        ), $attrs));

        $result = '<div class="motopress-wp_meta">';
        $type = 'WP_Widget_Meta';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetPages($attrs, $content = null) {
        $result = '';
        $title = '';
        $sortby = '';
        $exclude = '';
        extract(shortcode_atts(array(
            'title' => '',
            'sortby' => 'menu_order',
            'exclude' => null,
                        ), $attrs));

        $result = '<div class="motopress-wp_pages">';
        $type = 'WP_Widget_Pages';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetRecentPosts($attrs, $content = null) {
        $result = '';
        $title = '';
        $number = '';
        $show_date = '';
        extract(shortcode_atts(array(
            'title' => '',
            'number' => 5,
            'show_date' => false,
                        ), $attrs));
        $attrs['show_date'] = $show_date;

        $result = '<div class="motopress-wp_posts">';
        $type = 'WP_Widget_Recent_Posts';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetRecentComments($attrs, $content = null) {
        $result = '';
        $title = '';
        $number = '';
        extract(shortcode_atts(array(
            'title' => '',
            'number' => 5,
                        ), $attrs));

        $result = '<div class="motopress-wp_recentcomments">';
        $type = 'WP_Widget_Recent_Comments';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetRSS($attrs, $content = null) {
        $result = '';
        $title = '';
        $url = '';
        $items = '';
        $options = '';
        extract(shortcode_atts(array(
            'title' => '',
            'url' => '',
            'items' => 10,
            'options' => '',
                        ), $attrs));
        if ($url == '')
            return;
        $attrs['title'] = $title;
        $attrs['items'] = $items;

        $options = explode(",", $options);
        if (in_array("show_summary", $options))
            $attrs['show_summary'] = true;
        if (in_array("show_author", $options))
            $attrs['show_author'] = true;
        if (in_array("show_date", $options))
            $attrs['show_date'] = true;

        $result = '<div class="motopress-wp_rss">';
        $type = 'WP_Widget_RSS';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetSearch($attrs, $content = null) {
        extract(shortcode_atts(array(
            'title' => '',
            'align' => 'left',
                        ), $attrs));

        $result = '<div class="motopress-wp_search_widget">';
        $type = 'WP_Widget_Search';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetTagCloud($attrs, $content = null) {
        $result = '';
        $title = '';
        $taxonomy = '';
        extract(shortcode_atts(array(
            'title' => __('Tags'),
            'taxonomy' => 'post_tag',
                        ), $attrs));

        $result = '<div class="motopress-wp_tagcloud">';
        $type = 'WP_Widget_Tag_Cloud';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetArea($attrs, $content = null) {
        $result = '';
        $title = '';
        $sidebar = '';
        extract(shortcode_atts(array(
            'title' => '',
            'sidebar' => '',
            'custom_class' => ''
                        ), $attrs));

        $result = '<div class="motopress-wp_widgets_area ' . $custom_class . '">';

        if ($title)
            $result .= '<h2 class="widgettitle">' . $title . '</h2>';

        if (function_exists('dynamic_sidebar') && $sidebar && $sidebar != 'no') {
            ob_start();
            dynamic_sidebar($sidebar);
            $result .= ob_get_clean();

            $result .= '</div>';

            return $result;
        } else {
            return false;
        }
    }

    public function motopressGoogleMaps($attrs, $content = null) {
        global $motopressCESettings;
        require_once $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/includes/getLanguageDict.php';
        require_once $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/includes/Requirements.php';

        $motopressCELang = motopressCEGetLanguageDict();

        $result = $motopressCELang->CEGoogleMapNothingFound;
        $address = '';
        $zoom = '';
        extract( shortcode_atts(array(
            'address' => 'Sidney, New South Wales, Australia',
            'zoom' => '13',
                        ), $attrs ));

        if ( $address == '' ) { return $result; }

		$address = str_replace(" ", "+", $address);
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?address='. $address .'&sensor=false';

        $requirements = new MPCERequirements();
        if ($requirements->getCurl()) {
            $ch = curl_init();
            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true
            );
            curl_setopt_array($ch, $options);
            $jsonData = curl_exec($ch);
            curl_close($ch);
        } else {
            $jsonData = file_get_contents($url);
        }

        $data = json_decode($jsonData);

        if ($data && isset($data->results))
        {
            $results = $data->{'results'};
            if ($results && $results[0])
            {
                $address = $results[0]->{'formatted_address'};

                $result = '<div class="motopress-google_maps">';
                $result .= '<iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q='.$address.'&amp;t=m&amp;z='.$zoom.'&amp;output=embed&amp;iwloc=near"></iframe>';
                $result .= '</div>';
            }
        }
        return $result;
    }

}