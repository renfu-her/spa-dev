<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerTreatmentResource\Pages;
use App\Models\CustomerTreatment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerTreatmentResource extends Resource
{
    protected static ?string $model = CustomerTreatment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    
    protected static ?string $navigationLabel = '客戶療程記錄';
    
    protected static ?string $modelLabel = '客戶療程';
    
    protected static ?string $pluralModelLabel = '客戶療程';
    
    protected static ?string $navigationGroup = '客戶管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('療程資料')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label('客戶')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('package_id')
                            ->label('療程套餐')
                            ->relationship('package', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('sales_order_id')
                            ->label('銷售訂單')
                            ->relationship('salesOrder', 'order_number')
                            ->searchable()
                            ->preload(),
                    ]),
                Forms\Components\Section::make('有效期限與次數')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('開始日期')
                            ->required()
                            ->default(now())
                            ->format('Y-m-d'),
                        Forms\Components\DatePicker::make('expiry_date')
                            ->label('到期日期')
                            ->required()
                            ->format('Y-m-d'),
                        Forms\Components\TextInput::make('total_times')
                            ->label('總次數')
                            ->required()
                            ->numeric()
                            ->default(1),
                        Forms\Components\TextInput::make('remaining_times')
                            ->label('剩餘次數')
                            ->required()
                            ->numeric()
                            ->default(1),
                        Forms\Components\Toggle::make('is_active')
                            ->label('是否啟用')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('客戶')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('package.name')
                    ->label('療程套餐')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('開始日期')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('到期日期')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_times')
                    ->label('剩餘次數')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_times')
                    ->label('總次數')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('啟用狀態')
                    ->boolean(),
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
                Tables\Filters\SelectFilter::make('customer_id')
                    ->label('客戶')
                    ->relationship('customer', 'name'),
                Tables\Filters\Filter::make('is_active')
                    ->label('僅顯示啟用療程')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
                Tables\Filters\Filter::make('is_expired')
                    ->label('僅顯示已過期療程')
                    ->query(fn (Builder $query): Builder => $query->whereDate('expiry_date', '<', now())),
            ])
            ->actions([
                Tables\Actions\Action::make('record_usage')
                    ->label('記錄使用')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('treatment_item_id')
                            ->label('療程項目')
                            ->relationship('package.treatmentItems', 'name')
                            ->required(),
                        Forms\Components\Select::make('performed_by')
                            ->label('操作人員')
                            ->relationship('performedByStaff', 'name')
                            ->required(),
                        Forms\Components\DateTimePicker::make('performed_at')
                            ->label('操作時間')
                            ->required()
                            ->default(now()),
                        Forms\Components\Textarea::make('notes')
                            ->label('備註'),
                    ])
                    ->action(function (CustomerTreatment $record, array $data): void {
                        if ($record->remaining_times > 0 && $record->is_active) {
                            $record->usageRecords()->create([
                                'treatment_item_id' => $data['treatment_item_id'],
                                'performed_by' => $data['performed_by'],
                                'performed_at' => $data['performed_at'],
                                'notes' => $data['notes'],
                            ]);
                            
                            $record->update([
                                'remaining_times' => $record->remaining_times - 1,
                            ]);
                        }
                    })
                    ->visible(fn (CustomerTreatment $record): bool => $record->remaining_times > 0 && $record->is_active),
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
            'index' => Pages\ListCustomerTreatments::route('/'),
            'create' => Pages\CreateCustomerTreatment::route('/create'),
            'edit' => Pages\EditCustomerTreatment::route('/{record}/edit'),
        ];
    }
} 