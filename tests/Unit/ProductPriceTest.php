<?php

use App\Models\Product;

uses(Tests\TestCase::class);

it('uses the sale price when the product is on sale', function () {
    $product = new Product(['price' => 100, 'sale_price' => 80, 'is_sale' => true]);

    expect($product->final_price)->toBe('80.00');
});

it('uses the normal price when the product is not on sale', function () {
    $product = new Product(['price' => 100, 'sale_price' => 80, 'is_sale' => false]);

    expect($product->final_price)->toBe('100.00');
});

it('falls back to the normal price when there is no sale price', function () {
    $product = new Product(['price' => 100, 'sale_price' => null, 'is_sale' => true]);

    expect($product->final_price)->toBe('100.00');
});

it('calculates the final price', function (bool $isSale, ?int $salePrice, string $expected) {
    $product = new Product(['price' => 100, 'sale_price' => $salePrice, 'is_sale' => $isSale]);

    expect($product->final_price)->toBe($expected);
})->with([
    'on sale'          => [true,  80,   '80.00'],
    'not on sale'      => [false, 80,   '100.00'],
    'no sale price'    => [true,  null, '100.00'],
]);
