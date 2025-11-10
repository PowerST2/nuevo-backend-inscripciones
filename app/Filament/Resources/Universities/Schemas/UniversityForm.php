<?php

namespace App\Filament\Resources\Universities\Schemas;

use App\Models\Country;
use App\Models\Ubigeo;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class UniversityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('management')
                    ->required(),
                Select::make('country_id')
                    ->relationship('country', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (callable $set) {
                        $set('department', null);
                        $set('province', null);
                        $set('district', null);
                        $set('ubigeo_id', null);
                    }),
                Select::make('department')
                    ->label('Departamento')
                    ->placeholder('Selecciona un departamento')
                    ->options(function () {
                        return Ubigeo::distinct()
                            ->pluck('department', 'department')
                            ->toArray();
                    })
                    ->disabled(fn (Get $get) => !self::isPeru($get))
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('province', null)),
                Select::make('province')
                    ->label('Provincia')
                    ->placeholder('Selecciona una provincia')
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
                    ->disabled(fn (Get $get) => !$get('department') || !self::isPeru($get))
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('district', null)),
                Select::make('district')
                    ->label('Distrito')
                    ->placeholder('Selecciona un distrito')
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
                Toggle::make('active')
                    ->default(true),
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
