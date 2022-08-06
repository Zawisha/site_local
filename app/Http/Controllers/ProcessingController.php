<?php

namespace App\Http\Controllers;

use App\Models\BackupCustomers;
use App\Models\Customers;
use App\Models\OldClients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProcessingController extends Controller
{
    public function get_processing()
    {
        $customers = Customers::where('used','=',0)->limit(3000)->get();
        $count = Customers::where('used','=',0)->count();

        return response()->json([
            'status' => 'success',
            'count' => $count,
            'customers'=>$customers
        ], 201);
    }
    public function delete_cust(Request $request)
    {
//        $id_client=Customers::where('id', '>', '8300')->update(['used' =>0]);
        Customers::where('id', '=', $request->input('id'))->update(['used' =>1]);
    }
    public function add_cust(Request $request)
    {
        $id_client=Customers::where('id', '=', $request->input('id'))->get('cust_id');
        Customers::where('id', '=', $request->input('id'))->update(['used' =>1]);
        for ($i = 0; $i <= 2; $i++) {
            OldClients::create([
                'user_id' => $id_client[0]['cust_id'],
            ]);
        }
        BackupCustomers::create([
            'text' => $request->input('text'),
        ]);
        Storage::append('/result.txt', $request->input('text'));
        Storage::append('/result.txt', '======================================================================================');
        Storage::append('/result.txt', '');
    }
}
