<?php

namespace App\Console\Commands;

use App\ItemValor;
use App\Pedido;
use Illuminate\Console\Command;

class SanitizeTaxaEntregaConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sanitize-taxa-entrega';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sanitize taxa de entrega';

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
        $pedidos = Pedido::all();
        /** @var Pedido $pedido */
        foreach ($pedidos as $pedido){
            $query = $pedido->items()->where('iditem', 221);
            $items = $query->get();
            $sum = $items->sum('quantidade');
            $pedido->taxa_entrega = $sum;
            $pedido->save();

            $query->delete();
        }

        ItemValor::find(221)->delete();
    }
}
