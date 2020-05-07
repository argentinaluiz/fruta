<?php

namespace App\Console\Commands;

use App\Pedido;
use Illuminate\Console\Command;

class NormalizeItemPedidoValor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'normalize-item-pedido-valor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize item pedido valor';

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

        foreach ($pedidos as $pedido){
            foreach($pedido->items as $itemPedido){
              $itemPedido->valor = $itemPedido->item->valor;
              $itemPedido->save();
            }
        }
    }
}
