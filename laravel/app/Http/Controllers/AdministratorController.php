<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAdminRequest;
use App\Http\Requests\UpdateAdminPasswordRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Requests\UpdateVcardAsAdminRequest;
use App\Models\Administrator;
use Illuminate\Http\Request;
use App\Http\Resources\AdministratorResource;
use App\Http\Resources\VcardResource;
use App\Models\Vcard;
use Illuminate\Support\Facades\Hash;

class AdministratorController extends Controller
{
    public function getAll()
    {
        return AdministratorResource::collection(Administrator::all());
    }

    public function getOne(Administrator $administrator)
    {
        return new AdministratorResource($administrator);
    }

    public function update(UpdateAdminRequest $request, Administrator $administrator){
        $administrator->update($request->validated());
        return new AdministratorResource($administrator);
    }

    public function store(CreateAdminRequest $request)
    {
        $newAdministrator = Administrator::create($request->validated());
        $newAdministrator->password = bcrypt($request->password);
        $newAdministrator->save();
        return new AdministratorResource($newAdministrator);
    }

    public function updatePassword(UpdateAdminPasswordRequest $request, Administrator $administrator){
        $request->validated();
        if ($request->password) { //se se quiser fazer update à password
            if (Hash::check($request->current_password, $administrator->password)) {
                $administrator->fill(['password' => bcrypt($request->password)]);
            }
        }
        $administrator->save();
        return new AdministratorResource($administrator);
    }

    public function destroy(Administrator $administrator){
        $deletedAdmin = $administrator;
        $administrator->delete();
        return new AdministratorResource($deletedAdmin);
    }

    public function updateVcard(UpdateVcardAsAdminRequest $request, Vcard $vcard) {
        $vcard->update($request->validated());
        return new VcardResource($vcard);
    }
}
