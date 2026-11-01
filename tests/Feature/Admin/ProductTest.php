<?php

use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $permission = Permission::firstOrCreate([
        'name' => 'products.create',
        'guard_name' => 'web',
    ]);

    $role = Role::firstOrCreate([
        'name' => 'admin',
        'guard_name' => 'web',
    ]);

    $role->givePermissionTo($permission);

    $this->user = User::factory()->create();
    $this->user->assignRole($role);

    $this->store = Store::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->parentCategory = Category::factory()->create([
        'parent_id' => null,
    ]);

    $this->category = Category::factory()->create([
        'parent_id' => $this->parentCategory->id,
    ]);
});


it('creates a simple product with valid data', function () {
    $image = UploadedFile::fake()->image('product.jpg');

    $response = $this->actingAs($this->user)
        ->post(route('admin.products.store'), [
            'store_id' => $this->store->id,
            'parent_category_id' => $this->parentCategory->id,
            'category_id' => $this->category->id,

            'name_ar' => 'تيشيرت قطني',
            'name_en' => 'Cotton T-Shirt',
            'description_ar' => 'وصف المنتج',
            'description_en' => 'Product description',

            'sku' => 'TEST-SKU-1001',
            'slug' => '',

            'image' => $image,

            'status' => '1',
            'has_variants' => '0',
            'is_sale' => '1',

            'price' => '250',
            'sale_price' => '200',
            'stock' => '20',
        ]);

    // نتأكد إنه رجع لصفحة المنتجات
    $response->assertRedirect(route('admin.products.index'));

    // نتأكد إن رسالة النجاح ظهرت
    $response->assertSessionHas(
        'success',
        __('messages.created_successfully')
    );

    // نتأكد إن المنتج اتحفظ في قاعدة البيانات
    $this->assertDatabaseHas('products', [
        'sku' => 'TEST-SKU-1001',
        'name_ar' => 'تيشيرت قطني',
        'name_en' => 'Cotton T-Shirt',
        'name' => 'Cotton T-Shirt',
        'slug' => 'cotton-t-shirt',
        'price' => 250,
        'sale_price' => 200,
        'stock' => 20,
        'has_variants' => 0,
        'is_sale' => 1,
        'status' => 1,
    ]);

    $product = Product::where('sku', 'TEST-SKU-1001')->firstOrFail();

    expect($product->image)->not->toBeEmpty();

    File::delete(public_path($product->image));
});

it('does not create a product when sale price is greater than price', function () {
    $image = UploadedFile::fake()->image('product.jpg');

    $response = $this->actingAs($this->user)
        ->post(route('admin.products.store'), [
            'store_id' => $this->store->id,
            'parent_category_id' => $this->parentCategory->id,
            'category_id' => $this->category->id,

            'name_ar' => 'تيشيرت قطني',
            'name_en' => 'Cotton T-Shirt',

            'sku' => 'TEST-SKU-1002',
            'image' => $image,

            'status' => '1',
            'has_variants' => '0',
            'is_sale' => '1',

            'price' => '250',
            'sale_price' => '300',
            'stock' => '20',
        ]);

    // نتأكد إن فيه Validation Error على sale_price
    $response->assertSessionHasErrors(['sale_price']);

    // نتأكد إن المنتج متضافش
    $this->assertDatabaseMissing('products', [
        'sku' => 'TEST-SKU-1002',
    ]);
});
