<?php

namespace RuLong\UserAccount\Commands;

use Illuminate\Console\Command;
use RuLong\UserAccount\Models\UserAccount;

class InitAccounts extends Command
{

    protected $signature = 'user:account';

    protected $description = 'Init users account';

    private $directory;

    public function handle()
    {
        $this->info('Find all users.');

        $class = config('user_account.user_model');
        $model = new $class;
        $this->info('There are ' . $model->count() . ' users');

        foreach ($model->get() as $user) {
            UserAccount::firstOrCreate([
                'user_id' => $user->id,
            ], [
                'cash' => 0,
            ]);
            $this->info('Synced user account : ' . $user->id);
        }

        $this->info('Init users account success.');
    }
}
