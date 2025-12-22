<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AbilityResource;
use App\Http\Resources\FactionResource;
use App\Http\Resources\WeaponResource;
use App\Models\Faction;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Routing\Controller;

class FactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return FactionResource::collection(Faction::limit(5000)->orderByRaw("RAND()")->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreFactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HttpRequest $request)
    {
        // On crée une nouvelle TVA
        $faction = Faction::create($request->all());

        return new FactionResource($faction);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Faction  $faction
     * @return \Illuminate\Http\Response
     */
    public function show(Faction $faction)
    {
        return new FactionResource($faction);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UpdateFactionRequest  $request
     * @param  \App\Models\Faction  $faction
     * @return \Illuminate\Http\Response
     */
    public function update(HttpRequest $request, Faction $faction)
    {
        // On modifie les informations de la TVA
        $faction->update($request->all());

        // On retourne la réponse JSON
        return new FactionResource($faction);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Faction  $faction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Faction $faction)
    {
        //
    }

    public function weapons(Faction $faction)
    {
        return WeaponResource::collection($faction->weapons()->get());
    }

    public function abilities(Faction $faction)
    {
        return AbilityResource::collection($faction->abilities()->get());
    }
}