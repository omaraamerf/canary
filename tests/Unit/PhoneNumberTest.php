<?php

namespace Tests\Unit;

use App\Support\PhoneNumber;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{
    #[DataProvider('numbers')]
    public function test_local_numbers_become_international(?string $number, ?string $country, ?string $expected): void
    {
        $this->assertSame($expected, PhoneNumber::international($number, $country));
    }

    public static function numbers(): array
    {
        return [
            'syrian mobile with trunk zero' => ['0999 123 456', 'SY', '963999123456'],
            'without the trunk zero' => ['999123456', 'SY', '963999123456'],
            'already international with plus' => ['+963 999-123-456', 'SY', '963999123456'],
            'international with 00 prefix' => ['00963999123456', 'SY', '963999123456'],
            'other country code wins over the seller country' => ['+962 79 123 4567', 'SY', '962791234567'],
            'egypt two-digit code' => ['01001234567', 'EG', '201001234567'],
            'local number of an unknown country' => ['0999123456', null, null],
            'too short' => ['12345', 'SY', null],
            'empty' => [null, 'SY', null],
        ];
    }

    public function test_whatsapp_url_carries_the_message(): void
    {
        $this->assertSame(
            'https://wa.me/963999123456?text=%D9%85%D8%B1%D8%AD%D8%A8%D8%A7',
            PhoneNumber::whatsappUrl('0999123456', 'SY', 'مرحبا'),
        );
        $this->assertNull(PhoneNumber::whatsappUrl('', 'SY', 'مرحبا'));
    }
}
