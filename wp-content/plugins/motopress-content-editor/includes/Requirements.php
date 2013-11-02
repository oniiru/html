<?php
/**
 * Description of MPCERequirements
 *
 * @author dmitry
 */
class MPCERequirements {
    private $curl;
    private $gd;

    const MIN_JQUERY_VER = '1.8.3';
    const MIN_JQUERYUI_VER = '1.9.2';

    public function __construct() {
        @ini_set('display_errors', 1);
        @ini_set('magic_quotes_gpc', 0);
        @ini_set('magic_quotes_runtime', 0);
        @ini_set('magic_quotes_sybase', 0);
        @ini_set('allow_url_fopen', 1);

        $this->curl = $this->isCurlInstalled();
        $this->gd = $this->isGdInstalled();
    }

    private function isCurlInstalled() {
        return (in_array('curl', get_loaded_extensions()) && function_exists('curl_init')) ? true : false;
    }

    public function getCurl() {
        return $this->curl;
    }

    private function isGdInstalled() {
        return (in_array('gd', get_loaded_extensions()) && function_exists('gd_info')) ? true : false;
    }

    public function getGd() {
        return $this->gd;
    }
}