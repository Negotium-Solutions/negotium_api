<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

interface ApiInterface {
    public function get(Request $request, int $id = null) : Response;
    public function create(Request $request) : Response;
    public function update(Request $request, int $id) : Response;
    public function delete(int $id) : Response;
}
