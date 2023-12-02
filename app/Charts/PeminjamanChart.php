<?php

namespace App\Charts;

use App\Models\anggota;
use App\Models\buku;
use App\Models\pinjam;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\Carbon;

class PeminjamanChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\LineChart
    {

        $tahun = date('Y');
        $bulan = date('m');

        for ($i = 1; $i <= $bulan; $i++) {
            $totalpinjam = pinjam::select('*')
                ->whereYear('pinjams.created_at', $tahun)
                ->whereMonth('pinjams.created_at', $i)
                ->count();

            $databulan[] = Carbon::create()->month($i)->format('F');
            $datatotal[] = $totalpinjam;
        }

        for ($i = 1; $i <= $bulan; $i++) {
            $totalanggota = anggota::select('*')
                ->whereYear('created_at', $tahun)
                ->whereMonth('created_at', $i)
                ->count();

            // $databulananggota[] = Carbon::create()->month($i)->format('F');
            $datatotalanggota[] = $totalanggota;
        }

        for ($i = 1; $i <= $bulan; $i++) {
            $totalbuku = buku::select('*')
                ->whereYear('created_at', $tahun)
                ->whereMonth('created_at', $i)
                ->count();

            // $databulananggota[] = Carbon::create()->month($i)->format('F');
            $datatotalbuku[] = $totalbuku;
        }

        return $this->chart->lineChart()
            // ->setTitle('Peminjaman Buku TIP Literation')
            // ->setSubtitle('Total Peminjaman Buku Perbulan')
            ->addData('Total Anggota', $datatotalanggota)
            ->addData('Total Peminjaman', $datatotal)
            ->addData('Total Buku', $datatotalbuku)
            ->setHeight(250)
            ->setXAxis($databulan);
    }
}
