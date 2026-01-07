<?php

namespace App\Filament\Resources\Schools\Schemas;

use App\Models\Country;
use App\Models\Ubigeo;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('modular_code')
                    ->label(__('filament.labels.modular_code'))
                    ->required(),
                TextInput::make('annexed')
                    ->label(__('filament.labels.annexed'))
                    ->numeric(),
                TextInput::make('level')
                    ->label(__('filament.labels.level')),
                TextInput::make('name')
                    ->label(__('filament.labels.name')),
                TextInput::make('management_minedu')
                    ->label(__('filament.labels.management_minedu')),
                Select::make('management')
                    ->label(__('filament.labels.management'))
                    ->options([
                        'Privada' => 'Privada',
                        'Pública' => 'Pública',
                    ])
                    ->required(),
                TextInput::make('director')
                    ->label(__('filament.labels.director')),
                TextInput::make('address')
                    ->label(__('filament.labels.address')),
                TextInput::make('phones')
                    ->label(__('filament.labels.phones'))
                    ->tel(),
                TextInput::make('email')
                    ->label(__('filament.labels.email_address'))
                    ->email(),
                Select::make('country_id')
                    ->label(__('filament.labels.country_id'))
                    ->relationship('country', 'name')
                    ->required()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (callable $set) {
                        $set('department', null);
                        $set('province', null);
                        $set('district', null);
                        $set('ubigeo_id', null);
                    }),
                Select::make('department')
                    ->label(__('filament.labels.department'))
                    ->placeholder(__('filament.labels.select_department'))
                    ->options(function () {
                        return Ubigeo::distinct()
                            ->pluck('department', 'department')
                            ->toArray();
                    })
                    ->searchable()
                    ->disabled(fn (Get $get) => !self::isPeru($get))
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('province', null)),
                Select::make('province')
                    ->label(__('filament.labels.province'))
                    ->placeholder(__('filament.labels.select_province'))
                    ->options(function (Get $get) {
                        $department = $get('department');
                        if (!$department) {
                            return [];
                        }
                        return Ubigeo::where('department', $department)
                            ->distinct()
                            ->pluck('province', 'province')
                            ->toArray();
                    })
                    ->searchable()
                    ->disabled(fn (Get $get) => !$get('department') || !self::isPeru($get))
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('district', null)),
                Select::make('district')
                    ->label(__('filament.labels.district'))
                    ->placeholder(__('filament.labels.select_district'))
                    ->options(function (Get $get) {
                        $department = $get('department');
                        $province = $get('province');
                        if (!$department || !$province) {
                            return [];
                        }
                        return Ubigeo::where('department', $department)
                            ->where('province', $province)
                            ->distinct()
                            ->pluck('district', 'district')
                            ->toArray();
                    })
                    ->searchable()
                    ->disabled(fn (Get $get) => !$get('province') || !self::isPeru($get))
                    ->live()
                    ->afterStateUpdated(function (callable $set, Get $get) {
                        $department = $get('department');
                        $province = $get('province');
                        $district = $get('district');
                        
                        if ($department && $province && $district) {
                            $ubigeo = Ubigeo::where('department', $department)
                                ->where('province', $province)
                                ->where('district', $district)
                                ->first();
                            
                            if ($ubigeo) {
                                $set('ubigeo_id', $ubigeo->id);
                            }
                        }
                    }),
                Hidden::make('ubigeo_id'),
            ]);
    }

    private static function isPeru(Get $get): bool
    {
        $countryId = $get('country_id');
        if (!$countryId) {
            return false;
        }
        
        $country = Country::find($countryId);
        return $country?->name === 'Perú';
    }
}
