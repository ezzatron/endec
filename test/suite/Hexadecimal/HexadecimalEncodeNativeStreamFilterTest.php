<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Hexadecimal;

use Eloquent\Endec\Endec;
use PHPUnit_Framework_TestCase;

/**
 * @covers \Eloquent\Endec\Hexadecimal\HexadecimalEncodeNativeStreamFilter
 * @covers \Eloquent\Endec\Transform\AbstractNativeStreamFilter
 */
class HexadecimalEncodeNativeStreamFilterTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        Endec::registerFilters();
    }

    public function testFilter()
    {
        $path = tempnam(sys_get_temp_dir(), 'endec');
        $stream = fopen($path, 'wb');
        stream_filter_append($stream, 'endec.hexadecimal-encode');
        fwrite($stream, 'f');
        fwrite($stream, 'oobar');
        fclose($stream);
        $actual = file_get_contents($path);
        unlink($path);

        $this->assertSame('666f6f626172', $actual);
    }
}
