<?php

namespace App\Http\Controllers;

use App\Models\WebUsers;
use App\Models\Customer;
use Illuminate\Http\Request;
class WebUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $webusers = WebUsers::all();
    //      return view('webusers.index',compact('webusers'));
    // }
    public function index()
    {

         return redirect()->route('home');
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
        //
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
    // public function convert($id){
    //     $webusers = WebUsers::find($id);
    //     $name = $webusers->first_name . ' ' . $webusers->last_name . ' (' . $webusers->name . ') ';
    //     $email = $webusers->email;
    //     $phone = $webusers->phone;
    //     $avatar = $webusers->avatar;

    //     $lastCustomerId = Customer::max('customer_id');

    //     $customer = new Customer();
    //     $customer->name = $name;
    //     $customer->customer_id = $lastCustomerId+1;
    //     $customer->email = $email;
    //     $customer->contact = $phone;
    //     $customer->avatar = $avatar;
    //     $customer->created_by = \Auth::user()->creatorId();
    //     $customer->balance = 0;
    //     $customer->user_web_id = $id;
    //     $customer->user_web_av = 1;
    //     $customer->save();


    //     $webusers->customer_id = $customer->id; // استخدام id الجديد الذي تم إنشاؤه لـ Customer
    //     $webusers->convent = 1;
    //     $webusers->save();

    //    return back()->with('success', __('Customer successfully converted.'));
    // }
}
