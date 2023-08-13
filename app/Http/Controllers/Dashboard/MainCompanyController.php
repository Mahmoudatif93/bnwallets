<?php

namespace App\Http\Controllers\Dashboard;

use App\MainCompany;
use App\Cards;
use App\Anaiscodes;
use App\cards_anais;
use App\Order;
use App\Client;
use App\Order_anais;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use PDF2;
use App\Currency;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Contract\Firestore;
use Google\Cloud\Firestore\FirestoreClient;
use DB;
use App\currancylocal;

class MainCompanyController extends Controller
{
  

    public function index(Request $request)
    {

        ini_set("prce.backtrack_limit", "100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");

        $Companies = MainCompany::where('enable', 0)->when($request->search, function ($q) use ($request) {

            return $q->where('name', 'like', '%' .  $request->search . '%')
                ->orWhere('kind', 'like', '%' . $request->search . '%');
        })->latest()->paginate(5);

        return view('dashboard.MainCompanies.index', compact('Companies'));
    } //end of index

    public function create()
    {
        return view('dashboard.MainCompanies.create');
    } //end of create

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'kind' => 'required',
        ];
        $request->validate($rules);
        $request_data = $request->all();
       if ($request->company_image) {
          $dd= 
            
               Image::make($request->company_image)
                    ->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->save(public_path('uploads/Maincompany/' . $request->company_image->hashName()));
    
             $request_data['company_image'] = 'https://bn-plus.ly/BNplus/public/uploads/Maincompany/'.$request->company_image->hashName();
             
             
        } //end of if

        MainCompany::create($request_data);
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.MainCompanies.index');
    } //end of store

    public function edit($id)
    {
        $category = MainCompany::where('id', $id)->first();
        
        return view('dashboard.MainCompanies.edit', compact('category'));
    } //end of edit

    public function update(Request $request, $id)
    {
        $category = MainCompany::where('id', $id)->first();


        $request_data = $request->except(['_token', '_method']);
        if ($request->company_image) {

            if ($category->company_image != '') {

                Storage::disk('public_uploads')->delete('/Maincompany/' . $category->company_image);
            } //end of if

            Image::make($request->company_image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('uploads/Maincompany/' . $request->company_image->hashName()));

            $request_data['company_image'] = 'https://bn-plus.ly/BNplus/public/uploads/Maincompany/' . $request->company_image->hashName();
        } //end of if



        MainCompany::where('id', $id)->update($request_data);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.MainCompanies.index');
    } //end of update

    public function destroy($id)
    {
        $category = MainCompany::where('id', $id)->first();
        if ($category->company_image != '') {

            Storage::disk('public_uploads')->delete('/Maincompany/' . $category->company_image);
        } //end of if

        MainCompany::where('id', $id)->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.MainCompanies.index');
    } //end of destroy



    
}//end of controller
