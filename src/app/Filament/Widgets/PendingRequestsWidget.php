<?php

namespace App\Filament\Widgets;

use App\Models\CircleMember;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingRequestsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CircleMember::query()
                    ->whereHas('circle', fn ($query) => $query->where('owner_id', auth()->id()))
                    ->where('status', 'pending')
            )
            ->columns([
                Tables\Columns\TextColumn::make('circle.name'),
                Tables\Columns\TextColumn::make('user.name')->label('Requested By'),
                Tables\Columns\TextColumn::make('vouchedBy.name')->label('Vouched By'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->color('success')
                    ->action(fn (CircleMember $record) => $record->update(['status' => 'active'])),
                Tables\Actions\Action::make('reject')
                    ->color('danger')
                    ->action(fn (CircleMember $record) => $record->update(['status' => 'rejected'])),
            ]);
    }
}
