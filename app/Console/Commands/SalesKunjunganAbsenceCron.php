<?php

namespace App\Console\Commands;

use App\Models\Kunjungan;
use App\Models\SalesAttendance;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SalesKunjunganAbsenceCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales-kunjungan-absence:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command untuk mengakhiri kunjungan dan absensi setiap jam 11 malam';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //force finish kunjungan
        $kunjungan = Kunjungan::with('pelanggan')->whereNull('time_end')->get();
        foreach($kunjungan as $item){
            $concat_datetime = $item->date ." ". $item->time_start;
            $kunjungan_start = Carbon::parse("$concat_datetime");

            //mengambil selisih menit jika true maka terlambat
            $diff = $kunjungan_start->diffInMinutes(Carbon::now()->format('H:i'), false);

            Kunjungan::where('id', $item->id)->update([
                'time_end' => Carbon::now()->format('H:i'),
                'total_time' => $diff,
            ]);
        }

        $attendance = SalesAttendance::whereNull('date_end')->get();
        foreach($attendance as $item){
            SalesAttendance::where('id', $item->id)->update([
                'date_end' => Carbon::now(),
                'time_end' => Carbon::now()->format('H:i')
            ]);
        }

        \Log::info("Cron is working fine [sales-kunjungan-absence]->" . Carbon::now()->format('Y-m-d H:i:s'));
    }
}
