<?php

namespace Rikscss\BaseApi\Tests\Models;

use Rikscss\BaseApi\Models\BaseApiLog;
use Rikscss\BaseApi\Tests\TestCase;

class BaseApiTest extends TestCase
{
    public function testCanCreateBaseApiLog() : void
    {
        $baseApiLog = $this->createBaseApiLog();

        $this->assertTrue($baseApiLog->exists);
        $this->assertTrue($baseApiLog->code === 200);
        $this->assertTrue($baseApiLog->is_error === 'success');
    }

    public function testSearchBaseApiLog() : void
    {
        $baseApiLog = $this->createBaseApiLog();
        $baseApiLog->code = 500;
        $baseApiLog->is_error = 'error';
        $baseApiLog->save();

        $this->assertFalse($baseApiLog->code === 200);
        $this->assertTrue($baseApiLog->code === 500);
    }

    public function createBaseApiLog() : Model
    {
        $baseApiLog = new BaseApiLog();
        $baseApiLog->user_id = 1;
        $baseApiLog->route = 'api.crm';
        $baseApiLog->payload = '[]';
        $baseApiLog->response = '{"code":200,"message":"crms successfully retrieved","data":[{"id":1,"name":"Individual","order":1,"table_name":"crm_individual_20231101053957_738","created_at":"2023-11-01 15:39:57","updated_at":"2023-11-01 15:39:57","deleted_at":null,"sections_link":"http:\/\/localhost\/crm\/1\/section"}]}';
        $baseApiLog->old_value = '[]';
        $baseApiLog->new_value = '[]';
        $baseApiLog->message = 'crms successfully retrieved';
        $baseApiLog->code = 200;
        $baseApiLog->is_error = 'success';
        $baseApiLog->save();

        return $baseApiLog;
    }
}
