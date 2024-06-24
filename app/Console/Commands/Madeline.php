<?php

namespace App\Console\Commands;

use App\Tab;
use App\User;
use Helper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class Madeline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:madeline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'tabchi';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Tehran');

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $MadelineProto = new \danog\MadelineProto\API('session.madeline');
        $settings = $MadelineProto->getSettings();
        $MadelineProto->async(true);
        $MadelineProto->loop(function () use ($MadelineProto) {
            yield $MadelineProto->start();

            $me = yield $MadelineProto->getSelf();

            $MadelineProto->logger($me);

            if (!$me['bot']) {
                yield $MadelineProto->messages->sendMessage(['peer' => '@develowper', 'message' => "Hi!\nThanks for creating MadelineProto! <3"]);
                yield $MadelineProto->channels->joinChannel(['channel' => '@MadelineProto']);

                try {
                    yield $MadelineProto->messages->importChatInvite(['hash' => 'https://t.me/joinchat/Bgrajz6K-aJKu0IpGsLpBg']);
                } catch (\danog\MadelineProto\RPCErrorException $e) {
                    $MadelineProto->logger($e);
                }

                yield $MadelineProto->messages->sendMessage(['peer' => '@develowper', 'https://t.me/joinchat/Bgrajz6K-aJKu0IpGsLpBg', 'message' => 'Testing MadelineProto!']);
            }
            yield $MadelineProto->echo('OK, done!');
        });
    }


}
