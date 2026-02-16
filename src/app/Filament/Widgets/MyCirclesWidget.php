<?php

namespace App\Filament\Widgets;

use App\Models\CircleMember;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MyCirclesWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('My Circles', CircleMember::where('user_id', auth()->id())->where('status', 'active')->count())
                ->description('Circles you are a member of')
                ->descriptionIcon('heroicon-m-user-group'),
            Stat::make('Pending Invitations', CircleMember::where('user_id', auth()->id())->where('status', 'pending')->count())
                ->description('Wait for your validation')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
