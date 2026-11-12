<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name_ar' => 'سماعة بلوتوث لاسلكية', 'name_en' => 'Wireless Bluetooth Headphones', 'price' => 750, 'image' => 'images/demo/products/product-01.jpg', 'category_slug' => 'audio', 'store_slug' => 'tech-zone', 'sku' => 'DEMO-001'],
            ['name_ar' => 'ماوس لاسلكي', 'name_en' => 'Wireless Mouse', 'price' => 400, 'image' => 'images/demo/products/product-02.jpg', 'category_slug' => 'computers-laptops', 'store_slug' => 'tech-zone', 'sku' => 'DEMO-002'],
            ['name_ar' => 'كيبورد ميكانيكي', 'name_en' => 'Mechanical Keyboard', 'price' => 1800, 'image' => 'images/demo/products/product-03.jpg', 'category_slug' => 'computers-laptops', 'store_slug' => 'tech-zone', 'sku' => 'DEMO-003'],
            ['name_ar' => 'حامل لابتوب قابل للتعديل', 'name_en' => 'Adjustable Laptop Stand', 'price' => 550, 'image' => 'images/demo/products/product-04.jpg', 'category_slug' => 'computers-laptops', 'store_slug' => 'tech-zone', 'sku' => 'DEMO-004'],
            ['name_ar' => 'شاحن سريع USB-C', 'name_en' => 'USB-C Fast Charger', 'price' => 250, 'image' => 'images/demo/products/product-05.jpg', 'category_slug' => 'phones-accessories', 'store_slug' => 'tech-zone', 'sku' => 'DEMO-005'],
            ['name_ar' => 'هاتف ذكي', 'name_en' => 'Smartphone', 'price' => 4200, 'image' => 'images/demo/products/product-06.jpg', 'category_slug' => 'phones-accessories', 'store_slug' => 'tech-zone', 'sku' => 'DEMO-006'],
            ['name_ar' => 'ساعة ذكية', 'name_en' => 'Smart Watch', 'price' => 1650, 'image' => 'images/demo/products/product-07.jpg', 'category_slug' => 'smart-devices', 'store_slug' => 'tech-zone', 'sku' => 'DEMO-007'],
            ['name_ar' => 'سماعة رأس للألعاب', 'name_en' => 'Gaming Headset', 'price' => 980, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'gaming', 'store_slug' => 'tech-zone', 'sku' => 'DEMO-008'],
            ['name_ar' => 'كاميرا ويب HD', 'name_en' => 'HD Webcam', 'price' => 620, 'image' => 'images/demo/products/product-09.jpg', 'category_slug' => 'computers-laptops', 'store_slug' => 'tech-zone', 'sku' => 'DEMO-009'],
            ['name_ar' => 'مكبر صوت محمول', 'name_en' => 'Portable Bluetooth Speaker', 'price' => 890, 'image' => 'images/demo/products/product-10.jpg', 'category_slug' => 'audio', 'store_slug' => 'tech-zone', 'sku' => 'DEMO-010'],
            ['name_ar' => 'تيشيرت قطني رجالي', 'name_en' => 'Men Cotton T-Shirt', 'price' => 350, 'image' => 'images/demo/products/product-11.jpg', 'category_slug' => 'men-clothing', 'store_slug' => 'urban-style', 'sku' => 'DEMO-011'],
            ['name_ar' => 'قميص رجالي كلاسيك', 'name_en' => 'Classic Men Shirt', 'price' => 650, 'image' => 'images/demo/products/product-12.jpg', 'category_slug' => 'men-clothing', 'store_slug' => 'urban-style', 'sku' => 'DEMO-012'],
            ['name_ar' => 'بنطلون جينز', 'name_en' => 'Denim Jeans', 'price' => 800, 'image' => 'images/demo/products/product-02.jpg', 'category_slug' => 'men-clothing', 'store_slug' => 'urban-style', 'sku' => 'DEMO-013'],
            ['name_ar' => 'فستان صيفي', 'name_en' => 'Summer Dress', 'price' => 950, 'image' => 'images/demo/products/product-02.jpg', 'category_slug' => 'women-clothing', 'store_slug' => 'urban-style', 'sku' => 'DEMO-014'],
            ['name_ar' => 'جاكيت شتوي', 'name_en' => 'Winter Jacket', 'price' => 1500, 'image' => 'images/demo/products/product-11.jpg', 'category_slug' => 'men-clothing', 'store_slug' => 'urban-style', 'sku' => 'DEMO-015'],
            ['name_ar' => 'حذاء رياضي', 'name_en' => 'Casual Sneakers', 'price' => 1200, 'image' => 'images/demo/products/product-06.jpg', 'category_slug' => 'shoes', 'store_slug' => 'urban-style', 'sku' => 'DEMO-016'],
            ['name_ar' => 'حقيبة يد نسائية', 'name_en' => 'Women Handbag', 'price' => 900, 'image' => 'images/demo/products/product-02.jpg', 'category_slug' => 'bags', 'store_slug' => 'urban-style', 'sku' => 'DEMO-017'],
            ['name_ar' => 'محفظة جلد', 'name_en' => 'Leather Wallet', 'price' => 300, 'image' => 'images/demo/products/product-12.jpg', 'category_slug' => 'fashion-accessories', 'store_slug' => 'urban-style', 'sku' => 'DEMO-018'],
            ['name_ar' => 'حقيبة ظهر يومية', 'name_en' => 'Everyday Backpack', 'price' => 650, 'image' => 'images/demo/products/product-12.jpg', 'category_slug' => 'bags', 'store_slug' => 'urban-style', 'sku' => 'DEMO-019'],
            ['name_ar' => 'حزام جلد', 'name_en' => 'Leather Belt', 'price' => 280, 'image' => 'images/demo/products/product-11.jpg', 'category_slug' => 'fashion-accessories', 'store_slug' => 'urban-style', 'sku' => 'DEMO-020'],
            ['name_ar' => 'كوب قهوة سيراميك', 'name_en' => 'Ceramic Coffee Mug', 'price' => 180, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'kitchen-dining', 'store_slug' => 'home-avenue', 'sku' => 'DEMO-021'],
            ['name_ar' => 'زجاجة مياه', 'name_en' => 'Reusable Water Bottle', 'price' => 220, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'kitchen-dining', 'store_slug' => 'home-avenue', 'sku' => 'DEMO-022'],
            ['name_ar' => 'طقم ملايات', 'name_en' => 'Bed Sheet Set', 'price' => 850, 'image' => 'images/demo/products/product-07.jpg', 'category_slug' => 'bedding', 'store_slug' => 'home-avenue', 'sku' => 'DEMO-023'],
            ['name_ar' => 'وسادة نوم مريحة', 'name_en' => 'Comfort Sleeping Pillow', 'price' => 400, 'image' => 'images/demo/products/product-07.jpg', 'category_slug' => 'bedding', 'store_slug' => 'home-avenue', 'sku' => 'DEMO-024'],
            ['name_ar' => 'مصباح مكتبي', 'name_en' => 'Modern Desk Lamp', 'price' => 600, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'lighting', 'store_slug' => 'home-avenue', 'sku' => 'DEMO-025'],
            ['name_ar' => 'مزهرية ديكور', 'name_en' => 'Decorative Ceramic Vase', 'price' => 520, 'image' => 'images/demo/products/product-07.jpg', 'category_slug' => 'home-decor', 'store_slug' => 'home-avenue', 'sku' => 'DEMO-026'],
            ['name_ar' => 'سجادة ديكور', 'name_en' => 'Decorative Area Rug', 'price' => 1000, 'image' => 'images/demo/products/product-07.jpg', 'category_slug' => 'home-decor', 'store_slug' => 'home-avenue', 'sku' => 'DEMO-027'],
            ['name_ar' => 'طقم أواني طهي', 'name_en' => 'Cookware Set', 'price' => 1450, 'image' => 'images/demo/products/product-14.jpg', 'category_slug' => 'kitchen-dining', 'store_slug' => 'home-avenue', 'sku' => 'DEMO-028'],
            ['name_ar' => 'منظم أدراج', 'name_en' => 'Drawer Organizer', 'price' => 260, 'image' => 'images/demo/products/product-09.jpg', 'category_slug' => 'furniture', 'store_slug' => 'home-avenue', 'sku' => 'DEMO-029'],
            ['name_ar' => 'طاولة جانبية', 'name_en' => 'Side Table', 'price' => 1850, 'image' => 'images/demo/products/product-07.jpg', 'category_slug' => 'furniture', 'store_slug' => 'home-avenue', 'sku' => 'DEMO-030'],
            ['name_ar' => 'طقم عناية بالبشرة', 'name_en' => 'Skin Care Set', 'price' => 700, 'image' => 'images/demo/products/product-09.jpg', 'category_slug' => 'skincare', 'store_slug' => 'glow-beauty', 'sku' => 'DEMO-031'],
            ['name_ar' => 'مرطب وجه', 'name_en' => 'Face Moisturizer', 'price' => 320, 'image' => 'images/demo/products/product-09.jpg', 'category_slug' => 'skincare', 'store_slug' => 'glow-beauty', 'sku' => 'DEMO-032'],
            ['name_ar' => 'شامبو للشعر', 'name_en' => 'Hair Shampoo', 'price' => 190, 'image' => 'images/demo/products/product-03.jpg', 'category_slug' => 'haircare', 'store_slug' => 'glow-beauty', 'sku' => 'DEMO-033'],
            ['name_ar' => 'عطر نسائي', 'name_en' => 'Women Perfume', 'price' => 1300, 'image' => 'images/demo/products/product-03.jpg', 'category_slug' => 'perfumes', 'store_slug' => 'glow-beauty', 'sku' => 'DEMO-034'],
            ['name_ar' => 'عطر رجالي', 'name_en' => 'Men Perfume', 'price' => 1450, 'image' => 'images/demo/products/product-03.jpg', 'category_slug' => 'perfumes', 'store_slug' => 'glow-beauty', 'sku' => 'DEMO-035'],
            ['name_ar' => 'مجموعة مكياج', 'name_en' => 'Makeup Essentials Kit', 'price' => 980, 'image' => 'images/demo/products/product-09.jpg', 'category_slug' => 'makeup', 'store_slug' => 'glow-beauty', 'sku' => 'DEMO-036'],
            ['name_ar' => 'سيروم فيتامين سي', 'name_en' => 'Vitamin C Serum', 'price' => 450, 'image' => 'images/demo/products/product-09.jpg', 'category_slug' => 'skincare', 'store_slug' => 'glow-beauty', 'sku' => 'DEMO-037'],
            ['name_ar' => 'ماسك شعر مغذي', 'name_en' => 'Nourishing Hair Mask', 'price' => 290, 'image' => 'images/demo/products/product-03.jpg', 'category_slug' => 'haircare', 'store_slug' => 'glow-beauty', 'sku' => 'DEMO-038'],
            ['name_ar' => 'غسول وجه لطيف', 'name_en' => 'Gentle Face Cleanser', 'price' => 240, 'image' => 'images/demo/products/product-09.jpg', 'category_slug' => 'personal-care', 'store_slug' => 'glow-beauty', 'sku' => 'DEMO-039'],
            ['name_ar' => 'كريم يدين', 'name_en' => 'Hand Cream', 'price' => 150, 'image' => 'images/demo/products/product-03.jpg', 'category_slug' => 'personal-care', 'store_slug' => 'glow-beauty', 'sku' => 'DEMO-040'],
            ['name_ar' => 'حذاء جري رياضي', 'name_en' => 'Running Shoes', 'price' => 1200, 'image' => 'images/demo/products/product-06.jpg', 'category_slug' => 'sportswear', 'store_slug' => 'active-life', 'sku' => 'DEMO-041'],
            ['name_ar' => 'سجادة يوغا', 'name_en' => 'Yoga Mat', 'price' => 380, 'image' => 'images/demo/products/product-15.jpg', 'category_slug' => 'yoga', 'store_slug' => 'active-life', 'sku' => 'DEMO-042'],
            ['name_ar' => 'أوزان دمبل', 'name_en' => 'Fitness Dumbbells', 'price' => 650, 'image' => 'images/demo/products/product-15.jpg', 'category_slug' => 'fitness-equipment', 'store_slug' => 'active-life', 'sku' => 'DEMO-043'],
            ['name_ar' => 'زجاجة رياضية', 'name_en' => 'Sports Water Bottle', 'price' => 220, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'fitness-equipment', 'store_slug' => 'active-life', 'sku' => 'DEMO-044'],
            ['name_ar' => 'تيشيرت رياضي', 'name_en' => 'Sports T-Shirt', 'price' => 420, 'image' => 'images/demo/products/product-11.jpg', 'category_slug' => 'sportswear', 'store_slug' => 'active-life', 'sku' => 'DEMO-045'],
            ['name_ar' => 'حقيبة جيم', 'name_en' => 'Gym Duffel Bag', 'price' => 580, 'image' => 'images/demo/products/product-12.jpg', 'category_slug' => 'sportswear', 'store_slug' => 'active-life', 'sku' => 'DEMO-046'],
            ['name_ar' => 'حبل مقاومة', 'name_en' => 'Resistance Bands', 'price' => 190, 'image' => 'images/demo/products/product-15.jpg', 'category_slug' => 'fitness-equipment', 'store_slug' => 'active-life', 'sku' => 'DEMO-047'],
            ['name_ar' => 'كرة قدم', 'name_en' => 'Football', 'price' => 350, 'image' => 'images/demo/products/product-15.jpg', 'category_slug' => 'team-sports', 'store_slug' => 'active-life', 'sku' => 'DEMO-048'],
            ['name_ar' => 'مصباح تخييم', 'name_en' => 'Camping Lantern', 'price' => 480, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'outdoor', 'store_slug' => 'active-life', 'sku' => 'DEMO-049'],
            ['name_ar' => 'حقيبة ظهر للتخييم', 'name_en' => 'Hiking Backpack', 'price' => 950, 'image' => 'images/demo/products/product-12.jpg', 'category_slug' => 'outdoor', 'store_slug' => 'active-life', 'sku' => 'DEMO-050'],
            ['name_ar' => 'لعبة تعليمية للأطفال', 'name_en' => 'Educational Toy', 'price' => 500, 'image' => 'images/demo/products/product-16.jpg', 'category_slug' => 'toys', 'store_slug' => 'little-world', 'sku' => 'DEMO-051'],
            ['name_ar' => 'دبدوب أطفال', 'name_en' => 'Kids Teddy Bear', 'price' => 350, 'image' => 'images/demo/products/product-16.jpg', 'category_slug' => 'toys', 'store_slug' => 'little-world', 'sku' => 'DEMO-052'],
            ['name_ar' => 'تيشيرت أطفال قطني', 'name_en' => 'Kids Cotton T-Shirt', 'price' => 220, 'image' => 'images/demo/products/product-11.jpg', 'category_slug' => 'kids-clothing', 'store_slug' => 'little-world', 'sku' => 'DEMO-053'],
            ['name_ar' => 'حقيبة مدرسة', 'name_en' => 'School Backpack', 'price' => 420, 'image' => 'images/demo/products/product-12.jpg', 'category_slug' => 'school-supplies', 'store_slug' => 'little-world', 'sku' => 'DEMO-054'],
            ['name_ar' => 'زجاجة أطفال', 'name_en' => 'Kids Water Bottle', 'price' => 160, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'baby-care', 'store_slug' => 'little-world', 'sku' => 'DEMO-055'],
            ['name_ar' => 'مكعبات بناء', 'name_en' => 'Building Blocks Set', 'price' => 390, 'image' => 'images/demo/products/product-16.jpg', 'category_slug' => 'toys', 'store_slug' => 'little-world', 'sku' => 'DEMO-056'],
            ['name_ar' => 'بطانية أطفال', 'name_en' => 'Baby Soft Blanket', 'price' => 310, 'image' => 'images/demo/products/product-07.jpg', 'category_slug' => 'baby-care', 'store_slug' => 'little-world', 'sku' => 'DEMO-057'],
            ['name_ar' => 'حذاء أطفال', 'name_en' => 'Kids Sneakers', 'price' => 450, 'image' => 'images/demo/products/product-06.jpg', 'category_slug' => 'kids-clothing', 'store_slug' => 'little-world', 'sku' => 'DEMO-058'],
            ['name_ar' => 'عربة أطفال خفيفة', 'name_en' => 'Lightweight Baby Stroller', 'price' => 3200, 'image' => 'images/demo/products/product-05.jpg', 'category_slug' => 'strollers', 'store_slug' => 'little-world', 'sku' => 'DEMO-059'],
            ['name_ar' => 'علبة ألوان', 'name_en' => 'Coloring Pencils Set', 'price' => 120, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'school-supplies', 'store_slug' => 'little-world', 'sku' => 'DEMO-060'],
            ['name_ar' => 'ساعة يد كلاسيكية', 'name_en' => 'Classic Wrist Watch', 'price' => 1100, 'image' => 'images/demo/products/product-04.jpg', 'category_slug' => 'watches', 'store_slug' => 'time-luxe', 'sku' => 'DEMO-061'],
            ['name_ar' => 'ساعة ذكية أنيقة', 'name_en' => 'Elegant Smart Watch', 'price' => 1650, 'image' => 'images/demo/products/product-04.jpg', 'category_slug' => 'watches', 'store_slug' => 'time-luxe', 'sku' => 'DEMO-062'],
            ['name_ar' => 'نظارة شمسية', 'name_en' => 'Classic Sunglasses', 'price' => 450, 'image' => 'images/demo/products/product-10.jpg', 'category_slug' => 'sunglasses', 'store_slug' => 'time-luxe', 'sku' => 'DEMO-063'],
            ['name_ar' => 'سوار بسيط', 'name_en' => 'Minimal Bracelet', 'price' => 380, 'image' => 'images/demo/products/product-04.jpg', 'category_slug' => 'jewelry', 'store_slug' => 'time-luxe', 'sku' => 'DEMO-064'],
            ['name_ar' => 'قلادة أنيقة', 'name_en' => 'Elegant Necklace', 'price' => 520, 'image' => 'images/demo/products/product-04.jpg', 'category_slug' => 'jewelry', 'store_slug' => 'time-luxe', 'sku' => 'DEMO-065'],
            ['name_ar' => 'خاتم معدني', 'name_en' => 'Metal Ring', 'price' => 290, 'image' => 'images/demo/products/product-04.jpg', 'category_slug' => 'fine-accessories', 'store_slug' => 'time-luxe', 'sku' => 'DEMO-066'],
            ['name_ar' => 'أقراط ناعمة', 'name_en' => 'Minimal Earrings', 'price' => 330, 'image' => 'images/demo/products/product-04.jpg', 'category_slug' => 'jewelry', 'store_slug' => 'time-luxe', 'sku' => 'DEMO-067'],
            ['name_ar' => 'محفظة بطاقات', 'name_en' => 'Card Holder', 'price' => 260, 'image' => 'images/demo/products/product-12.jpg', 'category_slug' => 'fine-accessories', 'store_slug' => 'time-luxe', 'sku' => 'DEMO-068'],
            ['name_ar' => 'ساعة رياضية', 'name_en' => 'Sport Watch', 'price' => 950, 'image' => 'images/demo/products/product-04.jpg', 'category_slug' => 'watches', 'store_slug' => 'time-luxe', 'sku' => 'DEMO-069'],
            ['name_ar' => 'علبة حفظ مجوهرات', 'name_en' => 'Jewelry Organizer Box', 'price' => 420, 'image' => 'images/demo/products/product-07.jpg', 'category_slug' => 'fine-accessories', 'store_slug' => 'time-luxe', 'sku' => 'DEMO-070'],
            ['name_ar' => 'دفتر ملاحظات', 'name_en' => 'Notebook', 'price' => 95, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'notebooks', 'store_slug' => 'paper-trail', 'sku' => 'DEMO-071'],
            ['name_ar' => 'قلم حبر فاخر', 'name_en' => 'Premium Ink Pen', 'price' => 180, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'writing-tools', 'store_slug' => 'paper-trail', 'sku' => 'DEMO-072'],
            ['name_ar' => 'منظم مكتب', 'name_en' => 'Desk Organizer', 'price' => 240, 'image' => 'images/demo/products/product-07.jpg', 'category_slug' => 'desk-accessories', 'store_slug' => 'paper-trail', 'sku' => 'DEMO-073'],
            ['name_ar' => 'حامل أقلام', 'name_en' => 'Pen Holder', 'price' => 110, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'desk-accessories', 'store_slug' => 'paper-trail', 'sku' => 'DEMO-074'],
            ['name_ar' => 'دباسة مكتبية', 'name_en' => 'Office Stapler', 'price' => 85, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'office-supplies', 'store_slug' => 'paper-trail', 'sku' => 'DEMO-075'],
            ['name_ar' => 'ملفات مستندات', 'name_en' => 'Document Folder Set', 'price' => 130, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'office-supplies', 'store_slug' => 'paper-trail', 'sku' => 'DEMO-076'],
            ['name_ar' => 'مفكرة أسبوعية', 'name_en' => 'Weekly Planner', 'price' => 150, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'notebooks', 'store_slug' => 'paper-trail', 'sku' => 'DEMO-077'],
            ['name_ar' => 'مجموعة أقلام تلوين', 'name_en' => 'Color Marker Set', 'price' => 125, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'writing-tools', 'store_slug' => 'paper-trail', 'sku' => 'DEMO-078'],
            ['name_ar' => 'لوحة ماوس مكتبية', 'name_en' => 'Desk Mouse Pad', 'price' => 90, 'image' => 'images/demo/products/product-01.jpg', 'category_slug' => 'desk-accessories', 'store_slug' => 'paper-trail', 'sku' => 'DEMO-079'],
            ['name_ar' => 'آلة حاسبة', 'name_en' => 'Desktop Calculator', 'price' => 210, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'office-supplies', 'store_slug' => 'paper-trail', 'sku' => 'DEMO-080'],
            ['name_ar' => 'سرير حيوانات أليفة', 'name_en' => 'Pet Comfort Bed', 'price' => 780, 'image' => 'images/demo/products/product-16.jpg', 'category_slug' => 'pet-beds', 'store_slug' => 'pet-corner', 'sku' => 'DEMO-081'],
            ['name_ar' => 'لعبة حبل للكلاب', 'name_en' => 'Dog Rope Toy', 'price' => 120, 'image' => 'images/demo/products/product-16.jpg', 'category_slug' => 'pet-toys', 'store_slug' => 'pet-corner', 'sku' => 'DEMO-082'],
            ['name_ar' => 'وعاء طعام للحيوانات', 'name_en' => 'Pet Food Bowl', 'price' => 160, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'pet-accessories', 'store_slug' => 'pet-corner', 'sku' => 'DEMO-083'],
            ['name_ar' => 'حقيبة حمل للحيوانات', 'name_en' => 'Pet Carrier Bag', 'price' => 650, 'image' => 'images/demo/products/product-12.jpg', 'category_slug' => 'pet-accessories', 'store_slug' => 'pet-corner', 'sku' => 'DEMO-084'],
            ['name_ar' => 'فرشاة تمشيط', 'name_en' => 'Pet Grooming Brush', 'price' => 140, 'image' => 'images/demo/products/product-16.jpg', 'category_slug' => 'pet-accessories', 'store_slug' => 'pet-corner', 'sku' => 'DEMO-085'],
            ['name_ar' => 'طوق للحيوانات', 'name_en' => 'Adjustable Pet Collar', 'price' => 180, 'image' => 'images/demo/products/product-02.jpg', 'category_slug' => 'pet-accessories', 'store_slug' => 'pet-corner', 'sku' => 'DEMO-086'],
            ['name_ar' => 'كرة لعب للحيوانات', 'name_en' => 'Pet Play Ball', 'price' => 75, 'image' => 'images/demo/products/product-16.jpg', 'category_slug' => 'pet-toys', 'store_slug' => 'pet-corner', 'sku' => 'DEMO-087'],
            ['name_ar' => 'بطانية للحيوانات', 'name_en' => 'Pet Soft Blanket', 'price' => 220, 'image' => 'images/demo/products/product-07.jpg', 'category_slug' => 'pet-beds', 'store_slug' => 'pet-corner', 'sku' => 'DEMO-088'],
            ['name_ar' => 'حاوية طعام', 'name_en' => 'Pet Food Storage', 'price' => 290, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'pet-food', 'store_slug' => 'pet-corner', 'sku' => 'DEMO-089'],
            ['name_ar' => 'موزع طعام أوتوماتيكي', 'name_en' => 'Automatic Pet Feeder', 'price' => 980, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'pet-food', 'store_slug' => 'pet-corner', 'sku' => 'DEMO-090'],
            ['name_ar' => 'حامل هاتف للسيارة', 'name_en' => 'Car Phone Holder', 'price' => 180, 'image' => 'images/demo/products/product-06.jpg', 'category_slug' => 'car-accessories', 'store_slug' => 'auto-hub', 'sku' => 'DEMO-091'],
            ['name_ar' => 'منظم مقعد السيارة', 'name_en' => 'Car Seat Organizer', 'price' => 240, 'image' => 'images/demo/products/product-12.jpg', 'category_slug' => 'interior-accessories', 'store_slug' => 'auto-hub', 'sku' => 'DEMO-092'],
            ['name_ar' => 'شاحن سيارة سريع', 'name_en' => 'Fast Car Charger', 'price' => 160, 'image' => 'images/demo/products/product-05.jpg', 'category_slug' => 'car-accessories', 'store_slug' => 'auto-hub', 'sku' => 'DEMO-093'],
            ['name_ar' => 'مكنسة سيارة صغيرة', 'name_en' => 'Compact Car Vacuum', 'price' => 490, 'image' => 'images/demo/products/product-01.jpg', 'category_slug' => 'car-care', 'store_slug' => 'auto-hub', 'sku' => 'DEMO-094'],
            ['name_ar' => 'غطاء مقود', 'name_en' => 'Steering Wheel Cover', 'price' => 210, 'image' => 'images/demo/products/product-02.jpg', 'category_slug' => 'interior-accessories', 'store_slug' => 'auto-hub', 'sku' => 'DEMO-095'],
            ['name_ar' => 'عدة أدوات سيارة', 'name_en' => 'Car Tool Kit', 'price' => 650, 'image' => 'images/demo/products/product-14.jpg', 'category_slug' => 'auto-tools', 'store_slug' => 'auto-hub', 'sku' => 'DEMO-096'],
            ['name_ar' => 'منظف داخلي للسيارة', 'name_en' => 'Car Interior Cleaner', 'price' => 130, 'image' => 'images/demo/products/product-03.jpg', 'category_slug' => 'car-care', 'store_slug' => 'auto-hub', 'sku' => 'DEMO-097'],
            ['name_ar' => 'كاميرا داش كام', 'name_en' => 'Dash Camera', 'price' => 1250, 'image' => 'images/demo/products/product-06.jpg', 'category_slug' => 'car-accessories', 'store_slug' => 'auto-hub', 'sku' => 'DEMO-098'],
            ['name_ar' => 'منظم صندوق السيارة', 'name_en' => 'Trunk Organizer', 'price' => 280, 'image' => 'images/demo/products/product-12.jpg', 'category_slug' => 'interior-accessories', 'store_slug' => 'auto-hub', 'sku' => 'DEMO-099'],
            ['name_ar' => 'ضاغط هواء محمول', 'name_en' => 'Portable Air Compressor', 'price' => 850, 'image' => 'images/demo/products/product-14.jpg', 'category_slug' => 'auto-tools', 'store_slug' => 'auto-hub', 'sku' => 'DEMO-100'],
            ['name_ar' => 'قهوة عربية مختصة', 'name_en' => 'Specialty Arabic Coffee', 'price' => 240, 'image' => 'images/demo/products/product-03.jpg', 'category_slug' => 'coffee-tea', 'store_slug' => 'daily-choice', 'sku' => 'DEMO-101'],
            ['name_ar' => 'حبوب قهوة محمصة', 'name_en' => 'Roasted Coffee Beans', 'price' => 190, 'image' => 'images/demo/products/product-03.jpg', 'category_slug' => 'coffee-tea', 'store_slug' => 'daily-choice', 'sku' => 'DEMO-102'],
            ['name_ar' => 'شاي أخضر', 'name_en' => 'Green Tea', 'price' => 95, 'image' => 'images/demo/products/product-03.jpg', 'category_slug' => 'coffee-tea', 'store_slug' => 'daily-choice', 'sku' => 'DEMO-103'],
            ['name_ar' => 'مجموعة أكواب قهوة', 'name_en' => 'Coffee Cup Set', 'price' => 260, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'coffee-tea', 'store_slug' => 'daily-choice', 'sku' => 'DEMO-104'],
            ['name_ar' => 'مكسرات مشكلة', 'name_en' => 'Mixed Nuts', 'price' => 180, 'image' => 'images/demo/products/product-16.jpg', 'category_slug' => 'snacks', 'store_slug' => 'daily-choice', 'sku' => 'DEMO-105'],
            ['name_ar' => 'تمر فاخر', 'name_en' => 'Premium Dates', 'price' => 220, 'image' => 'images/demo/products/product-16.jpg', 'category_slug' => 'snacks', 'store_slug' => 'daily-choice', 'sku' => 'DEMO-106'],
            ['name_ar' => 'شوفان عضوي', 'name_en' => 'Organic Oats', 'price' => 85, 'image' => 'images/demo/products/product-16.jpg', 'category_slug' => 'healthy-food', 'store_slug' => 'daily-choice', 'sku' => 'DEMO-107'],
            ['name_ar' => 'عسل طبيعي', 'name_en' => 'Natural Honey', 'price' => 320, 'image' => 'images/demo/products/product-03.jpg', 'category_slug' => 'pantry', 'store_slug' => 'daily-choice', 'sku' => 'DEMO-108'],
            ['name_ar' => 'زبدة فول سوداني', 'name_en' => 'Peanut Butter', 'price' => 145, 'image' => 'images/demo/products/product-16.jpg', 'category_slug' => 'pantry', 'store_slug' => 'daily-choice', 'sku' => 'DEMO-109'],
            ['name_ar' => 'وجبات خفيفة صحية', 'name_en' => 'Healthy Snack Pack', 'price' => 170, 'image' => 'images/demo/products/product-16.jpg', 'category_slug' => 'healthy-food', 'store_slug' => 'daily-choice', 'sku' => 'DEMO-110'],
            ['name_ar' => 'حقيبة سفر متوسطة', 'name_en' => 'Medium Travel Suitcase', 'price' => 1850, 'image' => 'images/demo/products/product-05.jpg', 'category_slug' => 'luggage', 'store_slug' => 'travel-mate', 'sku' => 'DEMO-111'],
            ['name_ar' => 'حقيبة ظهر للسفر', 'name_en' => 'Travel Backpack', 'price' => 950, 'image' => 'images/demo/products/product-12.jpg', 'category_slug' => 'travel-bags', 'store_slug' => 'travel-mate', 'sku' => 'DEMO-112'],
            ['name_ar' => 'وسادة رقبة للسفر', 'name_en' => 'Travel Neck Pillow', 'price' => 180, 'image' => 'images/demo/products/product-07.jpg', 'category_slug' => 'travel-accessories', 'store_slug' => 'travel-mate', 'sku' => 'DEMO-113'],
            ['name_ar' => 'منظم حقائب', 'name_en' => 'Packing Organizer Set', 'price' => 240, 'image' => 'images/demo/products/product-12.jpg', 'category_slug' => 'travel-organizers', 'store_slug' => 'travel-mate', 'sku' => 'DEMO-114'],
            ['name_ar' => 'حقيبة يد للسفر', 'name_en' => 'Travel Duffel Bag', 'price' => 720, 'image' => 'images/demo/products/product-02.jpg', 'category_slug' => 'travel-bags', 'store_slug' => 'travel-mate', 'sku' => 'DEMO-115'],
            ['name_ar' => 'قفل حقيبة رقمي', 'name_en' => 'Digital Luggage Lock', 'price' => 140, 'image' => 'images/demo/products/product-04.jpg', 'category_slug' => 'travel-accessories', 'store_slug' => 'travel-mate', 'sku' => 'DEMO-116'],
            ['name_ar' => 'ميزان حقائب', 'name_en' => 'Luggage Scale', 'price' => 160, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'travel-accessories', 'store_slug' => 'travel-mate', 'sku' => 'DEMO-117'],
            ['name_ar' => 'حافظة جواز سفر', 'name_en' => 'Passport Holder', 'price' => 190, 'image' => 'images/demo/products/product-12.jpg', 'category_slug' => 'travel-organizers', 'store_slug' => 'travel-mate', 'sku' => 'DEMO-118'],
            ['name_ar' => 'زجاجة سفر قابلة لإعادة الاستخدام', 'name_en' => 'Reusable Travel Bottle', 'price' => 130, 'image' => 'images/demo/products/product-08.jpg', 'category_slug' => 'travel-accessories', 'store_slug' => 'travel-mate', 'sku' => 'DEMO-119'],
            ['name_ar' => 'حقيبة مستلزمات شخصية', 'name_en' => 'Travel Toiletry Bag', 'price' => 280, 'image' => 'images/demo/products/product-12.jpg', 'category_slug' => 'travel-organizers', 'store_slug' => 'travel-mate', 'sku' => 'DEMO-120'],
        ];

        $categoryIds = DB::table('categories')->pluck('id', 'slug');
        $storeIds = DB::table('stores')->pluck('id', 'slug');

        foreach ($products as $data) {
            if (! isset($categoryIds[$data['category_slug']])) {
                throw new RuntimeException("Missing category: {$data['category_slug']}. Run CategorySeeder first.");
            }
            if (! isset($storeIds[$data['store_slug']])) {
                throw new RuntimeException("Missing store: {$data['store_slug']}. Run StoreSeeder first.");
            }
        }

        $variantTypes = DB::table('variant_types')
            ->where('status', true)->get()->keyBy('name_en');
        $variantValues = DB::table('variant_values')
            ->where('status', true)->get()->groupBy('variant_type_id');


        DB::transaction(function () use (
            $products,
            $categoryIds,
            $storeIds,
            $variantTypes,
            $variantValues
        ) {
            foreach ($products as $index => $data) {
                $slug = Str::slug($data['name_en']);

                // Distribute stock statuses evenly:
                // 0 = Out of Stock, 1 = Low Stock, 2 = In Stock
                $stockStatus = $index % 3;

                $stock = match ($stockStatus) {
                    0 => 0,
                    1 => rand(1, max(1, Product::LOW_STOCK_THRESHOLD)),
                    default => rand(10, 50),
                };

                // Distribute sale and active statuses evenly.
                $isSale = $index % 2 === 0;
                $isActive = $index % 5 !== 0;

                $salePrice = $isSale
                    ? round($data['price'] * 0.85, 2)
                    : null;

                $product = Product::firstOrCreate(
                    ['sku' => $data['sku']],
                    [
                        'store_id' => $storeIds[$data['store_slug']],
                        'category_id' => $categoryIds[$data['category_slug']],
                        'name' => $data['name_en'],
                        'name_ar' => $data['name_ar'],
                        'name_en' => $data['name_en'],
                        'slug' => $slug . '-' . strtolower($data['sku']),
                        'description' => 'Quality product from our marketplace demo catalog.',
                        'description_ar' => 'منتج تجريبي عالي الجودة من متجرنا.',
                        'description_en' => 'Quality product from our marketplace demo catalog.',
                        'price' => $data['price'],
                        'sale_price' => $salePrice,
                        'is_sale' => $isSale,
                        'stock' => $stock,
                        'has_variants' => false,
                        'sku' => $data['sku'],
                        'image' => $data['image'],
                        'status' => $isActive,
                    ]
                );

                // Keep existing seeded products untouched on reruns.
                if (! $product->wasRecentlyCreated) {
                    continue;
                }

                $typeNames = $this->getVariantTypeNames($data['name_en']);

                $selectedTypes = collect($typeNames)
                    ->map(fn ($name) => $variantTypes->get($name))
                    ->filter(fn ($type) => $type
                        && $variantValues->has($type->id)
                        && $variantValues->get($type->id)->isNotEmpty()
                    )
                    ->values();

                // Create variants for eligible products in a predictable distribution.
                $shouldHaveVariants = ! empty($typeNames)
                    && $selectedTypes->count() === count($typeNames)
                    && $index % 2 === 0;

                if (! $shouldHaveVariants) {
                    continue;
                }

                // Build all possible variant combinations.
                $combinations = [[]];

                foreach ($selectedTypes as $type) {
                    $valueIds = $variantValues->get($type->id)
                        ->pluck('id')
                        ->all();

                    $next = [];

                    foreach ($combinations as $combination) {
                        foreach ($valueIds as $valueId) {
                            $next[] = array_merge($combination, [$valueId]);
                        }
                    }

                    $combinations = $next;
                }

                shuffle($combinations);

                // Low-stock products must keep their total quantity
                // within the configured low-stock threshold.
                $variantCount = $stockStatus === 1
                    ? max(1, min(4, Product::LOW_STOCK_THRESHOLD))
                    : rand(2, 4);

                $combinations = array_slice(
                    $combinations,
                    0,
                    min(count($combinations), $variantCount)
                );

                // Reset product stock and price before creating variants.
                $product->update([
                    'has_variants' => true,
                    'price' => 0,
                    'sale_price' => null,
                    'stock' => 0,
                ]);

                $product->variantTypes()->sync(
                    $selectedTypes->pluck('id')->all()
                );

                foreach ($combinations as $variantIndex => $valueIds) {
                    $variantPrice = $data['price'] + ($variantIndex * 25);

                    $variant = new ProductVariant();

                    $variant->sku = 'VAR-' . Str::upper(Str::random(10));
                    $variant->price = $variantPrice;

                    $variant->discount_price = $isSale
                        ? round($variantPrice * 0.85, 2)
                        : null;

                    $variant->quantity = match ($stockStatus) {
                        0 => 0,
                        1 => 1,
                        default => rand(10, 50),
                    };

                    $variant->status = true;

                    $product->variants()->save($variant);

                    $variant->values()->sync($valueIds);
                }

                // Update product summary based on its variants.
                $product->update([
                    'price' => $product->variants()->min('price'),
                    'sale_price' => $isSale
                        ? $product->variants()->min('discount_price')
                        : null,
                    'stock' => $product->variants()->sum('quantity'),
                ]);
            }
        });

        $this->command->info('120 demo products seeded successfully.');
    }

    private function getVariantTypeNames(string $name): array
    {
        $name = strtolower($name);

        if (str_contains($name, 't-shirt') || str_contains($name, 'shirt')
            || str_contains($name, 'jeans') || str_contains($name, 'dress')
            || str_contains($name, 'jacket') || str_contains($name, 'sneakers')) {
            return ['Size', 'Color', 'Material'];
        }

        if (str_contains($name, 'handbag') || str_contains($name, 'wallet')
            || str_contains($name, 'backpack') || str_contains($name, 'bag')) {
            return ['Color', 'Material'];
        }

        if (str_contains($name, 'watch') || str_contains($name, 'sunglasses')
            || str_contains($name, 'headphones') || str_contains($name, 'mouse')
            || str_contains($name, 'keyboard') || str_contains($name, 'charger')) {
            return ['Color'];
        }

        return [];
    }
}
