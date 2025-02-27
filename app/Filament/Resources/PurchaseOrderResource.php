<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Models\PurchaseOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseOrderExport;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationGroup = '採購管理';
    
    protected static ?string $navigationLabel = '採購單';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('採購單資訊')
                    ->schema([
                        Forms\Components\TextInput::make('purchase_number')
                            ->label('採購單號')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->default(fn() => 'PO-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT)),
                        Forms\Components\Select::make('supplier_id')
                            ->label('供應商')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('staff_id')
                            ->label('採購人員')
                            ->relationship('staff', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\DatePicker::make('purchase_date')
                            ->label('採購日期')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('status')
                            ->label('狀態')
                            ->options([
                                'draft' => '草稿',
                                'pending' => '待處理',
                                'approved' => '已核准',
                                'ordered' => '已下單',
                                'received' => '已收貨',
                                'cancelled' => '已取消',
                            ])
                            ->default('draft')
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('採購項目')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('採購項目')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('產品')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('數量')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn(Forms\Set $set, Forms\Get $get) => 
                                        $set('subtotal', $get('quantity') * $get('unit_price'))),
                                Forms\Components\TextInput::make('unit_price')
                                    ->label('單價')
                                    ->numeric()
                                    ->prefix('NT$')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn(Forms\Set $set, Forms\Get $get) => 
                                        $set('subtotal', $get('quantity') * $get('unit_price'))),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('小計')
                                    ->numeric()
                                    ->prefix('NT$')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->reorderable(false)
                            ->collapsible()
                            ->cloneable(),
                    ]),
                
                Forms\Components\Section::make('金額資訊')
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->label('總金額')
                            ->numeric()
                            ->prefix('NT$')
                            ->disabled()
                            ->dehydrated()
                            ->default(0),
                        Forms\Components\TextInput::make('tax_amount')
                            ->label('稅額')
                            ->numeric()
                            ->prefix('NT$')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn(Forms\Set $set, Forms\Get $get) => 
                                $set('final_amount', $get('total_amount') + $get('tax_amount') - $get('discount_amount'))),
                        Forms\Components\TextInput::make('discount_amount')
                            ->label('折扣金額')
                            ->numeric()
                            ->prefix('NT$')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn(Forms\Set $set, Forms\Get $get) => 
                                $set('final_amount', $get('total_amount') + $get('tax_amount') - $get('discount_amount'))),
                        Forms\Components\TextInput::make('final_amount')
                            ->label('最終金額')
                            ->numeric()
                            ->prefix('NT$')
                            ->disabled()
                            ->dehydrated()
                            ->default(0),
                    ])->columns(2),
                
                Forms\Components\Section::make('其他資訊')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('備註')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('pdf_path')
                            ->label('PDF 路徑')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->visible(fn($record) => $record && $record->pdf_path),
                        Forms\Components\TextInput::make('excel_path')
                            ->label('Excel 路徑')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->visible(fn($record) => $record && $record->excel_path),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('purchase_number')
                    ->label('採購單號')
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('供應商')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('staff.name')
                    ->label('採購人員')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->label('採購日期')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('狀態')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => '草稿',
                        'pending' => '待處理',
                        'approved' => '已核准',
                        'ordered' => '已下單',
                        'received' => '已收貨',
                        'cancelled' => '已取消',
                        default => $state,
                    })
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'pending',
                        'success' => 'approved',
                        'primary' => 'ordered',
                        'info' => 'received',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('總金額')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('final_amount')
                    ->label('最終金額')
                    ->money('TWD')
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('狀態')
                    ->options([
                        'draft' => '草稿',
                        'pending' => '待處理',
                        'approved' => '已核准',
                        'ordered' => '已下單',
                        'received' => '已收貨',
                        'cancelled' => '已取消',
                    ]),
                Tables\Filters\Filter::make('purchase_date')
                    ->form([
                        Forms\Components\DatePicker::make('purchase_date_from')
                            ->label('採購日期從'),
                        Forms\Components\DatePicker::make('purchase_date_until')
                            ->label('採購日期至'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['purchase_date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('purchase_date', '>=', $date),
                            )
                            ->when(
                                $data['purchase_date_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('purchase_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('export_pdf')
                    ->label('匯出 PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (PurchaseOrder $record) {
                        // 生成 PDF
                        $pdf = PDF::loadView('pdf.purchase-order', ['purchaseOrder' => $record]);
                        
                        // 儲存 PDF
                        $pdfPath = 'purchase-orders/' . $record->purchase_number . '.pdf';
                        Storage::put('public/' . $pdfPath, $pdf->output());
                        
                        // 更新記錄
                        $record->update([
                            'pdf_path' => $pdfPath,
                            'last_exported_at' => now(),
                        ]);
                        
                        // 下載 PDF
                        return response()->download(storage_path('app/public/' . $pdfPath));
                    }),
                Tables\Actions\Action::make('export_excel')
                    ->label('匯出 Excel')
                    ->icon('heroicon-o-table-cells')
                    ->action(function (PurchaseOrder $record) {
                        // 生成 Excel
                        $excelPath = 'purchase-orders/' . $record->purchase_number . '.xlsx';
                        Excel::store(new PurchaseOrderExport($record), 'public/' . $excelPath);
                        
                        // 更新記錄
                        $record->update([
                            'excel_path' => $excelPath,
                            'last_exported_at' => now(),
                        ]);
                        
                        // 下載 Excel
                        return response()->download(storage_path('app/public/' . $excelPath));
                    }),
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
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
} 