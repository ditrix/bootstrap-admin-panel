<?php

namespace Tests\Unit\Helpers;

use App\Helpers\BootstrapTableHelper;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class BootstrapTableHelperTest extends TestCase
{
    // -------------------------------------------------------------------------
    // parsePaginationParams
    // -------------------------------------------------------------------------

    public function test_returns_defaults_when_request_is_empty(): void
    {
        $params = BootstrapTableHelper::parsePaginationParams(new Request);

        $this->assertSame(10, $params['limit']);
        $this->assertSame(0, $params['offset']);
        $this->assertSame('', $params['search']);
        $this->assertNull($params['sort']);
        $this->assertSame('asc', $params['order']);
    }

    public function test_returns_correct_shape_with_all_keys(): void
    {
        $params = BootstrapTableHelper::parsePaginationParams(new Request);

        $this->assertArrayHasKey('limit', $params);
        $this->assertArrayHasKey('offset', $params);
        $this->assertArrayHasKey('search', $params);
        $this->assertArrayHasKey('sort', $params);
        $this->assertArrayHasKey('order', $params);
    }

    public function test_limit_is_clamped_to_minimum_one(): void
    {
        $params = BootstrapTableHelper::parsePaginationParams(
            Request::create('/', 'GET', ['limit' => 0])
        );

        $this->assertSame(1, $params['limit']);
    }

    public function test_limit_is_clamped_to_maximum_hundred(): void
    {
        $params = BootstrapTableHelper::parsePaginationParams(
            Request::create('/', 'GET', ['limit' => 200])
        );

        $this->assertSame(100, $params['limit']);
    }

    public function test_limit_accepts_valid_value_in_range(): void
    {
        $params = BootstrapTableHelper::parsePaginationParams(
            Request::create('/', 'GET', ['limit' => 25])
        );

        $this->assertSame(25, $params['limit']);
    }

    public function test_offset_cannot_be_negative(): void
    {
        $params = BootstrapTableHelper::parsePaginationParams(
            Request::create('/', 'GET', ['offset' => -10])
        );

        $this->assertSame(0, $params['offset']);
    }

    public function test_offset_accepts_zero(): void
    {
        $params = BootstrapTableHelper::parsePaginationParams(
            Request::create('/', 'GET', ['offset' => 0])
        );

        $this->assertSame(0, $params['offset']);
    }

    public function test_offset_accepts_positive_value(): void
    {
        $params = BootstrapTableHelper::parsePaginationParams(
            Request::create('/', 'GET', ['offset' => 50])
        );

        $this->assertSame(50, $params['offset']);
    }

    public function test_order_desc_uppercase_normalizes_to_lowercase(): void
    {
        $params = BootstrapTableHelper::parsePaginationParams(
            Request::create('/', 'GET', ['order' => 'DESC'])
        );

        $this->assertSame('desc', $params['order']);
    }

    public function test_order_asc_uppercase_normalizes_to_lowercase(): void
    {
        $params = BootstrapTableHelper::parsePaginationParams(
            Request::create('/', 'GET', ['order' => 'ASC'])
        );

        $this->assertSame('asc', $params['order']);
    }

    public function test_order_invalid_value_falls_back_to_asc(): void
    {
        $params = BootstrapTableHelper::parsePaginationParams(
            Request::create('/', 'GET', ['order' => 'random'])
        );

        $this->assertSame('asc', $params['order']);
    }

    public function test_search_is_passed_through_as_string(): void
    {
        $params = BootstrapTableHelper::parsePaginationParams(
            Request::create('/', 'GET', ['search' => 'foo bar'])
        );

        $this->assertSame('foo bar', $params['search']);
    }

    public function test_sort_is_passed_through_as_given(): void
    {
        $params = BootstrapTableHelper::parsePaginationParams(
            Request::create('/', 'GET', ['sort' => 'created_at'])
        );

        $this->assertSame('created_at', $params['sort']);
    }

    public function test_sort_is_null_when_absent(): void
    {
        $params = BootstrapTableHelper::parsePaginationParams(new Request);

        $this->assertNull($params['sort']);
    }
}
