<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    public function ekranaBas ($yazı): void{
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        $out->writeln($yazı);
    }
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            
        });
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
