<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ReformatPhoneNumbers extends Command
{
    protected $signature = 'phone:reformat';

    protected $description = 'Reformat all phone numbers in users table to standard format (08xxxx)';

    public function handle()
    {
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            $original = $user->nohp;
            $phone = preg_replace('/[^0-9]/', '', $user->nohp);

            if (substr($phone, 0, 3) === '620') {
                $formatted = '0' . substr($phone, 3);
            } elseif (substr($phone, 0, 2) === '62') {
                $formatted = '0' . substr($phone, 2);
            } elseif (substr($phone, 0, 1) === '0') {
                $formatted = $phone;
            } else {
                $formatted = '0' . $phone;
            }

            if ($original !== $formatted) {
                $user->nohp = $formatted;
                $user->save();
                $count++;
                $this->line("{$original} -> {$formatted}");
            }
        }

        $this->info("Done. {$count} phone number(s) reformatted.");
    }
}
