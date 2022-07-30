<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use App\mahasiswa;
use App\dosen;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {   
        if ( isset($data['nomer_id']) ) {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'nomer_id' => ['required', 'exists:dosens,nomer_id', 'unique:users,nomer_id'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ];

            $messages = [
                'nomer_id.exists' => 'Nomer Pegawai belum terdata, harap hubungi admin',
                'nomer_id.unique' => 'Nomer Pegawai sudah ada digunakan user lain, harap hubungi admin mendaftar.',
            ];
        }elseif ( isset($data['nrp']) ) {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'nrp' => ['required', 'exists:mahasiswas,nrp', 'unique:users,nrp'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ];

            $messages = [
                'nrp.exists' => 'NRP belum terdata, harap hubungi admin',
                'nrp.unique' => 'NRP sudah ada digunakan user lain, harap hubungi admin untuk mendaftar',
            ];
        }else{
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ];

            $messages = [
                'password.confirmed' => 'password tidak sesuai!'
            ];
        }

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {   
        if ( isset($data['nomer_id']) ){
            dosen::where('nomer_id', $data['nomer_id'])->first()->update(['status' => 1]);
            return User::create([
                'name' => $data['name'],
                'nomer_id' => $data['nomer_id'],
                'roles_id' => 3,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
        }elseif ( isset($data['nrp']) ){
            mahasiswa::where('nrp', $data['nrp'])->first()->update(['status' => 1]);
            return User::create([
                'name' => $data['name'],
                'nrp' => $data['nrp'],
                'roles_id' => 2,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
        }else {
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
        }
    }
}
