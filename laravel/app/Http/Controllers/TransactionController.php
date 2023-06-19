<?php

namespace App\Http\Controllers;

use App\Helpers\CollectionHelper;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\VcardResource;
use App\Models\Transaction;
use App\Models\Vcard;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function getStatistics()
    {
        $transactions[0] = DB::table('transactions')
            ->select('payment_type', DB::raw('count(*) as total'))
            ->groupBy('payment_type')
            ->get();
        $transactions[1]  = DB::table('transactions')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('name', DB::raw('count(*) as total'))
            ->groupBy('name')
            ->get();
        return $transactions;
    }


    public function getOne(Vcard $vcard, Transaction $transaction)
    {
        return new TransactionResource($transaction);
    }

    public function getVcardTransactions(Vcard $vcard, Request $request) {
        return TransactionResource::collection($vcard->transactions);
    }

    public function create(Vcard $vcard, StoreTransactionRequest $request)
    {
        $transaction = new Transaction();
        $transaction->fill($request->validated());
        $vcard->balance = $transaction->new_balance;
        $vcard->save();
        $transaction->save();


        if ($transaction->payment_type == "VCARD") {
            //So vai funcionar com o vcard -> payment reference vai ser sempre um numero de telemovel
            $payment_reference = $transaction->payment_reference;

            $pair_vcard = new Vcard();
            $pair_vcard = $pair_vcard->find($payment_reference); //devolve o objeto todo com o numero de telemovel

            $pair_transaction = new Transaction();
            //dump($pair_vcard->balance);
            //dump($pair_transaction);
            $pair_transaction->vcard = $pair_vcard->phone_number;
            $pair_transaction->date = $transaction->date;
            $pair_transaction->datetime = $transaction->datetime;
            $pair_transaction->type = "C";
            $pair_transaction->old_balance = $pair_vcard->balance;
            $pair_transaction->new_balance = $pair_vcard->balance + $transaction->value;
            //$pair_transaction->max_debit = $pair_vcard->max_debit;
            //$pair_transaction->balance = $pair_transaction->old_balance;
            $pair_transaction->pair_vcard = $transaction->vcard;
            $pair_transaction->pair_transaction = $transaction->id;
            $pair_transaction->value = $transaction->value;
            $pair_transaction->payment_type = $transaction->payment_type;
            $pair_transaction->payment_reference = $transaction->vcard;
            $pair_vcard->balance = $pair_transaction->new_balance;
            //dump($transaction);
            //dd($pair_transaction);
            //$pair_transaction->fill($pair_transaction->validated());
            $pair_transaction->save();
            $transaction->pair_vcard = $pair_transaction->vcard;
            $transaction->pair_transaction = $pair_transaction->id;
            $transaction->save();
            $pair_vcard->save();
            
        }



        return new TransactionResource($transaction);
    }

    public function update(Vcard $vcard, UpdateTransactionRequest $request, Transaction $transaction)
    {
        //Recebemos o vcard pq é parametro na rota, mas nao o utilizamos
        $transaction->update($request->validated());
        return new TransactionResource($transaction);
    }
}
