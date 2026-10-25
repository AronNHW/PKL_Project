<x-app-layout>
    <x-slot name="title">
        @if(auth()->user()->isOwner())
            Ringkasan Eksekutif & Valuasi Inventori
        @elseif(auth()->user()->isAdminPusat())
            Kontrol Pergudangan & Logistik Pusat
        @else
            Operasional Gudang {{ auth()->user()->lokasi->nama_lokasi ?? 'Cabang' }}
        @endif
    </x-slot>

    {{-- Render Dashboard Spesifik Sesuai Peran Pengguna --}}
    @if(auth()->user()->isOwner())
        @include('dashboard.owner')
    @elseif(auth()->user()->isAdminPusat())
        @include('dashboard.pusat')
    @else
        @include('dashboard.cabang')
    @endif

    {{-- Script Chart.js untuk Owner & Admin Pusat --}}
    @if(isset($chartLabels) && (auth()->user()->isOwner() || auth()->user()->isAdminPusat()))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('stokChart');
                if (ctx && window.Chart) {
                    new window.Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($chartLabels) !!},
                            datasets: [
                                {
                                    label: 'Gudang Pusat',
                                    data: {!! json_encode($chartDataPusat ?? []) !!},
                                    backgroundColor: '#0B6B73',
                                    borderRadius: 3,
                                    barPercentage: 0.65,
                                },
                                {
                                    label: 'Total 5 Cabang',
                                    data: {!! json_encode($chartDataCabangTotal ?? []) !!},
                                    backgroundColor: '#C8B98F',
                                    borderRadius: 3,
                                    barPercentage: 0.65,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 250 },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    align: 'end',
                                    labels: {
                                        boxWidth: 10,
                                        boxHeight: 10,
                                        font: { family: "'Instrument Sans', sans-serif", size: 11, weight: '500' },
                                        color: '#6B665A',
                                        padding: 12
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#1C1A15',
                                    titleFont: { family: "'Instrument Sans', sans-serif", size: 12, weight: '600' },
                                    bodyFont: { family: "'Instrument Sans', sans-serif", size: 11 },
                                    padding: 8,
                                    cornerRadius: 4
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { 
                                        font: { family: "'Instrument Sans', sans-serif", size: 10.5 },
                                        color: '#6B665A'
                                    }
                                },
                                y: {
                                    grid: { color: '#E2DED2', drawBorder: false },
                                    ticks: { 
                                        font: { family: "'Instrument Sans', sans-serif", size: 10.5 },
                                        color: '#6B665A'
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
