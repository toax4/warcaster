<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Unit;
use App\Models\UnitContact;
use Illuminate\Http\Request;
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
        return view('admin.units');
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
     * @param  \App\Http\Requests\StoreUnitRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $unit = Unit::make($request->all());

        $unit->save();

        return response()->json([
            'result' => true,
            'message' => 'Success Updated post',
            'data' => $unit
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit)
    {
        $unit->withTranslation();
        return view('admin.unit', compact('unit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(Unit $unit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUnitRequest  $request
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Unit $unit)
    {
        $unit->update($request->all());

        return response()->json([
            'result' => true,
            'message' => 'Success Updated post',
            'data' => $unit,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit)
    {
        $unit->archived = true;

        $unit->save();

        return response()->json([
            'result' => true,
            'message' => 'Deleted',
            'data' => $unit,
        ]);
    }

    public function modal(?int $idUnit = null)
    {
        if ($idUnit == null) {
            $unit = new Unit();
        } else {
            $unit = Unit::find($idUnit);
        }

        return view('modals.units', compact('unit'));
    }

    public function wizard()
    {
        return view('wizards.units');
    }

    public function wizardStore(Request $request)
    {
        // dd($request);

        $unit = Unit::make($request->only([
            'nom',
            'prenom',
            'date_naissance',
            'secu',
            'job_id',
            'rank_id',
            'company_id',
        ]));

        $unit->save();

        return response()->json(data: [
            'result' => true,
            'message' => 'Success Updated post',
            'data' => new UnitResource($unit)
        ], status: 201);
    }
}