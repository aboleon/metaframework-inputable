<?php

declare(strict_types=1);

namespace MetaFramework\Inputable\Tests\Unit;

use MetaFramework\Inputable\Support\Helpers;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function test_generate_input_id_replaces_special_characters_and_trims(): void
    {
        $this->assertSame('user_address_0__street', Helpers::generateInputId('user.address[0].street'));
        $this->assertSame('user_0', Helpers::generateInputId('user[0]'));
        $this->assertSame('user', Helpers::generateInputId('user.'));
    }

    public function test_generate_validation_id_normalizes_brackets_and_trims(): void
    {
        $this->assertSame('user.address.zip', Helpers::generateValidationId('user[address][zip]'));
        $this->assertSame('user', Helpers::generateValidationId('user.'));
    }

    public function test_generate_input_name_converts_dot_notation_to_brackets(): void
    {
        $this->assertSame('user[address][zip]', Helpers::generateInputName('user.address.zip'));
        $this->assertSame('plain', Helpers::generateInputName('plain'));
    }
}
