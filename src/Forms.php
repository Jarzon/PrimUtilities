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
        $row = ['type' => $type, 'name' => $name, 'class' => $class];

        if($min !== false && $max !== false) {
            if($type == 'text' || $type == 'password' || $type == 'email') {
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

        if($type == 'select' || $type == 'radio' || $type == 'checkbox') {
            $row['value'] = $value;
        } else {
            if($type == 'float') {
                $type = 'number';
            }

            $attributes['type'] = $type;
            if($value !== '') $attributes['value'] = $value;
        }

        $attributes['name'] = $name;

        if($type == 'radio' || $type == 'checkbox' ) {
            $row['html'] = [];

            foreach($value as $index => $attrValue) {
                $attr = ['type' => $type, 'name' => $name, 'value' => $attrValue];

                if($selected == $attrValue) {
                    $attr['checked'] = 'checked';
                }

                $row['html'][] = ['label' => $index, 'input' => $this->generateTag('input', $attr)];
            }
        }
        else if($type == 'select') {
            $content = '';

            foreach($value as $index => $attrValue) {
                $attr = ['value' => $attrValue];

                if($selected == $attrValue) {
                    $attr['selected'] = 'selected';
                }

                $content .= $this->generateTag('option', $attr, $index);
            }

            $row['html'] = $this->generateTag('select', $attributes, $content);
        }
        else {
            $row['html'] = $this->generateTag('input', $attributes);
        }

        return $row;
    }

    public function generateTag(string $tag, array $attributes, string $content = '') : string
    {
        $attr = '';

        foreach($attributes as $attribute => $value) {
            $attr .= " $attribute=\"$value\"";
        }

        $html = "<$tag$attr>";

        if($content != '') {
            $html .= "$content</$tag>";
        }

        return $html;
    }

    public function hidden(string $name, string $value = '', $max = false, array $attributes = [])
    {
        $this->forms[] = $this->row('hidden', false, $name, '', $value, $max, 0, $attributes);
    }

    public function text($label, string $name, string $class = '', string $value = '', $max = false, int $min = 0, array $attributes = [])
    {
        $this->forms[] = $this->row('text', $label, $name, $class, $value, $max, $min, $attributes);
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

    public function verification() : array
    {
        $params = [];

        foreach($this->forms as $input) {
            $value = $this->post[$input['name']];

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
                    var_dump($inputValue);
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

            $params[] = $value;
        }

        return $params;
    }

    public function getForms() : array
    {
        return $this->forms;
    }
}