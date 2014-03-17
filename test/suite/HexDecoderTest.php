<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Encoding;

use PHPUnit_Framework_TestCase;

/**
 * @covers \Eloquent\Endec\Encoding\HexDecoder
 * @covers \Eloquent\Endec\Encoding\AbstractCodec
 */
class HexDecoderTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->codec = new HexDecoder(10);

        $this->output = '';
        $this->codec->on(
            'data',
            function ($data, $codec) {
                $this->output .= $data;
            }
        );
    }

    public function testConstructor()
    {
        $this->assertSame(10, $this->codec->bufferSize());
    }

    public function testConstructorDefaults()
    {
        $this->codec = new HexDecoder;

        $this->assertSame(8192, $this->codec->bufferSize());
    }

    public function encodingData()
    {
        $data = array('Empty' => array(''));
        for ($i = 1; $i < 16; $i++) {
            $data[sprintf('%d byte(s)', $i)] = array(substr('foobarbazquxdoom', 0, $i));
        }

        return $data;
    }

    /**
     * @dataProvider encodingData
     */
    public function testWriteEnd($data)
    {
        $this->codec->write(bin2hex($data));
        $this->codec->end();

        $this->assertSame($data, $this->output);
    }

    /**
     * @dataProvider encodingData
     */
    public function testEnd($data)
    {
        $this->codec->end(bin2hex($data));

        $this->assertSame($data, $this->output);
    }
}
