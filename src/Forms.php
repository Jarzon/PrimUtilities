<?php
namespace PrimUtilities;

/*
* Trait that is going to be injected in the view
*/
trait Forms
{
    public function generateForms(array $forms)
    {
        foreach($forms as $form) {
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
                    <label><?=!empty($form['label'])? $this->translate($form['label']): $this->translate($form['name'])?>
                <?php }
                if($type == 'select'): ?>
                    <select name="<?=$form['name']?>" <?=isset($form['class'])? 'class="'.$form['class'].'"': ''?>>
                        <?php foreach($form['value'] as $index => $row) { ?>
                            <option value="<?=$row?>" <?=(isset($form['selected']) && $row == $form['selected'])? 'selected': ''?>><?=$index?></option>
                        <?php } ?>
                    </select>
                <?php else: ?>
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
                    >
                <?php endif ?>
                <?= isset($form['label'])? '</label>': ''; ?>
                <?php
            }
        }
    }
}