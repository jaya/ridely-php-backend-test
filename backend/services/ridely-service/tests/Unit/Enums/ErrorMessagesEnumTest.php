<?php

namespace Tests\Unit\Enums;

use App\Enums\ErrorMessagesEnum;
use Illuminate\Support\Facades\Log;
use Tests\Unit\UnitTestCase;

// TODO revisar os nomes dos testes
class ErrorMessagesEnumTest extends UnitTestCase
{
    public function testLabelMethodReturnsCorrectValue()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $this->assertEquals('common.success', ErrorMessagesEnum::OK->label());
        $this->assertEquals('common.error.nok', ErrorMessagesEnum::NOK->label());
        $this->assertEquals('common.error.unsupported_media_type_error', ErrorMessagesEnum::UNSUPPORTED_MEDIA_TYPE->label());
        $this->assertEquals('common.error.invalid_request', ErrorMessagesEnum::INVALID_REQUEST->label());
        $this->assertEquals('common.error.invalid_request_param', ErrorMessagesEnum::INVALID_REQUEST_PARAM->label());
    }

    public function testMessageWithoutParamsReturnsStaticMessage()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $this->assertEquals('Success', ErrorMessagesEnum::OK->message());
        $this->assertEquals('Error', ErrorMessagesEnum::NOK->message());
        $this->assertEquals('Unable to create driver.', ErrorMessagesEnum::UNABLE_TO_CREATE_DRIVER->message());
    }

    public function testMessageWithParamsReturnsFormattedMessage()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $msg = ErrorMessagesEnum::INVALID_REQUEST->message('Missing required field: name');
        $this->assertEquals('Invalid Request: Missing required field: name', $msg);

        $msg2 = ErrorMessagesEnum::UNSUPPORTED_MEDIA_TYPE->message('text/xml', 'application/json, application/xml');
        $this->assertEquals('Unsupported media type: text/xml, supported types are (application/json, application/xml)', $msg2);
    }

    public function testEnumValues()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $this->assertEquals(1, ErrorMessagesEnum::OK->value);
        $this->assertEquals(2, ErrorMessagesEnum::NOK->value);
        $this->assertEquals(11, ErrorMessagesEnum::INVALID_REQUEST->value);
        $this->assertEquals(34, ErrorMessagesEnum::DRIVER_NOT_FOUND->value);
    }
}