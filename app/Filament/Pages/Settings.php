<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use App\Models\Setting;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.settings';
    protected static ?string $title = 'System Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getSettingsData());
    }

    protected function getSettingsData(): array
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return [
            // Midtrans Settings
            'midtrans_merchant_id' => $settings['midtrans_merchant_id'] ?? '',
            'midtrans_server_key' => $settings['midtrans_server_key'] ?? '',
            'midtrans_client_key' => $settings['midtrans_client_key'] ?? '',
            'midtrans_is_production' => ($settings['midtrans_is_production'] ?? 'false') === 'true',
            'midtrans_is_sanitized' => ($settings['midtrans_is_sanitized'] ?? 'true') === 'true',
            'midtrans_is_3ds' => ($settings['midtrans_is_3ds'] ?? 'true') === 'true',

            // Email Notification Settings
            'email_notifications_enabled' => ($settings['email_notifications_enabled'] ?? 'false') === 'true',
            'email_on_order_created' => ($settings['email_on_order_created'] ?? 'true') === 'true',
            'email_on_order_paid' => ($settings['email_on_order_paid'] ?? 'true') === 'true',
            'email_on_order_approved' => ($settings['email_on_order_approved'] ?? 'true') === 'true',
            'email_on_order_processing' => ($settings['email_on_order_processing'] ?? 'true') === 'true',
            'email_on_order_shipped' => ($settings['email_on_order_shipped'] ?? 'true') === 'true',
            'email_on_order_delivered' => ($settings['email_on_order_delivered'] ?? 'true') === 'true',
            'email_on_order_cancelled' => ($settings['email_on_order_cancelled'] ?? 'true') === 'true',

            // SMTP Settings
            'mail_host' => $settings['mail_host'] ?? env('MAIL_HOST', ''),
            'mail_port' => $settings['mail_port'] ?? env('MAIL_PORT', '587'),
            'mail_username' => $settings['mail_username'] ?? env('MAIL_USERNAME', ''),
            'mail_password' => $settings['mail_password'] ?? env('MAIL_PASSWORD', ''),
            'mail_encryption' => $settings['mail_encryption'] ?? env('MAIL_ENCRYPTION', 'tls'),
            'mail_from_address' => $settings['mail_from_address'] ?? env('MAIL_FROM_ADDRESS', ''),
            'mail_from_name' => $settings['mail_from_name'] ?? env('MAIL_FROM_NAME', ''),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('Midtrans Payment Gateway')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Section::make('Midtrans Configuration')
                                    ->description('Configure Midtrans payment gateway settings')
                                    ->schema([
                                        TextInput::make('midtrans_merchant_id')
                                            ->label('Merchant ID')
                                            ->required()
                                            ->disabled(config('app.demo'))
                                            ->helperText('Your Midtrans Merchant ID from dashboard')
                                            ->columnSpanFull(),

                                        TextInput::make('midtrans_server_key')
                                            ->label('Server Key')
                                            ->required()
                                            ->password()
                                            ->disabled(config('app.demo'))
                                            ->helperText('Your Midtrans Server Key from dashboard')
                                            ->columnSpanFull(),

                                        TextInput::make('midtrans_client_key')
                                            ->label('Client Key')
                                            ->required()
                                            ->password()
                                            ->disabled(config('app.demo'))
                                            ->helperText('Your Midtrans Client Key from dashboard')
                                            ->columnSpanFull(),

                                        Toggle::make('midtrans_is_production')
                                            ->label('Production Mode')
                                            ->disabled(config('app.demo'))
                                            ->helperText('Enable this for production environment')
                                            ->inline(false),

                                        Toggle::make('midtrans_is_sanitized')
                                            ->label('Sanitize Input')
                                            ->disabled(config('app.demo'))
                                            ->helperText('Enable input sanitization for security')
                                            ->default(true)
                                            ->inline(false),

                                        Toggle::make('midtrans_is_3ds')
                                            ->label('Enable 3D Secure')
                                            ->disabled(config('app.demo'))
                                            ->helperText('Enable 3D Secure authentication')
                                            ->default(true)
                                            ->inline(false),
                                    ])
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Email Configuration')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                Section::make('Email Notification Settings')
                                    ->description('Enable or disable email notifications')
                                    ->schema([
                                        Toggle::make('email_notifications_enabled')
                                            ->label('Enable Email Notifications')
                                            ->helperText('Turn on to send email notifications to customers')
                                            ->live()
                                            ->disabled(config('app.demo'))
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Notification Events')
                                    ->description('Choose when to send email notifications')
                                    ->schema([
                                        Toggle::make('email_on_order_created')
                                            ->label('Order Created')
                                            ->disabled(config('app.demo'))
                                            ->helperText('Send email when customer creates a new order')
                                            ->inline(false),

                                        Toggle::make('email_on_order_paid')
                                            ->label('Order Paid')
                                            ->disabled(config('app.demo'))
                                            ->helperText('Send email when payment is confirmed')
                                            ->inline(false),

                                        Toggle::make('email_on_order_approved')
                                            ->label('Order Approved')
                                            ->disabled(config('app.demo'))
                                            ->helperText('Send email when admin approves the order')
                                            ->inline(false),

                                        Toggle::make('email_on_order_processing')
                                            ->label('Order Processing')
                                            ->disabled(config('app.demo'))
                                            ->helperText('Send email when order is being processed/packed')
                                            ->inline(false),

                                        Toggle::make('email_on_order_shipped')
                                            ->label('Order Shipped')
                                            ->disabled(config('app.demo'))
                                            ->helperText('Send email when order is shipped')
                                            ->inline(false),

                                        Toggle::make('email_on_order_delivered')
                                            ->label('Order Delivered')
                                            ->disabled(config('app.demo'))
                                            ->helperText('Send email when order is delivered')
                                            ->inline(false),

                                        Toggle::make('email_on_order_cancelled')
                                            ->label('Order Cancelled')
                                            ->disabled(config('app.demo'))
                                            ->helperText('Send email when order is cancelled')
                                            ->inline(false),
                                    ])
                                    ->columns(2)
                                    ->visible(fn(Get $get) => $get('email_notifications_enabled')),

                                Section::make('SMTP Settings')
                                    ->description('Configure email server settings')
                                    ->schema([
                                        TextInput::make('mail_host')
                                            ->label('SMTP Host')
                                            ->required()
                                            ->disabled(config('app.demo'))
                                            ->placeholder('smtp.gmail.com')
                                            ->helperText('Your SMTP server hostname'),

                                        TextInput::make('mail_port')
                                            ->label('SMTP Port')
                                            ->required()
                                            ->numeric()
                                            ->disabled(config('app.demo'))
                                            ->default('587')
                                            ->helperText('Usually 587 for TLS or 465 for SSL'),

                                        TextInput::make('mail_username')
                                            ->label('SMTP Username')
                                            ->password()
                                            ->required()
                                            ->disabled(config('app.demo'))
                                            ->placeholder('your-email@gmail.com')
                                            ->helperText('Your email address'),

                                        TextInput::make('mail_password')
                                            ->label('SMTP Password')
                                            ->required()
                                            ->password()
                                            ->disabled(config('app.demo'))
                                            ->helperText('Your email password or app password'),

                                        TextInput::make('mail_encryption')
                                            ->label('Encryption')
                                            ->required()
                                            ->disabled(config('app.demo'))
                                            ->default('tls')
                                            ->placeholder('tls')
                                            ->helperText('TLS or SSL'),

                                        TextInput::make('mail_from_address')
                                            ->label('From Email Address')
                                            ->required()
                                            ->email()
                                            ->disabled(config('app.demo'))
                                            ->placeholder('noreply@yourstore.com')
                                            ->helperText('Email address that will appear as sender'),

                                        TextInput::make('mail_from_name')
                                            ->label('From Name')
                                            ->required()
                                            ->disabled(config('app.demo'))
                                            ->placeholder('Your Store Name')
                                            ->helperText('Name that will appear as sender')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->visible(fn(Get $get) => $get('email_notifications_enabled')),
                            ]),
                    ])
                    ->columnSpanFull()
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save Midtrans settings
        Setting::set('midtrans_merchant_id', $data['midtrans_merchant_id'], 'text', 'midtrans');
        Setting::set('midtrans_server_key', $data['midtrans_server_key'], 'password', 'midtrans');
        Setting::set('midtrans_client_key', $data['midtrans_client_key'], 'text', 'midtrans');
        Setting::set('midtrans_is_production', $data['midtrans_is_production'] ? 'true' : 'false', 'boolean', 'midtrans');
        Setting::set('midtrans_is_sanitized', $data['midtrans_is_sanitized'] ? 'true' : 'false', 'boolean', 'midtrans');
        Setting::set('midtrans_is_3ds', $data['midtrans_is_3ds'] ? 'true' : 'false', 'boolean', 'midtrans');

        // Save Email Notification settings
        Setting::set('email_notifications_enabled', $data['email_notifications_enabled'] ? 'true' : 'false', 'boolean', 'email');
        Setting::set('email_on_order_created', $data['email_on_order_created'] ? 'true' : 'false', 'boolean', 'email');
        Setting::set('email_on_order_paid', $data['email_on_order_paid'] ? 'true' : 'false', 'boolean', 'email');
        Setting::set('email_on_order_approved', $data['email_on_order_approved'] ? 'true' : 'false', 'boolean', 'email');
        Setting::set('email_on_order_processing', $data['email_on_order_processing'] ? 'true' : 'false', 'boolean', 'email');
        Setting::set('email_on_order_shipped', $data['email_on_order_shipped'] ? 'true' : 'false', 'boolean', 'email');
        Setting::set('email_on_order_delivered', $data['email_on_order_delivered'] ? 'true' : 'false', 'boolean', 'email');
        Setting::set('email_on_order_cancelled', $data['email_on_order_cancelled'] ? 'true' : 'false', 'boolean', 'email');

        // Save SMTP settings (only if email notifications enabled)
        if ($data['email_notifications_enabled']) {
            Setting::set('mail_host', $data['mail_host'], 'text', 'smtp');
            Setting::set('mail_port', $data['mail_port'], 'number', 'smtp');
            Setting::set('mail_username', $data['mail_username'], 'text', 'smtp');
            Setting::set('mail_password', $data['mail_password'], 'password', 'smtp');
            Setting::set('mail_encryption', $data['mail_encryption'], 'text', 'smtp');
            Setting::set('mail_from_address', $data['mail_from_address'], 'text', 'smtp');
            Setting::set('mail_from_name', $data['mail_from_name'], 'text', 'smtp');
        }

        // Clear cache
        Setting::clearCache();

        // Update runtime config
        $this->updateRuntimeConfig($data);

        Notification::make()
            ->success()
            ->title('Settings saved successfully')
            ->body('All settings have been saved and applied.')
            ->send();
    }

    protected function updateRuntimeConfig(array $data): void
    {
        // Update Midtrans config at runtime
        config([
            'midtrans.merchant_id' => $data['midtrans_merchant_id'],
            'midtrans.server_key' => $data['midtrans_server_key'],
            'midtrans.client_key' => $data['midtrans_client_key'],
            'midtrans.is_production' => $data['midtrans_is_production'],
            'midtrans.is_sanitized' => $data['midtrans_is_sanitized'],
            'midtrans.is_3ds' => $data['midtrans_is_3ds'],
        ]);

        // Update mail config at runtime
        config([
            'mail.mailers.smtp.host' => $data['mail_host'],
            'mail.mailers.smtp.port' => $data['mail_port'],
            'mail.mailers.smtp.username' => $data['mail_username'],
            'mail.mailers.smtp.password' => $data['mail_password'],
            'mail.mailers.smtp.encryption' => $data['mail_encryption'],
            'mail.from.address' => $data['mail_from_address'],
            'mail.from.name' => $data['mail_from_name'],
        ]);
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label(config('app.demo') ? 'Demo Mode - Save Disabled' : 'Save Changes')
                ->disabled(config('app.demo'))
                ->submit('save'),
        ];
    }
}
