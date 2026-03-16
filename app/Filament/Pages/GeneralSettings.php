<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Slider;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Group;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;

class GeneralSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static \UnitEnum|string|null $navigationGroup = 'Configuration';
    protected static ?string $navigationLabel = 'Site Settings';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.general-settings';

    public static function canAccess(): bool
    {
        return auth()->user() && auth()->user()->role === 'super_admin';
    }

    public ?array $data = [];
    public int $pruneVisitsDays = 90;

    public function mount(): void
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make('Identity')
                            ->icon('heroicon-o-globe-alt')
                            ->components([
                                Section::make('Brand Information')
                                    ->components([
                                        TextInput::make('site_name')
                                            ->label('Site Name')
                                            ->required(),
                                        Textarea::make('site_description')
                                            ->label('Meta Description')
                                            ->rows(3),
                                    ]),
                                Section::make('Visual Assets')
                                    ->components([
                                        Grid::make(['default' => 3])
                                            ->components([
                                                FileUpload::make('site_logo')
                                                    ->label('Site Logo')
                                                    ->image()
                                                    ->directory('settings')
                                                    ->disk('public'),
                                                FileUpload::make('site_favicon')
                                                    ->label('Favicon')
                                                    ->image()
                                                    ->directory('settings')
                                                    ->disk('public'),
                                                FileUpload::make('site_og_image')
                                                    ->label('OpenGraph Image')
                                                    ->image()
                                                    ->directory('settings')
                                                    ->disk('public'),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('3D Experience')
                            ->icon('heroicon-o-cube')
                            ->components([
                                Section::make('Intro Sequence')
                                    ->description('Configure the welcoming preloader experience.')
                                    ->components([
                                        Grid::make(['default' => 2])
                                            ->components([
                                                TextInput::make('intro_sequence')
                                                    ->label('Text Sequence')
                                                    ->helperText('Comma-separated words (e.g. Creative, Innovative, Non-stop)'),
                                                TextInput::make('intro_speed')
                                                    ->label('Animation Speed (Duration)')
                                                    ->numeric()
                                                    ->step(0.1)
                                                    ->default(0.5)
                                                    ->placeholder('0.5')
                                                    ->prefix('SEC')
                                                    ->hintAction(Action::make('reset_speed')->icon('heroicon-m-arrow-path')->action(fn($set) => $set('intro_speed', 0.5))),
                                                
                                                Toggle::make('intro_use_logo')
                                                    ->label('Use Image Logo instead of Text')
                                                    ->reactive()
                                                    ->default(false),
                                                
                                                TextInput::make('intro_brand')
                                                    ->label('Reveal Brand Name')
                                                    ->visible(fn ($get) => !$get('intro_use_logo')),

                                                FileUpload::make('intro_logo')
                                                    ->label('Intro Logo Image')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->directory('settings')
                                                    ->disk('public')
                                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/svg+xml'])
                                                    ->visible(fn ($get) => $get('intro_use_logo')),
                                            ]),
                                    ]),
                                
                                Section::make('3D Universe Content')
                                    ->description('Upload your custom GLB model and fallback geometry.')
                                    ->components([
                                        Grid::make(['default' => 2])
                                            ->components([
                                                \Filament\Forms\Components\Select::make('3d_object_type')
                                                    ->label('Base Geometry (Fallback)')
                                                    ->options([
                                                        'torus_knot' => 'Torus Knot (Abstract)',
                                                        'sphere' => 'Sphere (Minimalist)',
                                                        'box' => 'Box (Industrial)',
                                                        'octahedron' => 'Octahedron (Crystal)',
                                                        'torus' => 'Torus (Digital)',
                                                        'custom' => 'Custom 3D Model (.GLB)',
                                                    ])
                                                    ->selectablePlaceholder(false),
                                                
                                                FileUpload::make('3d_model_custom')
                                                    ->label('Custom 3D Model (.GLB)')
                                                    ->acceptedFileTypes(['.glb', 'model/gltf-binary'])
                                                    ->directory('3d-models')
                                                    ->disk('public')
                                                    ->maxSize(51200) // 50MB
                                                    ->helperText('Max size: 50MB. Recommended format: .GLB for better performance. This model will replace the base geometry.'),

                                                Toggle::make('3d_animate_model')
                                                    ->label('Enable 3D Animation')
                                                    ->helperText('Play built-in animations if available in the .GLB model.')
                                                    ->default(false),
                                            ]),
                                    ]),

                                Section::make('Transformation Matrix')
                                    ->description('Precision control for the 3D world per scroll section (X, Y, Z coordinates).')
                                    ->components([
                                        \Filament\Forms\Components\Placeholder::make('viewport_3d')
                                            ->label('')
                                            ->content(view('filament.components.3d-viewport'))
                                            ->columnSpanFull(),
                                        Grid::make(['default' => 1, 'lg' => 3])
                                            ->components([
                                                \Filament\Forms\Components\Placeholder::make('guide_image')
                                                    ->label('')
                                                    ->content(new \Illuminate\Support\HtmlString('
                                                        <div class="relative group p-2 max-w-[280px]">
                                                            <div class="absolute -inset-1 bg-gradient-to-r from-red-500 via-green-500 to-blue-500 rounded-lg blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                                                            <img src="/images/admin/3d-guide.png?v='.time().'" alt="3D Guide" class="relative rounded-xl shadow-2xl w-full h-auto object-cover border border-gray-200 dark:border-gray-800">
                                                        </div>
                                                    '))
                                                    ->columnSpan(['lg' => 1]),
                                                \Filament\Forms\Components\Placeholder::make('guide_details')
                                                    ->label('')
                                                    ->content(new \Illuminate\Support\HtmlString('
                                                        <div class="h-full flex flex-col justify-center space-y-4 p-4">
                                                            <!-- X Axis -->
                                                            <div class="flex items-start gap-4">
                                                                <div class="flex-shrink-0 w-1 h-12 bg-red-500 rounded-full shadow-[0_0_15px_rgba(239,68,68,0.4)]"></div>
                                                                <div class="flex flex-col">
                                                                    <div class="text-red-600 dark:text-red-500 font-black text-xs tracking-tighter uppercase mb-0.5">X Axis - Horizontal</div>
                                                                    <div class="text-[11px] text-gray-600 dark:text-gray-300 leaders-none"><b>Position:</b> Left / Right &bull; <b>Rotation:</b> Pitch</div>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Y Axis -->
                                                            <div class="flex items-start gap-4">
                                                                <div class="flex-shrink-0 w-1 h-12 bg-green-500 rounded-full shadow-[0_0_15px_rgba(34,197,94,0.4)]"></div>
                                                                <div class="flex flex-col">
                                                                    <div class="text-green-600 dark:text-green-500 font-black text-xs tracking-tighter uppercase mb-0.5">Y Axis - Vertical</div>
                                                                    <div class="text-[11px] text-gray-600 dark:text-gray-300"><b>Position:</b> Up / Down &bull; <b>Rotation:</b> Yaw</div>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Z Axis -->
                                                            <div class="flex items-start gap-4">
                                                                <div class="flex-shrink-0 w-1 h-12 bg-blue-500 rounded-full shadow-[0_0_15px_rgba(59,130,246,0.4)]"></div>
                                                                <div class="flex flex-col">
                                                                    <div class="text-blue-600 dark:text-blue-500 font-black text-xs tracking-tighter uppercase mb-0.5">Z Axis - Depth</div>
                                                                    <div class="text-[11px] text-gray-600 dark:text-gray-300"><b>Position:</b> Forward / Backward &bull; <b>Rotation:</b> Roll</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    '))
                                                    ->columnSpan(['lg' => 2]),
                                            ]),
                                        Grid::make(['default' => 1])
                                            ->components([
                                                // Section 1
                                                Section::make('01. THE DISCOVERY (Hero)')
                                                    ->compact()
                                                    ->collapsible()
                                                    ->components([
                                                        Grid::make(['default' => 3])
                                                            ->components([
                                                                TextInput::make('3d_pos_1')->label('Position')->placeholder('{"x":0, "y":0, "z":0}')->prefix('POS')
                                                                    ->hintAction(Action::make('reset_pos_1')->icon('heroicon-m-arrow-path')->action(fn($set) => $set('3d_pos_1', '{"x":0, "y":0, "z":0}'))),
                                                                TextInput::make('3d_rot_1')->label('Rotation')->placeholder('{"x":0, "y":0, "z":0}')->prefix('ROT')
                                                                    ->hintAction(Action::make('reset_rot_1')->icon('heroicon-m-arrow-path')->action(fn($set) => $set('3d_rot_1', '{"x":0, "y":0, "z":0}'))),
                                                                Group::make([
                                                                    TextInput::make('3d_scale_1')
                                                                        ->label('Scale (Manual)')
                                                                        ->placeholder('1.0')
                                                                        ->prefix('SCL'),
                                                                    Slider::make('3d_scale_1')
                                                                        ->label('Scale (Slider)')
                                                                        ->minValue(0)
                                                                        ->maxValue(50.0)
                                                                        ->step(0.001),
                                                                ]),
                                                            ]),
                                                    ]),

                                                // Section 2
                                                Section::make('02. THE MATRIX (Services)')
                                                    ->compact()
                                                    ->collapsible()
                                                    ->components([
                                                        Grid::make(['default' => 3])
                                                            ->components([
                                                                TextInput::make('3d_pos_2')->label('Position')->prefix('POS')
                                                                    ->hintAction(Action::make('reset_pos_2')->icon('heroicon-m-arrow-path')->action(fn($set) => $set('3d_pos_2', '{"x":0, "y":0, "z":0}'))),
                                                                TextInput::make('3d_rot_2')->label('Rotation')->prefix('ROT')
                                                                    ->hintAction(Action::make('reset_rot_2')->icon('heroicon-m-arrow-path')->action(fn($set) => $set('3d_rot_2', '{"x":0, "y":0, "z":0}'))),
                                                                Group::make([
                                                                    TextInput::make('3d_scale_2')->label('Scale (Manual)')->prefix('SCL'),
                                                                    Slider::make('3d_scale_2')
                                                                        ->label('Scale (Slider)')
                                                                        ->minValue(0)
                                                                        ->maxValue(50.0)
                                                                        ->step(0.001),
                                                                ]),
                                                            ]),
                                                    ]),

                                                // Section 3
                                                Section::make('03. THE WORKS (Portfolio)')
                                                    ->compact()
                                                    ->collapsible()
                                                    ->components([
                                                        Grid::make(['default' => 3])
                                                            ->components([
                                                                TextInput::make('3d_pos_3')->label('Position')->prefix('POS')
                                                                    ->hintAction(Action::make('reset_pos_3')->icon('heroicon-m-arrow-path')->action(fn($set) => $set('3d_pos_3', '{"x":0, "y":0, "z":0}'))),
                                                                TextInput::make('3d_rot_3')->label('Rotation')->prefix('ROT')
                                                                    ->hintAction(Action::make('reset_rot_3')->icon('heroicon-m-arrow-path')->action(fn($set) => $set('3d_rot_3', '{"x":0, "y":0, "z":0}'))),
                                                                Group::make([
                                                                    TextInput::make('3d_scale_3')->label('Scale (Manual)')->prefix('SCL'),
                                                                    Slider::make('3d_scale_3')
                                                                        ->label('Scale (Slider)')
                                                                        ->minValue(0)
                                                                        ->maxValue(50.0)
                                                                        ->step(0.001),
                                                                ]),
                                                            ]),
                                                    ]),

                                                // Section 4
                                                Section::make('04. THE PEOPLE (Teams)')
                                                    ->compact()
                                                    ->collapsible()
                                                    ->components([
                                                        Grid::make(['default' => 3])
                                                            ->components([
                                                                TextInput::make('3d_pos_4')->label('Position')->prefix('POS')
                                                                    ->hintAction(Action::make('reset_pos_4')->icon('heroicon-m-arrow-path')->action(fn($set) => $set('3d_pos_4', '{"x":0, "y":0, "z":0}'))),
                                                                TextInput::make('3d_rot_4')->label('Rotation')->prefix('ROT')
                                                                    ->hintAction(Action::make('reset_rot_4')->icon('heroicon-m-arrow-path')->action(fn($set) => $set('3d_rot_4', '{"x":0, "y":0, "z":0}'))),
                                                                Group::make([
                                                                    TextInput::make('3d_scale_4')->label('Scale (Manual)')->prefix('SCL'),
                                                                    Slider::make('3d_scale_4')
                                                                        ->label('Scale (Slider)')
                                                                        ->minValue(0)
                                                                        ->maxValue(50.0)
                                                                        ->step(0.001),
                                                                ]),
                                                            ]),
                                                    ]),

                                                // Section 5
                                                Section::make('05. THE NARRATIVE (Journal)')
                                                    ->compact()
                                                    ->collapsible()
                                                    ->components([
                                                        Grid::make(['default' => 3])
                                                            ->components([
                                                                TextInput::make('3d_pos_5')->label('Position')->prefix('POS')
                                                                    ->hintAction(Action::make('reset_pos_5')->icon('heroicon-m-arrow-path')->action(fn($set) => $set('3d_pos_5', '{"x":0, "y":0, "z":0}'))),
                                                                TextInput::make('3d_rot_5')->label('Rotation')->prefix('ROT')
                                                                    ->hintAction(Action::make('reset_rot_5')->icon('heroicon-m-arrow-path')->action(fn($set) => $set('3d_rot_5', '{"x":0, "y":0, "z":0}'))),
                                                                Group::make([
                                                                    TextInput::make('3d_scale_5')->label('Scale (Manual)')->prefix('SCL'),
                                                                    Slider::make('3d_scale_5')
                                                                        ->label('Scale (Slider)')
                                                                        ->minValue(0)
                                                                        ->maxValue(50.0)
                                                                        ->step(0.001),
                                                                ]),
                                                            ]),
                                                    ]),

                                                // Section 6
                                                Section::make('06. THE CONNECTION (Contact)')
                                                    ->compact()
                                                     ->collapsible()
                                                     ->components([
                                                         Grid::make(['default' => 3])
                                                             ->components([
                                                                 TextInput::make('3d_pos_6')->label('Position')->prefix('POS')
                                                                    ->hintAction(Action::make('reset_pos_6')->icon('heroicon-m-arrow-path')->action(fn($set) => $set('3d_pos_6', '{"x":0, "y":0, "z":0}'))),
                                                                 TextInput::make('3d_rot_6')->label('Rotation')->prefix('ROT')
                                                                    ->hintAction(Action::make('reset_rot_6')->icon('heroicon-m-arrow-path')->action(fn($set) => $set('3d_rot_6', '{"x":0, "y":0, "z":0}'))),
                                                                 Group::make([
                                                                     TextInput::make('3d_scale_6')->label('Scale (Manual)')->prefix('SCL'),
                                                                     Slider::make('3d_scale_6')
                                                                         ->label('Scale (Slider)')
                                                                         ->minValue(0)
                                                                         ->maxValue(50.0)
                                                                         ->step(0.001),
                                                                 ]),
                                                             ]),
                                                     ]),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('Sections')
                            ->icon('heroicon-o-list-bullet')
                            ->components([
                                Section::make('01. Discovery (Hero)')
                                    ->components([
                                        TextInput::make('hero_title_1')->label('Hero Title Line 1')->placeholder('Non-stop'),
                                        TextInput::make('hero_title_2')->label('Hero Title Line 2')->placeholder('Creative'),
                                        TextInput::make('hero_tagline')->label('Hero Tagline')->placeholder('Where ideas meet infinity.'),
                                        TextInput::make('section_1_label')->label('Navigation Label')->placeholder('01. Insight'),
                                    ]),
                                Section::make('02. The Matrix (Services)')
                                    ->components([
                                        TextInput::make('section_2_label')->label('Navigation Label')->placeholder('02. Capabilities'),
                                        TextInput::make('section_2_title')->label('Section Title')->placeholder('Infinite Services'),
                                    ]),
                                Section::make('03. The Works (Projects)')
                                    ->components([
                                        TextInput::make('section_3_label')->label('Navigation Label')->placeholder('03. Archive'),
                                        TextInput::make('section_3_title')->label('Section Title')->placeholder('The Archive'),
                                    ]),
                                Section::make('04. The People (Team)')
                                    ->components([
                                        TextInput::make('section_4_label')->label('Navigation Label')->placeholder('04. Collective'),
                                        TextInput::make('section_4_title')->label('Section Title')->placeholder('Our Team'),
                                    ]),
                                Section::make('05. The Narrative (Journal)')
                                    ->components([
                                        TextInput::make('journal_label')->label('Navigation Label')->placeholder('05. Journal'),
                                        TextInput::make('journal_title')->label('Section Title')->placeholder('Latest Stories'),
                                    ]),
                                Section::make('06. The Connection (Contact)')
                                    ->components([
                                        TextInput::make('section_5_label')->label('Navigation Label')->placeholder('06. Reach'),
                                        Textarea::make('section_5_title')
                                            ->label('Section Title (HTML allowed)')
                                            ->placeholder("Let's <br/>Build <br/>Together.")
                                            ->rows(3),
                                    ]),
                                Section::make('Footer')
                                    ->components([
                                        TextInput::make('footer_text')->label('Footer Copyright Line'),
                                    ]),
                            ]),

                        Tab::make('Presence')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->components([
                                Section::make('Contact Details')
                                    ->components([
                                        Grid::make(['default' => 2])
                                            ->components([
                                                TextInput::make('contact_email'),
                                                TextInput::make('contact_phone'),
                                                Textarea::make('contact_address')->columnSpanFull(),
                                            ]),
                                    ]),
                                Section::make('Social Links')
                                    ->components([
                                        Grid::make(['default' => 2])
                                            ->components([
                                                TextInput::make('social_instagram')->prefix('instagram.com/'),
                                                TextInput::make('social_linkedin')->prefix('linkedin.com/in/'),
                                                TextInput::make('social_facebook'),
                                                TextInput::make('social_youtube'),
                                                TextInput::make('social_github'),
                                                TextInput::make('twitter_handle')->label('Twitter/X Handle'),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('Geo & SEO')
                            ->icon('heroicon-o-map-pin')
                            ->components([
                                Section::make('Location Tags')
                                    ->components([
                                        Grid::make(['default' => 2])
                                            ->components([
                                                TextInput::make('geo_placename'),
                                                TextInput::make('geo_region'),
                                                TextInput::make('geo_latitude'),
                                                TextInput::make('geo_longitude'),
                                            ]),
                                    ]),
                                Section::make('Discovery')
                                    ->components([
                                        TextInput::make('site_keywords')->placeholder('creative, agency, 3d'),
                                    ]),
                            ]),

                        Tab::make('Mail Config')
                            ->icon('heroicon-o-envelope')
                            ->components([
                                Section::make('SMTP Configuration')
                                    ->description('Configure your outgoing email server settings. These settings will override the default server configuration.')
                                    ->components([
                                        Grid::make(['default' => 2])
                                            ->components([
                                                Select::make('mail_mailer')
                                                    ->label('Mailer')
                                                    ->options([
                                                        'smtp' => 'SMTP',
                                                        'log' => 'Log (Development)',
                                                    ])
                                                    ->default('smtp'),
                                                TextInput::make('mail_host')
                                                    ->label('Host')
                                                    ->placeholder('smtp.mailtrap.io'),
                                                TextInput::make('mail_port')
                                                    ->label('Port')
                                                    ->placeholder('2525'),
                                                Select::make('mail_encryption')
                                                    ->label('Encryption')
                                                    ->options([
                                                        '' => 'None',
                                                        'tls' => 'TLS',
                                                        'ssl' => 'SSL',
                                                    ])
                                                    ->placeholder('Select encryption'),
                                                TextInput::make('mail_username')
                                                    ->label('Username')
                                                    ->placeholder('your-username'),
                                                TextInput::make('mail_password')
                                                    ->label('Password')
                                                    ->password()
                                                    ->revealable(),
                                                TextInput::make('mail_from_address')
                                                    ->label('From Address')
                                                    ->placeholder('hello@3flo.net'),
                                                TextInput::make('mail_from_name')
                                                    ->label('From Name')
                                                    ->placeholder('3FLO Team'),
                                            ]),
                                        
                                        Grid::make(['default' => 1])
                                            ->components([
                                                \Filament\Forms\Components\Placeholder::make('test_mail_action')
                                                    ->label('Verification')
                                                    ->content('Send a test email to verify your SMTP settings. Make sure to save your settings first or the test might fail.')
                                                    ->hintAction(
                                                        Action::make('sendTestEmail')
                                                            ->label('Send Test Email')
                                                            ->icon('heroicon-m-paper-airplane')
                                                            ->color('success')
                                                            ->form([
                                                                TextInput::make('recipient_email')
                                                                    ->label('Recipient Email')
                                                                    ->email()
                                                                    ->required()
                                                                    ->default(auth()->user()->email),
                                                            ])
                                                            ->action(function (array $data) {
                                                                $this->testSmtpConnection($data['recipient_email']);
                                                            })
                                                    ),
                                            ]),
                                    ]),
                            ]),
                        Tab::make('Maintenance')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->components([
                                Section::make('Storage Cleaner Settings')
                                    ->description('Configure which folders should be scanned for orphaned/deleted files.')
                                    ->components([
                                        Select::make('maintenance_monitored_folders')
                                            ->label('Monitored Folders')
                                            ->multiple()
                                            ->searchable()
                                            ->createOptionUsing(fn ($data) => $data['name'])
                                            ->options(function() {
                                                $dirs = Storage::disk('public')->directories();
                                                return array_combine($dirs, $dirs);
                                            })
                                            ->helperText('Select folders inside storage/app/public to be scanned for orphaned files. New folders created in storage will automatically appear here.')
                                            ->default(['3d-models', 'articles', 'projects', 'services', 'settings', 'team']),
                                    ]),

                                Section::make('Traffic Data Pruning')
                                    ->description('Manually delete old site visit records to keep the database lean. Data older than the selected threshold will be permanently removed.')
                                    ->icon('heroicon-o-trash')
                                    ->components([
                                        \Filament\Forms\Components\Placeholder::make('visit_stats')
                                            ->label('Current Traffic Records')
                                            ->content(function () {
                                                $total = \App\Models\SiteVisit::count();
                                                $oldest = \App\Models\SiteVisit::min('created_at');
                                                $oldest = $oldest ? \Carbon\Carbon::parse($oldest)->diffForHumans() : 'N/A';
                                                return new \Illuminate\Support\HtmlString(
                                                    "<span style='font-weight:700;font-size:1.5rem;color:#0f172a'>{$total}</span>"
                                                    . "<span style='color:#64748b;font-size:0.85rem;margin-left:0.5rem'>total rows — oldest record: {$oldest}</span>"
                                                );
                                            }),
                                        Select::make('prune_days_select')
                                            ->label('Delete records older than')
                                            ->options([
                                                '30'  => '30 Days',
                                                '60'  => '60 Days',
                                                '90'  => '90 Days (Recommended)',
                                                '180' => '6 Months',
                                                '365' => '1 Year',
                                            ])
                                            ->default('90')
                                            ->live()
                                            ->afterStateUpdated(fn ($state) => $this->pruneVisitsDays = (int) $state)
                                            ->helperText('Records within this window are kept. Everything older will be permanently deleted.'),
                                        \Filament\Forms\Components\Placeholder::make('prune_preview')
                                            ->label('Records to be deleted')
                                            ->content(function () {
                                                $count = \App\Models\SiteVisit::where('created_at', '<', now()->subDays($this->pruneVisitsDays))->count();
                                                $color = $count > 0 ? '#dc2626' : '#16a34a';
                                                $icon  = $count > 0 ? '⚠️' : '✅';
                                                return new \Illuminate\Support\HtmlString(
                                                    "<span style='font-weight:700;font-size:1.25rem;color:{$color}'>{$icon} {$count} records</span>"
                                                );
                                            }),
                                    ])
                                    ->footerActions([
                                        Action::make('prune_traffic')
                                            ->label('🗑️ Prune Now')
                                            ->color('danger')
                                            ->requiresConfirmation()
                                            ->modalHeading('Prune Traffic Data')
                                            ->modalDescription(fn () => "This will permanently delete all site_visits records older than {$this->pruneVisitsDays} days. This action cannot be undone.")
                                            ->action(fn () => $this->pruneTrafficData()),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // Pembersihan ekstraksi FileUpload karena Filament mengembalikan path dalam string atau array
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                // Diabaikan, Filament menghandle penyimpanan fisik via disk
                 continue; 
            }
            if (is_array($value)) {
                // Jika ini adalah monitored folders (Multiple Select), simpan sebagai JSON string
                if ($key === 'maintenance_monitored_folders') {
                    $value = json_encode(array_values($value));
                } else {
                    // Jika FileUpload atau array biasa lainnya, ambil path string pertama
                    $value = collect($value)->flatten()->filter(fn($v) => is_string($v))->first();
                }
            }

            // Handle Toggle/Boolean values explicitly
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            }

            // Simpan value murni sebagai string ke Database
            if ($value !== null && !is_string($value)) {
                $value = is_numeric($value) ? (string) $value : null;
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => $this->getTypeForKey($key)]
            );
        }

        Notification::make()
            ->title('Settings saved successfully!')
            ->success()
            ->send();

        // 🛡️ SECURITY LAYER: SVG Sanitization (Prevent XSS/XXE)
        $this->sanitizeUploadedSvgs();

        // Paksa refresh halaman menggunakan Internal URL yang aman (Mencegah Open Redirect)
        $this->redirect(static::getUrl());
    }

    protected function sanitizeUploadedSvgs(): void
    {
        $sanitizer = new \enshrined\svgSanitize\Sanitizer();
        
        // Ambil semua file yang baru disimpan di tabel Settings
        $settingsWithFiles = Setting::whereIn('key', ['site_logo', 'site_favicon', 'site_og_image', 'intro_logo'])
            ->whereNotNull('value')
            ->get();

        foreach ($settingsWithFiles as $setting) {
            $path = $setting->value;
            
            // Periksa apakah file adalah SVG
            if (str_ends_with(strtolower($path), '.svg')) {
                if (Storage::disk('public')->exists($path)) {
                    $originalContent = Storage::disk('public')->get($path);
                    $cleanContent = $sanitizer->sanitize($originalContent);
                    
                    // Simpan kembali konten yang sudah bersih
                    Storage::disk('public')->put($path, $cleanContent);
                }
            }
        }
    }

    public function testSmtpConnection(string $recipientEmail): void
    {
        $data = $this->form->getState();

        try {
            // Force reset the mailer instance to use new config
            Mail::purge('smtp_test');

            // Dynamic configuration for test mailer
            config([
                'mail.mailers.smtp_test' => [
                    'transport' => $data['mail_mailer'] ?? 'smtp',
                    'host' => $data['mail_host'] ?? '',
                    'port' => $data['mail_port'] ?? '',
                    'encryption' => $data['mail_encryption'] ?? '',
                    'username' => $data['mail_username'] ?? '',
                    'password' => $data['mail_password'] ?? '',
                    'timeout' => 15,
                    'stream' => [
                        'ssl' => [
                            'allow_self_signed' => true,
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                    ],
                ],
                'mail.from.address' => $data['mail_from_address'] ?? config('mail.from.address'),
                'mail.from.name' => $data['mail_from_name'] ?? config('mail.from.name'),
            ]);

            Mail::mailer('smtp_test')->to($recipientEmail)->send(new TestMail());

            Notification::make()
                ->title('SMTP Test Successful')
                ->body('Test email sent to ' . $recipientEmail . '. Please check your inbox and Junk folder.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            $error = $e->getMessage();
            
            // Add custom hints based on common errors
            if (str_contains($error, 'Connection could not be established')) {
                $error .= " | Hint: Check if Port " . ($data['mail_port'] ?? '') . " or Host " . ($data['mail_host'] ?? '') . " is blocked/incorrect.";
            } elseif (str_contains($error, 'Authentication failed')) {
                $error .= " | Hint: Verify your SMTP Username and Password.";
            } elseif (str_contains($error, 'Connection timed out')) {
                $error .= " | Hint: The server didn't respond. Possible firewall issue.";
            }

            Notification::make()
                ->title('SMTP Test Failed')
                ->body($error)
                ->danger()
                ->persistent()
                ->send();
        }
    }

    protected function getTypeForKey(string $key): string
    {
        if (in_array($key, ['site_logo', 'site_favicon', 'site_og_image', '3d_model_custom'])) {
            return 'image';
        }
        if (in_array($key, ['site_description', 'section_5_title'])) {
            return 'textarea';
        }
        if (str_starts_with($key, 'mail_')) {
            return 'smtp';
        }
        return 'text';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clear_cache')
                ->label('Clear System Cache')
                ->icon('heroicon-o-bolt')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
                    \Illuminate\Support\Facades\Artisan::call('view:clear');
                    Notification::make()->title('System Cache Cleared!')->success()->send();
                }),

            Action::make('clean_storage')
                ->label('Clean Orphaned Files')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Empty Garbage Storage')
                ->modalDescription('This will scan the monitored folders and delete files that are not linked in the database. Proceed with caution!')
                ->action(function () {
                    $this->runGarbageCollector();
                }),
        ];
    }

    protected function runGarbageCollector(): void
    {
        // 1. Get monitored folders (Now returns array from MultiSelect)
        $setting = Setting::where('key', 'maintenance_monitored_folders')->first();
        $folders = [];
        
        if ($setting) {
            $value = $setting->value;
            // Handle if stored as JSON (new MultiSelect) or Comma String (old legacy)
            if (str_starts_with($value, '[')) {
                $folders = json_decode($value, true) ?: [];
            } else {
                $folders = array_map('trim', explode(',', $value));
            }
        } else {
            $folders = ['3d-models', 'articles', 'projects', 'services', 'settings', 'team'];
        }

        $folders = array_filter($folders); // remove empty

        // 🛡️ SECURITY LAYER: Sanitization & Path Traversal Protection
        $folders = array_map(function($f) {
            // Clean up path navigation (../) and take only the last folder name
            $clean = basename(str_replace(['\\', '/'], '/', trim($f)));
            // Ensure it only contains safe characters (alphanumeric, hyphen, underscore)
            return preg_replace('/[^a-zA-Z0-9\-_]/', '', $clean);
        }, $folders);
        $folders = array_filter($folders); // re-filter after sanitization

        // 🛡️ SECURITY LAYER: Prevent Disaster (Double Check)
        $forbidden = ['', '/', '.', '..', 'public', 'storage', 'database', 'app', 'routes', 'config', 'bootstrap', 'vendor'];
        $folders = array_filter($folders, fn($f) => !in_array(strtolower($f), $forbidden));
        
        // 2. Build whitelist from Database
        $whitelist = [];
        
        // Settings table file references
        $settingsFiles = Setting::whereIn('key', ['3d_model_custom', 'site_logo', 'site_favicon', 'site_og_image', 'intro_logo'])
                                  ->pluck('value')->toArray();
        foreach($settingsFiles as $sf) if($sf) $whitelist[] = $sf;
        
        // Projects table
        if (\Illuminate\Support\Facades\Schema::hasTable('projects')) {
            $projects = \Illuminate\Support\Facades\DB::table('projects')->get(['banner_image', 'gallery']);
            foreach($projects as $p) {
                if($p->banner_image) $whitelist[] = $p->banner_image;
                if($p->gallery) {
                    $images = json_decode($p->gallery, true);
                    if(is_array($images)) {
                        foreach($images as $img) if($img) $whitelist[] = $img;
                    }
                }
            }
        }

        // Services table
        if (\Illuminate\Support\Facades\Schema::hasTable('services')) {
            $services = \Illuminate\Support\Facades\DB::table('services')->get(['icon', 'banner_image']);
            foreach($services as $s) {
                if($s->icon) $whitelist[] = $s->icon;
                if($s->banner_image) $whitelist[] = $s->banner_image;
            }
        }

        // Team members table
        if (\Illuminate\Support\Facades\Schema::hasTable('team_members')) {
            $teamPhotos = \Illuminate\Support\Facades\DB::table('team_members')->pluck('photo')->toArray();
            foreach($teamPhotos as $tp) if($tp) $whitelist[] = $tp;
        }

        // Articles table
        if (\Illuminate\Support\Facades\Schema::hasTable('articles')) {
            $articleImages = \Illuminate\Support\Facades\DB::table('articles')->pluck('thumbnail')->toArray();
            foreach($articleImages as $ai) if($ai) $whitelist[] = $ai;
        }

        // 3. Scan physical folders and delete if not in whitelist
        $deletedCount = 0;
        $freedBytes = 0;

        foreach ($folders as $folder) {
            // Prevent accidental deletion of livewire-tmp by standard loop
            if ($folder === 'livewire-tmp') continue;

            if (Storage::disk('public')->exists($folder)) {
                $files = Storage::disk('public')->allFiles($folder);
                foreach ($files as $file) {
                    if (!in_array($file, $whitelist)) {
                        $size = Storage::disk('public')->size($file);
                        Storage::disk('public')->delete($file);
                        $deletedCount++;
                        $freedBytes += $size;
                    }
                }
            }
        }

        // 4. Specifically Clean 'livewire-tmp' automatically for files older than 24 hours
        if (Storage::disk('public')->exists('livewire-tmp')) {
            $tmpFiles = Storage::disk('public')->allFiles('livewire-tmp');
            $now = now()->timestamp;
            
            foreach ($tmpFiles as $tmpFile) {
                // Delete if file is older than 24 hours (86400 seconds)
                if ($now - Storage::disk('public')->lastModified($tmpFile) > 86400) {
                    $size = Storage::disk('public')->size($tmpFile);
                    Storage::disk('public')->delete($tmpFile);
                    $deletedCount++;
                    $freedBytes += $size;
                }
            }
        }

        $mb = round($freedBytes / 1048576, 2);
        
        Notification::make()
            ->title("Garbage Collection Complete")
            ->body("Deleted {$deletedCount} orphaned files and freed {$mb} MB of space.")
            ->success()
            ->send();
    }

    public function pruneTrafficData(): void
    {
        $days = $this->pruneVisitsDays ?: 90;
        $cutoff = now()->subDays($days);
        $count = \App\Models\SiteVisit::where('created_at', '<', $cutoff)->count();

        if ($count === 0) {
            Notification::make()
                ->title('Nothing to Prune')
                ->body("No traffic records older than {$days} days found.")
                ->info()
                ->send();
            return;
        }

        \App\Models\SiteVisit::where('created_at', '<', $cutoff)->delete();

        Notification::make()
            ->title('Traffic Data Pruned')
            ->body("Successfully deleted {$count} visit records older than {$days} days.")
            ->success()
            ->send();
    }
}
