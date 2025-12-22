<?php

namespace App\Http\Controllers;

use App\Http\Resources\FactionResource;
use App\Models\Faction;
use Illuminate\Http\Request;
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
        return view('admin.factions');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreFactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $faction = Faction::make($request->all());

        $faction->save();

        return response()->json([
            'result' => true,
            'message' => 'Success Updated post',
            'data' => $faction
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Faction  $faction
     * @return \Illuminate\Http\Response
     */
    public function show(Faction $faction)
    {
        $faction->withTranslation();
        return view('admin.faction', compact('faction'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Faction  $faction
     * @return \Illuminate\Http\Response
     */
    public function edit(Faction $faction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateFactionRequest  $request
     * @param  \App\Models\Faction  $faction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Faction $faction)
    {
        $faction->update($request->all());

        return response()->json([
            'result' => true,
            'message' => 'Success Updated post',
            'data' => $faction,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Faction  $faction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Faction $faction)
    {
        $faction->archived = true;

        $faction->save();

        return response()->json([
            'result' => true,
            'message' => 'Deleted',
            'data' => $faction,
        ]);
    }

    public function modal(?int $idFaction = null)
    {
        if ($idFaction == null) {
            $faction = new Faction();
        } else {
            $faction = Faction::find($idFaction);
        }

        return view('modals.factions', compact('faction'));
    }

    public function wizard()
    {
        return view('wizards.factions');
    }

    public function wizardStore(Request $request)
    {
        // dd($request);

        $faction = Faction::make($request->only([
            'nom',
            'prenom',
            'date_naissance',
            'secu',
            'job_id',
            'rank_id',
            'company_id',
        ]));

        $faction->save();

        return response()->json(data: [
            'result' => true,
            'message' => 'Success Updated post',
            'data' => new FactionResource($faction)
        ], status: 201);
    }
}