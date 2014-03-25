<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Base64;

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

/**
 * @covers \Eloquent\Endec\Base64\Base64MimeEncodeTransform
 * @covers \Eloquent\Endec\Transform\AbstractDataTransform
 */
class Base64MimeEncodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new Base64MimeEncodeTransform;
    }

    public function transformData()
    {
        //                      input                                                        output bytesConsumed
        return array(
            'Empty'    => array('',                                                          '',    0),
            '1 byte'   => array('f',                                                         '',    0),
            '56 bytes' => array('12345678901234567890123456789012345678901234567890123456',  '',    0),

            '57 bytes' => array(
                '123456789012345678901234567890123456789012345678901234567',
                "MTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3ODkwMTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3\r\n",
                57
            ),
        );
    }

    /**
     * @dataProvider transformData
     */
    public function testTransform($input, $output, $bytesConsumed)
    {
        $this->assertSame(array($output, $bytesConsumed), $this->transform->transform($input));
    }

    public function transformEndData()
    {
        //                     input     output          bytesConsumed
        return array(
            'Empty'   => array('',       '',             0),
            '1 byte'  => array('f',      "Zg==\r\n",     1),
            '2 bytes' => array('fo',     "Zm8=\r\n",     2),
            '3 bytes' => array('foo',    "Zm9v\r\n",     3),
            '4 bytes' => array('foob',   "Zm9vYg==\r\n", 4),
            '5 bytes' => array('fooba',  "Zm9vYmE=\r\n", 5),
            '6 bytes' => array('foobar', "Zm9vYmFy\r\n", 6),

            '56 bytes' => array(
                '12345678901234567890123456789012345678901234567890123456',
                "MTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3ODkwMTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY=\r\n",
                56
            ),

            '57 bytes' => array(
                '123456789012345678901234567890123456789012345678901234567',
                "MTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3ODkwMTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3\r\n",
                57
            ),

            '90 bytes' => array(
                '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
                "MTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3ODkwMTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3\r\n" .
                "ODkwMTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3ODkw\r\n",
                90
            ),
        );
    }

    /**
     * @dataProvider transformEndData
     */
    public function testTransformEnd($input, $output, $bytesConsumed)
    {
        $this->assertSame(array($output, $bytesConsumed), $this->transform->transform($input, true));
    }

    public function testInstance()
    {
        $className = get_class($this->transform);
        Liberator::liberateClass($className)->instance = null;
        $instance = $className::instance();

        $this->assertInstanceOf($className, $instance);
        $this->assertSame($instance, $className::instance());
    }
}