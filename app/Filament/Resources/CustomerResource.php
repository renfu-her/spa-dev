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
                Forms\Components\TextInput::make('customer_code')
                    ->label('客戶編號')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn($record) => $record !== null),
                Forms\Components\TextInput::make('name')
                    ->label('姓名')
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->label('電話')
                    ->tel()
                    ->required()
                    ->validationMessages([
                        'required' => '請輸入電話號碼',
                    ]),
                Forms\Components\TextInput::make('email')
                    ->label('電子郵件')
                    ->email()
                    ->required()
                    ->unique(
                        table: 'customers',
                        column: 'email',
                        ignoreRecord: true,
                    )
                    ->validationMessages([
                        'required' => '請輸入電子郵件',
                        'email' => '請輸入有效的電子郵件格式',
                        'unique' => '此電子郵件已經被使用',
                    ]),
                Forms\Components\Textarea::make('address')
                    ->label('地址')
                    ->rows(3),
                Forms\Components\DatePicker::make('birthday')
                    ->label('生日'),
                Forms\Components\Toggle::make('is_member')
                    ->label('是否為會員')
                    ->reactive(),
                Forms\Components\DatePicker::make('member_since')
                    ->label('入會日期')
                    ->visible(fn(Forms\Get $get) => $get('is_member'))
                    ->required(fn(Forms\Get $get) => $get('is_member'))
                    ->default(fn() => \Carbon\Carbon::now()->toDateString())
                    ->maxDate(fn() => \Carbon\Carbon::now()),
                Forms\Components\Textarea::make('notes')
                    ->label('備註')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_code')
                    ->label('客戶編號')
                    ->searchable()
                    ->sortable(),
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
                    ->query(fn(Builder $query): Builder => $query->where('is_member', true)),
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
