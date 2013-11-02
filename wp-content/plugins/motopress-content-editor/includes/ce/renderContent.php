<?php
$motopressCELibrary = null;

function motopressCERenderContent() {
    require_once dirname(__FILE__).'/../verifyNonce.php';
    require_once dirname(__FILE__).'/../settings.php';
    require_once dirname(__FILE__).'/../access.php';
    require_once dirname(__FILE__).'/../Requirements.php';
    require_once dirname(__FILE__).'/../functions.php';
    require_once dirname(__FILE__).'/../getLanguageDict.php';

    $content = trim($_POST['data']);
    $post_id = $_POST['post_id'];

    global $motopressCESettings;
    $motopressCELang = motopressCEGetLanguageDict();
    $errors = array();

    global $motopressCELibrary;
    $motopressCELibrary = new MPCELibrary();

    $content = stripslashes($content);
    $content = motopressCECleanupShortcode($content);
    if (!empty($content)) {
        $content = motopressCEWrapOuterCode($content);
    }
    $content = shortcode_unautop($content);

    $output = motopressCEParseObjectsRecursive($content);

    $tmp_post_id = motopressCECreateTemporaryPost($post_id, $output);
    if ($tmp_post_id !== 0) {
        $result = array(
            'post_id' => $tmp_post_id,
            'src' => get_permalink($tmp_post_id)
        );
        echo json_encode($result);
    } else {
        $errors[] = $motopressCELang->CECreateTemporaryPostError;
    }

    if (!empty($errors)) {
        if ($motopressCESettings['debug']) {
            print_r($errors);
        } else {
            motopressCESetError($motopressCELang->CECreateTemporaryPostError);
        }
    }
    exit;
}

function motopressCEParseObjectsRecursive($matches) {
    global $motopressCELibrary;
    $regex = '/' . motopressCEGetMPShortcodeRegex() . '/';

    if (is_array($matches)) {
        $attstring = '';
        $parameters_str =' ' . MPCEShortcode::$attributes['parameters'];
        $unwrap = '';
        $atts = shortcode_parse_atts($matches[3]);
        $atts = (array) $atts;

        $list= $motopressCELibrary->getObjectsList();

        $parameters = $list[ $matches[2] ]['parameters'];

        $type = $list[$matches[2]]['type'];

        //set parameters of shortcode
        if (!empty($parameters)) {
            foreach($parameters as $name => $param) {
                if (array_key_exists($name, $atts)) {
                    $value = $atts[$name];
                    $parameters[$name]['value'] = str_replace(array('\'', '"'), array('&#039;', '&quot;'), $value);
                } else {
                    $parameters[$name] = new stdClass();
                }
            }
            $jsonParameters = (version_compare(PHP_VERSION, '5.4.0', '>=')) ? json_encode($parameters, JSON_UNESCAPED_UNICODE) : motopressCEJsonEncode($parameters);
            $parameters_str = " " . MPCEShortcode::$attributes['parameters'] . "='" . $jsonParameters . "' ";
        }

        // set close-type of shortcode
        if (preg_match('/\[\/' . $matches[2] .'\]$/', $matches[0])===1){
            $endstr = '[/' . $matches[2] .']';
            $closeType = MPCEObject::ENCLOSED;
        } else {
            $endstr = '';
            $closeType = MPCEObject::SELF_CLOSED;
        }

        //wrap custom code
        if ((!preg_match($regex, $matches[5])) && ($matches[5] !== '') && ($matches[5] !== '&nbsp;') && in_array($matches[2], array('mp_row', 'mp_row_inner', 'mp_span', 'mp_span_inner'))){
            $matches[5] = motopressCEWrapCustomCode($matches[5]);
        }

        // set system marking for "must-unwrap" code
        if ($matches[2] == 'mp_code') {
            if (!empty($matches[3])) {
                $atts = shortcode_parse_atts($matches[3]);
                if ($atts['unwrap'] === 'true') {
                    $unwrap = ' ' . MPCEShortcode::$attributes['unwrap'] . ' = "true"';
                }
            }
        }
        $dataContent = '';

        //setting data-motopress-content for all objects except layout
        if (!in_array($matches[2] , array('mp_row','mp_row_inner','mp_span','mp_span_inner'))){
            $dataContent = motopressCEScreeningDataAttrShortcodes($matches[5]);
        }

        return '<div '.MPCEShortcode::$attributes['closeType'].'="' . $closeType . '" '.MPCEShortcode::$attributes['shortcode'].'="' . $matches[2] .'" '.MPCEShortcode::$attributes['group'].'="' . $type .'"' . $parameters_str . ' '.MPCEShortcode::$attributes['content'].'="' . htmlentities($dataContent, ENT_QUOTES, 'UTF-8') . '" '  . $unwrap . '>[' . $matches[2] . ' ' . $matches[3]  . ']' . preg_replace_callback($regex, 'motopressCEParseObjectsRecursive', $matches[5]) . $endstr . '</div>';
    }

    return preg_replace_callback($regex, 'motopressCEParseObjectsRecursive', $matches);
}


function motopressCEWrapOuterCode($content) {
        $content = stripslashes( $content );
        if (!preg_match('/.*?\[mp_row\].*\[\/mp_row\].*/s', $content)){
            $content = '[mp_row][mp_span col="12"]' . $content . '[/mp_span][/mp_row]';
        }
        preg_match('/(\A.*?)(\[mp_row\].*\[\/mp_row\])(.*\Z)/s', $content, $matches);
        $result = '';
        $result .= !empty($matches[1]) ? '[mp_row][mp_span col="12"]' . $matches[1] . '[/mp_span][/mp_row]' :'';
        $result .= $matches[2];
        $result .= !empty($matches[3]) ? '[mp_row][mp_span col="12"]' . $matches[3] . '[/mp_span][/mp_row]' :'';
        return $result;
}

function motopressCEGetMPShortcodeRegex(){
    global $motopressCELibrary;

    $shortcodes = $motopressCELibrary->getObjectsNames();

    $tagnames = array_values($shortcodes);
    $tagregexp = join( '|', array_map('preg_quote', $tagnames) );

    $pattern  =
              '\\['                              // Opening bracket
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
            . '(' . $tagregexp . ')'                     // 2: Shortcode name
            . '\\b'                              // Word boundary
            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
            .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
            .     '(?:'
            .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
            .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
            .     ')*?'
            . ')'
            . '(?:'
            .     '(\\/)'                        // 4: Self closing tag ...
            .     '\\]'                          // ... and closing bracket
            . '|'
            .     '\\]'                          // Closing bracket
            .     '(?:'
            .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
            .             '[^\\[]*+'             // Not an opening bracket
            .             '(?:'
            .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
            .                 '[^\\[]*+'         // Not an opening bracket
            .             ')*+'
            .         ')'
            .         '\\[\\/\\2\\]'             // Closing shortcode tag
            .     ')?'
            . ')'
            . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]

    return $pattern;
}

/*
 * replacement of [ to [] for supression of incorect rendering
 */
function motopressCEScreeningDataAttrShortcodes($content){
    return htmlspecialchars_decode(preg_replace('/\[/', '[]', $content), ENT_QUOTES);
}

function motopressCEWrapCustomCode($content){
    return '[mp_code unwrap="true"]' . $content . '[/mp_code]';
}

/*Create temporary post with motopress adapted content*/
function motopressCECreateTemporaryPost($post_id, $content) {
    $post = get_post($post_id);
    $post->ID = '';
    $post->post_title = 'temporary';
    $post->post_content = $content;
    $post->post_status = 'trash';

    $userRole = wp_get_current_user()->roles[0];
    $optionName = 'motopress_tmp_post_id_' . $userRole;
    $id = get_option($optionName);

    if ($id) {
        if (is_null(get_post($id))) {
            $id = wp_insert_post($post, false);
            update_option($optionName, $id);
        }
    } else {
        $id = wp_insert_post($post, false);
        add_option($optionName, $id);
    }

    $post->ID = $id;
    wp_update_post($post);
    wp_untrash_post($id);
    $pageTemplate = get_post_meta($post_id, '_wp_page_template', true);
    $pageTemplate = (!$pageTemplate or empty($pageTemplate)) ? 'default' : $pageTemplate;
    update_post_meta($id, '_wp_page_template', $pageTemplate);

    return $id;
}

function motopressCECleanupShortcode($content) {
    return strtr($content, array (
        '<p>[' => '[',
        '</p>[' => '[',
        ']<p>' => ']',
        ']</p>' => ']',
        ']<br />' => ']'
    ));
}