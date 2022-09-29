<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverJobsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
  // HomePage
  public function index(){
    $initial_total = 0;
    foreach(User::where('role', 'U')->get() as $user){
      $initial_total += $user->initial_investment;
    }
    return view('admin.index')->with([
      'number_of_users' => User::where('role', 'U')->count(),
      'initial_total' => $initial_total
    ]);
  }


  // Helpers
  private function manipulate($name, $manipulator_type, $amount){
    if( // Zero Amount
      ((double)$amount == 0)
    ) return 0.00;

    switch($manipulator_type){
      case 1: // Direct API Manipulate
        try { 
          // $response = Http::get(config('app.currency_api_part_a') . $identifier . config('app.currency_api_part_b'));
          // $response = Http::get(config('app.currency_api_part_a') . substr($identifier, 0, strlen($identifier)-3) . config('app.currency_api_part_b'));
          $response = Http::get(config('app.currency_api_part_a') . $name . config('app.currency_api_part_b'));
          $response = $response->json();
          // $price = $response["price"];
          try { 
            $price = $response["data"][$name]["quote"]["USD"]["price"];
          } catch (\Throwable $th) {
            return 0.00;
          }
          if((double)$price == 0) return "Service Down";
          return (string)round((double)$price * (double)$amount, 2);
        } catch (\Throwable $th) {
          // throw $th . "\n" . $identifier . "\n";
          // die($identifier);
          return "Service Down";
        }
      case 3: // No-Conversion
        return $amount;
      default:
        return false;
    }
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
