<?php
namespace PrimUtilities;

/*
 * Form generator
 * */
class Forms
{
    public $forms = [];
    protected $dateFormat = '[0-9]{2}/[0-9]{2}/[0-9]{4}';
    protected $post = [];

    public function __construct(array $post)
    {
        $this->post = $post;
    }

    protected function row(string $type, $label, string $name, string $class, $value, $max, $min, array $attributes = [], $step = false, $selected = false) : array
    {
        $row = ['type' => $type, 'name' => $name, 'class' => $class, 'value' => $value, 'selected' => $selected];

        if($min !== false && $max !== false) {
            if($type == 'text' || $type == 'password' || $type == 'email' || $type == 'textarea') {
                $attributes['minlength'] = $min;
                $attributes['maxlength'] = $max;
            }
            else if($type == 'number' || $type == 'float') {
                $attributes['min'] = $min;
                $attributes['max'] = $max;
            }

            $row['min'] = $min;
            $row['max'] = $max;
        }

        if($class != '') {
            $attributes['class'] = $class;
        }

        if($step !== false) {
            $attributes['step'] = $step;
        }

        if($label === '') {
            $row['label'] = $name;
        } else if($label !== false) {
            $row['label'] = $label;
        }

        if($type != 'select' && $type != 'radio' && $type != 'checkbox' && $type != 'textarea') {
            if($type == 'float') {
                $type = 'number';
            }

            $attributes['type'] = $type;

            if($value !== '' && $type != 'file') $attributes['value'] = $value;
        }

        $attributes['name'] = $name;

        $row['attributes'] = $attributes;

        return $row;
    }

    public function updateValues(array $values = []) {
        if(empty($values)) {
            $values = $this->post;
        }

        foreach ($values as $name => $value) {
            $this->updateValue($name, $value);
        }
    }

    public function updateValue(string $name, $value) {
        foreach ($this->forms as &$form) {
            if($form['name'] == $name) {
                if($form['type'] == 'select' || $form['type'] == 'radio' || $form['type'] == 'checkbox') {
                    $form['selected'] = $value;
                } else {
                    $form['value'] = $value;
                    $form['attributes']['value'] = $value;
                }

                return;
            }
        }
    }

    public function generateTag(string $tag, array $attributes, $content = false) : string
    {
        $attr = '';

        foreach($attributes as $attribute => $value) {
            $attr .= " $attribute=\"$value\"";
        }

        $html = "<$tag$attr>";

        if($content !== false) {
            $html .= "$content</$tag>";
        }

        return $html;
    }

    public function generateInput(array $input)
    {
        if($input['type'] == 'radio' || $input['type'] == 'checkbox' ) {
            $html = [];

            foreach($input['value'] as $index => $attrValue) {
                $attr = ['type' => $input['type'], 'name' => $input['name'], 'value' => $attrValue];

                if($input['selected'] == $attrValue) {
                    $attr['checked'] = 'checked';
                }

                $html[] = ['label' => $index, 'input' => $this->generateTag('input', $attr)];
            }
        }
        else if($input['type'] == 'select') {
            $content = '';

            foreach($input['value'] as $index => $attrValue) {
                $attr = ['value' => $attrValue];

                if($input['selected'] == $attrValue) {
                    $attr['selected'] = 'selected';
                }

                $content .= $this->generateTag('option', $attr, $index);
            }

            $html = $this->generateTag('select', $input['attributes'], $content);
        }
        else if($input['type'] == 'textarea') {
            $html = $this->generateTag('textarea', $input['attributes'], $input['value']);
        }
        else {
            $html = $this->generateTag('input', $input['attributes']);
        }

        return $html;
    }

    public function generateInputs()
    {
        foreach ($this->forms as $index => &$form) {
            $this->forms[$index]['html'] = $this->generateInput($form);
        }
    }

    public function hidden(string $name, string $value = '', $max = false, array $attributes = [])
    {
        $this->forms[] = $this->row('hidden', false, $name, '', $value, $max, 0, $attributes);
    }

    public function text($label, string $name, string $class = '', string $value = '', $max = false, int $min = 0, array $attributes = [])
    {
        $this->forms[] = $this->row('text', $label, $name, $class, $value, $max, $min, $attributes);
    }

    public function textarea($label, string $name, string $class = '', string $value = '', $max = false, int $min = 0, array $attributes = [])
    {
        $this->forms[] = $this->row('textarea', $label, $name, $class, $value, $max, $min, $attributes);
    }

    public function password($label, string $name, string $class = '', string $value = '', $max = false, int $min = 0, array $attributes = [])
    {
        $this->forms[] = $this->row('password', $label, $name, $class, $value, $max, $min, $attributes);
    }

    public function email($label, string $name, string $class = '', string $value = '', $max = false, int $min = 0, array $attributes = [])
    {
        $this->forms[] = $this->row('email', $label, $name, $class, $value, $max, $min, $attributes);
    }

    public function url($label, string $name, string $class = '', string $value = '', $max = false, int $min = 0, array $attributes = [])
    {
        $this->forms[] = $this->row('url', $label, $name, $class, $value, $max, $min, $attributes);
    }

    public function number($label, string $name, string $class = '', string $value = '', $max = false, $min = false, float $step = 1, array $attributes = [])
    {
        $this->forms[] = $this->row('number', $label, $name, $class, $value, $max, $min, $attributes, $step);
    }

    public function float($label, string $name, string $class = '', string $value = '', $max = false, $min = false, float $step = 0.01, array $attributes = [])
    {
        $this->forms[] = $this->row('float', $label, $name, $class, $value, $max, $min, $attributes, $step);
    }

    public function date($label, string $name, string $class = '', string $value = '', string $pattern = '[0-9]{2}/[0-9]{2}/[0-9]{4}', array $attributes = [])
    {
        $attributes['pattern'] = $pattern;

        $this->forms[] = $this->row('text', $label, $name, $class, $value, false, false, $attributes);
    }

    public function datetime($label, string $name, string $class = '', string $value = '', string $pattern = '[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}', array $attributes = [])
    {
        $attributes['pattern'] = $pattern;

        $this->forms[] = $this->row('text', $label, $name, $class, $value, false, false, $attributes);
    }

    public function select($label, string $name, string $class = '', array $value = [], string $selected = '', array $attributes = [])
    {
        $this->forms[] = $this->row('select', $label, $name, $class, $value, false, false, $attributes, false, $selected);
    }

    public function radio(string $name, string $class = '', array $value = [], string $selected = '')
    {
        $this->forms[] = $this->row('radio', false, $name, $class, $value, false, false, [], false, $selected);
    }

    public function checkbox(string $name, string $class = '', array $value = [], string $selected = '')
    {
        $this->forms[] = $this->row('checkbox', false, $name, $class, $value, false, false, [], false, $selected);
    }

    public function file(string $label, string $name, string $class = '', bool $multiple = false, array $accept = [], array $attributes = [])
    {
        if($multiple) {
            $attributes['multiple'] = 'multiple';
        }
        if(!empty($accept)) {
            $attributes['accept'] = implode(', ', $accept);
        }

        $this->forms[] = $this->row('file', $label, $name, $class, $accept, false, false, $attributes, false, '');
    }

    public function verification() : array
    {
        $params = [];

        foreach($this->forms as $input) {
            if($input['type'] != 'file') {
                $value = $this->post[$input['name']];
            }

            if($input['type'] == 'number') {
                $value = (int)$value;
            }
            if($input['type'] == 'float') {
                $value = (float)$value;
            }

            if(($input['type'] == 'text' || $input['type'] == 'password' || $input['type'] == 'email') && isset($input['max'])) {
                $numberChars = mb_strlen($value);
                if($numberChars > $input['max'] && $input['max'] != -1) {
                    throw new \Exception($input['name'] . ' is too long');
                }
                else if($numberChars < $input['min']) {
                    throw new \Exception($input['name'] . ' is too short');
                }
            }
            else if(($input['type'] == 'number' || $input['type'] == 'float') && isset($input['min']) && isset($input['max'])) {
                if($value > $input['max'] && $input['max'] != -1) {
                    throw new \Exception($input['name'] . ' is too high');
                }
                else if($value < $input['min'] && $input['min'] != -1) {
                    throw new \Exception($input['name'] . ' is too low');
                }
            }
            else if($input['type'] == 'date') {
                // TODO: Be able to change the format for every date and match the $format with pattern

                $format = 'd/m/Y';
                $d = \DateTime::createFromFormat($format, $value);
                if(!$d || $d->format($format) != $value) {
                    throw new \Exception($input['name'] . ' is not a valid date');
                }
            }
            else if($input['type'] == 'select' || $input['type'] == 'radio') {
                $exist = false;

                foreach($input['value'] as $inputValue) {
                    if($value == $inputValue) {
                        $exist = true;
                    }
                }

                if(!$exist) {
                    throw new \Exception('error');
                }
            }
            else if($input['type'] == 'checkbox') {
                if(isset($this->post[$input['name']])) {
                    $value = true;
                } else {
                    $value = false;
                }
            }
            else if($input['type'] == 'email') {
                if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception($input['name'] . ' is not a valid email');
                }
            }
            else if($input['type'] == 'url') {
                if(!filter_var($value, FILTER_VALIDATE_URL)) {
                    throw new \Exception($input['name'] . ' is not a valid url');
                }
            }
            else if($input['type'] == 'file') {
                $infos = [];

                if(isset($input['attributes']['multiple'])) {
                    foreach ($_FILES[$input['name']]['error'] AS $index => $error) {
                        $this->fileErrors($error);

                        $infos[] = [
                            'name' => $_FILES[$input['name']]['name'][$index],
                            'type' => $_FILES[$input['name']]['type'][$index],
                            'location' => $_FILES[$input['name']]['tmp_name'][$index],
                            'size' => $_FILES[$input['name']]['size'][$index],
                        ];
                    }
                } else {
                    if(is_array($_FILES[$input['name']]['error'])) {
                        throw new \Exception('bypassed multiple limitation');
                    }

                    $this->fileErrors($_FILES[$input['name']]['error']);

                    $infos = [
                        'name' => $_FILES[$input['name']]['name'],
                        'type' => $_FILES[$input['name']]['type'],
                        'location' => $_FILES[$input['name']]['tmp_name'],
                        'size' => $_FILES[$input['name']]['size']
                    ];
                }

                $value = $infos;
            }

            $params[] = $value; // TODO: Add the input name as the key for the v1
        }

        return $params;
    }

    private function fileErrors($error)
    {
        switch ($error) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new \Exception('no file sent');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new \Exception('exceeded filesize limit');
            default:
                throw new \Exception('unknown errors');
        }
    }

    private function fileMove($tmp_name, $ext)
    {
        if (!move_uploaded_file(
            $tmp_name,
            sprintf('./uploads/%s.%s',
                sha1_file($tmp_name),
                $ext
            )
        )) {
            throw new \Exception('failed to move uploaded file');
        }
    }

    public function getForms() : array
    {
        $this->generateInputs();

        return $this->forms;
    }
}