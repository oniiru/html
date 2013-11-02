<?php
/**
 * Description of MPCEObject
 *
 * @author dmitry
 */
class MPCEObject extends MPCEElement {
    public $closeType = self::ENCLOSED;
    public $parameters = array();

    protected $errors = array(
        'id' => array(),
        'name' => array(),
        'icon' => array(),
        'title' => array(),
        'closeType' => array(),
        'parameters' => array()
    );

    const ENCLOSED = 'enclosed';
    const SELF_CLOSED = 'self-closed';

    const ICON_DIR = 'ce/object';

    public function __construct() {
        $this->setIcon('no-object.png');
    }

    public function setIcon($icon) {
        parent::icon($icon, self::ICON_DIR);
    }

    /**
     * @return string
     */
    public function getCloseType() {
        return $this->closeType;
    }

    /**
     * @global stdClass $motopressCELang
     * @param string $closeType
     */
    public function setCloseType($closeType) {
        global $motopressCELang;

        if (is_string($closeType)) {
            $closeType = trim($closeType);
            if (!empty($closeType)) {
                $closeType = filter_var($closeType, FILTER_SANITIZE_STRING);
                if ($closeType === self::ENCLOSED || $closeType === self::SELF_CLOSED) {
                    $this->closeType = $closeType;
                } else {
                    $this->addError('closeType', strtr($motopressCELang->CECloseTypeValidation, array('%enclosed%' => self::ENCLOSED, '%self-closed%' => self::SELF_CLOSED)));
                }
            } else {
                $this->addError('closeType', $motopressCELang->CEEmpty);
            }
        } else {
            $this->addError('closeType', strtr($motopressCELang->CEInvalidArgumentType, array('%name%' => gettype($closeType))));
        }
    }

    /**
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters) {
        global $motopressCELang;

        if (!empty($parameters)) {
            $this->parameters = $parameters;
        } else {
            $this->addError('parameters', $motopressCELang->CEEmpty);
        }
    }

    public function isValid() {
        return (empty($this->errors['id']) && empty($this->errors['name']) && empty($this->errors['icon']) && empty($this->errors['title']) && empty($this->errors['closeType']) && empty($this->errors['parameters'])) ? true : false;
    }
}