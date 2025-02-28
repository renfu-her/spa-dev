<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = '系統設定';
    
    protected static ?string $navigationLabel = '使用者管理';

    protected static ?string $modelLabel = '使用者';
    
    protected static ?int $navigationSort = 1;

    protected static function getRoleLabel($roleName): string
    {
        return match ($roleName) {
            'admin' => '管理者',
            'manager' => '經理',
            'staff' => '員工',
            'operator' => '操作者',
            default => $roleName,
        };
    }

    public static function form(Form $form): Form
    {
        $roles = Role::all()->mapWithKeys(fn ($role) => [
            $role->name => static::getRoleLabel($role->name)
        ])->toArray();

        return $form
            ->schema([
                Forms\Components\Section::make('使用者資訊')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('姓名')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('電子郵件')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->label('密碼')
                            ->password()
                            ->required(fn ($component, $get, $record) => ! $record)
                            ->maxLength(255)
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => bcrypt($state)),
                        Forms\Components\TextInput::make('phone')
                            ->label('電話')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->label('地址')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->label('啟用')
                            ->default(true),
                    ])->columns(2),
                
                Forms\Components\Section::make('角色與權限')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('角色')
                            ->multiple()
                            ->relationship(
                                'roles',
                                'name',
                                fn ($query) => $query->orderBy('name')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => static::getRoleLabel($record->name))
                            ->preload()
                            ->required(),
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
                Tables\Columns\TextColumn::make('email')
                    ->label('電子郵件')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('角色')
                    ->formatStateUsing(function ($record) {
                        return $record->getRoleNames()
                            ->map(fn ($role) => static::getRoleLabel($role))
                            ->implode('、');
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('電話')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('啟用')
                    ->boolean(),
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
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('啟用狀態'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn (User $record): string => $record->is_active ? '停用' : '啟用')
                    ->icon(fn (User $record): string => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (User $record): string => $record->is_active ? 'danger' : 'success')
                    ->action(function (User $record): void {
                        $record->update(['is_active' => !$record->is_active]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('啟用')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Builder $query): void {
                            $query->update(['is_active' => true]);
                        }),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('停用')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (Builder $query): void {
                            $query->update(['is_active' => false]);
                        }),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }
} 