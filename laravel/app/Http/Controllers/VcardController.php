<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteVcardRequest;
use App\Http\Requests\UpdateVcardRequest;
use App\Http\Requests\StoreVcardRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\VcardResource;
use App\Models\Category;
use App\Models\DefaultCategory;
use App\Models\Vcard;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;



class VcardController extends Controller
{

    public function getAll()
    {
        return VcardResource::collection(Vcard::all());
    }

    public function getOne(Vcard $vcard)
    {
        return new VcardResource($vcard);
    }

    public function getBalance(Vcard $vcard)
    {
        $vcard = new VcardResource($vcard);
        return collect($vcard)->get('balance');
    }


    public function create(StoreVcardRequest $request)
    {


        $vcard = new Vcard();
        $vcard->fill($request->validated());
        $vcard->password = bcrypt($request->password);
        $vcard->confirmation_code = bcrypt($request->confirmation_code);
        $vcard->balance = 0;
        $vcard->max_debit = 5000;
        $vcard->blocked = 0;

        if ($vcard->photo_url != null) { //uploadPhoto  
            $path = $request->file('photo_url')->storeAs('public/fotos', $vcard->phone_number . '.jpg');
            $vcard->fill(['photo_url' => basename($path)]);
        }



        $vcard->save();
        $defaultCategories = DefaultCategory::all();

        foreach ($defaultCategories as $defaultCat) {
            $category = new Category();
            $category->name = $defaultCat->name;
            $category->type = $defaultCat->type;
            $category->vcard = $request->phone_number;
            $category->save();
        }
        return new VcardResource($vcard);
    }

    public function updatePhoto(Request $request, Vcard $vcard)
    {
        if ($request->has('file')) {
            $old_photo_value = $vcard->photo_url;
            $path = $request->file('file')->storeAs('public/fotos', $vcard->phone_number . date('YmdHis') . '.jpg');
            $vcard->fill(['photo_url' => basename($path)]);
            $vcard->save();
            if ($old_photo_value != null) {
                Storage::delete('public/fotos/' . $old_photo_value);
            }
        }
    }

    public function update(UpdateVcardRequest $request, Vcard $vcard)
    {
        //return $request->all();
        //$request->validated();
        $vcard->fill([
            'name' => $request->name,
            'email' => $request->email,
            'confirmation_code' => $request->confirmation_code,
            'photo_url' => $request->photo_url
        ]);
        if ($request->new_password) { //se se quiser fazer update à password
            if (Hash::check($request->old_password, $vcard->password)) {
                $vcard->fill(['password' => bcrypt($request->new_password)]);
            }
        }
        if ($request->new_confirmation_code) { //se se quiser fazer update ao confirmation code
            if (Hash::check($request->old_password, $vcard->password)) {
                $vcard->fill(['confirmation_code' => bcrypt($request->new_confirmation_code)]);
            }
        }

        if ($request->new_photo_url != null) { //uploadPhoto  
            $old_photo_value = $vcard->photo_url;
            $path = $request->file('new_photo_url')->storeAs('public/fotos', $vcard->phone_number . date('YmdHis') . '.jpg');
            $vcard->fill(['photo_url' => basename($path)]);
            if ($old_photo_value != null) {
                Storage::delete('public/fotos/' . $old_photo_value);
            }
        }

        $vcard->save();
        return new VcardResource($vcard);
    }

    public function delete(Request $request, Vcard $vcard)
    {
        if ($vcard->balance == 0) {
            if ($request->credential['isAdmin'] == false) {

                if (!(Hash::check($request->credential['pin'], $vcard->confirmation_code) && Hash::check($request->credential['passwordUser'], $vcard->password))) {
                    return response()->json(['errors' => "Invalid Credentials",], 422);
                }
            }

            $haveTransaction = DB::table('transactions')->where('vcard', $vcard->phone_number)->first() != null ? true : false;
            $haveTransactionPair = DB::table('transactions')->where('pair_vcard', $vcard->phone_number)->first() != null ? true : false;

            if (!$haveTransaction && !$haveTransactionPair) {
                $vcard->categories()->forceDelete();
                $vcard->forceDelete();
            } else {
                //softDelete VCARD
                $vcard->categories()->delete();
                $vcard->delete();
            }

            $haveTransaction = DB::table('transactions')->where('vcard', $vcard->phone_number)->first() != null ? true : false;
            $haveTransactionPair = DB::table('transactions')->where('pair_vcard', $vcard->phone_number)->first() != null ? true : false;

            if (!$haveTransaction && $vcard->balance == 0 && !$haveTransactionPair) {
                $vcard->categories()->forceDelete();
                $vcard->forceDelete();
            } else {
                //softDelete VCARD
                $vcard->categories()->delete();
                $vcard->delete();
            }
        }
        else {
            return response()->json(
                ['error' => 'Vcard still has balance'],
                403
            );
        }

        return;
    }
    public function updatePiggyBank(Request $request, Vcard $vcard)
    {
        if ($request->value < 0) {
            return response()->json(['errors' => "Piggy Bank Credit Invalid"], 422);
        }

        if ($request->value <= $vcard->balance && $vcard->piggy_bank + $request->value <= $vcard->balance && $request->operation == "SUM") {
            $vcard->fill(['piggy_bank' => $vcard->piggy_bank + $request->value]);
            $vcard->fill(['balance' => $vcard->balance - $request->value]);
            $vcard->save();
            return;
        } else if ($request->value <= $vcard->piggy_bank && $request->operation == "MINUS") {
            $vcard->fill(['piggy_bank' => $vcard->piggy_bank - $request->value]);
            $vcard->fill(['balance' => $vcard->balance + $request->value]);
            $vcard->save();
            return;
        }
        return response()->json(['errors' => "Piggy Bank Credit Invalid"], 422);
    }
    public function updateActivactionPiggyBank(Request $request, Vcard $vcard)
    {

        if ($request->activationPiggyBank == 0 || $request->activationPiggyBank == 1) {
            $vcard->fill(['active_piggy_bank' => $request->activationPiggyBank]);
            $vcard->save();
            return;
        }
        return response()->json(['errors' => "Piggy Bank Update Invalid"], 422);
    }
}
