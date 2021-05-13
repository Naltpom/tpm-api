<?php

namespace App\DataFixtures\Utils;

class BlameableListener extends \Gedmo\Blameable\BlameableListener
{
    public function getFieldValue($meta, $field, $eventAdapter)
    {
        return 'fixtures';
    }
}
