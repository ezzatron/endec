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

use Eloquent\Confetti\TransformStream;
use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

class Base64MimeTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->encodeTransform = new Base64MimeEncodeTransform;
        $this->decodeTransform = new Base64MimeDecodeTransform;
        $this->codec = new Base64Mime($this->encodeTransform, $this->decodeTransform);
    }

    public function testConstructor()
    {
        $this->assertSame($this->encodeTransform, $this->codec->encodeTransform());
        $this->assertSame($this->decodeTransform, $this->codec->decodeTransform());
    }

    public function testConstructorDefaults()
    {
        $this->codec = new Base64Mime;

        $this->assertEquals($this->encodeTransform, $this->codec->encodeTransform());
        $this->assertEquals($this->decodeTransform, $this->codec->decodeTransform());
    }

    public function encodingData()
    {
        //                                           decoded   encoded
        return array(
            'RFC 4648 base64 test vector 1' => array('',       ''),
            'RFC 4648 base64 test vector 2' => array('f',      "Zg==\r\n"),
            'RFC 4648 base64 test vector 3' => array('fo',     "Zm8=\r\n"),
            'RFC 4648 base64 test vector 4' => array('foo',    "Zm9v\r\n"),
            'RFC 4648 base64 test vector 5' => array('foob',   "Zm9vYg==\r\n"),
            'RFC 4648 base64 test vector 6' => array('fooba',  "Zm9vYmE=\r\n"),
            'RFC 4648 base64 test vector 7' => array('foobar', "Zm9vYmFy\r\n"),
        );
    }

    /**
     * @dataProvider encodingData
     */
    public function testEncode($decoded, $encoded)
    {
        $this->assertSame($encoded, $this->codec->encode($decoded));
    }

    public function testEncodeFullAlphabet()
    {
        $this->assertSame(
            "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/AA==\r\n",
            $this->codec->encode(
                pack(
                    'H*',
                    '00108310518720928b30d38f41149351559761969b71d79f8218a39259a7a29aabb2dbafc31cb3d35db7e39ebbf3dfbf00'
                )
            )
        );
    }

    public function testEncodeLineBreaks()
    {
        $this->assertSame(
            "YWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFh\r\nYWFhYWFhYQ==\r\n",
            $this->codec->encode(str_repeat('a', 64))
        );
    }

    /**
     * @dataProvider encodingData
     */
    public function testDecode($decoded, $encoded)
    {
        $this->assertSame($decoded, $this->codec->decode($encoded));
    }

    public function testDecodeFullAlphabet()
    {
        $this->assertSame(
            '00108310518720928b30d38f41149351559761969b71d79f8218a39259a7a29aabb2dbafc31cb3d35db7e39ebbf3dfbf00',
            bin2hex($this->codec->decode('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/AA=='))
        );
    }

    public function testCreateEncodeStream()
    {
        $this->assertEquals(new TransformStream($this->encodeTransform, 111), $this->codec->createEncodeStream(111));
    }

    public function testCreateDecodeStream()
    {
        $this->assertEquals(new TransformStream($this->decodeTransform, 111), $this->codec->createDecodeStream(111));
    }

    public function testInstance()
    {
        $className = get_class($this->codec);
        Liberator::liberateClass($className)->instance = null;
        $instance = $className::instance();

        $this->assertInstanceOf($className, $instance);
        $this->assertSame($instance, $className::instance());
    }
}
