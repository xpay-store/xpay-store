<x-filament-panels::page>
    <div class="space-y-4">
        <form wire:submit.prevent>
            {{ $this->form }}
        </form>

        <div class="flex flex-wrap gap-2">
            <x-filament::button wire:click="downloadExcel" color="success">
                تصدير Excel (CSV)
            </x-filament::button>
            <x-filament::button wire:click="downloadPdf" color="gray">
                تصدير PDF
            </x-filament::button>
        </div>

        <x-filament::section>
            <x-slot name="heading">تفاصيل الأرباح</x-slot>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="px-3 py-2 text-right">الفترة</th>
                            <th class="px-3 py-2 text-right">عدد الطلبات</th>
                            <th class="px-3 py-2 text-right">USD</th>
                            <th class="px-3 py-2 text-right">SYP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->rows() as $row)
                            <tr class="border-b">
                                <td class="px-3 py-2">{{ $row['period'] }}</td>
                                <td class="px-3 py-2">{{ $row['orders'] }}</td>
                                <td class="px-3 py-2">{{ number_format($row['usd'], 2) }}</td>
                                <td class="px-3 py-2">{{ number_format($row['syp'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-3 py-3 text-center text-gray-500" colspan="4">لا توجد بيانات ضمن النطاق.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>

