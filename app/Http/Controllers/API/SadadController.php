<?php

namespace App\Http\Controllers\API;
use App\Paymentcommissionsmodel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Order;
use App\Cards;
use App\Anaiscodes;
use App\cards_anais;
use App\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class SadadController extends Controller
{
    use ApiResourceTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */





    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function verify(Request $request)
    {

        $card = Cards::where(array('id' => $request->card_id, 'avaliable' => 0, 'purchase' => 0, 'enable' => 0))->orderBy('id', 'desc')->first();
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








$allpricecomm=Paymentcommissionsmodel::where('companyname','سداد')->first();

if($allpricecomm->status==0){
    $amountprice=round($request->amount + (($allpricecomm->commissions * $request->amount) /100),1);
}else{
        $amountprice=$request->amount;

}
                    $response = Http::withHeaders([
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYjk3NTMxYzJkNDkzMjdhMzAwNmRjN2NiOTc4NTRlODFjMWMwYzVkYWMzN2UyNzhhZjViMjYyNmFmMTE5YjVjMDMxZTQzNGU0NDE3ODFlYjkiLCJpYXQiOjE2NDQzNTU0NjEsIm5iZiI6MTY0NDM1NTQ2MSwiZXhwIjoxNzcwNTg1ODYxLCJzdWIiOiI3Iiwic2NvcGVzIjpbXX0.s0Yat6614IuR3jMJ0njo4-50DzfSjCd5tASebIDUyUP_O_wxFp4ed3av1Dari_xDv4OBn23wjoIURUOSkuVGSz84sLTbkWrv418CzZ-ygxXHeQoyZ-JUXGbk8-1A35SEJbBQdjPI8svlVs2UL_RTlQarZbDLDMtXH5heCsf3sf0nuK79zY_bhFFAZD882P3uViYnD-YcecRGFxjmxVz3vrShspwskg-kwM1sIrmLD95lRg7n7ZJItGCyaXDC27XJuVZUhmtCLA48iFBSBoTdk1NE_5pGiWn0UOzwvdbxWfKioQoeBrdP-wVJF9MDklahycPI4wN1ooKiSeeFL3xtBHSwjpk8GP1_y3UZZl99ANlR7j_jgKj8g_VH-w3m6I8dSTkbSvclBXY8joowgguOWkn4R3QV1hQtH4w-nf_14wV90hJE1O1NNEyQ3smidSSdQp0Qd_vlTqYOTgJPzlvkERxW-T2efJ9uM_TJFRPnXbSiLugC0AIIJw9GkBDAtUEhFKazYpRX4r45bOaOUKQtO65FFf_h40MBp-0DiTL6VIZX0X-jSxeAZ75ilBQVl7TUF_-zx5YsIN2xRLqgC97aqIe80rViUFARqWAQNQFCQFfe8Z7igpb0t4L49ZJ4JykktG03k53HZN4W2GZPOT2RdI2fgQVcytXza1VfXYmU2xo',
          //  'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1lIjoiU2FkYWRQR1ciLCJjb3Jwb3JhdGVTZXJ2aWNlSWQiOjEwNjIsImFwcElkIjoiY2YxOWJiMmM2ZjFkNDBhYjhlNDVkMDI1NzFjNmRkYmMiLCJleHAiOjE2NjQ3OTM2NDcsImlzcyI6IkNvcmV0ZWMiLCJhdWQiOiJTQURBRFBHVyJ9.N17G2MDgAaiMcQzZ2yQ1jC4MZnezZHAJbD18Fqh-rJ8',
                        'X-API-KEY' => '984adf4c-44e1-418f-829b'
                    ])->post('https://api.plutus.ly/api/v1/transaction/sadadapi/verify', [
                        'mobile_number' => $request->mobile_number,
                        'birth_year' => $request->birth_year,
                        //'amount' => $request->amount
                      'amount' => $amountprice
                    ]);

                    //return $response;
                    $card = Cards::where(array('id' => $request->card_id, 'avaliable' => 0, 'purchase' => 0, 'enable' => 0))->orderBy('id', 'desc')->first();
                    if (!empty($card)) {


                        if (isset($response['error'])) {

                            return $this->apiResponse4(false, $response['error']['message'], $response['error']['status']);
                        } else {
                            $process_id = $response['result']["process_id"];

                            $request_data['card_id'] = $card->id;

  $sadadamout = $response['result']["amount"];
   $request_data['sadadamout'] = $sadadamout;
                            $request_data['client_id'] = $request->client_id;
                            $request_data['card_price'] = $request->amount;
                            $request_data['client_name'] = $request->client_name;
                            $request_data['client_number'] = $request->client_number;
                            $request_data['process_id'] = "$process_id";
                            $request_data['invoice_no'] = rand();
                            $request_data['paymenttype'] = "سداد";
                            // $order->invoice_no = rand();


                            $order = Order::create($request_data);
                            if ($card->api2 != 1) {
                                $dataa['avaliable'] = 1;
                                Cards::where('id', $order->card_id)->update($dataa);
                            }
                            return $this->apiResponse5(true, $response['message'], $response['status'], $response['result'], $order->id);
                        }
                    } else {
                        return $this->apiResponse4(false, 'No Avaliable Cards for this price', 400);
                    }
                }
            }
        } else {





$allpricecomm=Paymentcommissionsmodel::where('companyname','سداد')->first();

if($allpricecomm->status==0){
    $amountprice=round($request->amount + (($allpricecomm->commissions * $request->amount) /100),1);
}else{
        $amountprice=$request->amount;

}

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYjk3NTMxYzJkNDkzMjdhMzAwNmRjN2NiOTc4NTRlODFjMWMwYzVkYWMzN2UyNzhhZjViMjYyNmFmMTE5YjVjMDMxZTQzNGU0NDE3ODFlYjkiLCJpYXQiOjE2NDQzNTU0NjEsIm5iZiI6MTY0NDM1NTQ2MSwiZXhwIjoxNzcwNTg1ODYxLCJzdWIiOiI3Iiwic2NvcGVzIjpbXX0.s0Yat6614IuR3jMJ0njo4-50DzfSjCd5tASebIDUyUP_O_wxFp4ed3av1Dari_xDv4OBn23wjoIURUOSkuVGSz84sLTbkWrv418CzZ-ygxXHeQoyZ-JUXGbk8-1A35SEJbBQdjPI8svlVs2UL_RTlQarZbDLDMtXH5heCsf3sf0nuK79zY_bhFFAZD882P3uViYnD-YcecRGFxjmxVz3vrShspwskg-kwM1sIrmLD95lRg7n7ZJItGCyaXDC27XJuVZUhmtCLA48iFBSBoTdk1NE_5pGiWn0UOzwvdbxWfKioQoeBrdP-wVJF9MDklahycPI4wN1ooKiSeeFL3xtBHSwjpk8GP1_y3UZZl99ANlR7j_jgKj8g_VH-w3m6I8dSTkbSvclBXY8joowgguOWkn4R3QV1hQtH4w-nf_14wV90hJE1O1NNEyQ3smidSSdQp0Qd_vlTqYOTgJPzlvkERxW-T2efJ9uM_TJFRPnXbSiLugC0AIIJw9GkBDAtUEhFKazYpRX4r45bOaOUKQtO65FFf_h40MBp-0DiTL6VIZX0X-jSxeAZ75ilBQVl7TUF_-zx5YsIN2xRLqgC97aqIe80rViUFARqWAQNQFCQFfe8Z7igpb0t4L49ZJ4JykktG03k53HZN4W2GZPOT2RdI2fgQVcytXza1VfXYmU2xo',
                'X-API-KEY' => '984adf4c-44e1-418f-829b'
            ])->post('https://api.plutus.ly/api/v1/transaction/sadadapi/verify', [
                'mobile_number' => $request->mobile_number,
                'birth_year' => $request->birth_year,
                'amount' => $amountprice
            ]);

            //return $response;
            $card = Cards::where(array('id' => $request->card_id, 'avaliable' => 0, 'purchase' => 0, 'enable' => 0))->orderBy('id', 'desc')->first();
            if (!empty($card)) {


                if (isset($response['error'])) {

                    return $this->apiResponse4(false, $response['error']['message'], $response['error']['status']);
                } else {
                    $process_id = $response['result']["process_id"];
                  
                    $request_data['card_id'] = $card->id;
  $sadadamout = $response['result']["amount"];
                    $request_data['client_id'] = $request->client_id;
                    $request_data['card_price'] = $request->amount;
                    $request_data['client_name'] = $request->client_name;
                    $request_data['client_number'] = $request->client_number;
                    $request_data['process_id'] = $process_id;
                    $request_data['sadadamout'] = $sadadamout;
                    $request_data['invoice_no'] = rand();
                    $request_data['paymenttype'] = "سداد";
                    // $order->invoice_no = rand();


                    $order = Order::create($request_data);
                    if ($card->api2 != 1) {
                        $dataa['avaliable'] = 1;
                        Cards::where('id', $order->card_id)->update($dataa);
                    }
                    return $this->apiResponse5(true, $response['message'], $response['status'], $response['result'], $order->id);
                }
            } else {
                return $this->apiResponse4(false, 'No Avaliable Cards for this price', 400);
            }
        }





        // dd($response );
    }



    public function confirm(Request $request)
    {


        $idfirst = $request->order_id;
        $orderfirst = Order::find($idfirst);
        
       
        if (!empty($orderfirst)) {

            $cards =   Cards::where('id', $orderfirst->card_id)->first();

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


$allpricecomm=Paymentcommissionsmodel::where('companyname','سداد')->first();

if($allpricecomm->status==0){
    $amountprice=round($request->amount + (($allpricecomm->commissions * $request->amount) /100),1);
}else{
        $amountprice=$request->amount;

}


            $balancenational = curl_exec($curl);

       


                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYjk3NTMxYzJkNDkzMjdhMzAwNmRjN2NiOTc4NTRlODFjMWMwYzVkYWMzN2UyNzhhZjViMjYyNmFmMTE5YjVjMDMxZTQzNGU0NDE3ODFlYjkiLCJpYXQiOjE2NDQzNTU0NjEsIm5iZiI6MTY0NDM1NTQ2MSwiZXhwIjoxNzcwNTg1ODYxLCJzdWIiOiI3Iiwic2NvcGVzIjpbXX0.s0Yat6614IuR3jMJ0njo4-50DzfSjCd5tASebIDUyUP_O_wxFp4ed3av1Dari_xDv4OBn23wjoIURUOSkuVGSz84sLTbkWrv418CzZ-ygxXHeQoyZ-JUXGbk8-1A35SEJbBQdjPI8svlVs2UL_RTlQarZbDLDMtXH5heCsf3sf0nuK79zY_bhFFAZD882P3uViYnD-YcecRGFxjmxVz3vrShspwskg-kwM1sIrmLD95lRg7n7ZJItGCyaXDC27XJuVZUhmtCLA48iFBSBoTdk1NE_5pGiWn0UOzwvdbxWfKioQoeBrdP-wVJF9MDklahycPI4wN1ooKiSeeFL3xtBHSwjpk8GP1_y3UZZl99ANlR7j_jgKj8g_VH-w3m6I8dSTkbSvclBXY8joowgguOWkn4R3QV1hQtH4w-nf_14wV90hJE1O1NNEyQ3smidSSdQp0Qd_vlTqYOTgJPzlvkERxW-T2efJ9uM_TJFRPnXbSiLugC0AIIJw9GkBDAtUEhFKazYpRX4r45bOaOUKQtO65FFf_h40MBp-0DiTL6VIZX0X-jSxeAZ75ilBQVl7TUF_-zx5YsIN2xRLqgC97aqIe80rViUFARqWAQNQFCQFfe8Z7igpb0t4L49ZJ4JykktG03k53HZN4W2GZPOT2RdI2fgQVcytXza1VfXYmU2xo',
                    'X-API-KEY' => '984adf4c-44e1-418f-829b'
                ])->post('https://api.plutus.ly/api/v1/transaction/sadadapi/confirm', [

                    'process_id' => $orderfirst->process_id,
                    'code' => $request->code,
                    'amount' => $orderfirst->sadadamout,
                    'invoice_no' => $orderfirst->invoice_no,
                  //  'customer_ip' => $request->customer_ip,

                ]);
                if (isset($response['error'])) {
                    return $this->apiResponse4(false, $response['error']['message'], $response['error']['status']);
                } else {
                    $id = $request->order_id;
                    $order = Order::find($id);
                    if (!empty($order)) {
                        $order->transaction_id = $response['result']['transaction_id'];
                        $order->paid = 'true';
                        $order->paymenttype = "سداد";

                        ////////////dubai api///////////////
                        $dubiapi =  Cards::where('id', $order->card_id)->first();
                        $clientdata =  Client::where('id', $order->client_id)->first();

                        if ($dubiapi->api == 1) {


                            if (isset($balancenational) && !empty($balancenational) && $balancenational != 'error code: 1020') {


                         


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
                                
                                
                                      $cardsanaia= Cards::where('id',$order->card_id)->first();

    $cardsanaia = Cards::where('id', $order->card_id)->first();
                            $Anaiscards['id'] = $cardsanaia->id;
                           //$itemcomp = cards_anais::firstOrNew(array('id' => $cardsanaia->id));
                         /*  $itemcomp = cards_anais::firstOrNew(array('order_id' => $order->id));

                           $itemcomp->id = $cardsanaia->id;
                           $itemcomp->order_id =  $order->id;
                           $itemcomp->card_name = $cardsanaia->card_name;
                           $itemcomp->company_id = $cardsanaia->company_id;
                           $itemcomp->api =  $cardsanaia->api;
                           $itemcomp->card_price =  $cardsanaia->card_price;
                           $itemcomp->card_code =$this->decryptSerial($row['serialCode']);
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
                           
                           
                                //   $this->sendResetEmail($client->email, $this->decryptSerial($row['serialCode']), 'Your BNplus Code');
                            }

                            curl_close($curl);


                        } else {
                            return $this->apiResponse4(false, 'error in connection ', 400);
                        }


                        }
                        
                        if($dubiapi->api !=1 || $dubiapi->api2 !=1){
                        
                               $cardsanaia= Cards::where('id',$order->card_id)->first();
                                  
                                        $cardsanaia = Cards::where('id', $order->card_id)->first();
                            $Anaiscards['id'] = $cardsanaia->id;
                          // $itemcomp = cards_anais::firstOrNew(array('id' => $cardsanaia->id));
                         /*  $itemcomp = cards_anais::firstOrNew(array('order_id' => $order->id));

                           $itemcomp->id = $cardsanaia->id;
                           $itemcomp->order_id =  $order->id;
                           $itemcomp->card_name = $cardsanaia->card_name;
                           $itemcomp->company_id = $cardsanaia->company_id;
                           $itemcomp->api =  $cardsanaia->api;
                           $itemcomp->card_price =  $cardsanaia->card_price;
                           $itemcomp->card_code =$cardsanaia->card_code;
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


                        if ($dubiapi->api2 == 1) {
                            $client =  Client::where('id', $order->client_id)->first();
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
                                    'orderId' => $id,
                                    'quantity' => 1,
                                    'TotalValue' => $dubiapi->old_price,

                                ]

                            );


                            if (isset($orders->json()['data'])) {

                              
                             
                                foreach($orders->json()['data']['cards'] as $cardddds){
                                    $updatecardprssice['card_code'] =$cardddds['secretNumber'];
                                    Cards::where('id',$order->card_id)->update($updatecardprssice);
    
    
                                    $Anaiscodes['client_id'] = $order->client_id;
                                    $Anaiscodes['card_code'] = $cardddds['secretNumber'];
                                    $Anaiscodes['order_id'] = $order->id;
                            
                        
                                   Anaiscodes::create($Anaiscodes);

                                   $cardsanaia= Cards::where('id',$order->card_id)->first();

                                   
                                   
                                   
                                       $cardsanaia = Cards::where('id', $order->card_id)->first();
                            $Anaiscards['id'] = $cardsanaia->id;
                        //   $itemcomp = cards_anais::firstOrNew(array('id' => $cardsanaia->id));
                         /*  $itemcomp = cards_anais::firstOrNew(array('order_id' => $order->id));

                           $itemcomp->id = $cardsanaia->id;
                           $itemcomp->order_id =  $order->id;
                           $itemcomp->card_name = $cardsanaia->card_name;
                           $itemcomp->company_id = $cardsanaia->company_id;
                           $itemcomp->api =  $cardsanaia->api;
                           $itemcomp->card_price =  $cardsanaia->card_price;
                           $itemcomp->card_code =$cardddds['secretNumber'];
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
                              $itemcomp['card_code'] =$cardddds['secretNumber'];
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

                        ////////////////////////////////////////////////////////

                        if ($order->update()) {
                            if ($dubiapi->api2 == 0) {
                                $updatecard['purchase'] = 1;
                                $updatecard['avaliable'] = 1;
                                Cards::where('id', $order->card_id)->update($updatecard);
                            }
                            $cardemail =  Cards::where('id', $order->card_id)->first();
                            $client =  Client::where('id', $order->client_id)->first();

                            if ($dubiapi->api == 0) {
                                //    $this->sendResetEmail($client->email,  $cardemail->card_code, 'Your BNplus Code');
                            }

//return $response['status'];

                            return $this->apiResponse5(true, 'success', 200,'success', $order->id);


/*if(isset($response['status'])){
                                return $this->apiResponse5(true, $response['message'], $response['status'], $response['result']);

}else{
                               // return response()->json(['status' => 'error']);
                                return $this->apiResponse5(true, $response['message'], $response['status'], $response['result']);

}*/
                        } else {
                            return response()->json(['status' => 'error']);
                        }
                    } else {
                        return response()->json(['status' => 'error']);
                    }
                }
           
        } else {
            return $this->apiResponse4(false, 'no Order for this order id', 400);
        }
    }

  
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
}
