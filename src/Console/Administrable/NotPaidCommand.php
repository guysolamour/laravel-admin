<?php

namespace Guysolamour\Administrable\Console\Administrable;

use Guysolamour\Administrable\Console\BaseCommand;

class NotPaidCommand extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "administrable:notpaid
                            {--m|message= : message to display }
                            ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Put website in not paiment mode and redirect all requests to notpaid view';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Putting application in not paid mode');

        $this->saveMessage();
        $this->savePayOption();

        $this->info('Application put in not paid mode');
    }

    private function saveMessage(): void
    {
        $message = $this->option('message');

        if (empty($message)) {
            option_delete('app_notpaid_message');
        } else {
            $message = option_edit('app_notpaid_message', $message);
        }
    }

    private  function savePayOption(): void
    {
        option_edit('app_paid', '0');
    }
}
