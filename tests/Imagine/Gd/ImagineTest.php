<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Gd;

use Imagine\AbstractImagineTest;
use Imagine\Image\Box;

/**
 * @group gd
 */
class ImagineTest extends AbstractImagineTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
        }
    }

    protected function getEstimatedFontBox()
    {
        return new Box(112, 46);
    }

    protected function getImagine()
    {
        return new Imagine();
    }
}
