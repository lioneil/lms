<?php

namespace Core\Console\Commands\Make;

use Faker\Generator as Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use User\Models\User;
use User\Services\RoleServiceInterface;
use User\Services\UserServiceInterface;

class UserMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:user
                           {--r|random : Generate random account}
                           {--c|credentials : Generate ask about credentials}
                           {--p|pretend : Will not save to database}
                           ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate user for the site either manually or randomly.';

    /**
     * The user service instance.
     *
     * @var \User\Services\UserServiceInterface
     */
    protected $service;

    /**
     * The faker service instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Create a new command instance.
     *
     * @param  \User\Services\UserServiceInterface $service
     * @param  \User\Services\RoleServiceInterface $roles
     * @param  \Faker\Generator as Faker           $faker
     * @return void
     */
    public function __construct(UserServiceInterface $service, RoleServiceInterface $roles, Faker $faker)
    {
        parent::__construct();

        $this->service = $service;
        $this->roles = $roles;
        $this->faker = $faker;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->display(
            $this->isRandom()
            ? $this->generateRandomUser()
            : $this->generateUser()
        );
    }

    /**
     * Get the user input to generate a user.
     * If the random option is set, the app
     * will generate the fields value on-the-fly.
     *
     * @return mixed
     */
    protected function generateUser()
    {
        $user['firstname'] = $this->ask('First Name');
        $user['lastname'] = $this->ask('Last Name');
        $user['email'] = $this->ask('Email');
        while ($this->isUnique($user['email'], 'email')) {
            $this->error('Email already exists. Try new one.');
            $user['email'] = $this->ask('Email.');
        }
        $user['username'] = $this->ask('User name');
        while ($this->isUnique($user['username'], 'username')) {
            $this->error('User name already exists. Try new one.');
            $user['username'] = $this->ask('User name.');
        }
        $user['password'] = $this->ask('Password (visible)');
        $this->password = $user['password'];
        $user['password'] = Hash::make($user['password']);
        $roles = collect($this->choice(
            'Specify the role associated with the user',
            $this->roles->pluck('code', 'id')->toArray()
        ))->map(function ($role) {
            return $this->roles->whereCode($role)->first()->getKey();
        })->toArray();

        $model = $this->service->create($user);
        $model->roles()->sync($roles);

        return $this->option('pretend')
            ? $user
            : $model;
    }

    /**
     * Generate a random user.
     *
     * @return mixed
     */
    protected function generateRandomUser()
    {
        $username = Str::slug($this->faker->unique()->email());
        $password = $this->password = $this->faker->password();

        if ($this->option('credentials')) {
            $username = $this->ask('User name', Str::slug($this->faker->firstName()));
            $password = $this->password = $this->ask('Password', $this->faker->randomNumber($nb = 6));
        }

        $user = factory(User::class)->make([
            'username' => $username,
            'password' => Hash::make($password)
        ])->makeVisible('password')->toArray();

        $roles = collect($this->choice(
            'Specify the role associated with the user',
            $this->roles->pluck('code', 'id')->toArray()
        ))->map(function ($role) {
            return $this->roles->whereCode($role)->first()->getKey();
        })->toArray();

        $model = $this->service->create($user);
        $model->roles()->sync($roles);

        return $this->option('pretend')
            ? $user
            : $model;
    }

    /**
     * Check if value already exists.
     *
     * @param  string $key
     * @param  string $column
     * @return boolean
     */
    protected function isUnique(string $key, string $column = 'username')
    {
        return $this->service->model()->where($column, '=', $key)->exists();
    }

    /**
     * Check if user wants a random user.
     *
     * @return boolean
     */
    protected function isRandom()
    {
        return $this->option('random');
    }

    /**
     * Display fancy table.
     *
     * @param  array $user
     * @return void
     */
    protected function display($user)
    {
        $this->table(
            ['First Name', 'Last Name', 'Email', 'Username', 'Password', 'Role'],
            [array_merge(
                collect($user)->only([
                    'firstname', 'lastname', 'email', 'username'
                ])->toArray(),
                [
                    'password' => $this->password,
                    'role' => $user->role ?? null,
                ]
            )]
        );
    }
}
