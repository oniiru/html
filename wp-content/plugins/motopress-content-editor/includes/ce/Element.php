<?php
/**
 * Parent class for MPCEGroup and MPCEObject
 *
 * @abstract
 * @author dmitry
 */
abstract class MPCEElement {
    public $id = null;
    public $name = null;
    public $icon = null;
    public $title = null;

    private static $mimeTypes = array('image/jpeg', 'image/png');

    const ID_REGEXP = '/^[a-z_\-.]{1,50}$/is';
    const NAME_REGEXP = '/^[^\\\\"]{1,100}$/is';
    const TITLE_REGEXP = '/^[^\\\\"]{1,300}$/is';

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @global stdClass $motopressCELang
     * @param string $id
     */
    public function setId($id) {
        global $motopressCELang;

        if (is_string($id)) {
            $id = trim($id);
            if (!empty($id)) {
                $id = filter_var($id, FILTER_SANITIZE_STRING);
                if (preg_match(self::ID_REGEXP, $id)) {
                    $this->id = $id;
                } else {
                    $this->addError('id', $motopressCELang->CEIdValidation);
                }
            } else {
                $this->addError('id', $motopressCELang->CEEmpty);
            }
        } else {
            $this->addError('id', strtr($motopressCELang->CEInvalidArgumentType, array('%name%' => gettype($id))));
        }
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @global stdClass $motopressCELang
     * @param string $name
     */
    public function setName($name) {
        global $motopressCELang;

        if (is_string($name)) {
            $name = trim($name);
            if (!empty($name)) {
                $name = filter_var($name, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                if (preg_match(self::NAME_REGEXP, $name)) {
                    $this->name = $name;
                } else {
                    $this->addError('name', $motopressCELang->CENameValidation);
                }
            } else {
                $this->addError('name', $motopressCELang->CEEmpty);
            }
        } else {
            $this->addError('name', strtr($motopressCELang->CEInvalidArgumentType, array('%name%' => gettype($name))));
        }
    }

    /**
     * @return string
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * @abstract
     * @param string $icon
     */
    abstract public function setIcon($icon);

    /**
     * @final
     * @global array $motopressCESettings
     * @global MPCERequirements $motopressCERequirements
     * @global stdClass $motopressCELang
     * @param string $icon
     * @param string $iconDir
     */
    final protected function icon($icon, $iconDir) {
        global $motopressCESettings;
        global $motopressCERequirements;
        global $motopressCELang;

        if (is_string($icon)) {
            $icon = trim($icon);
            if (!empty($icon)) {
                $icon = filter_var($icon, FILTER_SANITIZE_STRING);
                $iconPath = $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/images/' . $iconDir . '/' . $icon;
                if (file_exists($iconPath)) {
                    $mimeType = null;
                    if ($motopressCERequirements->getGd()) {
                        $info = getimagesize($iconPath);
                        if ($info) {
                            $mimeType = $info['mime'];
                        }
                    }
                    if (is_null($mimeType)) {
                        $mimeType = mime_content_type($iconPath);
                    }

                    if (!is_null($mimeType)) {
                        if (in_array($mimeType, self::$mimeTypes)) {
                            $this->icon = $motopressCESettings['plugin_root_url'] . '/' . $motopressCESettings['plugin_name'] . '/images/' . $iconDir . '/' . $icon . '?ver=' . $motopressCESettings['plugin_version'];
                        } else {
                            $this->addError('icon', strtr($motopressCELang->CEIconValidation, array('%name%' => $mimeType)));
                        }
                    } else {
                        $this->addError('icon', $motopressCELang->CEUnknownMimeType);
                    }
                } else {
                    $this->addError('icon', strtr($motopressCELang->fileNotExists, array('%name%' => $iconPath)));
                }
            } else {
                $this->addError('icon', $motopressCELang->CEEmpty);
            }
        } else {
            $this->addError('icon', strtr($motopressCELang->CEInvalidArgumentType, array('%name%' => gettype($icon))));
        }
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @global stdClass $motopressCELang
     * @param string $title
     */
    public function setTitle($title) {
        global $motopressCELang;

        if (is_string($title)) {
            $title = trim($title);
            if (!empty($title)) {
                $title = filter_var($title, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                if (preg_match(self::TITLE_REGEXP, $title)) {
                    $this->title = $title;
                } else {
                    $this->addError('title', $motopressCELang->CETitleValidation);
                }
            } else {
                $this->addError('title', $motopressCELang->CEEmpty);
            }
        } else {
            $this->addError('title', strtr($motopressCELang->CEInvalidArgumentType, array('%name%' => gettype($title))));
        }
    }

    /**
     * @abstract
     * @return bool
     */
    abstract public function isValid();

    /**
     * @return array of string
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @final
     * @param string $key
     * @param string $message
     */
    final protected function addError($key, $message) {
        if (array_key_exists($key, $this->errors)) {
            $this->errors[$key][] = $message;
        } else {
            return false;
        }
    }

    /**
     * @final
     */
    final public function showErrors() {
        echo '<div class="alert">';
        echo '<span class="object">' . $this . '</span>';
        echo '<ul class="property">';
        foreach ($this->errors as $property => $errors) {
            if (!empty($errors)) {
                echo '<li>' . $property . ':';
                echo '<ul class="errors">';
                foreach ($errors as $error) {
                    echo '<li>' . $error . '</li>';
                }
                echo '</ul>';
                echo '</li>';
            }
        }
        echo '</ul>';
        echo '</div>';
    }

    /**
     * @final
     * @return string
     */
    final public function __toString() {
        $str = 'id: ' . $this->getId() . ', ';
        $str .= 'name: ' . $this->getName() . ', ';
        $str .= 'icon: ' . $this->getIcon() . ', ';
        $str .= 'title: ' . $this->getTitle();
        if ($this instanceof MPCEObject) $str .= ', closeType: ' . $this->getCloseType();
        if ($this instanceof MPCEGroup) $str .= ', position: ' . $this->getPosition();
        return $str;
    }
}