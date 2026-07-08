<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->products() as $index => $product) {
            $product['sort_order'] = $index;
            $product['image_url'] = $this->wikimediaUrl($product['image_url']);

            Product::updateOrCreate(['slug' => $product['slug']], $product);
        }
    }

    /**
     * Build a Wikimedia Commons image URL from a file name.
     */
    private function wikimediaUrl(string $file): string
    {
        return 'https://commons.wikimedia.org/wiki/Special:FilePath/'
            .str_replace('%20', '_', rawurlencode($file)).'?width=900';
    }

    /**
     * The seed catalogue, mirroring the original design's six products.
     *
     * @return array<int, array<string, mixed>>
     */
    private function products(): array
    {
        return [
            [
                'slug' => 'mustard-oil',
                'category' => 'oils',
                'price' => 320,
                'is_featured' => true,
                'image_url' => 'Mustard Oil & Seeds - Kolkata 2003-10-31 00537.JPG',
                'name' => ['en' => 'Cold-Pressed Mustard Oil', 'bn' => 'কোল্ড-প্রেসড সরিষার তেল'],
                'tag' => ['en' => 'Ghani-crushed, pungent & pure', 'bn' => 'ঘানি ভাঙা, ঝাঁঝালো ও খাঁটি'],
                'unit' => ['en' => ' / litre', 'bn' => ' / লিটার'],
                'description' => [
                    'en' => 'Slow ghani-crushed from hand-picked mustard seeds, this oil keeps its signature pungency and deep golden color. Perfect for bhorta, frying, and every Bengali dish that deserves the real thing.',
                    'bn' => 'হাতে বাছাই করা সরিষা থেকে ধীরে ঘানিতে ভাঙা, এই তেল তার আসল ঝাঁঝ ও গাঢ় সোনালি রং ধরে রাখে। ভর্তা, ভাজি ও প্রতিটি খাঁটি বাঙালি রান্নার জন্য উপযুক্ত।',
                ],
                'details' => [
                    'en' => ['Cold-pressed (ghani method)', 'No refining, no additives', 'High in natural omega-3', 'Bottled fresh every week'],
                    'bn' => ['কোল্ড-প্রেসড (ঘানি পদ্ধতি)', 'কোনো রিফাইনিং বা অ্যাডিটিভ নেই', 'প্রাকৃতিক ওমেগা-৩ সমৃদ্ধ', 'প্রতি সপ্তাহে তাজা বোতলজাত'],
                ],
            ],
            [
                'slug' => 'ghee',
                'category' => 'dairy',
                'price' => 950,
                'is_featured' => true,
                'image_url' => 'Pure Ghee-Homemade-Maharashtra.jpg',
                'name' => ['en' => 'Pure Cow Ghee', 'bn' => 'খাঁটি গাওয়া ঘি'],
                'tag' => ['en' => 'Golden, grainy, hand-churned', 'bn' => 'সোনালি, দানাদার, হাতে তৈরি'],
                'unit' => ['en' => ' / 500g', 'bn' => ' / ৫০০গ্রা'],
                'description' => [
                    'en' => 'Made in small batches from pure cow’s milk butter, slow-cooked until golden and fragrant. One spoon transforms rice, sweets, and everything in between.',
                    'bn' => 'বিশুদ্ধ গরুর দুধের মাখন থেকে অল্প পরিমাণে তৈরি, সোনালি ও সুগন্ধি না হওয়া পর্যন্ত ধীরে জ্বাল দেওয়া।',
                ],
                'details' => [
                    'en' => ['From grass-fed cow milk', 'Small-batch, hand-churned', 'Naturally grainy texture', 'No vegetable fat, ever'],
                    'bn' => ['ঘাস খাওয়া গরুর দুধ থেকে', 'অল্প ব্যাচে, হাতে তৈরি', 'প্রাকৃতিক দানাদার গঠন', 'কোনো ভেজিটেবল ফ্যাট নেই'],
                ],
            ],
            [
                'slug' => 'honey',
                'category' => 'sweeteners',
                'price' => 700,
                'is_featured' => true,
                'image_url' => 'Honey comb.jpg',
                'name' => ['en' => 'Wild Natural Honey', 'bn' => 'প্রাকৃতিক মধু'],
                'tag' => ['en' => 'Raw, unheated, straight from the comb', 'bn' => 'কাঁচা, তাপহীন, সরাসরি চাক থেকে'],
                'unit' => ['en' => ' / 500g', 'bn' => ' / ৫০০গ্রা'],
                'description' => [
                    'en' => 'Harvested from wildflower hives and never heated, so every enzyme and bit of pollen stays intact. Golden, floral, and deeply sweet.',
                    'bn' => 'বনফুলের চাক থেকে সংগ্রহ, কখনো গরম করা হয় না, তাই প্রতিটি এনজাইম অটুট থাকে। সোনালি, সুগন্ধি ও গভীর মিষ্টি।',
                ],
                'details' => [
                    'en' => ['Raw & unprocessed', 'Never heated or filtered', 'Sourced from Sundarbans region', 'Crystallizes naturally — a sign of purity'],
                    'bn' => ['কাঁচা ও অপ্রক্রিয়াজাত', 'কখনো গরম বা ফিল্টার নয়', 'সুন্দরবন অঞ্চল থেকে সংগৃহীত', 'প্রাকৃতিকভাবে জমে যায় — বিশুদ্ধতার প্রমাণ'],
                ],
            ],
            [
                'slug' => 'date-molasses',
                'category' => 'sweeteners',
                'price' => 450,
                'is_featured' => false,
                'image_url' => 'An image of jaggery powder.JPG',
                'name' => ['en' => 'Date Molasses (Khejur Gur)', 'bn' => 'খেজুরের গুড়'],
                'tag' => ['en' => 'Winter’s sweetest tradition', 'bn' => 'শীতের সবচেয়ে মিষ্টি ঐতিহ্য'],
                'unit' => ['en' => ' / 500g', 'bn' => ' / ৫০০গ্রা'],
                'description' => [
                    'en' => 'Collected from date palms in the cool of winter and reduced to a dark, smoky sweetness. The soul of pithas, payesh, and lazy weekend breakfasts.',
                    'bn' => 'শীতের সকালে খেজুর গাছ থেকে সংগ্রহ করে গাঢ় মিষ্টিতে জ্বাল দেওয়া। পিঠা, পায়েস ও ছুটির সকালের প্রাণ।',
                ],
                'details' => [
                    'en' => ['Pure khejur (date palm) sap', 'No added sugar or color', 'Seasonal winter harvest', 'Rich, smoky caramel notes'],
                    'bn' => ['বিশুদ্ধ খেজুরের রস', 'কোনো চিনি বা রঙ নেই', 'মৌসুমি শীতকালীন সংগ্রহ', 'গাঢ়, ধোঁয়াটে ক্যারামেল স্বাদ'],
                ],
            ],
            [
                'slug' => 'turmeric',
                'category' => 'spices',
                'price' => 180,
                'is_featured' => false,
                'image_url' => 'Turmeric-powder.jpg',
                'name' => ['en' => 'Stone-Ground Turmeric', 'bn' => 'হলুদ গুঁড়া'],
                'tag' => ['en' => 'Earthy, vivid, high-curcumin', 'bn' => 'মাটির ঘ্রাণ, উজ্জ্বল, উচ্চ-কারকিউমিন'],
                'unit' => ['en' => ' / 250g', 'bn' => ' / ২৫০গ্রা'],
                'description' => [
                    'en' => 'Sun-dried turmeric roots stone-ground into a fine, fragrant powder with a deep gold hue and no fillers. Colors and heals in equal measure.',
                    'bn' => 'রোদে শুকানো হলুদ শিলায় গুঁড়া করা, গাঢ় সোনালি রং, কোনো ভেজাল নেই।',
                ],
                'details' => [
                    'en' => ['Single-origin roots', 'Stone-ground, not machine-heated', 'No color enhancers', 'High natural curcumin'],
                    'bn' => ['একক-উৎসের কাঁচা হলুদ', 'শিলায় গুঁড়া করা', 'কোনো রং বাড়ানোর উপাদান নেই', 'উচ্চ প্রাকৃতিক কারকিউমিন'],
                ],
            ],
            [
                'slug' => 'chili',
                'category' => 'spices',
                'price' => 200,
                'is_featured' => false,
                'image_url' => 'Red chilli powder.jpg',
                'name' => ['en' => 'Red Chili Powder', 'bn' => 'মরিচ গুঁড়া'],
                'tag' => ['en' => 'Bold heat, natural red', 'bn' => 'তীব্র ঝাল, প্রাকৃতিক লাল'],
                'unit' => ['en' => ' / 250g', 'bn' => ' / ২৫০গ্রা'],
                'description' => [
                    'en' => 'Sun-ripened red chilies ground fresh for a clean, building heat and a natural red that needs no dye. As hot as your kitchen dares.',
                    'bn' => 'রোদে পাকা লাল মরিচ তাজা গুঁড়া, পরিষ্কার ঝাল ও প্রাকৃতিক লাল রং।',
                ],
                'details' => [
                    'en' => ['Sun-dried whole chilies', 'Ground fresh, no dye', 'Balanced heat & aroma', 'Nothing but chili'],
                    'bn' => ['রোদে শুকানো গোটা মরিচ', 'তাজা গুঁড়া, কোনো রং নেই', 'সুষম ঝাল ও ঘ্রাণ', 'শুধু মরিচ, আর কিছু নয়'],
                ],
            ],
        ];
    }
}
