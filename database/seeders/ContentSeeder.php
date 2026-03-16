<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use App\Models\Project;
use App\Models\Setting;
use App\Models\TeamMember;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories
        $categories = [
            [
                'title' => 'The Experiential',
                'description' => 'Crafting immersive physical environments and live events.',
                'sort_order' => 1
            ],
            [
                'title' => 'The Visual Soul',
                'description' => 'Giving life to brands through stunning graphics and motion.',
                'sort_order' => 2
            ],
            [
                'title' => 'The Digital Engine',
                'description' => 'Building high-performance digital platforms and identities.',
                'sort_order' => 3
            ],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['title']] = Category::updateOrCreate(['title' => $cat['title']], $cat);
        }

        // 2. Team Members
        $team = [
            [
                'name' => 'Alex Rivera',
                'position' => 'Founder & Creative Director',
                'bio' => 'Visionary leader with 10+ years experience in digital arts.',
                'instagram' => 'https://instagram.com/',
                'linkedin' => 'https://linkedin.com/',
                'twitter' => 'https://x.com/',
                'dribbble' => 'https://dribbble.com/',
                'sort_order' => 1
            ],
            [
                'name' => 'Sarah Chen',
                'position' => 'Head of Development',
                'bio' => 'Full-stack wizard specializing in interactive web experiences.',
                'instagram' => 'https://instagram.com/',
                'linkedin' => 'https://linkedin.com/',
                'github' => 'https://github.com/',
                'sort_order' => 2
            ],
            [
                'name' => 'Marcus Thorne',
                'position' => 'Lead Motion Graphic',
                'bio' => 'Master of visual storytelling and cinematic animation.',
                'instagram' => 'https://instagram.com/',
                'linkedin' => 'https://linkedin.com/',
                'twitter' => 'https://x.com/',
                'sort_order' => 3
            ],
        ];

        $teamModels = [];
        foreach ($team as $member) {
            $member['slug'] = Str::slug($member['name']);
            $teamModels[$member['name']] = TeamMember::updateOrCreate(['name' => $member['name']], $member);
        }

        // 3. Services
        $services = [
            // The Experiential
            [
                'title' => 'Creative Event',
                'category_id' => $categoryModels['The Experiential']->id,
                'sort_order' => 1,
                'icon' => 'bi bi-magic',
                'description' => 'Unforgettable immersive experiences.',
                'content' => '<h2>Full-Scale Event Production</h2><p>We create events that linger in memory long after the lights go out. From concept development to technical execution.</p><ul><li>Immersive Projections</li><li>Set Design & Construction</li><li>Audio-Visual Mastery</li></ul>'
            ],
            [
                'title' => 'Web Development',
                'category_id' => $categoryModels['The Digital Engine']->id,
                'sort_order' => 8,
                'icon' => 'bi bi-code-slash',
                'description' => 'Advanced high-performance platforms.',
                'content' => '<h2>Cutting-Edge Web Solutions</h2><p>We build web applications that are as beautiful as they are functional. Using the latest technologies like React, Laravel, and GSAP.</p>'
            ],
            [
                'title' => 'Graphic Design',
                'category_id' => $categoryModels['The Visual Soul']->id,
                'sort_order' => 4,
                'icon' => 'bi bi-palette',
                'description' => 'Brand identities & visual narratives.',
                'content' => '<h2>Visual Brilliance</h2><p>Communication through exceptional design. We build visual systems that define the next generation of brands.</p>'
            ],
        ];

        $serviceModels = [];
        foreach ($services as $svc) {
            $svc['slug'] = Str::slug($svc['title']);
            $serviceModels[$svc['title']] = Service::updateOrCreate(['slug' => $svc['slug']], $svc);
        }

        // 4. Sample Projects with Team & Service Assignments
        $projects = [
            [
                'title' => 'Neon Future Launch',
                'client' => 'CyberPulse Tech',
                'description' => 'A full scale immersive event with 3D projection mapping.',
                'gallery' => ['https://images.unsplash.com/photo-1550745165-9bc0b252726f', 'https://images.unsplash.com/photo-1558655146-d09347e92766'],
                'team' => ['Alex Rivera', 'Marcus Thorne'],
                'services' => ['Creative Event']
            ],
            [
                'title' => 'Eco-City Rebrand',
                'client' => 'GreenHorizon',
                'description' => 'Comprehensive graphic design and social media strategy.',
                'gallery' => ['https://images.unsplash.com/photo-1497366216548-37526070297c', 'https://images.unsplash.com/photo-1449156001935-cf6691e99464'],
                'team' => ['Alex Rivera', 'Sarah Chen'],
                'services' => ['Graphic Design', 'Web Development']
            ]
        ];

        foreach ($projects as $projData) {
            $teamNames = $projData['team'] ?? [];
            $serviceNames = $projData['services'] ?? [];
            unset($projData['team'], $projData['services']);
            
            $projData['slug'] = Str::slug($projData['title']);
            $projData['completion_date'] = now()->subMonths(rand(1, 12))->format('Y-m-d');
            $projData['content'] = '<h2>The Challenge</h2><p>Pushing the boundaries of what is possible in ' . $projData['title'] . '. We needed to create a system that was both technologically advanced and emotionally resonant.</p><h3>Our Approach</h3><p>We combined cutting-edge technology with human-centric design. Every pixel and every interaction was crafted to tell a story.</p><ul><li>Bespoke Architecture</li><li>Emotional Visuals</li><li>Unmatched Performance</li></ul>';

            $project = Project::updateOrCreate(['slug' => $projData['slug']], $projData);
            
            // Sync Team
            $teamIds = [];
            foreach ($teamNames as $name) {
                if (isset($teamModels[$name])) {
                    $teamIds[] = $teamModels[$name]->id;
                }
            }
            $project->teamMembers()->sync($teamIds);

            // Sync Services
            $svcIds = [];
            foreach ($serviceNames as $name) {
                if (isset($serviceModels[$name])) {
                    $svcIds[] = $serviceModels[$name]->id;
                }
            }
            $project->services()->sync($svcIds);
        }

        // 5. Default Settings (Removed: Handled by SettingSeeder)
    }
}
