<?php

namespace App\Exceptions;

use Exception;
use Throwable;

/**
 * Base cho lỗi "không tìm thấy tài nguyên" (domain).
 * Kế thừa và truyền message cụ thể, hoặc dùng message mặc định từ lang.
 */
abstract class ResourceNotFoundException extends Exception
{
    /**
     * HTTP status trả về cho client (thường là 404).
     *
     * @var int
     */
    protected $statusCode = 404;

    public function __construct(string $message = '', $code = 0, ?Throwable $previous = null)
    {
        if ($message === '') {
            $message = __('message.resource_not_found');
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
