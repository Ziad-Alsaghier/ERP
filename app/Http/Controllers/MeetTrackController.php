<?php

namespace App\Http\Controllers;

use App\Models\MeetTrack;
use Illuminate\Http\Request;

class MeetTrackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        $meets = MeetTrack::all();
        // dd($meets);
        return view('meettrack.index' , compact('meets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:100',
            'job_title' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'company_activites' => 'nullable|string',
            'email' => 'nullable|email|max:100',
            'tech_solutions' => 'nullable|string',
            'interest_level' => 'nullable|in:high,medium,low',
            'demo_request' => 'nullable|integer',
            'contact_time' => 'nullable|string|max:255',
            'contact_method' => 'nullable|in:email,phone,whatsapp',
        ]);

        // Create a new MeetTrack record
        MeetTrack::create($request->all());

        // Redirect or return response
        return redirect()->route('meet.page')->with('success', 'MeetTrack created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
