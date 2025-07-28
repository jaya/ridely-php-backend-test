<?php

namespace Tests\Unit\Exceptions;

use App\Enums\ErrorMessagesEnum;
use App\Exceptions\ServiceException;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\TestCase;
use Tests\Unit\UnitTestCase;

// TODO revisar os nomes dos testes
class ServiceExceptionTest extends UnitTestCase
{

    public function testInvalidRequest()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $params = ['Invalid payload data'];
        $message = 'Missing required field: name';

        $exception = ServiceException::invalidRequest($message, $params);

        $this->assertEquals(ErrorMessagesEnum::INVALID_REQUEST->value, $exception->getCode());
        $this->assertEquals('common.error.invalid_request', $exception->getLabel());
        $this->assertEquals(sprintf('Invalid Request: %s', $message), $exception->getMessage());
    }

    public function testInvalidRequestParam()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $params = ['invalid field: order_by'];
        $message = 'Field "order_by" must be ASC or DESC';

        $exception = ServiceException::invalidRequestParam($message, $params);

        $this->assertEquals(ErrorMessagesEnum::INVALID_REQUEST_PARAM->value, $exception->getCode());
        $this->assertEquals('common.error.invalid_request_param', $exception->getLabel());
        $this->assertEquals(sprintf('Invalid Request Parameter: %s', $message), $exception->getMessage());
    }
}
