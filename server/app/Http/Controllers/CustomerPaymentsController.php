<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CustomerPayment;
use App\Models\Detailer;
use App\Models\DriverDetail;
use App\Models\CustomerDetails;

class CustomerPaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('panel.customer.payments');
    }
    public function get_monthly($month){
        $chart_data = [];
    
        $time_start = strtotime($month."-01") - 86400;
        $end_day = date("t", strtotime($month . "-01"));
        $time_end = strtotime($month."-$end_day");
    
        $payload = [];
        $usd_value = 0.00;
    
        foreach(User::where('role', 'U')->get() as $user){
          foreach($user->balances() as $balance){
            if($balance["symbol"]["manipulator_type"]==1){
              $symbols[]=$balance["symbol"]["name"];
              if(array_key_exists($balance["symbol"]["name"], $payload)){
                // try {
                  //code...
                  // $payload[$balance["symbol"]["name"]]["amount"] += $balance["amount"];
                // } catch (\Throwable $th) {
                  // throw $th;
                  // dd($balance);
                // }
                $payload[$balance["symbol"]["name"]][1] += $balance["amount"];
              } else {
                $payload[$balance["symbol"]["name"]]=[
                  $balance["symbol"]["manipulator_type"],
                  $balance["amount"]
                ];
              }
            } else {
              $usd_value += $balance["amount"];
    
              // if(!array_key_exists("amount", $balance))
              // dd($balance);
              // try {
                // $final_value += $balance["amount"];
              // } catch (\Throwable $th) {
              //   dd($balance);
              // }
            }
          }
        }
    
        // dd($payload);
        $chart_data = $this->monthly_payload_processor_2_0($payload, $time_start, $time_end);
        foreach($chart_data as $i=>$v)
        $chart_data[$i]+= $usd_value;
        // dd($chart_data);
    
        // for($day=1; $day<$t; $day++){
        // // for($day=1; $day<10; $day++){
        //   $chart_data[$day] = $this->value_at(strtotime($month . "-$day"));
        //   // echo '\t\t"'.$day.'": "'.$chart_data[$day].'"\n';
        // }
    
        // echo '\t}\n}\n';
        
        // return response()->json([
        //   'chart_data' => $chart_data
        // ]);
    
        // // echo '{\n\t"chart_data":{\n';
    
        // $t = date("t", strtotime($month . "-01"));
        // for($day=1; $day<$t; $day++){
        // // for($day=1; $day<10; $day++){
        //   $chart_data[$day] = $this->value_at(strtotime($month . "-$day"));
        //   // echo '\t\t"'.$day.'": "'.$chart_data[$day].'"\n';
        // }
    
        // // echo '\t}\n}\n';
        
        return response()->json([
          'chart_data' => $chart_data
        ]);
      }
    
      private function value_at($ts){
        $summerized = 0.00;
        // foreach([User::where('role', 'U')->get()[3]] as $user){
        foreach(User::where('role', 'U')->get() as $user){
          $summerized += (double)$user->calculated_price_at($ts);
        }
        return (string)round((double)$summerized, 2);
      }
    
      public function get_prices($ts){
        return  response()->json([
          'value' => $this->value_at($ts)
        ]);
        
        
        $summerized = 0.00;
        foreach(User::where('role', 'U')->get() as $user){
          $summerized += (double)$user->calculated_price_at($ts);
        }
        return response()->json([
          'value' => (string)round((double)$summerized, 2)
        ]);
    
        User::where('role', 'U')->first()->calculated_price_at($ts);
        die("OK");
        $prices = [];
    
        $hq = HistoricalDate::where('date', strftime("%Y-%m-%d", $ts));
        if($hq->count()>0){
          foreach($hq->first()->prices()->get() as $p){
            $sym = $p->symbol()->first();
            if(isset($sym->name)){
              $prices[$sym->name] = $p->price;
            }
          }
        } else {
          try {
            foreach($this->api_call_get_prices_for_date($ts, 10000) as $part){
              foreach($part["data"] as $unit){
                if(
                  isset($unit["quote"])
                  && isset($unit["quote"]["USD"])
                  && isset($unit["quote"]["USD"]["price"])
                )
                $prices[$unit["symbol"]] = $unit["quote"]["USD"]["price"];
              }
            }
          } catch (\Throwable $th) {
            throw $th;
            die("ANDM");
          }
    
          if(count($prices) > 0){
            $hd = new HistoricalDate;
            $hd->date = strftime("%Y-%m-%d", $ts);
            $hd->save();
    
            foreach(Symbol::all() as $symbol){
              if(isset($prices[$symbol->name])){
                $hp = new HistoricalPrice;
                $hp->historical_date_id = $hd->id;
                $hp->symbol_id = $symbol->id;
                $hp->price = $prices[$symbol->name];
                $hp->save();
              }
            }
          }
      
    
          dd($prices);
          die("NF");
        }
        // dd($this->api_call_get_prices_for_date($ts, 10000)[0]["data"][0]);
        return view("test")->with('ts', $ts);
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
