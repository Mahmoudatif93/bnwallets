<?php

namespace App\Http\Controllers\API;
use App\Paymentcommissionsmodel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Company;
use App\Order;
use App\Cards;
use App\Client;
use App\Anaiscodes;
use App\cards_anais;
use App\Order_anais;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use PDF2;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use DB;
use DateTime;

use Carbon\CarbonPeriod;
class OrderController extends Controller
{

    use ApiResourceTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */







    public function reserveorder(Request $request)
    {



        $cardscount = Cards::where(array('id' => $request->card_id, 'avaliable' => 0, 'purchase' => 0, 'enable' => 0))->count();
        $card = Cards::where(array('id' => $request->card_id, 'avaliable' => 0, 'purchase' => 0, 'enable' => 0))->orderBy('id', 'desc')->first();

        if ($cardscount > 0) {

            if ($card->api2 == 1) {


                $uri = 'https://identity.anis.ly/connect/token';
                $params = array(
                  'grant_type' => 'user_credentials',
            'client_id' => 'bn-plus',
          //  'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
          //  'password' => 'P@ssw0rd1988',
          'client_secret'=>'VKOW0OR2wd893RFPQDM92i0q233HRFB8C2W87RGEetghkl9238',
            'password' => 'Mahdibnplus1988',
            'email' => 'info@bn-plus.ly'
            
                );
                $response = Http::asForm()->withHeaders([])->post($uri, $params);
                $token = $response->json()['access_token'];
                $token_type = $response->json()['token_type'];
                $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];

                $compurlcheck = 'https://gateway.anis.ly/api/consumers/v1/categories/cards/' . $card->api2id . '';

                $cardschek = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => $alltoken,

                ])->get($compurlcheck);


                if (isset($cardschek->json()['data'])) {

                    if ($cardschek->json()['data']['inStock'] == false) {
                        $updatecard['purchase'] = 1;
                        $updatecard['avaliable'] = 1;
                        Cards::where('id', $card->id)->update($updatecard);
                        return $this->apiResponse6(null, null, 'error to Reserve Order', 404);
                    } else {

                        $request_data['card_id'] = $card->id;
                        $request_data['client_id'] = $request->client_id;
                        $request_data['card_price'] = $request->card_price;
                        $request_data['client_name'] = $request->client_name;
                        $request_data['client_number'] = $request->client_number;
                        $request_data['paymenttype'] = "معاملات";

                        $order = Order::create($request_data);

                        if ($order) {

                            $message = "card reserved ";
                            return $this->apiResponse6($cardscount - 1, $order->id, $message, 200);
                        } else {

                            return $this->apiResponse6(null, null, 'error to Reserve Order', 404);
                        }
                    }
                }
            } else {



                if ($card->api == 1) {

                    $request_data['card_id'] = $card->id;
                } else {
                    $request_data['card_id'] = $card->id;
                }




                $request_data['client_id'] = $request->client_id;
                $request_data['card_price'] = $request->card_price;
                $request_data['client_name'] = $request->client_name;
                $request_data['client_number'] = $request->client_number;
                $request_data['paymenttype'] = "معاملات";

                $order = Order::create($request_data);

                if ($order) {
                    if ($card->api2 != 1) {
                        $dataa['avaliable'] = 1;
                        Cards::where('id', $order->card_id)->update($dataa);
                    }

                    $message = "card reserved ";
                    return $this->apiResponse6($cardscount - 1, $order->id, $message, 200);
                } else {

                    return $this->apiResponse6(null, null, 'error to Reserve Order', 404);
                }
            }
        } else {
            $message = "No Cards Avaliable For this Price";
            return $this->apiResponse6($cardscount, null, $message, 404);
        }
    }

    public function clientorder(Request $request)
    {

        $order = Order_anais::where(array('client_id' => $request->clientid, 'paid' => "true"))->with('cards')->get();


        /* foreach( $orders as $row){
             $carsss=Cards::where('id',$row->card_id)->get();
             if(!empty( $carsss)){
                 foreach( $carsss as $rowca){
                     
                       if($rowca->api2==1){
                     
                  $order = Order_anais::where(array('client_id' => $request->clientid, 'paid' => "true"))->with('cards')->get();
                  
                  
             }else{
             
                 $order = Order::where(array('client_id' => $request->clientid, 'paid' => "true"))->with('cards')->get(); 
                 
             }
              } 
             }
              
           
             
        }
      */




        if (count($order) > 0) {
            return $this->apiResponse($order, 'You have orders', 200);
        } else {
            return $this->apiResponse($order, 'No orders Avaliable', 200);
        }
    }




    public function finalorder(Request $request)
    {


        $id = $request->order_id;
        $order = Order::find($id);

        $dubiapi =  Cards::where('id', $order->card_id)->first();
        if (!empty($order)) {


            $is_expired = $order->created_at->addMinutes(5);
            if ($is_expired < \Carbon\Carbon::now()) {

                return response()->json(['status' => 'error']);
            } else {
                $order->transaction_id = $request->transaction_id;
                $order->paid = 'true';
                $order->paymenttype = "معاملات";

                $allcompanyid = array();
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://taxes.like4app.com/online/check_balance/",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => array(
                        'deviceId' => 'cd63173e952e3076462733a26c71bbd0b236291db71656ec65ee1552478402ef',
                        'email' => 'info@bn-plus.ly',
                        'password' => 'db7d8028631f3351731cf7ca0302651d',
                        'securityCode' => 'cd63173e952e3076462733a26c71bbd077d972e07e1d416cb9ab7f87bfc0c014',
                        'langId' => '1'
                    ),

                ));

                $balancenational = curl_exec($curl);


                $dubiapi =  Cards::where('id', $order->card_id)->first();
                $clientdata =  Client::where('id', $order->client_id)->first();
                if ($dubiapi->api != 1 || $dubiapi->api2 != 1) {

                    $cardsanaia = Cards::where('id', $order->card_id)->first();

                    $cardsanaia = Cards::where('id', $order->card_id)->first();
                    $Anaiscards['id'] = $cardsanaia->id;
                   /* $itemcomp = cards_anais::firstOrNew(array('order_id' => $order->id));
                    $itemcomp->id = $cardsanaia->id;
                    $itemcomp->order_id =  $order->id;
                    $itemcomp->card_name = $cardsanaia->card_name;
                    $itemcomp->company_id = $cardsanaia->company_id;
                    $itemcomp->api =  $cardsanaia->api;
                    $itemcomp->card_price =  $cardsanaia->card_price;
                    $itemcomp->card_code = $cardsanaia->card_code;
                    $itemcomp->amounts = $cardsanaia->amounts;
                    $itemcomp->avaliable = $cardsanaia->avaliable;
                    $itemcomp->purchase =  $cardsanaia->purchase;
                    $itemcomp->card_image = $cardsanaia->card_image;
                    $itemcomp->nationalcompany = $cardsanaia->nationalcompany;
                    $itemcomp->productId = $cardsanaia->productId;
                    $itemcomp->enable = $cardsanaia->enable;
                    $itemcomp->api2 = $cardsanaia->api2;
                    $itemcomp->api2id = $cardsanaia->api2id;
                    $itemcomp->save();*/
                    
                    
                       $itemcomp['id'] =  $cardsanaia->id;
                            $itemcomp['order_id'] =  $order->id;
                            $itemcomp['card_name'] = $cardsanaia->card_name;
                              $itemcomp['company_id'] = $cardsanaia->company_id;
                            $itemcomp['api'] = $cardsanaia->api;
                            $itemcomp['card_price'] =$cardsanaia->card_price;
                              $itemcomp['card_code'] =$cardsanaia->card_code;
                            $itemcomp['amounts'] = $cardsanaia->amounts;
                            $itemcomp['avaliable'] = $cardsanaia->avaliable;
                            
                             $itemcomp['purchase'] =   $cardsanaia->purchase;
                            $itemcomp['card_image'] = $cardsanaia->card_image;
                            $itemcomp['nationalcompany'] =$cardsanaia->nationalcompany;
                              $itemcomp['productId'] = $cardsanaia->productId;
                            $itemcomp['enable'] =  $cardsanaia->enable;
                            $itemcomp['api2'] = $cardsanaia->api2;
                              $itemcomp['api2id'] =   $cardsanaia->api2id;
                          cards_anais::create($itemcomp);
                }


                if ($dubiapi->api == 1) {


                    if (isset($balancenational) && !empty($balancenational) && $balancenational != 'error code: 1020') {

                        ////////////dubai api///////////////

                        $client =  Client::where('id', $order->client_id)->first();
                        $curl = curl_init();
                        $refrenceid = "Merchant_" . rand();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://taxes.like4app.com/online/create_order",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => array(
                                'deviceId' => 'cd63173e952e3076462733a26c71bbd0b236291db71656ec65ee1552478402ef',
                                'email' => 'info@bn-plus.ly',
                                'password' => 'db7d8028631f3351731cf7ca0302651d',
                                'securityCode' => 'cd63173e952e3076462733a26c71bbd077d972e07e1d416cb9ab7f87bfc0c014',
                                'langId' => '1',
                                'productId' => $order->card_id,
                                'referenceId' => $refrenceid,
                                'time' => time(),
                                'hash' => $this->generateHash($clientdata->phone, $clientdata->email),

                            ),

                        ));

                        $createorder = curl_exec($curl);

                        $json = json_decode($createorder, true);
                        //  return $json['serials'];

                        foreach ($json['serials'] as $row) {
                            //  return $row['serialCode'];
                            $updatecardprice['card_code'] =  $this->decryptSerial($row['serialCode']);
                            Cards::where('id', $order->card_id)->update($updatecardprice);
                            //  $this->sendResetEmail($client->email, $this->decryptSerial($row['serialCode']), 'Your BNplus Code');


                            $cardsanaia = Cards::where('id', $order->card_id)->first();




                            $cardsanaia = Cards::where('id', $order->card_id)->first();
                            $Anaiscards['id'] = $cardsanaia->id;
                            //  $itemcomp = cards_anais::firstOrNew(array('id' => $cardsanaia->id));
                           /* $itemcomp = cards_anais::firstOrNew(array('order_id' => $order->id));
                            $itemcomp->id = $cardsanaia->id;
                            $itemcomp->order_id =  $order->id;
                            $itemcomp->card_name = $cardsanaia->card_name;
                            $itemcomp->company_id = $cardsanaia->company_id;
                            $itemcomp->api =  $cardsanaia->api;
                            $itemcomp->card_price =  $cardsanaia->card_price;
                            $itemcomp->card_code = $this->decryptSerial($row['serialCode']);
                            $itemcomp->amounts = $cardsanaia->amounts;
                            $itemcomp->avaliable = $cardsanaia->avaliable;
                            $itemcomp->purchase =  $cardsanaia->purchase;
                            $itemcomp->card_image = $cardsanaia->card_image;
                            $itemcomp->nationalcompany = $cardsanaia->nationalcompany;
                            $itemcomp->productId = $cardsanaia->productId;
                            $itemcomp->enable = $cardsanaia->enable;
                            $itemcomp->api2 = $cardsanaia->api2;
                            $itemcomp->api2id = $cardsanaia->api2id;
                            $itemcomp->save();*/
                            
                            
                              
                       $itemcomp['id'] =  $cardsanaia->id;
                            $itemcomp['order_id'] =  $order->id;
                            $itemcomp['card_name'] = $cardsanaia->card_name;
                              $itemcomp['company_id'] = $cardsanaia->company_id;
                            $itemcomp['api'] = $cardsanaia->api;
                            $itemcomp['card_price'] =$cardsanaia->card_price;
                              $itemcomp['card_code'] =$this->decryptSerial($row['serialCode']);
                            $itemcomp['amounts'] = $cardsanaia->amounts;
                            $itemcomp['avaliable'] = $cardsanaia->avaliable;
                            
                             $itemcomp['purchase'] =   $cardsanaia->purchase;
                            $itemcomp['card_image'] = $cardsanaia->card_image;
                            $itemcomp['nationalcompany'] =$cardsanaia->nationalcompany;
                              $itemcomp['productId'] = $cardsanaia->productId;
                            $itemcomp['enable'] =  $cardsanaia->enable;
                            $itemcomp['api2'] = $cardsanaia->api2;
                              $itemcomp['api2id'] =   $cardsanaia->api2id;
                          cards_anais::create($itemcomp);
                            
                            
                            
                        }


                        curl_close($curl);
                    } else {


                        return response()->json(['status' => 'error']);
                    }
                }

                if ($dubiapi->api2 == 1) {



                    $client =  Client::where('id', $order->client_id)->first();
                   // dd($client);
                    //   rand();

                    $uri = 'https://identity.anis.ly/connect/token';
                    $params = array(
                          'grant_type' => 'user_credentials',
            'client_id' => 'bn-plus',
          //  'client_secret' => '3U8F3U9C9IM39VJ39FUCLWLC872MMXOW8K2STWI28ZJD3ERF',
          //  'password' => 'P@ssw0rd1988',
          'client_secret'=>'VKOW0OR2wd893RFPQDM92i0q233HRFB8C2W87RGEetghkl9238',
            'password' => 'Mahdibnplus1988',
            'email' => 'info@bn-plus.ly'
                    );
                    $response = Http::asForm()->withHeaders([])->post($uri, $params);
                    $token = $response->json()['access_token'];
                    $token_type = $response->json()['token_type'];
                    $alltoken = $response->json()['token_type'] . ' ' . $response->json()['access_token'];

                    $orders = Http::withHeaders([
                        'Accept' => 'application/json',
                        'Authorization' => $alltoken,

                    ])->post(
                        'https://gateway.anis.ly/api/consumers/v1/order',
                        [


                            'walletId' => 'E1521F1F-C592-42F3-7A1A-08D9F31F6661',
                            'cardId' => $dubiapi->api2id,
                            'pinNumber' => '1988',
                            'orderId' => rand(),
                            'quantity' => 1,
                            'TotalValue' => $dubiapi->old_price,

                        ]

                    );

                    if (isset($orders->json()['data'])) {
                        //  dd($orders->json()['data']);
                        foreach ($orders->json()['data']['cards'] as $cardddds) {
                            $updatecardprssice['card_code'] = $cardddds['secretNumber'];
                            Cards::where('id', $order->card_id)->update($updatecardprssice);


                            $Anaiscodes['client_id'] = $order->client_id;
                            $Anaiscodes['card_code'] = $cardddds['secretNumber'];
                            $Anaiscodes['order_id'] = $order->id;


                            Anaiscodes::create($Anaiscodes);


                            $cardsanaia = Cards::where('id', $order->card_id)->first();
                            $Anaiscards['id'] = $cardsanaia->id;
                            //  $itemcomp = cards_anais::firstOrNew(array('id' => $cardsanaia->id));
                            /*$itemcomp = cards_anais::firstOrNew(array('order_id' => $order->id));

                             $itemcomp->id  = $cardsanaia->id;
                            $itemcomp->order_id =  $order->id;
                            $itemcomp->card_name = $cardsanaia->card_name;
                            $itemcomp->company_id = $cardsanaia->company_id;
                            $itemcomp->api =  $cardsanaia->api;
                            $itemcomp->card_price =  $cardsanaia->card_price;
                            $itemcomp->card_code = $cardddds['secretNumber'];
                            $itemcomp->amounts = $cardsanaia->amounts;
                            $itemcomp->avaliable = $cardsanaia->avaliable;
                            $itemcomp->purchase =  $cardsanaia->purchase;
                            $itemcomp->card_image = $cardsanaia->card_image;
                            $itemcomp->nationalcompany = $cardsanaia->nationalcompany;
                            $itemcomp->productId = $cardsanaia->productId;
                            $itemcomp->enable = $cardsanaia->enable;
                            $itemcomp->api2 = $cardsanaia->api2;
                            $itemcomp->api2id = $cardsanaia->api2id;
                             $itemcomp->save();
                            */
                              $itemcomp['id'] =  $cardsanaia->id;
                            $itemcomp['order_id'] =  $order->id;
                            $itemcomp['card_name'] = $cardsanaia->card_name;
                              $itemcomp['company_id'] = $cardsanaia->company_id;
                            $itemcomp['api'] = $cardsanaia->api;
                            $itemcomp['card_price'] =$cardsanaia->card_price;
                              $itemcomp['card_code'] =  $cardddds['secretNumber'];
                            $itemcomp['amounts'] = $cardsanaia->amounts;
                            $itemcomp['avaliable'] = $cardsanaia->avaliable;
                            
                             $itemcomp['purchase'] =   $cardsanaia->purchase;
                            $itemcomp['card_image'] = $cardsanaia->card_image;
                            $itemcomp['nationalcompany'] =$cardsanaia->nationalcompany;
                              $itemcomp['productId'] = $cardsanaia->productId;
                            $itemcomp['enable'] =  $cardsanaia->enable;
                            $itemcomp['api2'] = $cardsanaia->api2;
                              $itemcomp['api2id'] =   $cardsanaia->api2id;
                          cards_anais::create($itemcomp);

                           
                        }
                    }



                    $compurlcheck = 'https://gateway.anis.ly/api/consumers/v1/categories/cards/' . $dubiapi->api2id . '';

                    $cardschek = Http::withHeaders([
                        'Accept' => 'application/json',
                        'Authorization' => $alltoken,

                    ])->get($compurlcheck);


                    if (isset($cardschek->json()['data'])) {

                        if ($cardschek->json()['data']['inStock'] == false) {
                            $updatecard['purchase'] = 1;
                            $updatecard['avaliable'] = 1;
                            Cards::where('id', $order->card_id)->update($updatecard);
                        }
                    }
                }





                /////////////


                if ($order->update()) {
                    if ($dubiapi->api2 == 0) {
                        $updatecard['purchase'] = 1;
                        $updatecard['avaliable'] = 1;
                        Cards::where('id', $order->card_id)->update($updatecard);
                    }
                    $cardemail =  Cards::where('id', $order->card_id)->first();
                    $client =  Client::where('id', $order->client_id)->first();
                    if ($dubiapi->api == 0) {
                        //$this->sendResetEmail($client->email,  $cardemail->card_code, 'Your BNplus Code');
                    }

                    return response()->json(['status' => 'success']);
                } else {
                    return response()->json(['status' => 'error']);
                }
            }
        } else {
            return response()->json(['status' => 'error']);
        }
    }
    /*
    public function finalordernotdubai($request)
    {



        $id = $request->order_id;
        $order = Order::find($id);
        if (!empty($order)) {
            
            $order->transaction_id = $request->transaction_id;
            $order->paid = 'true';
            $order->paymenttype = "معاملات";

            /////////////
            if ($order->update()) {
                $updatecard['purchase'] = 1;
                $updatecard['avaliable'] = 1;
                Cards::where('id', $order->card_id)->update($updatecard);

                $cardemail =  Cards::where('id', $order->card_id)->first();
                $client =  Client::where('id', $order->client_id)->first();

                $this->sendResetEmail($client->email,  $cardemail->card_code, 'Your BNplus Code');


                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error']);
            }
        } else {
            return response()->json(['status' => 'error']);
        }
    }





    public function finalorder(Request $request)
    {


        $id = $request->order_id;
        $order = Order::find($id);
        if (!empty($order)) {
            $cards =   Cards::where('id', $order->card_id)->first();

            if ($cards->api == 1) {

                $this->finalorderdubai($request);
            } else {
                
                $this->finalordernotdubai($request);
            }
        } else {
            return response()->json(['status' => 'error']);
        }
    }*/



    public function sendResetEmail($user, $content, $subject)
    {

        $send =   Mail::send(
            'dashboard.Contacts.content',
            ['user' => $user, 'content' => $content, 'subject' => $subject],
            function ($message) use ($user, $subject) {
                $message->to($user);
                $message->subject("$subject");
            }
        );
    }

    function generateHash($phone, $mail)
    {
        $email = strtolower($mail);
        $key = hash('sha256', 't-3zafRa');
        $time = time();
        return hash('sha256', $time . $email . $phone . $key);
    }


    function decryptSerial($encrypted_txt)
    {
        $secret_key = 't-3zafRa';
        $secret_iv = 'St@cE4eZ';
        $encrypt_method = 'AES-256-CBC';
        $key = hash('sha256', $secret_key);

        //iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning          
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        return openssl_decrypt(base64_decode($encrypted_txt), $encrypt_method, $key, 0, $iv);
    }


    /////////////////Tlyncapi/////////////////


public function InitiateTlync(Request $request){


$allpricecomm=Paymentcommissionsmodel::where('companyname','معاملات')->first();

if($allpricecomm->status==0){
    $amountprice=round($request->amount + (($allpricecomm->commissions * $request->amount) /100),1) ;
}else{
        $amountprice=$request->amount;

}


    $response = Http::withHeaders([
        'Authorization' => 'Bearer oeKNF8MEnoTtcaCOAcr0XT7dYQgnLXUsiyTNyGvY',
        'Content-Type' => 'application/x-www-form-urlencoded'
    ])->post('https://c7drkx2ege.execute-api.eu-west-2.amazonaws.com/payment/initiate', [
        'id' => $request->id,
        'amount	' => $amountprice,
        'phone' => $request->phone,
        'email' => $request->email,
        'backend_url' => $request->backend_url,
        'frontend_url	' => $request->frontend_url	,
        'custom_ref' => $request->custom_ref,
    ]);
    return $response->json();

}


public function InitiatePayment(Request $request)
{
  //  id MBQKYkEgg27WdwDV6O49QnERrLJNqY8AYx8a5KMyXej1GzoAb3kBl0xmPz23mV0o
  
  $allpricecomm=Paymentcommissionsmodel::where('companyname','معاملات')->first();

  if($allpricecomm->status==0){
    $amountprice=round($request->amount + (($allpricecomm->commissions * $request->amount) /100),1);
}else{
        $amountprice=$request->amount;

}



    $curl = curl_init();
   
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://c7drkx2ege.execute-api.eu-west-2.amazonaws.com/payment/initiate',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => 'id= '.$request->id.'&amount='.$amountprice.'&phone='.$request->phone.'&email='.$request->email.'&backend_url='.$request->backend_url.'&custom_ref='.$request->custom_ref.'&frontend_url='.$request->frontend_url.'',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json',
        'Authorization: Bearer 0r14CIjDO0MDErCakVQsoohAvvyklGUvbxUAXpXg'
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    echo $response;

}


public function transactionPayment(Request $request)
{
  //  id MBQKYkEgg27WdwDV6O49QnERrLJNqY8AYx8a5KMyXej1GzoAb3kBl0xmPz23mV0o
    $curl = curl_init();
   
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://c7drkx2ege.execute-api.eu-west-2.amazonaws.com/receipt/transaction',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => 'store_id='.$request->store_id.'&transaction_ref='.$request->transaction_ref.'&custom_ref='.$request->custom_ref.'',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json',
        'Authorization: Bearer 0r14CIjDO0MDErCakVQsoohAvvyklGUvbxUAXpXg'
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    echo $response;

}


public function commissionmoa(Request $request)
{
  //  id MBQKYkEgg27WdwDV6O49QnERrLJNqY8AYx8a5KMyXej1GzoAb3kBl0xmPz23mV0o
  
  $allpricecomm=Paymentcommissionsmodel::where(array('companyname'=>'معاملات','status'=>0))->first();
if(!empty($allpricecomm)){
    $commission=$allpricecomm->commissions;
}else{
        $commission=0;
}
return $this->apiResponse88w($commission, 200);
}


public function commissions(Request $request)
{
  //  id MBQKYkEgg27WdwDV6O49QnERrLJNqY8AYx8a5KMyXej1GzoAb3kBl0xmPz23mV0o
  
  $allpricecomm=Paymentcommissionsmodel::where('status','0')->get();
if(!empty($allpricecomm)){
    return $this->apiResponse($allpricecomm, 200);
}else{
       return $this->apiResponse($allpricecomm, 400);
}

}









  public function companiessales(Request $request)
    {
        
        
       

  $type=$request->type;
  
  //dd($month2);
  if(isset($request->type)){
  if(isset($request->date1) & isset($request->date2)){
       $date1=$request->date1;
        $date2=$request->date2;
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        $interval = $datetime1->diff($datetime2);
        $days = $interval->format('%a');
      
        
         $result = CarbonPeriod::create($date1, '1 month', $date2);

    $diff = strtotime($date2, 0) - strtotime($date1, 0);
//echo floor($diff / 604800);


        $year1 = Carbon::createFromFormat('Y-m-d', $date1)->format('Y');
        $year2 = Carbon::createFromFormat('Y-m-d', $date2)->format('Y');

 $month1 = Carbon::createFromFormat('Y-m-d', $date1)->format('Y-m');
        $month2 = Carbon::createFromFormat('Y-m-d', $date2)->format('Y-m');
  
  if(  $type=="DAY"){
                 $orders=    DB::select("select sum(card_price) as x,updated_at y FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID'))  and updated_at between '$request->date1' and '$request->date2'
                                       and updated_at between '$request->date1' and '$request->date2'
                                       
                                       and paid='true' GROUP BY  updated_at order by updated_at");
                                       
                                       
                                       $totorders=    DB::select("select sum(card_price) as total FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID')) 
                                       and updated_at between '$request->date1' and '$request->date2'
                                        and paid='true'"); }
                                       
                                       
                                  
                                  
                                   if( $type=="YEAR"){
                 $orders=    DB::select("select sum(card_price) as x,
                  CONVERT(YEAR(updated_at), CHAR)
                 y FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID'))  and updated_at between '$request->date1' and '$request->date2'
                                       and YEAR(updated_at) between '$year1' and '$year2'
                                       
                                       and paid='true' GROUP BY  YEAR(updated_at) order by updated_at");
                                       
                                       
                                       $totorders=    DB::select("select sum(card_price) as total FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID')) 
                                       and YEAR(updated_at) between '$year1' and '$year2'
                                        and paid='true'"); }
                                        
                                        
                                        
                                           if( $type=="MONTH"){
                 $orders=    DB::select("select sum(card_price) as x,DATE_FORMAT(updated_at, '%Y-%m') y FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID'))  and updated_at between '$request->date1' and '$request->date2'
                                       and DATE_FORMAT(updated_at, '%Y-%m') between '$month1' and '$month2'
                                       
                                       and paid='true' GROUP BY  DATE_FORMAT(updated_at, '%Y-%m') order by updated_at");
                                       
                                       
                                       $totorders=    DB::select("select sum(card_price) as total FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID')) 
                                         and DATE_FORMAT(updated_at, '%Y-%m') between '$month1' and '$month2'
                                        and paid='true'"); }
                                        
                                       
      
        if(  $type=="WEEK"){
                 $orders=    DB::select("select sum(card_price) as x,
                  CONVERT(WEEK(updated_at), CHAR)
               y FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID'))  and updated_at between '$request->date1' and '$request->date2'
                                       
                                       and paid='true' GROUP BY  WEEK(updated_at) order by updated_at");
                                       
                                       
                                       $totorders=    DB::select("select sum(card_price) as total FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID')) 
                                         and updated_at between '$request->date1' and '$request->date2'
                                        and paid='true'"); }
                                        
    }
                                        
                                        
                                        
       else{
              
              
              
  if(  $type=="DAY"){
                 $orders=    DB::select("select sum(card_price) as x,updated_at y FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID'))
                                       
                                       and paid='true' GROUP BY  updated_at order by updated_at");
                                       
                                       
                                       $totorders=    DB::select("select sum(card_price) as total FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID')) 
                                       
                                        and paid='true'"); }
                                       
                                       
                                  
                                  
                                   if( $type=="YEAR"){
                 $orders=    DB::select("select sum(card_price) as x,
                 CONVERT(YEAR(updated_at), CHAR)
                  y FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID'))  
                                       
                                       and paid='true' GROUP BY  YEAR(updated_at) order by updated_at");
                                       
                                       
                                       $totorders=    DB::select("select sum(card_price) as total FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID')) 
                                      
                                        and paid='true'"); }
                                        
                                        
                                        
                                           if( $type=="MONTH"){
                 $orders=    DB::select("select sum(card_price) as x,DATE_FORMAT(updated_at, '%Y-%m') 
                 
                 
                 y FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID'))  
                                      
                                
                                       and paid='true' GROUP BY  DATE_FORMAT(updated_at, '%Y-%m') order by updated_at");
                                       
                                       
                                       $totorders=    DB::select("select sum(card_price) as total FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID')) 
                                       
                                        and paid='true'"); }
                                        
                                       
      
        if(  $type=="WEEK"){
                 $orders=    DB::select("select sum(card_price) as x,  CONVERT(WEEK(updated_at), CHAR)  y FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID'))  
                                       
                                       and paid='true' GROUP BY  WEEK(updated_at) order by updated_at");
                                       
                                       
                                       $totorders=    DB::select("select sum(card_price) as total FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID')) 
                                        
                                        and paid='true'"); }
             
             
             
                                       
       }}else{
           
           
           
                $orders=    DB::select("select sum(card_price) as x,updated_at y FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID'))
                                       
                                       and paid='true' GROUP BY  updated_at order by updated_at");
                                       
                                       
                                       $totorders=    DB::select("select sum(card_price) as total FROM `orders` WHERE card_id in (SELECT id from cards where company_id in (SELECT id from companies where
                                       AcountID='$request->AcountID')) 
                                       
                                        and paid='true'");
           
       }
       
     $companyname= Company::where('AcountID',$request->AcountID)->first();
     if(!empty($totorders)){
         $totals=$totorders[0]->total;
     }else{
          $totals=0;
         
     }
        
                                       
                                       
                                       
     if(!empty($companyname)){
         $company='مبيعات شركه '.$companyname->name;
     }else{
         $company="";
     }
            return $this->apiResponsesales($orders,$totals, $company, 200);



    }









  public function localcompanies(Request $request)
    {

      $companies= Company::where('kind','local')->get();


       

            return $this->apiResponse($companies, '', 200);



    }






}
