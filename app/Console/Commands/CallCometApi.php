<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CallCometApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'call:comet-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call the comet api.html API';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //return Command::SUCCESS;
        
        // $subject = 'Cron Job Test';
        // $content = 'Cron job test at ' . now();

        // // Replace with your email logic
        // Mail::raw($content, function ($message) use ($subject) {
        //     $message->to('duaaa6460@gmail.com')->subject($subject);
        // });

        // $this->info('Cron job test email sent successfully.');

        // Make the API call using Laravel's HTTP client
        $response = Http::get(url('https://greengen.crm-labloid.com/CommetApi'));

        // Process the API response as needed
        // For example, you can log the response or perform other actions

        $this->info('Comet API Response:');
        $this->line($response->body());
    }
}
