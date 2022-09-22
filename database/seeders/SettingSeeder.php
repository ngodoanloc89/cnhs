<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Tag;
use Botble\Setting\Models\Setting as SettingModel;
use Botble\Slug\Models\Slug;
use SlugHelper;

class SettingSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SettingModel::whereIn('key', ['media_random_hash', 'simple_slider_using_assets', 
'ecommerce_load_countries_states_cities_from_location_plugin'])->orWhere('key', 'LIKE', 'payment_%')->delete();

        SettingModel::insertOrIgnore([
            [
                'key'   => 'media_random_hash',
                'value' => md5(time()),
            ],
            [
                'key'   => 'simple_slider_using_assets',
                'value' => 0,
            ],
        ]);

        SettingModel::insertOrIgnore([
            [
                'key'   => SlugHelper::getPermalinkSettingKey(Post::class),
                'value' => 'blog',
            ],
            [
                'key'   => SlugHelper::getPermalinkSettingKey(Category::class),
                'value' => 'blog',
            ],
            [
                'key'   => SlugHelper::getPermalinkSettingKey(Tag::class),
                'value' => 'tag',
            ],
            [
                'key'   => 'payment_cod_status',
                'value' => 1,
            ],
            [
                'key'   => 'payment_cod_description',
                'value' => 'Please pay money directly to the postman, if you choose cash on delivery method (COD).',
            ],
            [
                'key'   => 'payment_bank_transfer_status',
                'value' => 1,
            ],
            [
                'key'   => 'payment_bank_transfer_description',
                'value' => 'Please send money to our bank account: ACB - 3240 921 19.',
            ],
            [
                'key'   => 'plugins_ecommerce_customer_new_order_status',
                'value' => '0',
            ],
            [
                'key'   => 'plugins_ecommerce_admin_new_order_status',
                'value' => '0',
            ],
            [
                'key'   => 'sale_popup_display_pages',
                'value' => json_encode(['public.index']),
            ],
            [
                'key'   => 'ecommerce_load_countries_states_cities_from_location_plugin',
                'value' => '0',
            ],
            [
                'key'   => 'payment_stripe_payment_type',
                'value' => 'stripe_checkout',
            ],
        ]);

        Slug::where('reference_type', Post::class)->update(['prefix' => 'blog']);
        Slug::where('reference_type', Category::class)->update(['prefix' => 'blog']);
        Slug::where('reference_type', Tag::class)->update(['prefix' => 'tag']);
    }
}
