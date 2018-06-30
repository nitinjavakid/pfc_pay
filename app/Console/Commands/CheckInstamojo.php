<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use \Instamojo\Instamojo;
use App\Payment;
use Carbon\Carbon;

class CheckInstamojo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:instamojo {id? : ID of request}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check instamojo for payments';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $instamojo;
    public function __construct(Instamojo $instamojo)
    {
        parent::__construct();
        $this->instamojo = $instamojo;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = $this->argument("id");
        if($id != null)
        {
            print_r($this->instamojo->paymentRequestStatus($id));
            return;
        }

        $payments = Payment::where("status", "!=", "paid")
            ->where("type", "instamojo")->get();
        foreach($payments as $payment)
        {
            $response = $this->instamojo->paymentRequestStatus($payment->external_id);
            if(isset($response['payments']) &&
               isset($response['payments'][0]) &&
               $response['payments'][0]['status'] == 'Credit')
            {
                DB::transaction(function() use ($payment, $response) {
                    $payment->net_amount = ((float) $response['payments'][0]['quantity'] * 
                                            (float) $response['payments'][0]['unit_price']) - 
                                            (float) $response['payments'][0]['fees'] -
                                            ((float)$response['payments'][0]['fees'] * 18 / 100.0);
                    $payment->status = 'paid';
                    $payment->save();
                });
            }
            else if(isset($response['expires_at']) &&
                    $response['expires_at'] != null &&
                    Carbon::now()->gt(Carbon::parse($response['expires_at'])))
            {
                $payment->delete();
            }
        }
    }
}
