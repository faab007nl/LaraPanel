<?php

namespace App\Filament\Sites\Resources;

use App\Enums\SiteType;
use App\Filament\CustomFields\TypeSelectField;
use App\Filament\Sites\Resources\SiteResource\Pages;
use App\Models\Site;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    public static function form(Form $form): Form
    {
        // TODO: Implement app installer
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Type')
                        ->schema([
                            TypeSelectField::make('type')
                                ->label('Site Type')
                                ->options(SiteType::getOptions())
                                ->icons(SiteType::getIcons())
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (mixed $state, Set $set) {
                                    $set('type', $state);
                                }),
                        ]),
                    Wizard\Step::make('Domain')
                        ->schema([
                            TextInput::make('domain')
                                ->label('Domain')
                                ->required()
                                ->regex('/^(?!:\/\/)([a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+\.[a-zA-Z]{2,6}$/')
                                ->unique(ignoreRecord: true)
                                ->validationMessages([
                                    'regex' => 'The domain field must be a valid domain name (e.g., example.com).',
                                    'unique' => ':attribute is already taken.',
                                ]),
                        ]),


                    Wizard\Step::make('PHP Configuration')
                        ->schema([
                            Grid::make()
                                ->columns()
                                ->schema([
                                    Select::make('php_version')
                                        ->label('PHP Version')
                                        ->options([
                                            '8.1' => 'PHP 8.1',
                                            '8.2' => 'PHP 8.2',
                                            '8.3' => 'PHP 8.3',
                                            '8.4' => 'PHP 8.4',
                                        ])
                                        ->default('8.4')
                                        ->required(),
                                ]),
                            Grid::make()
                                ->columns()
                                ->schema([
                                    Select::make('php_memory_limit')
                                        ->label('PHP Memory Limit')
                                        ->options([
                                            '64' => "64 MB",
                                            '128' => "128 MB",
                                            '256' => "256 MB",
                                            '512' => "512 MB",
                                            '1024' => "1 GB",
                                            '2048' => "2 GB",
                                            '4096' => "4 GB",
                                            '5120' => "5 GB",
                                        ])
                                        ->default('512')
                                        ->required(),
                                    Select::make('max_execution_time')
                                        ->label('Max Execution Time')
                                        ->options([
                                            '15' => "15 seconds",
                                            '30' => "30 seconds",
                                            '60' => "1 minute",
                                            '120' => "2 minutes",
                                            '180' => "3 minutes",
                                            '300' => "5 minutes",
                                            '600' => "10 minutes",
                                            '900' => "15 minutes",
                                            '1800' => "30 minutes",
                                            '3600' => "1 hour",
                                        ])
                                        ->default('60')
                                        ->required(),
                                ]),
                            Grid::make()
                                ->columns()
                                ->schema([
                                    Select::make('max_input_time')
                                        ->label('Max Input Time')
                                        ->options([
                                            '15' => "15 seconds",
                                            '30' => "30 seconds",
                                            '60' => "1 minute",
                                            '120' => "2 minutes",
                                            '180' => "3 minutes",
                                            '300' => "5 minutes",
                                            '600' => "10 minutes",
                                            '900' => "15 minutes",
                                            '1800' => "30 minutes",
                                            '3600' => "1 hour",
                                        ])
                                        ->default('60')
                                        ->required(),
                                    Select::make('max_input_vars')
                                        ->label('Max Input Vars')
                                        ->options([
                                            '1000' => "1.000",
                                            '2000' => "2.000",
                                            '5000' => "5.000",
                                            '10000' => "10.000",
                                            '20000' => "20.000",
                                            '50000' => "50.000",
                                            '100000' => "100.000",
                                        ])
                                        ->default('10000')
                                        ->required(),
                                ]),
                            Grid::make()
                                ->columns()
                                ->schema([
                                    Select::make('max_post_size')
                                        ->label('Max Post Size')
                                        ->options([
                                            '2' => "2 MB",
                                            '4' => "4 MB",
                                            '8' => "8 MB",
                                            '16' => "16 MB",
                                            '32' => "32 MB",
                                            '64' => "64 MB",
                                            '128' => "128 MB",
                                            '256' => "256 MB",
                                            '512' => "512 MB",
                                            '1024' => "1 GB",
                                            '2048' => "2 GB",
                                            '4096' => "4 GB",
                                            '5120' => "5 GB",
                                        ])
                                        ->default('64')
                                        ->required(),
                                    Select::make('upload_max_filesize')
                                        ->label('Max Upload Size')
                                        ->options([
                                            '2' => "2 MB",
                                            '4' => "4 MB",
                                            '8' => "8 MB",
                                            '16' => "16 MB",
                                            '32' => "32 MB",
                                            '64' => "64 MB",
                                            '128' => "128 MB",
                                            '256' => "256 MB",
                                            '512' => "512 MB",
                                            '1024' => "1 GB",
                                            '2048' => "2 GB",
                                            '4096' => "4 GB",
                                            '5120' => "5 GB",
                                        ])
                                        ->default('64')
                                        ->required(),
                                ]),
                            Grid::make()
                                ->columns(1)
                                ->schema([
                                    Textarea::make('additional_php_config')
                                        ->label('Additional PHP Configuration')
                                        ->placeholder('Enter any additional PHP configuration settings here...')
                                        ->rows(4)
                                        ->default(''),
                                ]),
                        ])
                        ->visible(
                            fn (Get $get): bool => $get('type') === SiteType::PHP->value
                        ),
                    Wizard\Step::make('NodeJS Configuration')
                        ->schema([
                            Grid::make()
                                ->columns()
                                ->schema([
                                    Select::make('nodejs_version')
                                        ->label('NodeJS Version')
                                        ->options([
                                            '12' => 'NodeJS 12',
                                            '14' => 'NodeJS 14',
                                            '16' => 'NodeJS 16',
                                            '18' => 'NodeJS 18',
                                            '20' => 'NodeJS 20',
                                            '22' => 'NodeJS 22',
                                        ])
                                        ->default('22')
                                        ->required(),
                                    TextInput::make('app_port')
                                        ->label('App Port')
                                        ->numeric()
                                        ->default(3000)
                                        ->required(),
                                ]),
                        ])
                        ->visible(
                            fn (Get $get): bool => $get('type') === SiteType::NodeJS->value
                        ),
                    Wizard\Step::make('Proxy Configuration')
                        ->schema([
                            TextInput::make('reverse_proxy_url')
                                ->label('Reverse Proxy Url')
                                ->url()
                                ->default("http://127.0.0.1:8000")
                                ->required(),
                        ])
                        ->visible(
                            fn (Get $get): bool => $get('type') === SiteType::ReverseProxy->value
                        ),

                    Wizard\Step::make('Additional Configuration')
                        ->schema([
                            // group, tags, description
                            Grid::make()
                                ->columns(2)
                                ->schema([
                                    Select::make('group')
                                        // TODO: Implement groups table. User unique
//                                        ->relationship(name: 'group', titleAttribute: 'name')
                                        ->createOptionForm([
                                            TextInput::make('name')
                                                ->required(),
                                        ]),
                                    TagsInput::make('tags')
                                        ->label("Tags"),
                                ]),
                            Grid::make()
                                ->columns(1)
                                ->schema([
                                    RichEditor::make('description')
                                        ->label("Site Description"),
                                ]),
                        ]),
                ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                    <x-filament::button
                        size="sm"
                        color="gray"
                        wire:click="cancelCustom"
                    >
                        Cancel
                    </x-filament::button>
                    <x-filament::button
                        size="sm"
                        color="gray"
                        wire:click="create"
                    >
                        Create & create another
                    </x-filament::button>
                    <x-filament::button
                        size="sm"
                        wire:click="create"
                    >
                        Create
                    </x-filament::button>
                BLADE)))
            ])->columns('full');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSites::route('/'),
            'create' => Pages\CreateSite::route('/create'),
            'edit' => Pages\EditSite::route('/{record}/edit'),
        ];
    }

}
