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
    protected $update = false;

    protected $lastRow;

    public function __construct(array $post)
    {
        $this->post = $post;
    }

    protected function row(string $type, string $name)
    {
        $row = ['type' => $type, 'name' => $name, 'attributes' => ['name' => $name], 'label' => $name, 'value' => ''];

        if(!in_array($type, ['textarea', 'select', 'radio'])) {
            $row['attributes']['type'] = $type;
        }

        $this->forms[$name] = $row;

        $this->lastRow =& $this->forms[$name];
    }

    public function updateValues($values = []) {
        if(empty($values)) {
            $values = $this->post;
        } else {
            $this->update = true;
        }

        foreach ($values as $name => $value) {
            $this->updateValue($name, $value);
        }
    }

    public function updateValue(string $name, $value) {
        if(isset($this->forms[$name])) {
            $form =& $this->forms[$name];

            if($form['type'] == 'checkbox') {
                if($form['selected'] && $value === '') {
                    unset($form['attributes']['checked']);
                }
                elseif (!$form['selected'] && $value !== '') {
                    $form['attributes']['checked'] = null;
                }
            }
            if($form['type'] == 'select' || $form['type'] == 'radio') {
                $form['selected'] = $value;
            } else if ($form['type'] != 'file') {
                $form['value'] = $value;
                $form['attributes']['value'] = $value;
            }
        }

        return;
    }

    public function generateTag(string $tag, array $attributes, $content = false) : string
    {
        $attr = '';

        foreach($attributes as $attribute => $value) {
            if($value === null) {
                $attr .= " $attribute";
            } else {
                $attr .= " $attribute=\"$value\"";
            }
        }

        $html = "<$tag$attr>";

        if($content !== false) {
            $html .= "$content</$tag>";
        }

        return $html;
    }

    public function generateInput(array $input)
    {
        if($input['type'] == 'radio') {
            $html = [];

            foreach($input['value'] as $index => $attrValue) {
                $attr = ['type' => $input['type'], 'name' => $input['name'], 'value' => $attrValue];

                if(isset($input['selected']) && $input['selected'] === $attrValue) {
                    $attr['checked'] = null;
                }

                $html[] = ['label' => $index, 'input' => $this->generateTag('input', $attr)];
            }
        }
        else if($input['type'] == 'select') {
            $content = '';

            foreach($input['value'] as $index => $attrValue) {
                $attr = ['value' => $attrValue];

                if(isset($input['selected']) && $input['selected'] === $attrValue) {
                    $attr['selected'] = null;
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

    public function hidden(string $name)
    {
        $this->row('hidden', $name);

        return $this;
    }

    public function text(string $name)
    {
        $this->row('text', $name);

        return $this;
    }

    public function textarea(string $name)
    {
        $this->row('textarea', $name);
        $this->lastRow['value'] = '';

        return $this;
    }

    public function password(string $name)
    {
        $this->row('password', $name);

        return $this;
    }

    public function email(string $name)
    {
        $this->row('email', $name);

        return $this;
    }

    public function url(string $name)
    {
        $this->row('url', $name);

        return $this;
    }

    public function number(string $name)
    {
        $this->row('number', $name);

        $this->lastRow['attributes']['step'] = 1;

        return $this;
    }

    public function float(string $name)
    {
        $this->row('float', $name);

        $this->lastRow['attributes']['step'] = 0.01;
        $this->lastRow['attributes']['type'] = 'number';

        return $this;
    }

    public function date(string $name)
    {
        $this->row('date', $name);
        $this->lastRow['attributes']['type'] = 'text';

        $this->lastRow['attributes']['pattern'] = '[0-9]{2}/[0-9]{2}/[0-9]{4}';

        return $this;
    }

    public function datetime(string $name)
    {
        $this->row('datetime', $name);
        $this->lastRow['attributes']['type'] = 'text';

        $this->lastRow['attributes']['pattern'] = '[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}';

        return $this;
    }

    public function select(string $name)
    {
        $this->row('select', $name);
        $this->lastRow['selected'] = '';

        return $this;
    }

    public function radio(string $name)
    {
        $this->row('radio', $name);

        return $this;
    }

    public function checkbox(string $name)
    {
        $this->row('checkbox', $name);

        $this->lastRow['selected'] = false;

        return $this;
    }

    public function file(string $name, string $destination, string $ext = '')
    {
        $this->row('file', $name);

        $this->lastRow['destination'] = $destination;
        $this->lastRow['ext'] = $ext;

        return $this;
    }

    public function min(int $min = 0)
    {
        $row =& $this->lastRow;
        $attr = 'minlength';
        if($row['type'] == 'number' || $row['type'] == 'float') {
            $attr = 'min';
        }

        $row['attributes'][$attr] = $min;

        $row['min'] = $min;

        return $this;
    }

    public function max(int $max = 0)
    {
        $row =& $this->lastRow;
        $attr = 'maxlength';
        if($row['type'] == 'number' || $row['type'] == 'float') {
            $attr = 'max';
        }

        $row['attributes'][$attr] = $max;

        $row['max'] = $max;

        return $this;
    }

    public function required(bool $required = true)
    {
        if($required) {
            $this->lastRow['attributes']['required'] = null;
        } else {
            unset($this->lastRow['attributes']['required']);
        }

        return $this;
    }

    public function value($value = '')
    {
        // TODO: force variable type base on input type
        // TODO: don't add value attribut if its a radio or a select
        $this->lastRow['value'] = $value;

        if(!is_array($value)) {
            $this->lastRow['attributes']['value'] = $value;
        }

        return $this;
    }

    public function types($types = [])
    {
        // TODO: implements

        $this->lastRow['attributes']['accept'] = implode(', ', $types);

        return $this;
    }

    public function class(string $classes = '')
    {
        if($this->lastRow['type'] == 'radio') {
            $this->lastRow['class'] = $classes;
        } else {
            $this->lastRow['attributes']['class'] = $classes;
        }

        return $this;
    }

    public function selected($selected = true)
    {
        if($this->lastRow['type'] === 'checkbox') {
            $this->lastRow['attributes']['checked'] = null;
        }

        $this->lastRow['selected'] = $selected;

        return $this;
    }

    public function multiple($multiple = true)
    {
        if($multiple) {
            $this->lastRow['attributes']['multiple'] = null;
        } else {
            unset($this->lastRow['attributes']['multiple']);
        }

        return $this;
    }

    public function pattern($pattern = false)
    {
        if($pattern) {
            $this->lastRow['attributes']['pattern'] = $pattern;
        } else {
            unset($this->lastRow['attributes']['pattern']);
        }


        return $this;
    }

    public function label($label = false)
    {
        if($label) {
            $this->lastRow['label'] = $label;
        } else {
            unset($this->lastRow['label']);
        }

        return $this;
    }

    public function attributes($attributes = [])
    {
        $this->lastRow['attributes'] += $attributes;

        return $this;
    }

    public function RemoveAttribute($attribute)
    {
        unset($this->lastRow['attributes'][$attribute]);

        return $this;
    }

    public function verification() : array
    {
        $values = [];

        foreach($this->forms as $input) {
            $value = '';

            if($input['type'] === 'checkbox') {
                if(isset($this->post[$input['name']])) {
                    $value = $input['value'];
                } else {
                    $value = false;
                }
            }
            else if($input['type'] === 'file') {
                if(empty($_FILES[$input['name']]) && isset($this->post[$input['name']])) {
                    throw new \Error('form seems to miss enctype attribute');
                }

                if($_FILES[$input['name']]['error'] !== UPLOAD_ERR_NO_FILE) {
                    $value = $_FILES[$input['name']];
                }
            }
            else if(isset($this->post[$input['name']])) {
                $value = $this->post[$input['name']];
            }

            if($input['type'] == 'number') {
                $value = (int)$value;
            }
            else if($input['type'] == 'float') {
                $value = (float)$value;
            }

            if(array_key_exists('required', $input['attributes']) && $value === '') {
                throw new \Exception($input['name'] . ' is required');
            }
            else if((!array_key_exists('required', $input['attributes']) && !empty($value))) {
                if(($input['type'] == 'text' || $input['type'] == 'password' || $input['type'] == 'email')) {
                    $numberChars = mb_strlen($value);
                    if(!empty($input['max']) && $numberChars > $input['max']) {
                        throw new \Exception($input['name'] . ' is too long');
                    }
                    else if(!empty($input['min']) && $numberChars < $input['min']) {
                        throw new \Exception($input['name'] . ' is too short');
                    }
                }
                else if(($input['type'] == 'number' || $input['type'] == 'float')) {
                    if(!empty($input['max']) && $value > $input['max']) {
                        throw new \Exception($input['name'] . ' is too high');
                    }
                    else if(!empty($input['min']) && $value < $input['min']) {
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
                        throw new \Error("$value doesn't exist");
                    }
                }

                if($input['type'] == 'email') {
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

                    // TODO: verify file type

                    if(array_key_exists('multiple', $input['attributes'])) {
                        foreach ($value['error'] AS $index => $error) {
                            $this->fileErrors($error);

                            list($location, $name) = $this->fileMove($value['tmp_name'][$index], $input['destination'], $input['ext']);

                            $infos[] = [
                                'name' => $name,
                                'original_name' => $value['name'][$index],
                                'type' => $value['type'][$index],
                                'location' => $location,
                                'size' => $value['size'][$index],
                            ];
                        }
                    } else {
                        if(is_array($value['error'])) {
                            throw new \Error('bypassed multiple limitation');
                        }

                        $this->fileErrors($value['error']);

                        list($location, $name) = $this->fileMove($value['tmp_name'], $input['destination'], $input['ext']);

                        $infos = [
                            'name' => $name,
                            'original_name' => $value['name'],
                            'type' => $value['type'],
                            'location' => $location,
                            'size' => $value['size']
                        ];
                    }

                    $value = $infos;
                }
            }

            $updated = false;

            if($input['type'] === 'file') {
                if($value !== '') {
                    $updated = true;
                }
            }
            else if($input['type'] == 'select' || $input['type'] == 'radio') {
                if($value != $input['selected']) {
                    $updated = true;
                }
            }
            else if($input['type'] === 'checkbox') {
                if(($input['selected'] && $value === '') || (!$input['selected'] && $value !== '')) {
                    $updated = true;
                }
            }
            else if($value !== $input['value']) {
                $updated = true;
            }

            if($updated) {
                $this->updateValue($input['name'], $value);
            }

            if((array_key_exists('required', $input['attributes']) && !$this->update) || $updated) {
                $values[$input['name']] = $value;
            }
        }

        return $values;
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
                throw new \Error('unknown upload error');
        }
    }

    private function fileMove(string $tmp_name, string $dest, string $ext = '')
    {
        $name = sha1_file($tmp_name);

        $file = sprintf('%s/%s',
            $dest,
            $name
        );

        if($ext != '') {
            $file .= ".$ext";
        }

        if (!$this->move_uploaded_file(
            $tmp_name,
            $file
        )) {
            throw new \Error('failed to move uploaded file');
        }

        return [$file, $name];
    }

    public function move_uploaded_file($tmp_name, $dest) {
        return move_uploaded_file(
            $tmp_name,
            $dest
        );
    }

    public function getForms() : array
    {
        $this->generateInputs();

        return $this->forms;
    }
}