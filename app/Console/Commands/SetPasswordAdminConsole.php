<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SetPasswordAdminConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:set-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set password for Admin User';

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
     * @return mixed
     */
    public function handle()
    {
        if(!User::all()->count()){
            User::create([
                'name' => 'Admin',
                'email' => 'admin@frutaeraiz.com.br',
                'password' => bcrypt(Str::random('6'))
            ]);
        }

        $password = $this->secret('Digite a senha (mínimo 6 caracteres)');

        if(strlen($password) < 6){
            $this->error('A senha precisa ter no mínimo 6 caracteres');
        }

        $user = User::first();
        $user->password = bcrypt($password);
        $user->save();

        $this->info('Senha salva com sucesso');
    }
}
