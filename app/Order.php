<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['card_id', 'client_id','transaction_id','card_price','paymenttype',
    'client_name','client_number','paid','invoice_no','process_id','sadadamout','cobon_code'];




        protected $casts = [

        'card_price' => 'double'
        
    ];
    
    
    public function cardss()
    {
        return $this->belongsTo(Cards::class,'card_id','id');

    }//end of cards

    public function client()
    {
        return $this->belongsTo(Client::class);

    }//end of cards
    
        public function cards()
    {
       return $this->belongsTo(cards_anais::class,'id','order_id');
        
        

    
    }//end of cards



}
