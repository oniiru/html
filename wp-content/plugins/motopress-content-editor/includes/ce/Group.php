<?php
/**
 * Description of MPCEGroup
 *
 * @author dmitry
 */
class MPCEGroup extends MPCEElement {
    public $position = 0;
    public $objects = array();

    protected $errors = array(
        'id' => array(),
        'name' => array(),
        'icon' => array(),
        'title' => array(),
        'position' => array(),
        'objects' => array()
    );

    const ICON_DIR = 'ce/group';

    public function __construct() {
        $this->setIcon('no-group.png');
    }

    public function setIcon($icon) {
        parent::icon($icon, self::ICON_DIR);
    }

    /**
     * @return int
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * @global stdClass $motopressCELang
     * @param int $position
     */
    public function setPosition($position) {
        global $motopressCELang;

        if (is_int($position)) {
            $position = filter_var($position, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0, 'max_range' => 100)));
            if ($position !== false) {
                $this->position = $position;
            } else {
                $this->addError('position', $motopressCELang->CEPositionValidation);
            }
        } else {
            $this->addError('position', strtr($motopressCELang->CEInvalidArgumentType, array('%name%' => gettype($position))));
        }
    }

    /**
     * @return array of MPCEObject
     */
    public function getObjects() {
        return $this->objects;
    }

    /**
     * @param array of MPCEObject $objects
     */
    public function setObjects(array $objects) {
        global $motopressCELang;

        if (!empty($objects)) {
            uasort($objects, array('MPCELibrary', 'nameCmp'));
            foreach ($objects as $object) {
                if ($object instanceof MPCEObject) {
                    if ($object->isValid()) {
                        $this->objects[$object->getId()] = $object;
                    } else {
                        if (!MPCELibrary::$isAjaxRequest) {
                            $object->showErrors();
                        }
                    }
                }
            }
        } else {
            $this->addError('objects', $motopressCELang->CEEmpty);
        }
    }

    public function isValid() {
        return (empty($this->errors['id']) && empty($this->errors['name']) && empty($this->errors['icon']) && empty($this->errors['title']) && empty($this->errors['position']) && empty($this->errors['objects'])) ? true : false;
    }
}