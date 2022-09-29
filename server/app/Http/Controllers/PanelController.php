<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PanelController extends Controller{
    public function dashboard(){
        return view("panel.dashboard");
    }

    public function index(){
        $creation_time = strtotime(auth()->user()->created_at);
        if(
          ($creation_time < time())
          && (
            (date("y", $creation_time) < date("y"))
            || (date("m", $creation_time) < date("m"))
          )
        ){
          
        }
    
        if(auth()->user()->role == 'A'){
          return redirect('/admin');
        }
        return view("dashboard.index")->with($this->withCount());
      }
    
      // Get Portfolio Value of Logged in User
      public function get_portfolio_value($user_id){
        return [
          'price' => auth()->user()->calculated_price()
        ];
      }
    
      public function index_charts_1d(){
        $chart_data = [];
        $time_start = strftime("%Y-%m-%dT00:00:00Z", time());
        $time_end = strftime("%Y-%m-%dT23:00:00Z", time());
    
        $payload = [];
        $usd_value = 0.00;
    
        $user = auth()->user();
        foreach($user->balances() as $balance){
          if($balance["symbol"]["manipulator_type"]==1){
            if(in_array($balance["symbol"]["name"], $this->blunder_symbols))
            continue;
            $symbols[]=$balance["symbol"]["name"];
            if(array_key_exists($balance["symbol"]["name"], $payload)){
              $payload[$balance["symbol"]["name"]][1] += $balance["amount"];
            } else {
              $payload[$balance["symbol"]["name"]]=[
                $balance["symbol"]["manipulator_type"],
                $balance["amount"]
              ];
            }
          } else {
            $usd_value += $balance["amount"];
          }
        }
    
        $chart_data = $this->charts_data_payload_processor_1d($payload, $time_start, $time_end);
        $result = [];
    
        foreach($chart_data as $symbol => $days){
          foreach($days as $unit){
            if(!array_key_exists($unit['timestamp'], $result))
            $result[$unit['timestamp']] = [
              "open" => $usd_value,
              "high" => $usd_value,
              "low" => $usd_value,
              "close" => $usd_value,
              "volume" => $usd_value,
            ];
            foreach($result[$unit['timestamp']] as $k=>$v){
              $result[$unit['timestamp']][$k] += $payload[$symbol][1] * $unit[$k];
            }
          }
        }
    
        return response()->json($result);
      }
    
      public function index_charts(){
        $chart_data = [];
        $time_end = strftime("%Y-%m-%d", time());
        $time_start = strftime("%Y-%m-%d", time() - strtotime("1970-03-00"));
    
        $payload = [];
        $usd_value = 0.00;
    
        $user = auth()->user();
        foreach($user->balances() as $balance){
          if($balance["symbol"]["manipulator_type"]==1){
            if(in_array($balance["symbol"]["name"], $this->blunder_symbols))
            continue;
            $symbols[]=$balance["symbol"]["name"];
            if(array_key_exists($balance["symbol"]["name"], $payload)){
              $payload[$balance["symbol"]["name"]][1] += $balance["amount"];
            } else {
              $payload[$balance["symbol"]["name"]]=[
                $balance["symbol"]["manipulator_type"],
                $balance["amount"]
              ];
            }
          } else {
            $usd_value += $balance["amount"];
          }
        }
    
        $chart_data = $this->charts_data_payload_processor($payload, $time_start, $time_end);
        $result = [];
    
        foreach($chart_data as $symbol => $days){
          foreach($days as $unit){
            if(!array_key_exists(substr($unit['timestamp'], 0, 10), $result))
            $result[substr($unit['timestamp'], 0, 10)] = [
              "open" => $usd_value,
              "high" => $usd_value,
              "low" => $usd_value,
              "close" => $usd_value,
              "volume" => $usd_value,
            ];
            foreach($result[substr($unit['timestamp'], 0, 10)] as $k=>$v){
              $result[substr($unit['timestamp'], 0, 10)][$k] += $payload[$symbol][1] * $unit[$k];
            }
          }
        }
    
        return response()->json($result);
      }
    
      private function charts_data_payload_processor_1d($payload, $time_start, $time_end){
        $value = [];
        $symbol_prices = [];
        $symbols = array_keys($payload);
        $counter=0;
    
        try {
          $response = Http::get(config('app.charts_api_1d') . implode(",", $symbols) . "&time_start=$time_start&time_end=$time_end");
          $response = $response->json();
    
          try {
            if($response["status"]["error_code"]!=0){
              foreach($symbols as $symbol){
                $symbol_prices[$symbol] = 0.00;
              }
            } else if (isset($response["data"])){
              foreach($symbols as $symbol){
                if(
                  isset($response["data"][$symbol])
                  && (count($response["data"][$symbol]) > 0)
                  && isset($response["data"][$symbol][0]["quotes"])
                  && (count($response["data"][$symbol][0]["quotes"]) > 0)
                ) {
                  $symbol_prices[$symbol] = [];
                  for($i=0; $i<count($response["data"][$symbol][0]["quotes"]); $i++){
                    if(
                      isset($response["data"][$symbol][0]["quotes"][$i]["quote"])
                      && isset($response["data"][$symbol][0]["quotes"][$i]["quote"]["USD"])
                    ) $symbol_prices[$symbol] [$i+1]= $response["data"][$symbol][0]["quotes"][$i]["quote"]["USD"];
                  }
                }
              }
            }
    
            return $symbol_prices;
          } catch (\Throwable $th) {
            throw $th;
            return 0.00;
          }
        } catch (\Throwable $th) {
          dd($th);
          dd("ERR"); 
          return 0.00;
        }
      }
    
      private function charts_data_payload_processor($payload, $time_start, $time_end){
        $value = [];
        $symbol_prices = [];
        $symbols = array_keys($payload);
        $counter=0;
    
        try {
          $response = Http::get(config('app.charts_api') . implode(",", $symbols) . "&time_start=$time_start&time_end=$time_end");
          $response = $response->json();
    
          // dd($response);
          try {
            if($response["status"]["error_code"]!=0){
              foreach($symbols as $symbol){
                $symbol_prices[$symbol] = 0.00;
              }
            } else if (isset($response["data"])){
              foreach($symbols as $symbol){
                if(
                  isset($response["data"][$symbol])
                  && (count($response["data"][$symbol]) > 0)
                  && isset($response["data"][$symbol][0]["quotes"])
                  && (count($response["data"][$symbol][0]["quotes"]) > 0)
                ) {
                  $symbol_prices[$symbol] = [];
                  // dd("zero, ", $symbol);
                  for($i=0; $i<count($response["data"][$symbol][0]["quotes"]); $i++){
                    // dd(isset($response["data"][$symbol][0]["quotes"][$i]["quote"])
                    //   , isset($response["data"][$symbol][0]["quotes"][$i]["quote"]["USD"])
                    // );
                    if(
                      isset($response["data"][$symbol][0]["quotes"][$i]["quote"])
                      && isset($response["data"][$symbol][0]["quotes"][$i]["quote"]["USD"])
                    ) $symbol_prices[$symbol] [$i+1]= $response["data"][$symbol][0]["quotes"][$i]["quote"]["USD"];
                  }
                }
              }
            }
    
            return $symbol_prices;
          } catch (\Throwable $th) {
            throw $th;
            return 0.00;
          }
        } catch (\Throwable $th) {
          dd($th);
          dd("ERR"); 
          return 0.00;
        }
      }
    
      // Admin Messages Page
      public function admin_messages(){
        return view("dashboard.messages")->with($this->withCount());
      }
    
      // Fetch Messages
      public function fetch_messages(Request $request){
        $request->validate([
          'last_message_id' => 'required|integer'
        ]);
        $user_messages = auth()->user()->user_messages()->where('id', '>', $request['last_message_id']);
        $messages = $user_messages->get();
    
        $messages = $user_messages->get();
        if(auth()->user()->user_last_read_messages_count() > 0){
          auth()->user()->set_last_read($messages[count($messages) - 1]->id);
        }
    
        return [
          "success" => true,
          "data" => [
            "messages" => $messages,
            "count" => $user_messages->count(),
          ],
        ];
      }
    
      // Compose a New Message to Admin
      public function compose_message(Request $request){
        $request->validate([
          'message' => 'required'
        ]);
    
        $user_message = new UserMessage;
        $user_message->type = 1;
        $user_message->user_id = auth()->user()->id;
        $user_message->message = trim($request['message']);
        
        return [
          'success' => $user_message->save() ? true : false
        ];
      }

      // Car Models
  public function update_car_makes(){
    $updated_ones = [];
    foreach(Car::all() as $car){
      if(is_numeric($car->make) || empty($car->make)){
        foreach(explode(" ", $car->name) as $chunk){
          if(!is_numeric($chunk) && !empty($chunk)){
            $updated_ones[$car->id]=[$car->make, $chunk];
            $car->make = $chunk;
            $car->save();
            break;
          }
        }
      }
    }

    return response()->json($updated_ones);
  }

  // Car Models
  public function update_car_models(){
    $updated_ones = [];
    foreach(Car::all() as $c){
      if(is_numeric($c->model) || empty($c->model) || ($c->model == "Land") || ($c->model == "Rover") || ($c->model == "Range")){
        foreach(explode(" ", $c->name) as $chunk){
          if(!(empty($chunk) || is_numeric($chunk) || ($chunk == $c->make) || ($chunk == "Land") || ($chunk == "Rover") || ($chunk == "Range"))){
            $updated_ones[$c->id]=[$c->model, $chunk];
            $c->model = $chunk;
            $c->save();
            break;
          }
        }
      }
    }

    return response()->json($updated_ones);
  }
}
