<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SchemaController extends Controller
{
    public function showSchema()
    {
        $schema = \DB::select('PRAGMA table_info(volcanoes)');
        return response()->json($schema);
    }
}
