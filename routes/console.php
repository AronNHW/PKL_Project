<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduler CetakStok: Hitung rekap prediksi bulanan dan ROP setiap awal bulan
Schedule::command('cetakstok:hitung-prediksi')->monthlyOn(1, '01:00');
