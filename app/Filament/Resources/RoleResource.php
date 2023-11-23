<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Pages\ViewUser;
use Filament\Tables;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Infolists\Components\TextEntry;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RoleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Filament\Resources\RoleResource\RelationManagers\PermissionsRelationManager;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup="Users Management";
    protected static ?int $navigationSort=2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 self::getNameField($form->getOperation()),
                // Forms\Components\TextInput::make('name')->required()->unique()->alphaDash(),
             
                Forms\Components\Select::make('permissions')->multiple()->preload()->relationship('permissions', 'name')
              
            ]);
    }
    public static function getNameField(string $operation)
    {
        if ($operation === 'create') {
            return Forms\Components\TextInput::make('name')->required()->unique()->alphaDash();
        } else {
            return Forms\Components\TextInput::make('name')->extraInputAttributes(['readonly' => true]); // A completely different definition
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // public static function getRelations(): array
    // {
    //     return [
    //         PermissionsRelationManager::class,
    //     ];
    // }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
          
        ];
    }
    
}
