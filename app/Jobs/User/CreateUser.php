<?php

namespace App\Jobs\User;

use App\Models\User;
use App\Models\UserCompany;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;

class CreateUser
{

    use Dispatchable;

    protected $request;

    protected $account;

    protected $company;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct(Request $request, $account, $company)
    {
        $this->request = $request;
        $this->account = $account;
        $this->company = $company;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $user = new User();
        $user->account_id = $this->account->id;
        $user->password = bcrypt($this->request->input('password'));
        $user->accepted_terms_version = config('ninja.terms_version');
        $user->confirmation_code = strtolower(str_random(RANDOM_KEY_LENGTH));
        $user->db = config('database.default');
        $user->fill($this->request->all());
        $user->save();


        UserCompany::create([
            'user_id' => $user->id,
            'account_id' => $this->account->id,
            'company_id' => $this->company->id,
            'is_admin' => true,
            'is_owner' => true,
            'permissions' => '',

        ]);

        return $user;
    }
}
