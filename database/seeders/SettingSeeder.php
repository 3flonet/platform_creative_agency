<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Brand Identity
            ['key' => 'site_name', 'value' => '3FLO.', 'type' => 'text'],
            ['key' => 'site_description', 'value' => 'Non-stop Creative Agency providing elite 3D platform solutions.', 'type' => 'textarea'],
            ['key' => 'site_keywords', 'value' => 'creative, agency, 3d, web development, motion graphics', 'type' => 'text'],

            // Intro & Hero
            ['key' => 'intro_sequence', 'value' => 'Creative,Innovative,Non-stop', 'type' => 'text'],
            ['key' => 'intro_brand', 'value' => '3FLO.COM', 'type' => 'text'],
            ['key' => 'intro_speed', 'value' => '0.5', 'type' => 'text'],
            ['key' => 'intro_use_logo', 'value' => '0', 'type' => 'text'],
            ['key' => 'intro_logo', 'value' => '', 'type' => 'text'],
            ['key' => 'hero_title_1', 'value' => 'Non-stop', 'type' => 'text'],
            ['key' => 'hero_title_2', 'value' => 'Creative', 'type' => 'text'],
            ['key' => 'hero_tagline', 'value' => 'Where ideas meet infinity.', 'type' => 'text'],

            // Section 1: Discovery (Home)
            ['key' => 'section_1_label', 'value' => '01. Insight', 'type' => 'text'],

            // Section 2: Services
            ['key' => 'section_2_label', 'value' => '02. Capabilities', 'type' => 'text'],
            ['key' => 'section_2_title', 'value' => 'Infinite Services', 'type' => 'text'],

            // Section 3: Projects
            ['key' => 'section_3_label', 'value' => '03. Archive', 'type' => 'text'],
            ['key' => 'section_3_title', 'value' => 'The Archive', 'type' => 'text'],

            // Section 4: Team
            ['key' => 'section_4_label', 'value' => '04. Collective', 'type' => 'text'],
            ['key' => 'section_4_title', 'value' => 'Our Team', 'type' => 'text'],

            // Section 5: Journal
            ['key' => 'journal_label', 'value' => '05. Journal', 'type' => 'text'],
            ['key' => 'journal_title', 'value' => 'Latest Stories', 'type' => 'text'],

            // Section 6: Contact
            ['key' => 'section_5_label', 'value' => '06. Reach', 'type' => 'text'],
            ['key' => 'section_5_title', 'value' => "Let's <br/>Build <br/>Together.", 'type' => 'textarea'],
            ['key' => 'contact_email', 'value' => 'hello@3flo.net', 'type' => 'text'],
            ['key' => 'contact_phone', 'value' => '+62 21 3456 7890', 'type' => 'text'],
            ['key' => 'contact_address', 'value' => 'Creative Soul Tower, 8th Floor, Sudirman Central Business District, Jakarta.', 'type' => 'textarea'],

            // Social Controls
            ['key' => 'social_instagram', 'value' => '3flo_agency', 'type' => 'text'],
            ['key' => 'social_linkedin', 'value' => 'company/3flo', 'type' => 'text'],

            // Footer
            ['key' => 'footer_text', 'value' => 'Jakarta • Creative Agency • ' . date('Y'), 'type' => 'text'],
            ['key' => 'geo_placename', 'value' => 'Jakarta', 'type' => 'text'],

            // 3D Scene Config
            ['key' => '3d_object_type', 'value' => 'torus_knot', 'type' => 'text'],
            ['key' => '3d_animate_model', 'value' => '1', 'type' => 'text'],
            
            // Section 1 Transforms
            ['key' => '3d_pos_1', 'value' => '{"x":0, "y":0, "z":0}', 'type' => 'text'],
            ['key' => '3d_rot_1', 'value' => '{"x":0.2, "y":0.4, "z":0}', 'type' => 'text'],
            ['key' => '3d_scale_1', 'value' => '1.2', 'type' => 'text'],

            // Section 2 Transforms
            ['key' => '3d_pos_2', 'value' => '{"x":2, "y":-0.5, "z":-1}', 'type' => 'text'],
            ['key' => '3d_rot_2', 'value' => '{"x":-0.3, "y":1.2, "z":0.2}', 'type' => 'text'],
            ['key' => '3d_scale_2', 'value' => '1.8', 'type' => 'text'],

            // Section 3 Transforms
            ['key' => '3d_pos_3', 'value' => '{"x":-2.5, "y":0.2, "z":-1.5}', 'type' => 'text'],
            ['key' => '3d_rot_3', 'value' => '{"x":0.5, "y":-0.8, "z":-0.1}', 'type' => 'text'],
            ['key' => '3d_scale_3', 'value' => '2.5', 'type' => 'text'],

            // Section 4 Transforms
            ['key' => '3d_pos_4', 'value' => '{"x":0, "y":1.5, "z":-4}', 'type' => 'text'],
            ['key' => '3d_rot_4', 'value' => '{"x":0, "y":3.14, "z":0}', 'type' => 'text'],
            ['key' => '3d_scale_4', 'value' => '4.0', 'type' => 'text'],

            // Section 5 Transforms
            ['key' => '3d_pos_5', 'value' => '{"x":0, "y":0, "z":1.5}', 'type' => 'text'],
            ['key' => '3d_rot_5', 'value' => '{"x":0.8, "y":0.4, "z":0.4}', 'type' => 'text'],
            ['key' => '3d_scale_5', 'value' => '0.8', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type']
                ]
            );
        }
    }
}
