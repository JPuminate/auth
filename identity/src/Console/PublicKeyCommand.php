<?php


namespace JPuminate\Auth\Identity\Console;


use Illuminate\Console\Command;

/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 16/08/2017
 * Time: 19:46
 */

class PublicKeyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'identity:public-key';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a file that should be contain public-key used by Auth Server';

    public function handle(){
        $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
-----END PUBLIC KEY-----
EOD;
        file_put_contents(\JPuminate\Auth\Identity\Identity::keyPath('oauth-public.key') ,$publicKey);
        $this->info('Public key file create, you need to put your public key');
    }
}