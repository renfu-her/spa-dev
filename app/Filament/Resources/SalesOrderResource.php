<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesOrderResource\Pages;
use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\Product;
use App\Models\TreatmentPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesOrderResource extends Resource
{
    protected static ?string $model = SalesOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = '銷售管理';

    protected static ?string $navigationLabel = '銷售訂單';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('訂單資訊')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('訂單編號')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->default(fn() => 'SO-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT)),
                        Forms\Components\Select::make('customer_id')
                            ->label('客戶')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('姓名')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->label('電話')
                                    ->required()
                                    ->tel()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->label('電子郵件')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('address')
                                    ->label('地址')
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('birthday')
                                    ->label('生日'),
                                Forms\Components\Toggle::make('is_member')
                                    ->label('會員')
                                    ->default(false),
                            ])
                            ->required(),
                        Forms\Components\Select::make('staff_id')
                            ->label('銷售人員')
                            ->relationship('staff', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\DatePicker::make('order_date')
                            ->label('訂單日期')
                            ->required()
                            ->default(now()),
                    ])->columns(2),

                Forms\Components\Section::make('訂單項目')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('訂單項目')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('產品')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            if ($product) {
                                                $set('unit_price', $product->regular_price);
                                                $set('subtotal', $product->regular_price * $get('quantity'));
                                            }
                                        }
                                    }),
                                Forms\Components\Select::make('package_id')
                                    ->label('療程套裝')
                                    ->relationship('package', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state) {
                                            $package = TreatmentPackage::find($state);
                                            if ($package) {
                                                $set('unit_price', $package->price);
                                                $set('subtotal', $package->price * $get('quantity'));
                                            }
                                        }
                                    }),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('數量')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $unitPrice = $get('unit_price') ?? 0;
                                        $discount = $get('discount') ?? 0;
                                        $subtotal = $unitPrice * $state - $discount;
                                        $set('subtotal', $subtotal);
                                    }),
                                Forms\Components\TextInput::make('unit_price')
                                    ->label('單價')
                                    ->required()
                                    ->numeric()
                                    ->prefix('NT$')
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $quantity = $get('quantity') ?? 1;
                                        $discount = $get('discount') ?? 0;
                                        $subtotal = $state * $quantity - $discount;
                                        $set('subtotal', $subtotal);
                                    }),
                                Forms\Components\TextInput::make('discount')
                                    ->label('折扣')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('NT$')
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $unitPrice = $get('unit_price') ?? 0;
                                        $quantity = $get('quantity') ?? 1;
                                        $subtotal = $unitPrice * $quantity - $state;
                                        $set('subtotal', $subtotal);
                                    }),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('小計')
                                    ->required()
                                    ->numeric()
                                    ->prefix('NT$')
                                    ->disabled(),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(
                                fn(array $state): ?string =>
                                $state['product_id']
                                    ? Product::find($state['product_id'])?->name
                                    : (
                                        $state['package_id']
                                        ? TreatmentPackage::find($state['package_id'])?->name
                                        : null
                                    )
                            ),
                    ]),

                Forms\Components\Section::make('付款資訊')
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->label('總金額')
                            ->required()
                            ->numeric()
                            ->prefix('NT$')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('discount_amount')
                            ->label('折扣金額')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('NT$')
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                $totalAmount = $get('total_amount') ?? 0;
                                $finalAmount = $totalAmount - $state;
                                $set('final_amount', $finalAmount);
                            }),
                        Forms\Components\TextInput::make('final_amount')
                            ->label('最終金額')
                            ->required()
                            ->numeric()
                            ->prefix('NT$')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\Select::make('payment_method')
                            ->label('付款方式')
                            ->options([
                                'cash' => '現金',
                                'credit_card' => '信用卡',
                                'mobile_payment' => '行動支付',
                                'mixed' => '混合支付',
                            ])
                            ->required()
                            ->reactive(),
                        Forms\Components\Select::make('payment_status')
                            ->label('付款狀態')
                            ->options([
                                'pending' => '待付款',
                                'partial' => '部分付款',
                                'paid' => '已付款',
                                'refunded' => '已退款',
                            ])
                            ->required()
                            ->default('pending'),
                        Forms\Components\TextInput::make('credit_card_last_digits')
                            ->label('信用卡末四碼')
                            ->maxLength(4)
                            ->visible(fn(Forms\Get $get) => $get('payment_method') === 'credit_card' || $get('payment_method') === 'mixed'),
                        Forms\Components\TextInput::make('credit_card_type')
                            ->label('信用卡類型')
                            ->maxLength(255)
                            ->visible(fn(Forms\Get $get) => $get('payment_method') === 'credit_card' || $get('payment_method') === 'mixed'),
                        Forms\Components\TextInput::make('installment_months')
                            ->label('分期月數')
                            ->numeric()
                            ->visible(fn(Forms\Get $get) => $get('payment_method') === 'credit_card' || $get('payment_method') === 'mixed'),
                        Forms\Components\Select::make('mobile_payment_provider')
                            ->label('行動支付提供商')
                            ->options([
                                'line_pay' => 'Line Pay',
                                'apple_pay' => 'Apple Pay',
                                'google_pay' => 'Google Pay',
                                'jko_pay' => 'JKO Pay',
                                'taiwan_pay' => '台灣 Pay',
                                'other' => '其他',
                            ])
                            ->visible(fn(Forms\Get $get) => $get('payment_method') === 'mobile_payment' || $get('payment_method') === 'mixed'),
                        Forms\Components\TextInput::make('mobile_payment_reference')
                            ->label('行動支付參考號')
                            ->maxLength(255)
                            ->visible(fn(Forms\Get $get) => $get('payment_method') === 'mobile_payment' || $get('payment_method') === 'mixed'),
                        Forms\Components\Textarea::make('notes')
                            ->label('備註')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('訂單編號')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('客戶')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('staff.name')
                    ->label('銷售人員')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_date')
                    ->label('訂單日期')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('總金額')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_amount')
                    ->label('折扣金額')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('final_amount')
                    ->label('最終金額')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('付款方式')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'cash' => '現金',
                        'credit_card' => '信用卡',
                        'mobile_payment' => '行動支付',
                        'mixed' => '混合支付',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'cash',
                        'success' => 'credit_card',
                        'warning' => 'mobile_payment',
                        'danger' => 'mixed',
                    ]),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('付款狀態')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => '待付款',
                        'partial' => '部分付款',
                        'paid' => '已付款',
                        'refunded' => '已退款',
                        default => $state,
                    })
                    ->colors([
                        'danger' => 'pending',
                        'warning' => 'partial',
                        'success' => 'paid',
                        'gray' => 'refunded',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新時間')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('付款方式')
                    ->options([
                        'cash' => '現金',
                        'credit_card' => '信用卡',
                        'mobile_payment' => '行動支付',
                        'mixed' => '混合支付',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('付款狀態')
                    ->options([
                        'pending' => '待付款',
                        'partial' => '部分付款',
                        'paid' => '已付款',
                        'refunded' => '已退款',
                    ]),
                Tables\Filters\Filter::make('order_date')
                    ->form([
                        Forms\Components\DatePicker::make('order_date_from')
                            ->label('訂單日期從'),
                        Forms\Components\DatePicker::make('order_date_until')
                            ->label('訂單日期至'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['order_date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('order_date', '>=', $date),
                            )
                            ->when(
                                $data['order_date_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('order_date', '<=', $date),
                            );
                    }),
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
            'index' => Pages\ListSalesOrders::route('/'),
            'create' => Pages\CreateSalesOrder::route('/create'),
            'edit' => Pages\EditSalesOrder::route('/{record}/edit'),
        ];
    }
}
