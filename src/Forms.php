<?php
namespace PrimUtilities;

class Forms
{
    protected $forms = [];
    protected $dateFormat = '[0-9]{2}/[0-9]{2}/[0-9]{4}';

    protected function row($type, $label, string $name, string $class, $value, string $placeholder, string $pattern, int $max, int $min, $step = false)
    {
        $row = ['type' => $type, 'name' => $name, 'class' => $class, 'value' => $value, 'min' => $min];

        if($label !== false) {
            array_push($row, ['label' => $label]);
        }
        if($placeholder !== '') {
            array_push($row, ['placholder' => $placeholder]);
        }
        if($pattern !== '') {
            array_push($row, ['pattern' => $pattern]);
        }
        if($max !== false) {
            array_push($row, ['max' => $max]);
        }
        if($min !== false) {
            array_push($row, ['min' => $min]);
        }
        if($step !== false) {
            array_push($row, ['step' => $step]);
        }

        return $row;
    }

    public function hidden(string $name, string $value = '', $max = false)
    {
        $this->forms[] = $this->row('password', false, $name, '', $value, '', '', $max, -1);
    }

    public function text($label = '', string $name, string $class = '', string $value = '', string $placeholder = '', string $pattern = '', $max = false, int $min = 0)
    {
        $this->forms[] = $this->row('text', $label, $name, $class, $value, $placeholder, $pattern, $max, $min);
    }

    public function password(string $label = '', string $name, string $class = '', string $value = '', string $placeholder = '', $max = false, int $min = 0)
    {
        $this->forms[] = $this->row('password', $label, $name, $class, $value, $placeholder, '', $max, $min);
    }

    public function email(string $label = '', string $name, string $class = '', string $value = '', string $placeholder = '', $max = false, int $min = 0)
    {
        $this->forms[] = $this->row('email', $label, $name, $class, $value, $placeholder, '', $max, $min);
    }

    public function number(string $label = '', string $name, string $class = '', string $value = '', float $step = 1, string $placeholder = '', $max = false, $min = false)
    {
        $this->forms[] = $this->row('number', $label, $name, $class, $value, $placeholder, '', $max, $min, $step);
    }

    public function float(string $label = '', string $name, string $class = '', string $value = '', float $step = 0.01, string $placeholder = '', $max = false, $min = false)
    {
        $this->forms[] = $this->row('number', $label, $name, $class, $value, $placeholder, '', $max, $min, $step);
    }

    public function date(string $label = '', string $name, string $class = '', string $value = '', string $placeholder = '', string $pattern = '[0-9]{2}/[0-9]{2}/[0-9]{4}')
    {
        $this->forms[] = $this->row('text', $label, $name, $class, $value, $placeholder, $pattern, false, false);
    }

    public function datetime(string $label = '', string $name, string $class = '', string $value = '', string $placeholder = '', string $pattern = '[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}')
    {
        $this->forms[] = $this->row('text', $label, $name, $class, $value, $placeholder, $pattern, false, false);
    }

    public function select(string $label = '', string $name, string $class = '', array $value = [], string $checked = '')
    {
        $this->forms[] = $this->row('text', $label, $name, $class, $value, '', '', false, false);
    }

    public function radio(string $name, string $class = '', array $value = [], string $checked = '')
    {
        $this->forms[] = $this->row('text', false, $name, $class, $value, '', '', false, false);
    }

    public function verification(array $post)
    {
        $params = [];
        foreach($this->forms as $input) {
            $value = $post[$input['name']];

            if($input['type'] == 'text' || $input['type'] == 'password') {
                $numberChars = mb_strlen($value);
                if($numberChars > $input['max'] && $input['max'] != -1) {
                    return $input['name'] . ' is too long';
                }
                else if($numberChars < $input['min']) {
                    return $input['name'] . ' is too short';
                }
            }
            else if($input['type'] == 'number') {
                if($value > $input['max'] && $input['max'] != -1) {
                    return $input['name'] . ' is too high';
                }
                else if($value < $input['min'] && $input['min'] != -1) {
                    return $input['name'] . ' is too low';
                }
            }
            else if($input['type'] == 'date') {
                $format = 'd/m/Y';
                $d = \DateTime::createFromFormat($format, $value);
                if(!$d || $d->format($format) != $value) {
                    return $input['name'] . ' is not a valid date';
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
                    return $input['name'] . ' is not a valid email';
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
                        <input type="<?=$type?>" name="<?=$form['name']?>" value="<?=$row?>"  <?=(isset($form['checked']) && $row == $form['checked'])? 'checked': ''?>>
                        <?=$this->translate($index)?>
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

                        <?php if($form['type'] == 'text' || $form['type'] == 'password' || $form['type'] == 'email') { ?>
                            <?=isset($form['min'])? 'minlength="'.$form['min'].'"': ''?>
                            <?=isset($form['max'])? 'maxlength="'.$form['max'].'"': ''?>
                        <?php } ?>
                        <?php if($form['type'] == 'text' || $form['type'] == 'password' || $form['type'] == 'email') { ?>
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