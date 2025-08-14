<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AbilityResource;
use App\Http\Resources\UnitResource;
use App\Http\Resources\WeaponResource;
use App\Models\Unit;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Routing\Controller;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UnitResource::collection(Unit::limit(5000)->orderByRaw("RAND()")->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreUnitRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HttpRequest $request)
    {
        // On crée une nouvelle TVA
        $unit = Unit::create($request->all());

        return new UnitResource($unit);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit)
    {
        return new UnitResource($unit);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UpdateUnitRequest  $request
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(HttpRequest $request, Unit $unit)
    {
        // On modifie les informations de la TVA
        $unit->update($request->all());

        // On retourne la réponse JSON
        return new UnitResource($unit);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit)
    {
        //
    }

    public function weapons(Unit $unit)
    {
        return WeaponResource::collection($unit->weapons()->get());
    }
    
    public function abilities(Unit $unit)
    {
        return AbilityResource::collection($unit->abilities()->get());
    }
}