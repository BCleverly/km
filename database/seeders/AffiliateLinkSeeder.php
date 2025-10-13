<?php

namespace Database\Seeders;

use App\Models\AffiliateLink;
use App\Models\User;
use Illuminate\Database\Seeder;

class AffiliateLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a system user to assign as the author of affiliate links
        $systemUser = User::where('email', 'system@kinkmaster.com')->first();
        
        if (!$systemUser) {
            $systemUser = User::create([
                'name' => 'System User',
                'email' => 'system@kinkmaster.com',
                'password' => bcrypt('password'),
            ]);
            
            $systemUser->email_verified_at = now();
            $systemUser->save();
        }

        // Create sample affiliate links
        $affiliateLinks = [
            [
                'name' => 'Lovehoney',
                'description' => 'Leading online retailer for sex toys, lingerie, and adult products',
                'url' => 'https://www.lovehoney.com/affiliate',
                'partner_type' => 'toys',
                'commission_type' => 'percentage',
                'commission_rate' => 8.5,
                'currency' => 'USD',
                'is_active' => true,
                'is_premium' => true,
                'tracking_id' => 'LH-AFF-2024',
                'notes' => 'High converting partner with excellent product range',
            ],
            [
                'name' => 'Adam & Eve',
                'description' => 'Popular adult toy store with discreet shipping',
                'url' => 'https://www.adameve.com/affiliate',
                'partner_type' => 'toys',
                'commission_type' => 'percentage',
                'commission_rate' => 7.0,
                'currency' => 'USD',
                'is_active' => true,
                'is_premium' => false,
                'tracking_id' => 'AE-AFF-2024',
                'notes' => 'Good for beginners and couples',
            ],
            [
                'name' => 'SheVibe',
                'description' => 'Inclusive sex toy store with body-safe products',
                'url' => 'https://www.shevibe.com/affiliate',
                'partner_type' => 'toys',
                'commission_type' => 'percentage',
                'commission_rate' => 6.0,
                'currency' => 'USD',
                'is_active' => true,
                'is_premium' => false,
                'tracking_id' => 'SV-AFF-2024',
                'notes' => 'Focus on body-safe and inclusive products',
            ],
            [
                'name' => 'Victoria\'s Secret',
                'description' => 'Premium lingerie and intimate apparel',
                'url' => 'https://www.victoriassecret.com/affiliate',
                'partner_type' => 'clothing',
                'commission_type' => 'percentage',
                'commission_rate' => 4.0,
                'currency' => 'USD',
                'is_active' => true,
                'is_premium' => true,
                'tracking_id' => 'VS-AFF-2024',
                'notes' => 'High-end lingerie and intimate wear',
            ],
            [
                'name' => 'Agent Provocateur',
                'description' => 'Luxury lingerie and intimate apparel',
                'url' => 'https://www.agentprovocateur.com/affiliate',
                'partner_type' => 'clothing',
                'commission_type' => 'percentage',
                'commission_rate' => 5.5,
                'currency' => 'USD',
                'is_active' => true,
                'is_premium' => true,
                'tracking_id' => 'AP-AFF-2024',
                'notes' => 'Ultra-luxury lingerie brand',
            ],
            [
                'name' => 'Good Vibrations',
                'description' => 'Educational sex toy store with expert advice',
                'url' => 'https://www.goodvibes.com/affiliate',
                'partner_type' => 'toys',
                'commission_type' => 'percentage',
                'commission_rate' => 6.5,
                'currency' => 'USD',
                'is_active' => true,
                'is_premium' => false,
                'tracking_id' => 'GV-AFF-2024',
                'notes' => 'Educational focus with expert guidance',
            ],
            [
                'name' => 'PinkCherry',
                'description' => 'Affordable sex toys and adult products',
                'url' => 'https://www.pinkcherry.com/affiliate',
                'partner_type' => 'toys',
                'commission_type' => 'percentage',
                'commission_rate' => 7.5,
                'currency' => 'USD',
                'is_active' => true,
                'is_premium' => false,
                'tracking_id' => 'PC-AFF-2024',
                'notes' => 'Budget-friendly option with good selection',
            ],
            [
                'name' => 'Savage X Fenty',
                'description' => 'Rihanna\'s inclusive lingerie and intimate wear brand',
                'url' => 'https://www.savagex.com/affiliate',
                'partner_type' => 'clothing',
                'commission_type' => 'percentage',
                'commission_rate' => 4.5,
                'currency' => 'USD',
                'is_active' => true,
                'is_premium' => true,
                'tracking_id' => 'SXF-AFF-2024',
                'notes' => 'Inclusive sizing and modern designs',
            ],
            [
                'name' => 'Babeland',
                'description' => 'Feminist sex toy store with educational content',
                'url' => 'https://www.babeland.com/affiliate',
                'partner_type' => 'toys',
                'commission_type' => 'percentage',
                'commission_rate' => 5.0,
                'currency' => 'USD',
                'is_active' => true,
                'is_premium' => false,
                'tracking_id' => 'BB-AFF-2024',
                'notes' => 'Feminist approach to sexual wellness',
            ],
            [
                'name' => 'Honey Birdette',
                'description' => 'Luxury lingerie and intimate accessories',
                'url' => 'https://www.honeybirdette.com/affiliate',
                'partner_type' => 'clothing',
                'commission_type' => 'percentage',
                'commission_rate' => 6.0,
                'currency' => 'USD',
                'is_active' => true,
                'is_premium' => true,
                'tracking_id' => 'HB-AFF-2024',
                'notes' => 'High-end lingerie with unique designs',
            ],
        ];

        foreach ($affiliateLinks as $linkData) {
            AffiliateLink::create([
                ...$linkData,
                'user_id' => $systemUser->id,
            ]);
        }

        $this->command->info('Created ' . count($affiliateLinks) . ' affiliate links');
    }
}