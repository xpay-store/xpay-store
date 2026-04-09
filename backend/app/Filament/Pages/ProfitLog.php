<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfitLog extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationGroup = 'المالية';
    protected static ?string $navigationLabel = 'سجل الأرباح';
    protected static string $view = 'filament.pages.profit-log';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'from' => now()->subDays(30)->toDateString(),
            'to' => now()->toDateString(),
            'group_by' => 'day',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            DatePicker::make('from')->label('من')->required(),
            DatePicker::make('to')->label('إلى')->required(),
            Select::make('group_by')
                ->label('التجميع')
                ->options([
                    'day' => 'يومي',
                    'month' => 'شهري',
                    'year' => 'سنوي',
                ])
                ->required(),
        ])->statePath('data');
    }

    /**
     * @return array<int, array{period: string, orders: int, usd: float, syp: float}>
     */
    public function rows(): array
    {
        $from = Carbon::parse((string) ($this->data['from'] ?? now()->subDays(30)->toDateString()))->startOfDay();
        $to = Carbon::parse((string) ($this->data['to'] ?? now()->toDateString()))->endOfDay();
        $groupBy = (string) ($this->data['group_by'] ?? 'day');

        $orders = Order::query()
            ->where('status', 'accept')
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get();

        $buckets = [];
        foreach ($orders as $order) {
            $created = $order->created_at ? Carbon::parse($order->created_at) : now();
            $key = match ($groupBy) {
                'year' => $created->format('Y'),
                'month' => $created->format('Y-m'),
                default => $created->format('Y-m-d'),
            };
            if (! isset($buckets[$key])) {
                $buckets[$key] = ['period' => $key, 'orders' => 0, 'usd' => 0.0, 'syp' => 0.0];
            }
            $buckets[$key]['orders']++;
            $buckets[$key]['usd'] += (float) ($order->total_price['USD'] ?? 0);
            $buckets[$key]['syp'] += (float) ($order->total_price['SYP'] ?? 0);
        }

        ksort($buckets);

        return array_values($buckets);
    }

    public function downloadExcel(): StreamedResponse
    {
        $rows = $this->rows();
        $filename = 'profit-log-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'wb');
            fputcsv($out, ['Period', 'Orders', 'USD', 'SYP']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row['period'],
                    $row['orders'],
                    number_format($row['usd'], 2, '.', ''),
                    number_format($row['syp'], 2, '.', ''),
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function downloadPdf()
    {
        $rows = $this->rows();
        $html = view('filament.pages.profit-log-pdf', [
            'rows' => $rows,
            'from' => $this->data['from'] ?? '',
            'to' => $this->data['to'] ?? '',
            'groupBy' => $this->data['group_by'] ?? 'day',
        ])->render();

        return Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->download('profit-log-'.now()->format('Ymd-His').'.pdf');
    }

    public static function canAccess(): bool
    {
        return auth()->check() && (auth()->user()->role ?? null) === 'admin';
    }
}

