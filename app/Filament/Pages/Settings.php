<?php

namespace App\Filament\Pages;

use App\Models\Settings as SettingsModel;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.settings';

    protected static ?string $title = 'Application Settings';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?int $navigationSort = 999;

    public ?array $data = [];
    
    public $app_name = '';
    public $app_logo = null;
    public $favicon = null;
    public $footer_text = '';

    public function mount(): void
    {
        $settings = SettingsModel::getInstance();
        $this->app_name = $settings->app_name ?? 'Visit Logger';
        $this->app_logo = $settings->app_logo;
        $this->favicon = $settings->favicon;
        $this->footer_text = $settings->footer_text ?? 'Powered by Visit Logger';
        
        $this->form->fill([
            'app_name' => $this->app_name,
            'app_logo' => $this->app_logo,
            'favicon' => $this->favicon,
            'footer_text' => $this->footer_text,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('app_name')
                ->label('Application Name')
                ->required()
                ->maxLength(255)
                ->default('Visit Logger')
                ->helperText('The name displayed in the application header'),

            FileUpload::make('app_logo')
                ->label('Application Logo')
                ->image()
                ->disk('public')
                ->directory('settings')
                ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg', 'image/svg+xml'])
                ->maxSize(2048)
                ->helperText('Upload a logo for your application (PNG, JPG, SVG - Max 2MB)'),

            FileUpload::make('favicon')
                ->label('Favicon')
                ->image()
                ->disk('public')
                ->directory('settings')
                ->acceptedFileTypes(['image/png', 'image/ico', 'image/x-icon'])
                ->maxSize(512)
                ->helperText('Small icon shown in browser tab (ICO, PNG - Max 512KB)'),

            Textarea::make('footer_text')
                ->label('Footer Text')
                ->rows(3)
                ->maxLength(500)
                ->placeholder('Enter custom footer text...')
                ->helperText('Text displayed in the application footer'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            
            $settings = SettingsModel::getInstance();
            $settings->fill($data);
            $settings->save();

            Notification::make()
                ->title('Settings saved successfully!')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving settings')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->action('save')
                ->color('primary')
                ->icon('heroicon-m-check'),
        ];
    }
}
