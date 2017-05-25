<?php
namespace PrimUtilities;

class Forms
{
    protected $forms = [];
    protected $dateFormat = '[0-9]{2}/[0-9]{2}/[0-9]{4}';

    public function hidden(string $name, string $value = '', int $max = -1, int $min = 0)
    {
        $this->forms[] = ['type' => 'hidden', 'label' => '', 'name' => $name, 'value' => $value, 'max' => $max, 'min' => $min];
    }

    public function text(string $label = '', string $name, string $class = '', string $value = '', string $pattern = '', int $max = -1, int $min = 0)
    {
        $this->forms[] = ['type' => 'text', 'label' => $label, 'name' => $name, 'class' => $class, 'value' => $value, 'max' => $max, 'min' => $min, 'pattern' => $pattern];
    }

    public function password(string $label = '', string $name, string $class = '', string $value = '', string $pattern = '', int $max = -1, int $min = -1)
    {
        $this->forms[] = ['type' => 'password', 'label' => $label, 'name' => $name, 'class' => $class, 'value' => $value, 'max' => $max, 'min' => $min];
    }

    public function email(string $label = '', string $name, string $class = '', string $value = '', int $max = -1, int $min = 0)
    {
        $this->forms[] = ['type' => 'email', 'label' => $label, 'name' => $name, 'class' => $class, 'value' => $value, 'max' => $max, 'min' => $min];
    }

    public function number(string $label = '', string $name, string $class = '', string $value = '', float $step = 1, int $max = -1, int $min = -1)
    {
        $this->forms[] = ['type' => 'number', 'label' => $label, 'name' => $name, 'class' => $class, 'value' => $value, 'max' => $max, 'min' => $min, 'step' => $step];
    }

    public function float(string $label = '', string $name, string $class = '', string $value = '', float $step = 0.01, float $max = -1.0, float $min = -1.0)
    {
        $this->forms[] = ['type' => 'number', 'label' => $label, 'name' => $name, 'class' => $class, 'value' => $value, 'max' => $max, 'min' => $min, 'step' => $step];
    }

    public function date(string $label = '', string $name, string $class = '', string $value = '', string $pattern = '[0-9]{2}/[0-9]{2}/[0-9]{4}')
    {
        $this->forms[] = ['type' => 'text', 'label' => $label, 'name' => $name, 'class' => $class, 'value' => $value, 'pattern' => $pattern];
    }

    public function datetime(string $label = '', string $name, string $class = '', string $value = '', string $pattern = '[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}')
    {
        $this->forms[] = ['type' => 'text', 'label' => $label, 'name' => $name, 'class' => $class, 'value' => $value, 'pattern' => $pattern];
    }

    public function select(string $label = '', string $name, string $class = '', array $value = [], string $checked = '')
    {
        $this->forms[] = ['type' => 'select', 'label' => $label, 'name' => $name, 'class' => $class, 'value' => $value, 'checked' => $checked];
    }

    public function radio(string $name, string $class = '', array $value = [], string $checked = '')
    {
        $this->forms[] = ['type' => 'radio', 'name' => $name, 'class' => $class, 'value' => $value, 'checked' => $checked];
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