<?php

namespace App\Http\Controllers;

use App\Part;
use App\WhseStk;
use Illuminate\Http\Request;
use Faker\Factory as Faker;

class PartsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Part::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $parts = Part::create($request->all());
        return response()->json($parts);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    public function importParts()
    {
        $parts = WhseStk::join('WhseMst','WhseStk.WHWhseID','=','WhseMst.WhseLink')
            ->join('StkItem','WhseStk.WHStockLink','=','StkItem.StockLink')
            ->select("StkItem.StockLink","StkItem.Code","StkItem.Description_1","StkItem.AveUCst")
            ->get();
        $existing = Part::get();
        $found_assets = [];
        if ($existing->count() < 1){
            return $this->storeParts($parts);
        }
        $ids = $existing->map(function($asset){
            return  $asset->stock_link;
        });

        foreach ($parts as $asset){
            if (!in_array($asset->StockLink,$ids->all())){
                $found_assets[] = $asset;
            }
        }
        if(!$found_assets){
            return response()->json([]);
        }
        return $this->storeParts($found_assets);
    }
    function storeParts($assets){
        $faker = Faker::create();
         $inserted = [];
          foreach ($assets as $asset){
            $inserted[] = Part::create([
                'code' => $asset->Code,
                'description' => $asset->Description_1,
                'cost' => $asset->AveUCst==0 ? $faker->unique()->numberBetween(50,250) : $asset->AveUCst ,
                'stock_link' => $asset->StockLink,
                ]);

        }
        return response()->json($inserted);
    }
      /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Part::find($id)->update($request->all());
        return response()->json(Part::find($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Part::destroy($id);
        return response()->json($id);
    }
}
