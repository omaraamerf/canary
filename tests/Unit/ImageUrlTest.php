<?php

namespace Tests\Unit;

use App\Support\ImageUrl;
use Tests\TestCase;

class ImageUrlTest extends TestCase
{
    private const UPLOAD = 'https://res.cloudinary.com/demo/image/upload/v1712/birds/abc.jpg';

    public function test_cloudinary_images_are_asked_for_the_shown_width_in_a_modern_format(): void
    {
        $this->assertSame(
            'https://res.cloudinary.com/demo/image/upload/f_auto,q_auto,c_limit,w_320/v1712/birds/abc.jpg',
            ImageUrl::width(self::UPLOAD, 320),
        );
    }

    public function test_a_url_that_already_has_a_transformation_is_left_alone(): void
    {
        $url = 'https://res.cloudinary.com/demo/image/upload/c_fill,w_300/v1712/birds/abc.jpg';

        $this->assertSame($url, ImageUrl::width($url, 800));
        $this->assertNull(ImageUrl::srcset($url, [400, 800]));
    }

    public function test_other_sources_pass_through_without_a_srcset(): void
    {
        $this->assertSame('https://example.com/bird.jpg', ImageUrl::width('https://example.com/bird.jpg', 400));
        $this->assertNull(ImageUrl::srcset('https://example.com/bird.jpg', [400]));
        $this->assertNull(ImageUrl::width(null, 400));
    }

    public function test_srcset_lists_each_width_once_in_order(): void
    {
        $this->assertSame(
            'https://res.cloudinary.com/demo/image/upload/f_auto,q_auto,c_limit,w_160/v1712/birds/abc.jpg 160w, '
            .'https://res.cloudinary.com/demo/image/upload/f_auto,q_auto,c_limit,w_320/v1712/birds/abc.jpg 320w',
            ImageUrl::srcset(self::UPLOAD, [320, 160, 320]),
        );
    }

    public function test_local_placeholders_use_their_smaller_files(): void
    {
        $this->assertSame('/images/birds/yellow-canary-400.jpg', ImageUrl::width('/images/birds/yellow-canary.jpg', 320));
        $this->assertSame('/images/birds/yellow-canary-800.jpg', ImageUrl::width('/images/birds/yellow-canary.jpg', 760));
        $this->assertSame('/images/birds/yellow-canary.jpg', ImageUrl::width('/images/birds/yellow-canary.jpg', 1600));
        $this->assertSame(
            '/images/birds/yellow-canary-400.jpg 400w, /images/birds/yellow-canary-800.jpg 800w, /images/birds/yellow-canary.jpg 1200w',
            ImageUrl::srcset('/images/birds/yellow-canary.jpg', [320]),
        );
    }
}
