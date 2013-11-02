<?php
require_once 'Element.php';
require_once 'Group.php';
require_once 'Object.php';

/**
 * Description of MPCELibrary
 *
 * @author dmitry
 */
class MPCELibrary {
    private $library = array();
    private $skippedGroups = array();
    public static $isAjaxRequest;

    /**
     * @global stdClass $motopressCELang
     */
    public function __construct() {
        global $motopressCELang;
        self::$isAjaxRequest = $this->isAjaxRequest();

        /* objects */
        //grid
        $rowObj = new MPCEObject();
        $rowObj->setId(MPCEShortcode::PREFIX . 'row');
        $rowObj->setName($motopressCELang->CERowObjName);
        $rowObj->setTitle($motopressCELang->CERowObjTitle);
        $rowObj->setCloseType(MPCEObject::ENCLOSED);
        $rowInnerObj = new MPCEObject();
        $rowInnerObj->setId(MPCEShortcode::PREFIX . 'row_inner');
        $rowInnerObj->setName($motopressCELang->CERowInnerObjName);
        $rowInnerObj->setTitle($motopressCELang->CERowInnerObjTitle);
        $rowInnerObj->setCloseType(MPCEObject::ENCLOSED);
        $spanObj = new MPCEObject();
        $spanObj->setId(MPCEShortcode::PREFIX . 'span');
        $spanObj->setName($motopressCELang->CESpanObjName);
        $spanObj->setTitle($motopressCELang->CESpanObjTitle);
        $spanObj->setCloseType(MPCEObject::ENCLOSED);
        $spanObj->setParameters(array(
            'col' => array(
                'type' => 'number',
                'values' => range(1, 12),
                'default' => '12'
            )
        ));
        $spanInnerObj = new MPCEObject();
        $spanInnerObj->setId(MPCEShortcode::PREFIX . 'span_inner');
        $spanInnerObj->setName($motopressCELang->CESpanInnerObjName);
        $spanInnerObj->setTitle($motopressCELang->CESpanInnerObjTitle);
        $spanInnerObj->setCloseType(MPCEObject::ENCLOSED);
        $spanInnerObj->setParameters(array(
            'col' => array(
                'type' => 'number',
                'values' => range(1, 12),
                'default' => 12
            )
        ));

        //text
        $textObj = new MPCEObject();
        $textObj->setId(MPCEShortcode::PREFIX . 'text');
        $textObj->setName($motopressCELang->CETextObjName);
        $textObj->setIcon('text.png');
        $textObj->setTitle($motopressCELang->CETextObjTitle);
        $textObj->setCloseType(MPCEObject::ENCLOSED);

        //heading
        $headingObj = new MPCEObject();
        $headingObj->setId(MPCEShortcode::PREFIX . 'heading');
        $headingObj->setName($motopressCELang->CEHeadingObjName);
        $headingObj->setIcon('heading.png');
        $headingObj->setTitle($motopressCELang->CEHeadingObjTitle);
        $headingObj->setCloseType(MPCEObject::ENCLOSED);

        //image
        $imageObj = new MPCEObject();
        $imageObj->setId(MPCEShortcode::PREFIX . 'image');
        $imageObj->setName($motopressCELang->CEImageObjName);
        $imageObj->setIcon('image.png');
        $imageObj->setTitle($motopressCELang->CEImageObjTitle);
        $imageObj->setCloseType(MPCEObject::SELF_CLOSED);
        $imageObj->setParameters(array(
            'id' => array(
                'type' => 'image',
                'label' => $motopressCELang->CEImageObjSrcLabel,
                'default' => '',
                'description' => $motopressCELang->CEImageObjSrcDesc
            ),
            'link' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEImageObjLinkLabel,
                'default' => '#',
                'description' => $motopressCELang->CEImageObjLinkDesc
            ),
            'align' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEImageObjAlignLabel,
                'default' => 'left',
                'description' => $motopressCELang->CEImageObjAlignDesc,
                'list' => array(
                    'left' => $motopressCELang->CELeft,
                    'center' => $motopressCELang->CECenter,
                    'right' => $motopressCELang->CERight
                )
            )
        ));

        $imageSlider = new MPCEObject();
        $imageSlider->setId(MPCEShortcode::PREFIX . 'image_slider');
        $imageSlider->setName($motopressCELang->CEImageSliderObjName);
        $imageSlider->setIcon('image-slider.png');
        $imageSlider->setTitle($motopressCELang->CEImageSliderObjTitle);
        $imageSlider->setCloseType(MPCEObject::SELF_CLOSED);
        $imageSlider->setParameters(array(
            'ids' => array(
                'type' => 'multi-images',
                'label' => $motopressCELang->CEImageSliderObjIdsLabel,
                'default' => '',
                'description' => $motopressCELang->CEImageSliderObjIdsDesc,
                'text' => $motopressCELang->CEImageSliderObjIdsText,
            )
        ));

        //button
        $buttonObj = new MPCEObject();
        $buttonObj->setId(MPCEShortcode::PREFIX . 'button');
        $buttonObj->setName($motopressCELang->CEButtonObjName);
        $buttonObj->setIcon('button.png');
        $buttonObj->setTitle($motopressCELang->CEButtonObjTitle);
        $buttonObj->setCloseType(MPCEObject::SELF_CLOSED);
        $buttonObj->setParameters(array(
            'text' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEButtonObjTextLabel,
                'default' => $motopressCELang->CEButtonObjName,
                'description' => $motopressCELang->CEButtonObjTextDesc
            ),
            'link' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEButtonObjLinkLabel,
                'default' => '#',
                'description' => $motopressCELang->CEButtonObjLinkDesc,
            ),
            'target' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEButtonObjTargetLabel,
                'default' => 'false',
                'description' => $motopressCELang->CEButtonObjTargetDesc,
            ),
            'color' => array(
                'class-prefix' => 'mp-button-',
                'type' => 'color-select',
                'label' => $motopressCELang->CEButtonObjColorLabel,
                'default' => 'default',
                'description' => $motopressCELang->CEButtonObjColorDesc,
                'list' => array(
                    'default' => $motopressCELang->CESilver,
                    'red' => $motopressCELang->CERed,
                    'pink-dreams' => $motopressCELang->CEPinkDreams,
                    'warm' => $motopressCELang->CEWarm,
                    'hot-summer' => $motopressCELang->CEHotSummer,
                    'olive-garden' => $motopressCELang->CEOliveGarden,
                    'green-grass' => $motopressCELang->CEGreenGrass,
                    'skyline' => $motopressCELang->CESkyline,
                    'aqua-blue' => $motopressCELang->CEAquaBlue,
                    'violet' => $motopressCELang->CEViolet,
                    'dark-grey' => $motopressCELang->CEDarkGrey,
                    'black' => $motopressCELang->CEBlack
                )
            ),
            'size' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEButtonObjSizeLabel,
                'default' => 'default',
                'description' => $motopressCELang->CEButtonObjSizeDesc,
                'list' => array(
                    'large' => $motopressCELang->CELarge,
                    'default' => $motopressCELang->CEMiddle,
                    'small' => $motopressCELang->CESmall,
                    'mini' => $motopressCELang->CEMini
                )
            ),
            'align' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEButtonObjAlignLabel,
                'default' => 'left',
                'description' => $motopressCELang->CEButtonObjAlignDesc,
                'list' => array(
                    'left' => $motopressCELang->CELeft,
                    'center' => $motopressCELang->CECenter,
                    'right' => $motopressCELang->CERight
                )
            ),
             'custom_class' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEObjCustomClassLabel,
                'default' => '',
                'description' => $motopressCELang->CEObjCustomClassDesc
            )
        ));

        //media
        $videoObj = new MPCEObject();
        $videoObj->setId(MPCEShortcode::PREFIX . 'video');
        $videoObj->setName($motopressCELang->CEVideoObjName);
        $videoObj->setIcon('video.png');
        $videoObj->setTitle($motopressCELang->CEVideoObjTitle);
        $videoObj->setCloseType(MPCEObject::SELF_CLOSED);
        $videoObj->setParameters(array(
            'src' => array(
                'type' => 'video',
                'label' => $motopressCELang->CEVideoObjSrcLabel,
                'default' => MPCEShortcode::DEFAULT_VIDEO,
                'description' => $motopressCELang->CEVideoObjSrcDesc
            )
        ));

        //other
        $codeObj = new MPCEObject();
        $codeObj->setId(MPCEShortcode::PREFIX . 'code');
        $codeObj->setName($motopressCELang->CECodeObjName);
        $codeObj->setIcon('wordpress.png');
        $codeObj->setTitle($motopressCELang->CECodeObjTitle);
        $codeObj->setCloseType(MPCEObject::ENCLOSED);

        $spaceObj = new MPCEObject();
        $spaceObj->setId(MPCEShortcode::PREFIX . 'space');
        $spaceObj->setName($motopressCELang->CESpaceObjName);
        $spaceObj->setIcon('space.png');
        $spaceObj->setTitle($motopressCELang->CESpaceObjTitle);
        $spaceObj->setCloseType(MPCEObject::SELF_CLOSED);

        /* wp widgets */
        // archives
        $wpArchiveObj = new MPCEObject();
        $wpArchiveObj->setId(MPCEShortcode::PREFIX . 'wp_archives');
        $wpArchiveObj->setName($motopressCELang->CEwpArchives);
        $wpArchiveObj->setIcon('wordpress.png');
        $wpArchiveObj->setTitle($motopressCELang->CEwpArchives);
        $wpArchiveObj->setCloseType(MPCEObject::SELF_CLOSED);
        $wpArchiveObj->setParameters(array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpArchives,
                'description' => $motopressCELang->CEwpArchivesDescription
            ),
            'dropdown' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpDisplayAsDropDown,
                'default' => '',
                'description' => ''
            ),
            'count' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpShowPostCounts,
                'default' => '',
                'description' => ''
            ),
        ));

        // calendar
        $wpCalendarObj = new MPCEObject();
        $wpCalendarObj->setId(MPCEShortcode::PREFIX . 'wp_calendar');
        $wpCalendarObj->setName($motopressCELang->CEwpCalendar);
        $wpCalendarObj->setIcon('wordpress.png');
        $wpCalendarObj->setTitle($motopressCELang->CEwpCalendar);
        $wpCalendarObj->setCloseType(MPCEObject::SELF_CLOSED);
        $wpCalendarObj->setParameters(array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpCalendar,
                'description' => $motopressCELang->CEwpCalendarDescription
            )
        ));

        // wp_categories
        $wpCategoriesObj = new MPCEObject();
        $wpCategoriesObj->setId(MPCEShortcode::PREFIX . 'wp_categories');
        $wpCategoriesObj->setName($motopressCELang->CEwpCategories);
        $wpCategoriesObj->setIcon('wordpress.png');
        $wpCategoriesObj->setTitle($motopressCELang->CEwpCategories);
        $wpCategoriesObj->setCloseType(MPCEObject::SELF_CLOSED);
        $wpCategoriesObj->setParameters(array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpCategories,
                'description' => $motopressCELang->CEwpCategoriesDescription
            ),
            'dropdown' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpDisplayAsDropDown,
                'default' => '',
                'description' => ''
            ),
            'count' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpShowPostCounts,
                'default' => '',
                'description' => ''
            ),
            'hierarchy' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpCategoriesShowHierarchy,
                'default' => '',
                'description' => ''
            ),
        ));

        // wp_navmenu
        $wpCustomMenuObj = new MPCEObject();
        $wpCustomMenuObj->setId(MPCEShortcode::PREFIX . 'wp_navmenu');
        $wpCustomMenuObj->setName($motopressCELang->CEwpCustomMenu);
        $wpCustomMenuObj->setIcon('wordpress.png');
        $wpCustomMenuObj->setTitle($motopressCELang->CEwpCustomMenu);
        $wpCustomMenuObj->setCloseType(MPCEObject::SELF_CLOSED);

        $wpCustomMenu_menus = get_terms('nav_menu');
        $wpCustomMenu_array = array();
		$wpCustomMenu_default = '';
        if ($wpCustomMenu_menus){
            foreach($wpCustomMenu_menus as $menu){
				if (empty($wpCustomMenu_default))
					$wpCustomMenu_default = $menu->slug;
                $wpCustomMenu_array[$menu->slug] = $menu->name;
            }
        }else{
            $wpCustomMenu_array['no'] = $motopressCELang->CEwpCustomMenuNoMenus;
        }

        $wpCustomMenuObj->setParameters(array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpCustomMenu,
                'description' => $motopressCELang->CEwpCustomMenuDescription
            ),
            'nav_menu' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEwpCustomMenuSelectMenu,
                'default' => $wpCustomMenu_default,
                'description' => '',
                'list' => $wpCustomMenu_array
            )
        ));

        // wp_meta
        $wpMetaObj = new MPCEObject();
        $wpMetaObj->setId(MPCEShortcode::PREFIX . 'wp_meta');
        $wpMetaObj->setName($motopressCELang->CEwpMeta);
        $wpMetaObj->setIcon('wordpress.png');
        $wpMetaObj->setTitle($motopressCELang->CEwpMeta);
        $wpMetaObj->setCloseType(MPCEObject::SELF_CLOSED);
        $wpMetaObj->setParameters(array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpMeta,
                'description' => $motopressCELang->CEwpMetaDescription
            ),
        ));

        // wp_pages
        $wpPagesObj = new MPCEObject();
        $wpPagesObj->setId(MPCEShortcode::PREFIX . 'wp_pages');
        $wpPagesObj->setName( $motopressCELang->CEwpPages );
        $wpPagesObj->setIcon('wordpress.png');
        $wpPagesObj->setTitle($motopressCELang->CEwpPages);
        $wpPagesObj->setCloseType(MPCEObject::SELF_CLOSED);
        $wpPagesObj->setParameters(array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpPages,
                'description' => $motopressCELang->CEwpPagesDescription
            ),
            'sortby' => array(
                'type' => 'select',
                'label' => $motopressCELang->CESortBy,
                'default' => 'menu_order',
                'description' => '',
                'list' => array(
                    'post_title' => $motopressCELang->CESortByPageTitle,
                    'menu_order' => $motopressCELang->CESortByPageOrder,
                    'ID' => $motopressCELang->CESortByPageID
                ),
            ),
            'exclude' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEExclude,
                'default' => '',
                'description' => $motopressCELang->CEwpPagesExcludePages
            )
        ));

        // wp_posts
        $wpPostsObj = new MPCEObject();
        $wpPostsObj->setId(MPCEShortcode::PREFIX . 'wp_posts');
        $wpPostsObj->setName( $motopressCELang->CEwpRecentPosts );
        $wpPostsObj->setIcon('wordpress.png');
        $wpPostsObj->setTitle($motopressCELang->CEwpRecentPosts);
        $wpPostsObj->setCloseType(MPCEObject::SELF_CLOSED);
        $wpPostsObj->setParameters(array(
            'title' => array(
                    'type' => 'text',
                    'label' => $motopressCELang->CEParametersTitle,
                    'default' => $motopressCELang->CEwpRecentPosts,
                    'description' => $motopressCELang->CEwpRecentPostsDescription
            ),
            'number' => array(
                    'type' => 'text',
                    'label' => $motopressCELang->CEwpRecentPostsNumber,
                    'default' => '5',
                    'description' => ''
            ),
            'show_date' => array(
                    'type' => 'checkbox',
                    'label' => $motopressCELang->CEwpRecentPostsDisplayDate,
                    'default' => '',
                    'description' => ''
            ),
        ));

        // wp_comments
        $wpRecentCommentsObj = new MPCEObject();
        $wpRecentCommentsObj->setId(MPCEShortcode::PREFIX . 'wp_comments');
        $wpRecentCommentsObj->setName( $motopressCELang->CEwpRecentComments );
        $wpRecentCommentsObj->setIcon('wordpress.png');
        $wpRecentCommentsObj->setTitle($motopressCELang->CEwpRecentComments);
        $wpRecentCommentsObj->setCloseType(MPCEObject::SELF_CLOSED);
        $wpRecentCommentsObj->setParameters(array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpRecentComments,
                'description' => $motopressCELang->CEwpRecentCommentsDescription
            ),
            'number' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEwpRecentCommentsNumber,
                'default' => '5',
                'description' => ''
            )
        ));

        // wp_rss
        $wpRSSObj = new MPCEObject();
        $wpRSSObj->setId(MPCEShortcode::PREFIX . 'wp_rss');
        $wpRSSObj->setName( $motopressCELang->CEwpRSS );
        $wpRSSObj->setIcon('wordpress.png');
        $wpRSSObj->setTitle($motopressCELang->CEwpRSS);
        $wpRSSObj->setCloseType(MPCEObject::SELF_CLOSED);
        $wpRSSObj->setParameters(array(
            'url' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEwpRSSUrl,
                'default' => 'http://www.getmotopress.com/feed/',
                'description' => $motopressCELang->CEwpRSSUrlDescription
            ),
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEwpRSSFeedTitle,
                'default' => '',
                'description' => $motopressCELang->CEwpRSSFeedTitleDescription
            ),
            'items' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEwpRSSQuantity,
                'default' => 10,
                'description' => $motopressCELang->CEwpRSSQuantityDescription,
                'list' => range(1, 20),
            ),
            'show_summary' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpRSSDisplayContent,
                'default' => '',
                'description' => ''
            ),
            'show_author' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpRSSDisplayAuthor,
                'default' => '',
                'description' => ''
            ),
            'show_date' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpRSSDisplayDate,
                'default' => '',
                'description' => ''
            ),
        ));

	// search
        $wpSearchObj = new MPCEObject();
        $wpSearchObj->setId(MPCEShortcode::PREFIX . 'wp_search');
        $wpSearchObj->setName( $motopressCELang->CEwpRSSSearch );
        $wpSearchObj->setIcon('wordpress.png');
        $wpSearchObj->setTitle($motopressCELang->CEwpRSSSearch);
        $wpSearchObj->setCloseType(MPCEObject::SELF_CLOSED);
        $wpSearchObj->setParameters(array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpRSSSearch,
                'description' => $motopressCELang->CEwpRSSSearchDescription
            ),
        ));

        // tag cloud
        $wpTagCloudObj = new MPCEObject();
        $wpTagCloudObj->setId(MPCEShortcode::PREFIX . 'wp_tagcloud');
        $wpTagCloudObj->setName( $motopressCELang->CEwpTagCloud );
        $wpTagCloudObj->setIcon('wordpress.png');
        $wpTagCloudObj->setTitle( $motopressCELang->CEwpTagCloud );
        $wpTagCloudObj->setCloseType(MPCEObject::SELF_CLOSED);
        $wpTagCloudObj->setParameters(array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpTags,
                'description' => $motopressCELang->CEwpTagCloudDescription
            ),
            'taxonomy' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEwpTagCloudTaxonomy,
                'default' => 10,
                'description' => '',
                'list' => array(
                    'post_tag' => $motopressCELang->CEwpTags,
                    'category' => $motopressCELang->CEwpTagCloudCategories,
                )
            )
        ));
        /* wp widgets END */

        // WP Widgets Area
        $wpWidgetsAreaObj = new MPCEObject();
        $wpWidgetsAreaObj->setId(MPCEShortcode::PREFIX . 'wp_widgets_area');
        $wpWidgetsAreaObj->setName($motopressCELang->CEwpWidgetsArea);
        $wpWidgetsAreaObj->setIcon('sidebar.png');
        $wpWidgetsAreaObj->setTitle($motopressCELang->CEwpWidgetsArea);
        $wpWidgetsAreaObj->setCloseType(MPCEObject::SELF_CLOSED);

        global $wp_registered_sidebars;
        $wpWidgetsArea_array = array();
        $wpWidgetsArea_default = '';

        if ( $wp_registered_sidebars ){
            foreach ( $wp_registered_sidebars as $sidebar ) {
                if (empty($wpWidgetsArea_default))
                        $wpWidgetsArea_default = $sidebar['id'];
                $wpWidgetsArea_array[$sidebar['id']] = $sidebar['name'];
            }
        }else {
            $wpWidgetsArea_array['no'] = $motopressCELang->CEwpWidgetsAreaNoSidebars;
        }

        $wpWidgetsAreaObj->setParameters(array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => '',
                'description' => $motopressCELang->CEwpWidgetsAreaDescription
            ),
            'sidebar' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEwpWidgetsAreaSelect,
                'default' => $wpWidgetsArea_default,
                'description' => '',
                'list' => $wpWidgetsArea_array
            ),
			'custom_class' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEObjCustomClassLabel,
                'default' => '',
                'description' => $motopressCELang->CEObjCustomClassDesc
            )
        ));

        //google map
        $gMapObj = new MPCEObject();
        $gMapObj->setId(MPCEShortcode::PREFIX.'gmap');
        $gMapObj->setName($motopressCELang->CEGoogleMapObjName);
        $gMapObj->setIcon('map.png');
        $gMapObj->setTitle($motopressCELang->CEGoogleMapObjTitle);
        $gMapObj->setCloseType(MPCEObject::SELF_CLOSED);
        $gMapObj->setParameters(array(
            'address' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEGoogleMapObjAddressLabel,
                'default' => 'Sidney, New South Wales, Australia',
                'description' => $motopressCELang->CEGoogleMapObjAddressDesc
            ),
            'zoom' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEGoogleMapObjZoomLabel,
                'default' => 13,
                'description' => $motopressCELang->CEGoogleMapObjZoomDesc,
                'list' => array_combine(range(3, 19), range(3, 19)),
            )
        ));

        /* groups */
        $gridGroup = new MPCEGroup();
        $gridGroup->setId('grid');
        $gridGroup->setName($motopressCELang->CEGridGroupName);
        $gridGroup->setTitle($motopressCELang->CEGridGroupTitle);
        $gridGroup->setObjects(array($rowObj, $rowInnerObj, $spanObj, $spanInnerObj));

        $textGroup = new MPCEGroup();
        $textGroup->setId('text');
        $textGroup->setName($motopressCELang->CETextGroupName);
        $textGroup->setIcon('text.png');
        $textGroup->setTitle($motopressCELang->CETextGroupTitle);
        $textGroup->setPosition(0);
        $textGroup->setObjects(array($textObj, $headingObj, $codeObj));

        $imageGroup = new MPCEGroup();
        $imageGroup->setId('image');
        $imageGroup->setName($motopressCELang->CEImageGroupName);
        $imageGroup->setIcon('image.png');
        $imageGroup->setTitle($motopressCELang->CEImageGroupTitle);
        $imageGroup->setPosition(1);
        $imageGroup->setObjects(array($imageObj, $imageSlider));

        $buttonGroup = new MPCEGroup();
        $buttonGroup->setId('button');
        $buttonGroup->setName($motopressCELang->CEButtonGroupName);
        $buttonGroup->setIcon('button.png');
        $buttonGroup->setTitle($motopressCELang->CEButtonGroupTitle);
        $buttonGroup->setPosition(2);
        $buttonGroup->setObjects(array($buttonObj));

        $mediaGroup = new MPCEGroup();
        $mediaGroup->setId('media');
        $mediaGroup->setName($motopressCELang->CEMediaGroupName);
        $mediaGroup->setIcon('media.png');
        $mediaGroup->setTitle($motopressCELang->CEMediaGroupTitle);
        $mediaGroup->setPosition(3);
        $mediaGroup->setObjects(array($videoObj));

        $otherGroup = new MPCEGroup();
        $otherGroup->setId('other');
        $otherGroup->setName($motopressCELang->CEOtherGroupName);
        $otherGroup->setIcon('other.png');
        $otherGroup->setTitle($motopressCELang->CEOtherGroupTitle);
        $otherGroup->setPosition(4);
        $otherGroup->setObjects(array($gMapObj, $spaceObj));

        $wordpressGroup = new MPCEGroup();
        $wordpressGroup->setId('wordpress');
        $wordpressGroup->setName($motopressCELang->CEWordPressGroupName);
        $wordpressGroup->setIcon('wordpress.png');
        $wordpressGroup->setTitle($motopressCELang->CEWordPressGroupTitle);
        $wordpressGroup->setPosition(5);
        $wordpressGroup->setObjects(array($wpArchiveObj, $wpCalendarObj, $wpCategoriesObj, $wpCustomMenuObj, $wpMetaObj, $wpPagesObj, $wpPostsObj, $wpRecentCommentsObj, $wpRSSObj, $wpSearchObj, $wpTagCloudObj, $wpWidgetsAreaObj));

        $this->setLibrary(array($gridGroup, $textGroup, $imageGroup, $buttonGroup, $mediaGroup, $otherGroup, $wordpressGroup));
        $this->setSkippedGroups(array($gridGroup));
    }

    /**
     * @return array of MPCEGroup
     */
    public function getLibrary() {
        return $this->library;
    }

    /**
     * @param array of MPCEGroup $groups
     */
    private function setLibrary(array $groups) {
        if (!empty($groups)) {
            uasort($groups, array(__CLASS__, 'positionCmp'));
            foreach ($groups as $group) {
                if ($group instanceof MPCEGroup) {
                    if ($group->isValid()) {
                        if (count($group->getObjects()) > 0) {
                            $this->library[$group->getId()] = $group;
                        }
                    } else {
                        if (!self::$isAjaxRequest) {
                            $group->showErrors();
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array of MPCEGroup
     */
    public function getSkippedGroups() {
        return $this->skippedGroups;
    }

    /**
     * @param array of MPCEGroup $skippedGroups
     */
    private function setSkippedGroups(array $skippedGroups) {
        if (!empty($skippedGroups)) {
            foreach ($skippedGroups as $skippedGroup) {
                if ($skippedGroup instanceof MPCEGroup) {
                    if ($skippedGroup->isValid()) {
                        $this->skippedGroups[] = $skippedGroup;
                    }
                }
            }
        }
    }

    /**
     * @return string|bool
     */
    public function toJson() {
        $motopressCELibrary = array();
        foreach ($this->library as $group) {
            if (!in_array($group, $this->skippedGroups)) {
                $motopressCELibrary[$group->getId()] = $group;
            }
        }
        uasort($motopressCELibrary, array(__CLASS__, 'positionCmp'));
        return json_encode($motopressCELibrary);
    }

    /**
     * @return array
     */
    public function getObjectsList() {
        $list = array();
        foreach ($this->library as $group){
            foreach ($group->getObjects() as $object) {
                $parameters = $object->getParameters();
                if (!empty($parameters)) {
                    foreach ($parameters as $key => $value) {
                        unset($parameters[$key]);
                        $parameters[$key] = array();
                    }
                }

                $list[$object->getId()] = array(
                    'parameters' => $parameters,
                    'type' => $group->getId()
                );
            }
        }
        return $list;
    }


    /**
     * @return array
     */
    public function getObjectsNames() {
        $names = array();
        foreach ($this->library as $group){
            foreach ($group->getObjects() as $object){
                $names[] = $object->getId();
            }
        }
        return $names;
    }

    /**
     * @static
     * @param MPCEObject $a
     * @param MPCEObject $b
     * @return int
     */
    public static function nameCmp(MPCEObject $a, MPCEObject $b) {
        return strcmp($a->getName(), $b->getName());
    }

    /**
     * @param MPCEGroup $a
     * @param MPCEGroup $b
     * @return int
     */
    public function positionCmp(MPCEGroup $a, MPCEGroup $b) {
        $aPosition = $a->getPosition();
        $bPosition = $b->getPosition();
        if ($aPosition == $bPosition) {
            return 0;
        }
        return ($aPosition < $bPosition) ? -1 : 1;
    }

    /**
     * @return bool
     */
    private function isAjaxRequest() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ? true : false;
    }
}