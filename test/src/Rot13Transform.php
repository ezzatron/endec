<?php

use Eloquent\Endec\Transform\DataTransformInterface;

class Rot13Transform implements DataTransformInterface
{
    public function transform($data, $isEnd = false)
    {
        return array(str_rot13($data), strlen($data));
    }
}
