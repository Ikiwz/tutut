<?php

namespace App\Filament\Widgets;

use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\TestSession;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Students', User::where('role', 'student')->count())
                ->description('Registered students')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Total Questions', Question::count())
                ->description('Across all sections')
                ->descriptionIcon('heroicon-m-question-mark-circle')
                ->color('success'),
            Stat::make('Active Tests', TestSession::where('is_active', true)->count())
                ->description('Available test sessions')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('warning'),
            Stat::make('Avg Score', number_format(ExamAttempt::where('status', 'completed')->avg('total_score') ?? 0, 0))
                ->description('Average TOEFL score')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }
}
