<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = '客戶管理';
    
    protected static ?string $modelLabel = '客戶';
    
    protected static ?string $pluralModelLabel = '客戶';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本資料')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('姓名')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('電話')
                            ->required()
                            ->tel()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('電子郵件')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->label('地址')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birthday')
                            ->label('生日')
                            ->format('Y-m-d'),
                    ]),
                Forms\Components\Section::make('會員資料')
                    ->schema([
                        Forms\Components\Toggle::make('is_member')
                            ->label('是否為會員')
                            ->default(false)
                            ->reactive(),
                        Forms\Components\DatePicker::make('member_since')
                            ->label('成為會員日期')
                            ->format('Y-m-d')
                            ->visible(fn (Forms\Get $get) => $get('is_member')),
                        Forms\Components\Textarea::make('notes')
                            ->label('備註')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('姓名')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('電話')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('電子郵件')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_member')
                    ->label('會員')
                    ->boolean(),
                Tables\Columns\TextColumn::make('member_since')
                    ->label('成為會員日期')
                    ->date('Y-m-d'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立日期')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新日期')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_member')
                    ->label('僅顯示會員')
                    ->query(fn (Builder $query): Builder => $query->where('is_member', true)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
} 