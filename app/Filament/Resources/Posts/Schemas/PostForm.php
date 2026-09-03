<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->required()
                    ->maxLength(255),

                // Lo generado era TextInput::make('categoria_id')->numeric():
                // Filament vio un entero en la tabla, no una relacion.
                Select::make('categoria_id')
                    ->label('Categoría')
                    ->relationship('categoria', 'nombre')
                    ->required(),

                Textarea::make('contenido')
                    ->required()
                    ->columnSpanFull(),

                // Nivel 1 del ejercicio (requiere la migracion de la columna)
                TextInput::make('resumen')
                    ->maxLength(160)
                    ->columnSpanFull(),

                Toggle::make('publicado')
                    ->default(true),
            ]);
    }
}
