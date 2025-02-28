<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = '採購管理';

    protected static ?string $navigationLabel = '供應商';

    protected static ?string $modelLabel = '供應商';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('供應商名稱')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_person')
                    ->label('聯絡人')
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('電話')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('電子郵件')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tax_id')
                    ->label('統一編號')
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->label('地址')
                    ->rows(3)
                    ->maxLength(255),
                Forms\Components\TextInput::make('payment_terms')
                    ->label('付款條件')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->label('是否啟用')
                    ->default(true),
                Forms\Components\Textarea::make('notes')
                    ->label('備註')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('供應商名稱')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_person')
                    ->label('聯絡人')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('電話')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('電子郵件')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tax_id')
                    ->label('統一編號')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('狀態')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('僅顯示啟用中的供應商')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->can('edit_suppliers')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->can('delete_suppliers')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_suppliers');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_suppliers');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('edit_suppliers');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('delete_suppliers');
    }
} 