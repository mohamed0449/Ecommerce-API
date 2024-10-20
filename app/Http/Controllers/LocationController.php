<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    
    public function store(Request $request)
    {
        $request->validate([
            'street' => 'required',
            'building' => 'required',
            'area' => 'required',
        ]);

        $location = new Location();
        $location->user_id = Auth::id();
        $location->street = $request->street;
        $location->building = $request->building;
        $location->area = $request->area;
        $location->save();

        return response()->json('location added', 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'street' => 'required',
            'building' => 'required',
            'area' => 'required',
        ]);
        $location = Location::find($id);
        $location->street= $request->street;
        $location->building= $request->building;
        $location->area= $request->area;
        $location->save();
    }

    public function destroy($id)
    {
        Location::where('id', $id)->delete();

        return response()->json('location deleted', 200);
    }
}
