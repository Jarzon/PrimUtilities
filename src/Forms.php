<?php
namespace PrimUtilities;

class Forms
{
    public $forms = [];
    protected $dateFormat = '[0-9]{2}/[0-9]{2}/[0-9]{4}';

    public function __construct($view)
    {
        $this->view = $view;
    }

    protected function row(string $type, $label, string $name, string $class, $value, $max, $min, string $placeholder, string $pattern, $step = false, string $selected = '') : array
    {
        $row = ['type' => $type, 'name' => $name, 'class' => $class, 'value' => $value];

        if($label === '') {
            $row['label'] = $name;
        } else if($label !== false) {
            $row['label'] = $label;
        }
        if($placeholder !== '') {
            $row['placholder'] = $placeholder;
        }
        if($pattern !== '') {
            $row['pattern'] = $pattern;
        }
        if($max !== false) {
            $row['max'] = $max;
        }
        if($min !== false) {
            $row['min'] = $min;
        }
        if($step !== false) {
            $row['step'] = $step;
        }
        if($selected !== '') {
            $row['selected'] = $selected;
        }

        return $row;
    }

    public function hidden(string $name, string $value = '', $max = false)
    {
        $this->forms[] = $this->row('password', false, $name, '', $value, $max, 0, '', '');
    }

    public function text($label = '', string $name, string $class = '', string $value = '', $max = false, int $min = 0, string $placeholder = '', string $pattern = '')
    {
        $this->forms[] = $this->row('text', $label, $name, $class, $value, $max, $min, $placeholder, $pattern);
    }

    public function password(string $label = '', string $name, string $class = '', string $value = '', $max = false, int $min = 0, string $placeholder = '')
    {
        $this->forms[] = $this->row('password', $label, $name, $class, $value, $max, $min, $placeholder, '');
    }

    public function email(string $label = '', string $name, string $class = '', string $value = '', $max = false, int $min = 0, string $placeholder = '')
    {
        $this->forms[] = $this->row('email', $label, $name, $class, $value, $max, $min, $placeholder, '');
    }

    public function url(string $label = '', string $name, string $class = '', string $value = '', $max = false, int $min = 0, string $placeholder = '')
    {
        $this->forms[] = $this->row('url', $label, $name, $class, $value, $max, $min, $placeholder, '');
    }

    public function number(string $label = '', string $name, string $class = '', string $value = '', $max = false, $min = false, float $step = 1, string $placeholder = '')
    {
        $this->forms[] = $this->row('number', $label, $name, $class, $value, $max, $min, $placeholder, '', $step);
    }

    public function float(string $label = '', string $name, string $class = '', string $value = '', $max = false, $min = false, float $step = 0.01, string $placeholder = '')
    {
        $this->forms[] = $this->row('number', $label, $name, $class, $value, $max, $min, $placeholder, '', $step);
    }

    public function date(string $label = '', string $name, string $class = '', string $value = '', string $placeholder = '', string $pattern = '[0-9]{2}/[0-9]{2}/[0-9]{4}')
    {
        $this->forms[] = $this->row('text', $label, $name, $class, $value, false, false, $placeholder, $pattern);
    }

    public function datetime(string $label = '', string $name, string $class = '', string $value = '', string $placeholder = '', string $pattern = '[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}')
    {
        $this->forms[] = $this->row('text', $label, $name, $class, $value, false, false, $placeholder, $pattern);
    }

    public function select(string $label = '', string $name, string $class = '', array $value = [], string $selected = '')
    {
        $this->forms[] = $this->row('select', $label, $name, $class, $value, false, false, '', '', $selected);
    }

    public function radio(string $name, string $class = '', array $value = [], string $selected = '')
    {
        $this->forms[] = $this->row('radio', false, $name, $class, $value, false, false, '', '', $selected);
    }

    public function verification(array $post) : array
    {
        $params = [];
        foreach($this->forms as $input) {
            $value = $post[$input['name']];

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
                $format = 'd/m/Y';
                $d = \DateTime::createFromFormat($format, $value);
                if(!$d || $d->format($format) != $value) {
                    throw new \Exception($input['name'] . ' is not a valid date');
                }
            }
            else if($input['type'] == 'select' || $input['type'] == 'radio') {

            }
            else if($input['type'] == 'checkbox') {
                if(isset($post[$input['name']])) {
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

    public function generateForms()
    {
        foreach($this->forms as $form) {
            $type = isset($form['type'])? $form['type']: 'text';

            if($type == 'radio') { ?>
                <div <?=isset($form['class'])? 'class="'.$form['class'].'"': ''?>>
                    <?php foreach($form['value'] as $index => $row) { ?>
                    <label>
                        <input type="<?=$type?>" name="<?=$form['name']?>" value="<?=$row?>"  <?=(isset($form['selected']) && $row == $form['selected'])? 'checked': ''?>>
                        <?=$this->view->translate($index)?>
                    </label>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <?php if(isset($form['label'])) { ?>
                    <label><?=$form['label']?>
                <?php }
                if($type == 'select') { ?>
                    <select name="<?=$form['name']?>" <?=isset($form['class'])? 'class="'.$form['class'].'"': ''?>>
                        <?php foreach($form['value'] as $index => $row) { ?>
                            <option value="<?=$row?>" <?=(isset($form['selected']) && $row == $form['selected'])? 'selected': ''?>><?=$index?></option>
                        <?php } ?>
                    </select>
                <?php } else { ?>
                    <input
                        type="<?=$type?>"
                        name="<?=$form['name']?>"
                        value="<?=$form['value']?>"
                        <?=isset($form['class'])? 'class="'.$form['class'].'"': ''?>
                        <?=isset($form['step'])? 'step="'.$form['step'].'"': ''?>
                        <?=isset($form['pattern'])? 'pattern="'.$form['pattern'].'"': ''?>
                        <?=isset($form['novalidate'])? 'novalidate': ''?>
                        <?=isset($form['required'])? 'required': ''?>
                        <?=isset($form['checked'])? 'checked': ''?>
                        <?=isset($form['placeholder'])? 'placeholder="'.$form['placeholder'].'"': ''?>

                        <?php if($type == 'text' || $type == 'password' || $type == 'email') { ?>
                            <?=isset($form['min'])? 'minlength="'.$form['min'].'"': ''?>
                            <?=isset($form['max'])? 'maxlength="'.$form['max'].'"': ''?>
                        <?php } ?>
                        <?php if($type == 'text' || $type == 'password' || $type == 'email') { ?>
                            <?=isset($form['min'])? 'min="'.$form['min'].'"': ''?>
                            <?=isset($form['max'])? 'max="'.$form['max'].'"': ''?>
                        <?php } ?>

                    >
                <?php } ?>
                <?= isset($form['label'])? '</label>': ''; ?>
                <?php
            }
        }
    }
}