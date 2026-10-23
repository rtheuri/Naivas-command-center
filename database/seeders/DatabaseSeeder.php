<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Brief;
use App\Models\Budget;
use App\Models\Campaign;
use App\Models\StoreTask;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create the 4 Judge Persona Users
        $imcLead = User::create([
            'name' => 'Amina Mwangi',
            'email' => 'imc.lead@naivas.co.ke',
            'role' => 'imc_lead',
            'department' => 'IMC',
            'job_title' => 'Integrated Marketing Communications Lead',
            'branch' => 'Headquarters - Nairobi',
            'phone' => '+254 712 345 678',
            'password' => Hash::make('naivas2026'),
        ]);

        $dpmBuyer = User::create([
            'name' => 'Kevin Omondi',
            'email' => 'dpm.buyer@naivas.co.ke',
            'role' => 'dpm_buyer',
            'department' => 'DPM',
            'job_title' => 'Digital & Performance Marketing Lead',
            'branch' => 'Headquarters - Nairobi',
            'phone' => '+254 723 456 789',
            'password' => Hash::make('naivas2026'),
        ]);

        $omeManager = User::create([
            'name' => 'Faith Wanjiku',
            'email' => 'ome.manager@naivas.co.ke',
            'role' => 'ome_manager',
            'department' => 'OME',
            'job_title' => 'Store Operations Manager',
            'branch' => 'Naivas Mega City',
            'phone' => '+254 734 567 890',
            'password' => Hash::make('naivas2026'),
        ]);

        $cmoExec = User::create([
            'name' => 'Peter Mumo',
            'email' => 'cmo.exec@naivas.co.ke',
            'role' => 'marketing_exec',
            'department' => 'Executive',
            'job_title' => 'Chief Marketing Officer (CMO)',
            'branch' => 'Executive Office - Nairobi',
            'phone' => '+254 745 678 901',
            'password' => Hash::make('naivas2026'),
        ]);

        // 2. Create Pre-seeded Campaigns

        // Campaign 1: Weekend Produce Promo (Ready for Launch - unlocked for DPM & OME!)
        $c1 = Campaign::create([
            'code' => 'CMP-2026-001',
            'title' => 'Weekend Produce Promo',
            'description' => 'Drive footfall and basket size in fresh produce aisles across top Nairobi & satellite branches with discounts on leafy greens, avocados, and Rift Valley tomatoes.',
            'category' => 'Fresh Produce',
            'priority' => 'high',
            'status' => 'ready_for_launch',
            'total_budget' => 3500000,
            'allocated_budget' => 3500000,
            'spent_budget' => 1840000,
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(5),
            'target_audience' => 'Household shoppers, families, loyalty reward members',
            'banner_image' => 'produce_hero.jpg',
        ]);

        $b1 = Brief::create([
            'campaign_id' => $c1->id,
            'created_by_user_id' => $imcLead->id,
            'title' => 'Fresh Greens & Farm Harvest POSM Suite',
            'objective' => 'Achieve 28% increase in fresh produce department gross turnover over Friday-Sunday.',
            'target_segment' => 'Mothers, urban professionals, bulk buyers',
            'key_messaging' => 'Naivas Farm Fresh: Directly sourced, crisp freshness delivered daily at real pocket-friendly prices!',
            'channel' => 'in_store_posm',
            'status' => 'approved',
            'feedback_notes' => 'Approved by IMC Lead. Clean, high contrast imagery aligned with brand green (#006837).',
            'submitted_at' => now()->subDays(3),
            'approved_at' => now()->subDays(2),
            'approved_by_user_id' => $imcLead->id,
        ]);

        $a1_1 = Asset::create([
            'campaign_id' => $c1->id,
            'brief_id' => $b1->id,
            'title' => 'Weekend Produce Gondola End Banner 1200x600',
            'asset_type' => 'gondola_header',
            'file_url' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1000&q=80',
            'dimensions' => '1200x600mm',
            'format' => 'High-Res PDF / Print Ready',
            'version' => 2,
            'status' => 'approved',
            'approval_notes' => 'Color checked against Naivas Green and Orange specs. Approved for printing and branch dispatch.',
            'approved_at' => now()->subDays(2),
            'approved_by_user_id' => $imcLead->id,
        ]);

        $a1_2 = Asset::create([
            'campaign_id' => $c1->id,
            'brief_id' => $b1->id,
            'title' => 'Social Media Promo Carousel (Instagram & Meta)',
            'asset_type' => 'digital_graphic',
            'file_url' => 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=1000&q=80',
            'dimensions' => '1080x1080px',
            'format' => 'PNG 300DPI',
            'version' => 1,
            'status' => 'approved',
            'approval_notes' => 'Ready for DPM Meta ad manager staging.',
            'approved_at' => now()->subDays(2),
            'approved_by_user_id' => $imcLead->id,
        ]);

        Budget::create([
            'campaign_id' => $c1->id,
            'channel_name' => 'Meta Ads (Facebook & Instagram)',
            'channel_icon' => 'meta',
            'allocated_amount' => 1200000,
            'spent_amount' => 720000,
            'impressions' => 640000,
            'clicks' => 31200,
            'conversions' => 4180,
            'pacing_status' => 'on_track',
            'is_deployed' => true,
            'deployed_at' => now()->subDays(2),
        ]);

        Budget::create([
            'campaign_id' => $c1->id,
            'channel_name' => 'Google Ads & YouTube Bumper',
            'channel_icon' => 'google',
            'allocated_amount' => 800000,
            'spent_amount' => 410000,
            'impressions' => 380000,
            'clicks' => 18400,
            'conversions' => 1950,
            'pacing_status' => 'on_track',
            'is_deployed' => true,
            'deployed_at' => now()->subDays(2),
        ]);

        Budget::create([
            'campaign_id' => $c1->id,
            'channel_name' => 'TikTok In-Feed Video Ads',
            'channel_icon' => 'tiktok',
            'allocated_amount' => 600000,
            'spent_amount' => 310000,
            'impressions' => 490000,
            'clicks' => 24100,
            'conversions' => 2800,
            'pacing_status' => 'on_track',
            'is_deployed' => true,
            'deployed_at' => now()->subDays(2),
        ]);

        Budget::create([
            'campaign_id' => $c1->id,
            'channel_name' => 'SMS Blast & Loyalty WhatsApp',
            'channel_icon' => 'sms',
            'allocated_amount' => 400000,
            'spent_amount' => 400000,
            'impressions' => 220000,
            'clicks' => 19500,
            'conversions' => 3600,
            'pacing_status' => 'on_track',
            'is_deployed' => true,
            'deployed_at' => now()->subDays(2),
        ]);

        Budget::create([
            'campaign_id' => $c1->id,
            'channel_name' => 'In-Store POSM & Price Danglers',
            'channel_icon' => 'posm',
            'allocated_amount' => 500000,
            'spent_amount' => 0,
            'impressions' => 150000,
            'clicks' => 0,
            'conversions' => 0,
            'pacing_status' => 'pending_deployment',
            'is_deployed' => false,
            'deployed_at' => null,
        ]);

        StoreTask::create([
            'campaign_id' => $c1->id,
            'branch_name' => 'Naivas Mega City',
            'assigned_to_user_id' => $omeManager->id,
            'task_name' => 'Install Gondola End Banner in Fresh Produce Aisle',
            'zone' => 'Produce Aisle 1',
            'due_date' => now()->addDays(1),
            'status' => 'pending',
            'proof_image_url' => null,
            'proof_notes' => 'Awaiting branch maintenance team positioning.',
        ]);

        StoreTask::create([
            'campaign_id' => $c1->id,
            'branch_name' => 'Naivas Westlands',
            'assigned_to_user_id' => $omeManager->id,
            'task_name' => 'Affix Price Danglers for Sukuma & Spinach',
            'zone' => 'Vegetable Chillers',
            'due_date' => now()->addDays(1),
            'status' => 'pending',
            'proof_image_url' => null,
            'proof_notes' => null,
        ]);

        StoreTask::create([
            'campaign_id' => $c1->id,
            'branch_name' => 'Naivas Junction',
            'assigned_to_user_id' => $omeManager->id,
            'task_name' => 'Position Avocado & Citrus Feature Standee',
            'zone' => 'Main Fresh Entrance',
            'due_date' => now()->addDays(2),
            'status' => 'in_progress',
            'proof_image_url' => null,
            'proof_notes' => 'Materials delivered from central warehouse; assembly underway.',
        ]);

        // Campaign 2: Back to School Mega Sale (Active In Market)
        $c2 = Campaign::create([
            'code' => 'CMP-2026-002',
            'title' => 'Back to School Mega Sale',
            'description' => 'Flagship nationwide education readiness campaign featuring bundled stationery, geometry sets, school shoes, and student snacks with tiered cashback.',
            'category' => 'Back to School',
            'priority' => 'urgent',
            'status' => 'active',
            'total_budget' => 6800000,
            'allocated_budget' => 6800000,
            'spent_budget' => 4920000,
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(14),
            'target_audience' => 'Parents, guardians, high school students, teachers',
            'banner_image' => 'school_hero.jpg',
        ]);

        $b2 = Brief::create([
            'campaign_id' => $c2->id,
            'created_by_user_id' => $imcLead->id,
            'title' => 'Back to School Station & Footwear Showcase',
            'objective' => 'Capture 40% market share in school opening basket purchases.',
            'target_segment' => 'Primary and secondary school parents across Kenya',
            'key_messaging' => 'Soma na Naivas: Zero stress, 100% genuine school supplies all under one roof!',
            'channel' => 'in_store_posm',
            'status' => 'approved',
            'feedback_notes' => 'Approved with high priority priority flags for branch dispatch.',
            'submitted_at' => now()->subDays(12),
            'approved_at' => now()->subDays(11),
            'approved_by_user_id' => $imcLead->id,
        ]);

        Asset::create([
            'campaign_id' => $c2->id,
            'brief_id' => $b2->id,
            'title' => 'Entrance Archway & Aisle Header POSM',
            'asset_type' => 'posm_banner',
            'file_url' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1000&q=80',
            'dimensions' => '3000x2400mm',
            'format' => 'Vinyl Banner Print',
            'version' => 1,
            'status' => 'approved',
            'approval_notes' => 'Tested for branch clearance and visibility.',
            'approved_at' => now()->subDays(11),
            'approved_by_user_id' => $imcLead->id,
        ]);

        Budget::create([
            'campaign_id' => $c2->id,
            'channel_name' => 'Meta Ads (Facebook & Instagram)',
            'channel_icon' => 'meta',
            'allocated_amount' => 2500000,
            'spent_amount' => 2100000,
            'impressions' => 1450000,
            'clicks' => 74200,
            'conversions' => 11200,
            'pacing_status' => 'on_track',
            'is_deployed' => true,
            'deployed_at' => now()->subDays(10),
        ]);

        Budget::create([
            'campaign_id' => $c2->id,
            'channel_name' => 'Google Search & P-Max Campaign',
            'channel_icon' => 'google',
            'allocated_amount' => 1800000,
            'spent_amount' => 1320000,
            'impressions' => 920000,
            'clicks' => 45600,
            'conversions' => 6400,
            'pacing_status' => 'on_track',
            'is_deployed' => true,
            'deployed_at' => now()->subDays(10),
        ]);

        Budget::create([
            'campaign_id' => $c2->id,
            'channel_name' => 'TikTok Spark Ads & Creator Challenges',
            'channel_icon' => 'tiktok',
            'allocated_amount' => 1500000,
            'spent_amount' => 1100000,
            'impressions' => 1890000,
            'clicks' => 89000,
            'conversions' => 9300,
            'pacing_status' => 'overpacing',
            'is_deployed' => true,
            'deployed_at' => now()->subDays(10),
        ]);

        Budget::create([
            'campaign_id' => $c2->id,
            'channel_name' => 'SMS Broadcast to 350K Loyalty Members',
            'channel_icon' => 'sms',
            'allocated_amount' => 1000000,
            'spent_amount' => 400000,
            'impressions' => 350000,
            'clicks' => 31200,
            'conversions' => 5200,
            'pacing_status' => 'underpacing',
            'is_deployed' => true,
            'deployed_at' => now()->subDays(8),
        ]);

        StoreTask::create([
            'campaign_id' => $c2->id,
            'branch_name' => 'Naivas Mega City',
            'assigned_to_user_id' => $omeManager->id,
            'task_name' => 'Erect Back to School Entrance Archway',
            'zone' => 'Main Turnstile Entrance',
            'due_date' => now()->subDays(8),
            'status' => 'executed_pending_verification',
            'proof_image_url' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=1000&q=80',
            'proof_notes' => 'Archway installed securely above entrance doors. Verified lighting and branded header visible from parking.',
            'executed_at' => now()->subHours(4),
        ]);

        StoreTask::create([
            'campaign_id' => $c2->id,
            'branch_name' => 'Naivas Kilimani',
            'assigned_to_user_id' => $omeManager->id,
            'task_name' => 'Display Stationery Bundle Floor Stackers',
            'zone' => 'Central Promotional Concourse',
            'due_date' => now()->subDays(6),
            'status' => 'verified',
            'proof_image_url' => 'https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=1000&q=80',
            'proof_notes' => 'Floor stackers installed with clear pricing placards.',
            'executed_at' => now()->subDays(5),
            'verified_by_user_id' => $cmoExec->id,
            'verified_at' => now()->subDays(4),
        ]);

        StoreTask::create([
            'campaign_id' => $c2->id,
            'branch_name' => 'Naivas Mombasa Road',
            'assigned_to_user_id' => $omeManager->id,
            'task_name' => 'Hang Aisle Price Danglers (Oxford Math Sets)',
            'zone' => 'Stationery Aisle 4',
            'due_date' => now()->subDays(5),
            'status' => 'verified',
            'proof_image_url' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=1000&q=80',
            'proof_notes' => 'Hang tags mounted at eye level.',
            'executed_at' => now()->subDays(4),
            'verified_by_user_id' => $cmoExec->id,
            'verified_at' => now()->subDays(3),
        ]);

        // Campaign 3: Fresh Harvest Week (Pending IMC Approval - Key Live Demo for Judges!)
        $c3 = Campaign::create([
            'code' => 'CMP-2026-003',
            'title' => 'Fresh Harvest Week',
            'description' => 'Spotlighting direct farm-gate partnerships with Kinangop and Nakuru smallholder farmers, showcasing fresh kales, potatoes, and carrots with exclusive farm gate pricing.',
            'category' => 'Fresh Produce',
            'priority' => 'high',
            'status' => 'pending_approval', // The judge can approve this live in IMC module!
            'total_budget' => 2800000,
            'allocated_budget' => 2800000,
            'spent_budget' => 350000,
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(9),
            'target_audience' => 'Healthy living consumers, bulk family shoppers',
            'banner_image' => 'harvest_hero.jpg',
        ]);

        $b3 = Brief::create([
            'campaign_id' => $c3->id,
            'created_by_user_id' => $imcLead->id,
            'title' => 'Rift Valley Direct Harvest POSM & Social Activation',
            'objective' => 'Communicate fresh-from-the-soil delivery within 24 hours of harvest.',
            'target_segment' => 'Nutrition-conscious shoppers looking for authentic farm value',
            'key_messaging' => 'Kutoka Shambani Hadi Naivas: Crisp, organic taste directly supporting local Kenyan farmers!',
            'channel' => 'in_store_posm',
            'status' => 'pending_review',
            'feedback_notes' => 'Creative team uploaded final Gondola banners and video cuts. Awaiting IMC Lead Amina Mwangi final sign-off.',
            'submitted_at' => now()->subHours(6),
        ]);

        Asset::create([
            'campaign_id' => $c3->id,
            'brief_id' => $b3->id,
            'title' => 'Farm Direct Crate Skirts & Overhead Hanging Decals',
            'asset_type' => 'posm_banner',
            'file_url' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=1000&q=80',
            'dimensions' => '1500x800mm',
            'format' => 'UV Matte Vinyl',
            'version' => 1,
            'status' => 'pending_review', // READY FOR LIVE APPROVAL
            'approval_notes' => 'Awaiting Amina Mwangi (IMC Lead) approval to trigger DPM Ad budget unlocking.',
        ]);

        Asset::create([
            'campaign_id' => $c3->id,
            'brief_id' => $b3->id,
            'title' => 'Farmer Stories Reel - Nakuru Organic Greens',
            'asset_type' => 'video_reel',
            'file_url' => 'https://images.unsplash.com/photo-1592417817098-8f3d6910985b?auto=format&fit=crop&w=1000&q=80',
            'dimensions' => '1080x1920 9:16',
            'format' => 'MP4 High-Bitrate',
            'version' => 1,
            'status' => 'draft',
            'approval_notes' => 'Audio voiceover being finalized in Swahili & English.',
        ]);

        Budget::create([
            'campaign_id' => $c3->id,
            'channel_name' => 'Meta Ads (Facebook & Instagram)',
            'channel_icon' => 'meta',
            'allocated_amount' => 1000000,
            'spent_amount' => 0,
            'impressions' => 0,
            'clicks' => 0,
            'conversions' => 0,
            'pacing_status' => 'pending_deployment',
            'is_deployed' => false,
            'deployed_at' => null,
        ]);

        Budget::create([
            'campaign_id' => $c3->id,
            'channel_name' => 'Google Ads Local Store Feed',
            'channel_icon' => 'google',
            'allocated_amount' => 800000,
            'spent_amount' => 0,
            'impressions' => 0,
            'clicks' => 0,
            'conversions' => 0,
            'pacing_status' => 'pending_deployment',
            'is_deployed' => false,
            'deployed_at' => null,
        ]);

        Budget::create([
            'campaign_id' => $c3->id,
            'channel_name' => 'TikTok Food & Recipe Influencers',
            'channel_icon' => 'tiktok',
            'allocated_amount' => 500000,
            'spent_amount' => 0,
            'impressions' => 0,
            'clicks' => 0,
            'conversions' => 0,
            'pacing_status' => 'pending_deployment',
            'is_deployed' => false,
            'deployed_at' => null,
        ]);

        Budget::create([
            'campaign_id' => $c3->id,
            'channel_name' => 'In-Store POSM Print & Distribution',
            'channel_icon' => 'posm',
            'allocated_amount' => 500000,
            'spent_amount' => 350000,
            'impressions' => 90000,
            'clicks' => 0,
            'conversions' => 0,
            'pacing_status' => 'on_track',
            'is_deployed' => true,
            'deployed_at' => now()->subDay(),
        ]);

        StoreTask::create([
            'campaign_id' => $c3->id,
            'branch_name' => 'Naivas Mega City',
            'assigned_to_user_id' => $omeManager->id,
            'task_name' => 'Place Farm Direct Crate Skirts & Standees',
            'zone' => 'Entrance Fresh Island',
            'due_date' => now()->addDays(2),
            'status' => 'pending',
            'proof_image_url' => null,
            'proof_notes' => 'Scheduled for Thursday night store setup.',
        ]);

        StoreTask::create([
            'campaign_id' => $c3->id,
            'branch_name' => 'Naivas Westlands',
            'assigned_to_user_id' => $omeManager->id,
            'task_name' => 'Mount Overhead Produce Banners',
            'zone' => 'Vegetable Department',
            'due_date' => now()->addDays(3),
            'status' => 'pending',
            'proof_image_url' => null,
            'proof_notes' => null,
        ]);

        // Campaign 4: Naivas Super Brand Month (Planning Stage)
        $c4 = Campaign::create([
            'code' => 'CMP-2026-004',
            'title' => 'Naivas Super Brand Month',
            'description' => 'Month-long brand equity and private label campaign emphasizing quality guarantee on Naivas Cooking Oil, Naivas Sugar, and Naivas Grain assortments.',
            'category' => 'Private Label FMCG',
            'priority' => 'medium',
            'status' => 'planning',
            'total_budget' => 5000000,
            'allocated_budget' => 4000000,
            'spent_budget' => 600000,
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(37),
            'target_audience' => 'Loyal repeat shoppers, price-sensitive consumers',
            'banner_image' => 'private_label_hero.jpg',
        ]);

        $b4 = Brief::create([
            'campaign_id' => $c4->id,
            'created_by_user_id' => $imcLead->id,
            'title' => 'Naivas Essentials Pantry Staples Refresh',
            'objective' => 'Increase own-brand basket penetration from 18% to 26%.',
            'target_segment' => 'Bulk grocery buyers',
            'key_messaging' => 'Naivas Brands: Uncompromising Quality, Best Price Guaranteed.',
            'channel' => 'in_store_posm',
            'status' => 'draft',
            'feedback_notes' => 'Awaiting packaging photography session from agency.',
        ]);

        Asset::create([
            'campaign_id' => $c4->id,
            'brief_id' => $b4->id,
            'title' => 'Private Label Endcap Header - Naivas Golden Oil',
            'asset_type' => 'gondola_header',
            'file_url' => 'https://images.unsplash.com/photo-1608686207856-001b95cf60ca?auto=format&fit=crop&w=1000&q=80',
            'dimensions' => '1200x500mm',
            'format' => 'High-Res PDF',
            'version' => 1,
            'status' => 'draft',
            'approval_notes' => 'Initial mockup created by creative designer.',
        ]);

        Budget::create([
            'campaign_id' => $c4->id,
            'channel_name' => 'Meta Ads (Facebook & Instagram)',
            'channel_icon' => 'meta',
            'allocated_amount' => 1800000,
            'spent_amount' => 200000,
            'impressions' => 120000,
            'clicks' => 5400,
            'conversions' => 890,
            'pacing_status' => 'underpacing',
            'is_deployed' => true,
            'deployed_at' => now()->subDays(2),
        ]);

        Budget::create([
            'campaign_id' => $c4->id,
            'channel_name' => 'Google Search Ads',
            'channel_icon' => 'google',
            'allocated_amount' => 1200000,
            'spent_amount' => 150000,
            'impressions' => 95000,
            'clicks' => 4100,
            'conversions' => 540,
            'pacing_status' => 'on_track',
            'is_deployed' => true,
            'deployed_at' => now()->subDays(2),
        ]);

        Budget::create([
            'campaign_id' => $c4->id,
            'channel_name' => 'In-Store POSM & Endcaps',
            'channel_icon' => 'posm',
            'allocated_amount' => 1000000,
            'spent_amount' => 250000,
            'impressions' => 80000,
            'clicks' => 0,
            'conversions' => 0,
            'pacing_status' => 'on_track',
            'is_deployed' => true,
            'deployed_at' => now()->subDays(2),
        ]);

        StoreTask::create([
            'campaign_id' => $c4->id,
            'branch_name' => 'Naivas Mega City',
            'assigned_to_user_id' => $omeManager->id,
            'task_name' => 'Set up Endcap Display for Naivas Cooking Oil',
            'zone' => 'FMCG Aisle 5',
            'due_date' => now()->addDays(5),
            'status' => 'pending',
            'proof_image_url' => null,
            'proof_notes' => 'Endcap inventory scheduled for arrival from depot.',
        ]);

        // Campaign 5: Customer Appreciation Days (Active)
        $c5 = Campaign::create([
            'code' => 'CMP-2026-005',
            'title' => 'Customer Appreciation Days',
            'description' => 'Triple loyalty reward points weekend, surprise cart giveaways, and in-store instant discount scratch cards celebrating Naivas customers nationwide.',
            'category' => 'Loyalty & Festive',
            'priority' => 'urgent',
            'status' => 'active',
            'total_budget' => 4200000,
            'allocated_budget' => 4200000,
            'spent_budget' => 3150000,
            'start_date' => now()->subDays(4),
            'end_date' => now()->addDays(3),
            'target_audience' => 'Naivas Loyalty cardholders, recurring supermarket visitors',
            'banner_image' => 'loyalty_hero.jpg',
        ]);

        $b5 = Brief::create([
            'campaign_id' => $c5->id,
            'created_by_user_id' => $imcLead->id,
            'title' => 'Triple Points & Instant Checkout Perks Promo',
            'objective' => 'Re-activate 120,000 dormant loyalty members and lift average basket spend by 18%.',
            'target_segment' => 'Naivas Reward Club Cardholders',
            'key_messaging' => 'Asante Sana: Naivas rewards you 3X with every single purchase this weekend!',
            'channel' => 'sms_broadcast',
            'status' => 'approved',
            'feedback_notes' => 'SMS copy tested and approved for high deliverability.',
            'submitted_at' => now()->subDays(6),
            'approved_at' => now()->subDays(5),
            'approved_by_user_id' => $imcLead->id,
        ]);

        Asset::create([
            'campaign_id' => $c5->id,
            'brief_id' => $b5->id,
            'title' => 'Cashier Till Wobblers & Loyalty Signage',
            'asset_type' => 'shelf_talker',
            'file_url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1000&q=80',
            'dimensions' => '200x200mm',
            'format' => 'Die-cut Plastic',
            'version' => 1,
            'status' => 'approved',
            'approval_notes' => 'Approved and delivered to all 100+ branches.',
            'approved_at' => now()->subDays(5),
            'approved_by_user_id' => $imcLead->id,
        ]);

        Budget::create([
            'campaign_id' => $c5->id,
            'channel_name' => 'SMS Broadcast (650K Messages)',
            'channel_icon' => 'sms',
            'allocated_amount' => 1800000,
            'spent_amount' => 1600000,
            'impressions' => 645000,
            'clicks' => 58000,
            'conversions' => 14200,
            'pacing_status' => 'on_track',
            'is_deployed' => true,
            'deployed_at' => now()->subDays(4),
        ]);

        Budget::create([
            'campaign_id' => $c5->id,
            'channel_name' => 'Meta Geo-Targeted Flash Push',
            'channel_icon' => 'meta',
            'allocated_amount' => 1400000,
            'spent_amount' => 950000,
            'impressions' => 810000,
            'clicks' => 39000,
            'conversions' => 7400,
            'pacing_status' => 'on_track',
            'is_deployed' => true,
            'deployed_at' => now()->subDays(4),
        ]);

        Budget::create([
            'campaign_id' => $c5->id,
            'channel_name' => 'In-Store Till Wobblers & Badges',
            'channel_icon' => 'posm',
            'allocated_amount' => 1000000,
            'spent_amount' => 600000,
            'impressions' => 220000,
            'clicks' => 0,
            'conversions' => 0,
            'pacing_status' => 'on_track',
            'is_deployed' => true,
            'deployed_at' => now()->subDays(4),
        ]);

        StoreTask::create([
            'campaign_id' => $c5->id,
            'branch_name' => 'Naivas Mega City',
            'assigned_to_user_id' => $omeManager->id,
            'task_name' => 'Affix Cashier Till Screen Stickers (Points 3X)',
            'zone' => 'Cashier Checkout Tills 1-24',
            'due_date' => now()->subDays(2),
            'status' => 'verified',
            'proof_image_url' => 'https://images.unsplash.com/photo-1556742049-0a67c5574f73?auto=format&fit=crop&w=1000&q=80',
            'proof_notes' => 'All 24 checkout counters updated with loyalty promotional decals.',
            'executed_at' => now()->subDays(2),
            'verified_by_user_id' => $cmoExec->id,
            'verified_at' => now()->subDays(1),
        ]);

        StoreTask::create([
            'campaign_id' => $c5->id,
            'branch_name' => 'Naivas Mega City',
            'assigned_to_user_id' => $omeManager->id,
            'task_name' => 'Position Customer Appreciation Welcome Arch',
            'zone' => 'Front Porch Entrance',
            'due_date' => now()->subDay(),
            'status' => 'executed_pending_verification',
            'proof_image_url' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=1000&q=80',
            'proof_notes' => 'Welcome arch mounted and helium balloon clusters positioned.',
            'executed_at' => now()->subHours(2),
        ]);

        // 3. Seed Realistic Cross-Department Activity Logs
        ActivityLog::create([
            'user_id' => $imcLead->id,
            'user_name' => 'Amina Mwangi',
            'user_role' => 'IMC Lead',
            'department' => 'IMC',
            'campaign_id' => $c1->id,
            'action_type' => 'ASSET_APPROVED',
            'title' => 'Asset Approved: Weekend Produce Gondola End Banner',
            'description' => 'Amina Mwangi approved creative asset "Weekend Produce Gondola End Banner 1200x600". Campaign automatically transitioned to "Ready for Launch".',
            'metadata' => ['asset_id' => $a1_1->id, 'version' => 2],
            'created_at' => now()->subDays(2)->addMinutes(15),
        ]);

        ActivityLog::create([
            'user_id' => $dpmBuyer->id,
            'user_name' => 'Kevin Omondi',
            'user_role' => 'DPM Media Buyer',
            'department' => 'DPM',
            'campaign_id' => $c1->id,
            'action_type' => 'CAMPAIGN_LAUNCHED',
            'title' => 'Digital Ads Deployed: Weekend Produce Promo',
            'description' => 'Unlocked by IMC approval: Kevin Omondi deployed Meta Ads & Google Ads with combined budget allocation of KES 2,000,000.',
            'metadata' => ['channels' => ['meta', 'google', 'tiktok']],
            'created_at' => now()->subDays(2)->addHours(1),
        ]);

        ActivityLog::create([
            'user_id' => $dpmBuyer->id,
            'user_name' => 'Kevin Omondi',
            'user_role' => 'DPM Media Buyer',
            'department' => 'DPM',
            'campaign_id' => $c2->id,
            'action_type' => 'BUDGET_REALLOCATED',
            'title' => 'Budget Reallocated: Back to School Mega Sale',
            'description' => 'Shifted KES 300,000 from SMS Broadcast to TikTok Spark Ads due to 3.2X higher ROAS on student reels.',
            'metadata' => ['from' => 'sms', 'to' => 'tiktok', 'amount' => 300000],
            'created_at' => now()->subDays(1)->addHours(4),
        ]);

        ActivityLog::create([
            'user_id' => $omeManager->id,
            'user_name' => 'Faith Wanjiku',
            'user_role' => 'OME Store Manager',
            'department' => 'OME',
            'campaign_id' => $c2->id,
            'action_type' => 'PROOF_SUBMITTED',
            'title' => 'Store Proof Uploaded: Naivas Mega City',
            'description' => 'Faith Wanjiku uploaded photographic proof of execution for "Erect Back to School Entrance Archway". Status updated to "Executed - Pending Verification".',
            'metadata' => ['branch' => 'Naivas Mega City', 'task' => 'Entrance Archway'],
            'created_at' => now()->subHours(4),
        ]);

        ActivityLog::create([
            'user_id' => $omeManager->id,
            'user_name' => 'Faith Wanjiku',
            'user_role' => 'OME Store Manager',
            'department' => 'OME',
            'campaign_id' => $c5->id,
            'action_type' => 'PROOF_SUBMITTED',
            'title' => 'Store Proof Uploaded: Customer Appreciation Welcome Arch',
            'description' => 'Faith Wanjiku submitted store rollout photo verification at Naivas Mega City. Instant notification pushed to Executive Dashboard.',
            'metadata' => ['branch' => 'Naivas Mega City', 'task' => 'Welcome Arch'],
            'created_at' => now()->subHours(2),
        ]);

        ActivityLog::create([
            'user_id' => $cmoExec->id,
            'user_name' => 'Peter Mumo',
            'user_role' => 'Marketing Executive',
            'department' => 'Executive',
            'campaign_id' => $c2->id,
            'action_type' => 'STORE_VERIFIED',
            'title' => 'Branch Execution Verified: Naivas Kilimani',
            'description' => 'Peter Mumo verified in-store execution for Stationery Bundle Floor Stackers.',
            'metadata' => ['branch' => 'Naivas Kilimani'],
            'created_at' => now()->subDays(4),
        ]);
    }
}
