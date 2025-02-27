<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolePermissionResource\Pages;
use App\Models\RolePermission;
use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RolePermissionResource extends Resource
{
    protected static ?string $model = RolePermission::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    
    protected static ?string $navigationGroup = '系統管理';
    
    protected static ?string $navigationLabel = '角色權限';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('角色權限資訊')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('角色')
                            ->options([
                                'admin' => '管理員',
                                'manager' => '經理',
                                'staff' => '員工',
                                'operator' => '操作員',
                            ])
                            ->required(),
                        Forms\Components\Select::make('permission_id')
                            ->label('權限')
                            ->options(Permission::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('role')
                    ->label('角色')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'admin' => '管理員',
                        'manager' => '經理',
                        'staff' => '員工',
                        'operator' => '操作員',
                        default => $state,
                    })
                    ->colors([
                        'danger' => 'admin',
                        'warning' => 'manager',
                        'success' => 'staff',
                        'primary' => 'operator',
                    ]),
                Tables\Columns\TextColumn::make('permission.name')
                    ->label('權限名稱')
                    ->searchable(),
                Tables\Columns\TextColumn::make('permission.slug')
                    ->label('權限代碼')
                    ->searchable(),
                Tables\Columns\TextColumn::make('permission.module')
                    ->label('模組')
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('role')
                    ->label('角色')
                    ->options([
                        'admin' => '管理員',
                        'manager' => '經理',
                        'staff' => '員工',
                        'operator' => '操作員',
                    ]),
                Tables\Filters\SelectFilter::make('permission_module')
                    ->label('模組')
                    ->options(function () {
                        return Permission::distinct()->pluck('module', 'module')->toArray();
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'],
                                fn (Builder $query, $module): Builder => $query->whereHas(
                                    'permission',
                                    fn (Builder $query) => $query->where('module', $module)
                                ),
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
            'index' => Pages\ListRolePermissions::route('/'),
            'create' => Pages\CreateRolePermission::route('/create'),
            'edit' => Pages\EditRolePermission::route('/{record}/edit'),
        ];
    }
} 