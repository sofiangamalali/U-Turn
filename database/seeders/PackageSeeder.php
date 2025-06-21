<?php
namespace Database\Seeders;

use App\Models\SubscriptionFeature;
use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\PerAdFeature;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $individual = Package::create([
            'name' => 'للأفراد',
            'type' => 'pay_per_ad',
        ]);

        PerAdFeature::insert([
            [
                'package_id' => $individual->id,
                'label' => 'إعلان مجاني',
                'price' => 0,
                'duration_days' => 7,
                'is_free' => true,
                'order' => 1,
                'level' => 'basic',
            ],
            [
                'package_id' => $individual->id,
                'label' => 'إعلان إضافي',
                'price' => 15,
                'duration_days' => 7,
                'is_free' => false,
                'order' => 2,
                'level' => 'basic',
            ],
            [
                'package_id' => $individual->id,
                'label' => 'إعلان مميز',
                'price' => 25,
                'duration_days' => 7,
                'is_free' => false,
                'order' => 3,
                'level' => 'highlight',
            ],
            [
                'package_id' => $individual->id,
                'label' => 'إعلان مميز جدًا',
                'price' => 40,
                'duration_days' => 14,
                'is_free' => false,
                'order' => 4,
                'level' => 'premium',
            ],
        ]);

        $subscription = Package::create([
            'name' => 'للبائعين المحترفين',
            'type' => 'subscription',
        ]);

        SubscriptionFeature::insert([
            [
                'package_id' => $subscription->id,
                'title' => 'حتى 5 سيارات',
                'max_ads' => 5,
                'price' => 79,
                'description' => 'دعم مباشر + تقارير مشاهدات',
                'duration_days' => 30,
            ],
            [
                'package_id' => $subscription->id,
                'title' => 'حتى 15 سيارة',
                'max_ads' => 15,
                'price' => 149,
                'description' => '2 إعلانات مميزة شهريًا + ترتيب أفضل في البحث',
                'duration_days' => 30,
            ],
            [
                'package_id' => $subscription->id,
                'title' => 'حتى 30 سيارة',
                'max_ads' => 30,
                'price' => 249,
                'description' => 'دعم أولوية + لوحة تحكم شاملة + خصم 10% على الخدمات الإضافية',
                'duration_days' => 30,
            ],
        ]);
    }
}
