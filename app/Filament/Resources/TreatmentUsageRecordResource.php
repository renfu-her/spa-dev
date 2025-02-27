<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TreatmentUsageRecordResource\Pages;
use App\Models\TreatmentUsageRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TreatmentUsageRecordResource extends Resource
{
    protected static ?string $model = TreatmentUsageRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = '療程使用記錄';
    
    protected static ?string $modelLabel = '療程使用記錄';
    
    protected static ?string $pluralModelLabel = '療程使用記錄';
    
    protected static ?string $navigationGroup = '客戶管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('使用記錄')
                    ->schema([
                        Forms\Components\Select::make('customer_treatment_id')
                            ->label('客戶療程')
                            ->relationship('customerTreatment', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->customer->name} - {$record->package->name}")
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('treatment_item_id')
                            ->label('療程項目')
                            ->relationship('treatmentItem', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('performed_by')
                            ->label('操作人員')
                            ->relationship('performedByStaff', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\DateTimePicker::make('performed_at')
                            ->label('操作時間')
                            ->required()
                            ->default(now()),
                        Forms\Components\Textarea::make('notes')
                            ->label('備註')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customerTreatment.customer.name')
                    ->label('客戶')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customerTreatment.package.name')
                    ->label('療程套餐')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('treatmentItem.name')
                    ->label('療程項目')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('performedByStaff.name')
                    ->label('操作人員')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('performed_at')
                    ->label('操作時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('treatment_item_id')
                    ->label('療程項目')
                    ->relationship('treatmentItem', 'name'),
                Tables\Filters\SelectFilter::make('performed_by')
                    ->label('操作人員')
                    ->relationship('performedByStaff', 'name'),
                Tables\Filters\Filter::make('performed_at')
                    ->label('今日操作')
                    ->query(fn (Builder $query): Builder => $query->whereDate('performed_at', now())),
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
            'index' => Pages\ListTreatmentUsageRecords::route('/'),
            'create' => Pages\CreateTreatmentUsageRecord::route('/create'),
            'edit' => Pages\EditTreatmentUsageRecord::route('/{record}/edit'),
        ];
    }
} 