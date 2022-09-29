<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CarBrand;
use App\Models\CustomerPayment;
use App\Models\Detailer;
use App\Models\DriverDetail;
use App\Models\DriverJob;
use App\Models\Gift;
use App\Models\GiftCard;
use App\Models\Package;


class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    

    public function filter(Request $req){
        $cars = Car::all();
        $elim_keys = [];

        // Color Filter
        foreach($this->colors as $color){
            if(isset($req["color_$color"]) && $req["color_$color"]){
                foreach($cars as $key => $c){
                    $car_col = $c->exterior()->first()->label;
                    if(!isset($req["color_$car_col"]) || !$req["color_$car_col"]){
                        $elim_keys []= $key;
                    }
                }
                break;
            }
        }

        if(isset($req->date_range) && $req->date_range){
            [$req->filter_from, $req->filter_to] = explode("|", $req->date_range);

            // Date Filter
            if(isset($req->filter_from) && $req->filter_from != "-1"){
                foreach($cars as $key => $c){
                    if(strtotime($c->created_at) < strtotime($req->filter_from)){
                        $elim_keys []= $key;
                    }
                }
            }
            if(isset($req->filter_to) && $req->filter_to != "-1"){
                foreach($cars as $key => $c){
                    if(strtotime($c->filter_to) > strtotime($req->filter_to)){
                        $elim_keys []= $key;
                    }
                }
            }
        }

        // Price Filter
        if(isset($req->price_from) && $req->price_from){
            foreach($cars as $key => $c){
                if($c->online_price < $req->price_from){
                    $elim_keys []= $key;
                }
            }
        }
        if(isset($req->price_to) && $req->price_to){
            foreach($cars as $key => $c){
                if($c->online_price > $req->price_to){
                    $elim_keys []= $key;
                }
            }
        }

        foreach($elim_keys as $key){
            $cars->forget($key);
        } 

        return view('portal.inventory')->with([
            'cars' => $cars,
            'filter' => count($elim_keys) > 0 ? "Y" : "N",
            'min_max' => [Car::min('online_price'), Car::max('online_price')],
        ]);
    }


    public $steps = [
        'one', 'two', 'three', 'four'
      ];
      private $default_new_veh = [
        "current" => 1,
        "data" => [
          "s1" => [],
          "s2" => [],
          "s3" => [],
          "s4" => [],
        ],
      ];
    
      public function index(Request $req){
        // Redirections only
    
        if(
          $req->session()->has('new_id')
          && $req->session()->has('new_step')
          && ($req->session()->get('new_step', 5) < 5)
        ) return response()->redirect()->route('portal.new.' . $this->steps[$req->session()->get('new_step')-1]);
        return redirect()->route("portal.new.one");
      }
    
      // Step One
      public function new_one(){
        $new_veh = session('new_veh', $this->default_new_veh);
    
        // Confirm Step
        if($new_veh["current"]!=1)
          return $this->red($new_veh);
    
        return view("portal.new.one")->with([
          'new_veh' => $new_veh
        ]);
      }
      
      // Step Two
      public function new_two(){
        $new_veh = session('new_veh', $this->default_new_veh);
    
        // Confirm Step
        if($new_veh["current"]!=2)
          return $this->red($new_veh);
    
        return view("portal.new.two")->with([
          'new_veh' => $new_veh
        ]);
      }
      
      // Step Three
      public function new_three(){
        $new_veh = session('new_veh', $this->default_new_veh);
    
        // Confirm Step
        if($new_veh["current"]!=3)
          return $this->red($new_veh);
    
        return view("portal.new.three")->with([
          'new_veh' => $new_veh,
          'option_groups' => OptionGroup::all(),
          'option_exists' => function($o, $opts){
            foreach($opts as $opt)
              if(
                ($opt["gid"] == $o[0]) && 
                ($opt["oid"] == $o[1])
              ) return true;
            return false;
          },
        ]);
      }
    
      // Step Four
      public function new_four(){
        $new_veh = session('new_veh', $this->default_new_veh);
    
        // Confirm Step
        if($new_veh["current"]!=4)
          return $this->red($new_veh);
    
        return view("portal.new.four")->with([
          'new_veh' => $new_veh
        ]);
      }
    
      // Submit-1
      public function new_one_submit(Request $req){
        $req->validate([
          'vin' => 'required|min:17|max:17',
          'year' => 'required|int',
          'make' => 'required',
          'model' => 'required',
          'miles' => 'required',
        ]);
    
        $req->miles = str_replace(",", "", $req->miles); // remove commas
        $new_veh = session('new_veh', $this->default_new_veh);
        $new_veh["data"]["s1"] = [
          'vin' => $req->vin,
          'year' => $req->year,
          'make' => $req->make,
          'model' => $req->model,
          'miles' => $req->miles,
        ];
        $new_veh["current"] = 2;
        session(['new_veh' => $new_veh]);
        
        return $this->red($new_veh);
      }
    
      // Submit-2
      public function new_two_submit(Request $req){
        $req->validate([
          'online_price' => 'required',
          'purchase_price' => 'required',
          'stock_number' => 'required',
        ]);
        
        // remove commas
        $req->online_price = str_replace(",", "", $req->online_price);
        $req->purchase_price = str_replace(",", "", $req->purchase_price);
        $req->stock_number = str_replace(",", "", $req->stock_number);
        
        // Save to Session
        $new_veh = session('new_veh', $this->default_new_veh);
        $new_veh["data"]["s2"] = [
          'online_price' => $req->online_price,
          'purchase_price' => $req->purchase_price,
          'stock_number' => $req->stock_number,
        ];
        $new_veh["current"] = 3;
        session(['new_veh' => $new_veh]);
    
        return $this->red($new_veh);
      }
    
      // Submit-3
      public function new_three_submit(Request $req){
        $req->validate([
          "exterior_color" => 'required',
          "interior_color" => 'required',
          "drive_train" => 'required',
        ]);
    
        // Format Options
        $options=[];
        if(isset($req->options))
          foreach($req->options as $gid => $d)
            foreach($d as $opid => $val)
              if($val == "on")
                $options[]=[
                  "gid" => $gid,
                  "oid" => $opid,
                ];
        
        // Commit
        $new_veh = session('new_veh', $this->default_new_veh);
        $new_veh["data"]["s3"] = [
          'exterior_color' => $req->exterior_color,
          'interior_color' => $req->interior_color,
          'drive_train' => $req->drive_train,
          'options' => $options,
        ];
        $new_veh["current"] = 4;
        session(['new_veh' => $new_veh]);
    
        return $this->red($new_veh);
      }
    
      // Submit-4.1
      public function new_four_images(Request $req){
        $new_veh = session('new_veh', $this->default_new_veh);
        $uploaded_files = $new_veh["data"]["s4"]["uploaded_images"] ?? [];
    
        // Upload Images
        foreach($req->images ?? [] as $i => $file){
          $path = "/img/cars"; $name = "uploaded_image_" . time() . "_" . rand(1000000000, 9999999999) . "." . $file->getClientOriginalExtension();
          $src = "$path/$name";
          if($rr = $file->move(public_path() . $path, $name)){
            $uploaded_files[]=$src;
          } else dd("failed");
        }
        $new_veh["data"]["s4"]["uploaded_images"] = $uploaded_files;
        session(['new_veh' => $new_veh]);
    
        return $this->red($new_veh);
      }
    
      public function new_four_images_del(Request $req){
        $new_veh = session('new_veh', $this->default_new_veh);
        $uploaded_files = $new_veh["data"]["s4"]["uploaded_images"] ?? [];
    
        // Upload Images
        $updated = [];
        foreach($uploaded_files as $i => $f_src)
          if($f_src != $req->src ?? '')
            $updated []= $f_src;
        $new_veh["data"]["s4"]["uploaded_images"] = $updated;
        session(['new_veh' => $new_veh]);
    
        return response()->json([ 'success' => true ]);
      }
    
      // Submit-4.2
      public function new_four_submit(Request $req){
        $new_veh = session('new_veh', $this->default_new_veh);
        $new_veh["data"]["s4"]["description"] = $req->description ?? '';
        
        if($this->store_vehicle($new_veh)){
          session(['new_veh' => $this->default_new_veh]);
          return redirect(route('portal.inventory'));
        }
        
        return $this->red($new_veh);
      }
    
      // Back to Specified Step
      public function back_to($step){
        $new_veh = session('new_veh', $this->default_new_veh);
        
        if(($step!=1) && empty($new_veh["data"]["s".($step-1)]))
          return $this->back_to($step-1);
          
        // for($i=$step+1; $i<5; $i++)
        //   $new_veh["data"]["s$step"] = [];
          
        $new_veh['current'] = $step;
        session(['new_veh' => $new_veh]);
        return $this->red($new_veh);
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
