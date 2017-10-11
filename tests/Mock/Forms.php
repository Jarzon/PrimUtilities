<?php
namespace Tests\Mock;

Class Forms extends \PrimUtilities\Forms {

    public function move_uploaded_file($tmp_name, $dest) {
        return rename(
            $tmp_name,
            $dest
        );
    }
}