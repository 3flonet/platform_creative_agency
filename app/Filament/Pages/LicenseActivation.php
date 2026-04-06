<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\LicenseService;

class LicenseActivation extends Page implements \Filament\Forms\Contracts\HasForms
{
    use \Filament\Forms\Concerns\InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationLabel = 'License';
    protected static ?string $title = 'License Activation';
    protected static ?string $slug = 'license/activate';
    protected string $view = 'filament.pages.license-activation';

    public ?array $data = [];
    public bool $isConfigured = false;

    public function mount(): void
    {
        $this->isConfigured = !empty(config('services.license_hub.product_secret'));
        
        $this->form->fill([
            'license_key' => \App\Models\Setting::get('license_key'),
            'license_hub_url' => config('services.license_hub.url'),
            'product_secret' => config('services.license_hub.product_secret'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->components([
                        // Form Section (Left)
                        Group::make()
                            ->columnSpan(['default' => 2])
                            ->components([
                                // Step 1: Connection Setup (Visible only if not configured)
                                Section::make('Step 1: Connection Setup')
                                    ->description('Configure the connection to the license server.')
                                    ->visible(!$this->isConfigured)
                                    ->components([
                                        TextInput::make('license_hub_url')
                                            ->label('License Server URL')
                                            ->default('https://license.3flo.net/')
                                            ->required(),
                                        TextInput::make('product_secret')
                                            ->label('Product Secret Key')
                                            ->password()
                                            ->helperText('Get this from your LicenseHub dashboard.')
                                            ->required(),
                                    ]),

                                // Step 2: Activation
                                Section::make('Step 2: Product Activation')
                                    ->description('Enter your license key to unlock premium features and updates.')
                                    ->visible($this->isConfigured)
                                    ->components([
                                        TextInput::make('license_key')
                                            ->label('License Key')
                                            ->placeholder('3FL0-XXXX-XXXX-XXXX')
                                            ->required()
                                            ->helperText('Your key is unique to this installation.')
                                            ->extraInputAttributes(['class' => 'font-mono uppercase']),
                                    ]),
                                
                                Html::make('help')
                                    ->content(new HtmlString('
                                        <div style="margin-top: 24px; padding: 20px; background: linear-gradient(135deg, #fffbeb 0%, #fff7ed 100%); border: 1px solid #fef3c7; border-radius: 24px; display: flex; gap: 16px; position: relative; overflow: hidden; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                                            <div style="position: absolute; right: -16px; top: -16px; width: 96px; height: 96px; background: rgba(254, 243, 199, 0.4); border-radius: 50%; filter: blur(24px);"></div>
                                            <div style="flex-shrink: 0; width: 44px; height: 44px; background: #ffffff; border-radius: 14px; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #fef3c7;">
                                                <svg style="width: 20px; height: 20px; color: #d97706;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </div>
                                            <div style="padding-top: 2px;">
                                                <span style="display: block; font-weight: 800; color: #92400e; font-size: 14px; margin-bottom: 2px;">Butuh bantuan?</span> 
                                                <span style="color: #b45309; font-size: 13px; line-height: 1.5; opacity: 0.9;">Jika Anda mengalami kendala aktivasi atau ingin mereset kuota domain, silakan akses <a href="https://license.3flo.net//contact" target="_blank" style="font-weight: 700; text-decoration: underline; color: #ca8a04;">Layanan Support 3FLO</a>.</span>
                                            </div>
                                        </div>
                                    ')),
                            ]),

                        // Status Section (Right)
                        Section::make('Current Integrity')
                            ->columnSpan(['default' => 1])
                            ->components([
                                Html::make('status_info')
                                    ->content(function(LicenseService $service) {
                                        $status = strtoupper(\App\Models\Setting::get('license_status', 'inactive'));
                                        
                                        // 🛡️ SECURITY Check
                                        $localStatus = $service->check();
                                        if ($localStatus === 'tampered') $status = 'TAMPERED';
                                        
                                        $isActive = $status === 'ACTIVE';
                                        $isTampered = $status === 'TAMPERED';
                                        
                                        $mainColor = $isActive ? '#10b981' : ($isTampered ? '#7c3aed' : '#f43f5e');
                                        $subColor = $isActive ? '#ecfdf5' : ($isTampered ? '#f5f3ff' : '#fff1f2');
                                        $borderColor = $isActive ? '#d1fae5' : ($isTampered ? '#ede9fe' : '#ffe4e6');
                                        
                                        $icon = $isActive 
                                            ? '<svg style="width: 32px; height: 32px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                                            : ($isTampered 
                                                ? '<svg style="width: 32px; height: 32px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>'
                                                : '<svg style="width: 32px; height: 32px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>');

                                        return new HtmlString("
                                            <div style='display: flex; flex-direction: column; align-items: center; padding: 8px 0;'>
                                                <div style='position: relative; margin-bottom: 24px;'>
                                                    <div style='position: absolute; inset: 0; filter: blur(16px); opacity: 0.2; background: {$mainColor};'></div>
                                                    <div style='position: relative; width: 72px; height: 72px; background: {$subColor}; color: {$mainColor}; border: 2px solid {$borderColor}; border-radius: 22px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);'>
                                                        {$icon}
                                                    </div>
                                                </div>
                                                
                                                <div style='text-align: center; margin-bottom: 32px;'>
                                                    <div style='font-size: 22px; font-weight: 900; letter-spacing: -0.025em; font-style: italic; color: {$mainColor}; line-height: 1;'>
                                                        {$status}
                                                    </div>
                                                    <div style='font-size: 9px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.2em; margin-top: 6px;'>
                                                        System Integrity
                                                    </div>
                                                </div>

                                                <div style='width: 100%; border-top: 1px solid #f3f4f6; padding-top: 24px;'>
                                                    <div style='margin-bottom: 20px;'>
                                                        <div style='font-size: 9px; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px;'>Host Identity</div>
                                                        <div style='background: #f9fafb; border: 1px solid #f3f4f6; padding: 12px; border-radius: 14px; font-family: monospace; font-size: 11px; font-weight: 700; color: #374151; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;'>
                                                            " . request()->getHost() . "
                                                        </div>
                                                    </div>
                                                    
                                                    <div style='display: flex; align-items: center; justify-content: space-between;'>
                                                        <div style='font-size: 9px; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em;'>Engine</div>
                                                        <div style='background: #111827; color: #ffffff; padding: 4px 10px; border-radius: 9999px; font-size: 9px; font-weight: 900; letter-spacing: -0.01em;'>
                                                            VSecure-1.0
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        ");
                                    })
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save_config')
                ->label('Save Connection')
                ->color('primary')
                ->visible(!$this->isConfigured)
                ->action('saveConfig'),

            Action::make('activate')
                ->label('Activate License')
                ->submit('activate')
                ->visible($this->isConfigured),

            Action::make('deactivate')
                ->label('Deactivate')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn (LicenseService $service) => $this->deactivateLicense($service))
                ->visible(fn () => \App\Models\Setting::get('license_status') === 'active'),
        ];
    }

    public function saveConfig(): void
    {
        $data = $this->form->getState();
        
        $success = \App\Helpers\EnvHelper::setMany([
            'LICENSE_HUB_URL' => $data['license_hub_url'],
            'LICENSE_HUB_PRODUCT_SECRET' => $data['product_secret'],
        ]);

        if ($success) {
            Notification::make()
                ->title('Connection Saved')
                ->success()
                ->send();
            
            $this->redirect('/admin/license/activate');
        } else {
            Notification::make()
                ->title('Error')
                ->body('Failed to write to .env file. Please check permissions.')
                ->danger()
                ->send();
        }
    }

    public function activate(LicenseService $service): void
    {
        $data = $this->form->getState();
        $result = $service->verify($data['license_key'] ?? '');

        if ($result['success']) {
            Notification::make()
                ->title('License Activated')
                ->success()
                ->send();
            
            $this->redirect('/admin');
        } else {
            Notification::make()
                ->title('Activation Failed')
                ->body($result['message'])
                ->danger()
                ->send();
        }
    }

    public function deactivateLicense(LicenseService $service): void
    {
        if ($service->deactivate()) {
            Notification::make()
                ->title('License Deactivated')
                ->success()
                ->send();
            
            $this->form->fill(['license_key' => '']);
        } else {
            Notification::make()
                ->title('Deactivation Failed')
                ->danger()
                ->send();
        }
    }
}
