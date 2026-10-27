<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AgentCommissionWidget;
use App\Filament\Widgets\BookingStatusChart;
use App\Filament\Widgets\FinanceSummaryWidget;
use App\Filament\Widgets\LeadFollowUpWidget;
use App\Filament\Widgets\MuthawifClusterWidget;
use App\Filament\Widgets\PendingApprovalsWidget;
use App\Filament\Widgets\PendingPaymentsWidget;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\UpcomingDeparturesWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            RevenueChart::class,
            BookingStatusChart::class,
            UpcomingDeparturesWidget::class,
            PendingPaymentsWidget::class,
            PendingApprovalsWidget::class,
            FinanceSummaryWidget::class,
            AgentCommissionWidget::class,
            MuthawifClusterWidget::class,
            LeadFollowUpWidget::class,
        ];
    }
}
