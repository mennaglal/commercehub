<?php

use Illuminate\Support\Facades\DB;

it('runs against the test database', function () {
    expect(DB::getDatabaseName())->toBe('commercehub_test');
});
