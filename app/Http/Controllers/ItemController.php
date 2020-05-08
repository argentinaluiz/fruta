<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();
        if ($request->get('item')) {
            $query->where('id', $request->item);
        }
        if ($request->get('categoria')) {
            $query->where('idcategoria', $request->categoria);
        }
        $items = $query->paginate();
        $allItems = Item::orderBy('item', 'asc')->get(['id', 'item']);
        $categorias = Categoria::orderBy('nome', 'asc')->get(['id', 'nome']);
        return view('item.index', compact('items', 'allItems', 'categorias'));
    }


    public function create()
    {
        $categorias = Categoria::all();
        return view('item.create', [
            'categorias' => $categorias
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'item' => 'required|max:255',
            'categoria' => 'required|exists:categoria,id',
            'ingredientes' => 'required',
            'valores' => 'required|array',
            'valores.*.valor' => 'required|min:1|numeric',
        ]);

        /** @var Item $item */
        $item = Item::create([
            'item' => $request->item,
            'idcategoria' => $request->categoria,
            'ingredientes' => $request->ingredientes,
        ]);
        foreach ($request->valores as $valor) {
            $item->itemValores()->create([
                'volume' => $valor['volume'] ?? null,
                'valor' => $valor['valor']
            ]);
        }
        return redirect(route('items.index'));
    }

    public function show($id)
    {
        //
    }

    public function edit(Item $item)
    {
        $categorias = Categoria::all();

        return view('item.edit', [
            'item' => $item,
            'categorias' => $categorias,
        ]);
    }

    public function update(Request $request, Item $item)
    {
        $this->validate($request, [
            'item' => 'required|max:255',
            'categoria' => 'required|exists:categoria,id',
            'ingredientes' => 'required',
            'valores' => 'required|array',
            'valores.*.valor' => 'required|min:1|numeric',
        ]);

        $item->fill([
            'item' => $request->item,
            'idcategoria' => $request->categoria,
            'ingredientes' => $request->ingredientes,
        ]);
        $item->save();
        $item->itemValores()->delete();
        foreach ($request->valores as $valor) {
            $item->itemValores()->create([
                'volume' => $valor['volume'] ?? null,
                'valor' => $valor['valor']
            ]);
        }
        return redirect(route('items.index'));
    }

    public function destroy($id)
    {
        //
    }
}
