<?php

namespace SertxuDeveloper\Lyra\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use SertxuDeveloper\Lyra\Lyra;
use SertxuDeveloper\Lyra\Models\Role;
use SertxuDeveloper\Lyra\Models\User;

class UserMakeCommand extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'lyra:user';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new Lyra user';

  public function handle() {
    if (config('lyra.authenticator') !== Lyra::MODE_ADVANCED) {
      $this->error('This command is only available using the Advanced mode!');
      exit(1);
    }

    $name = $this->ask('Insert the name of the new user');
    $email = $this->askEmail();
    $password = $this->askPassword();
    $passwordConfirmation = $this->askPasswordConfirmation();

    $role_id = $this->askRole();

    if ($password !== $passwordConfirmation) {
      $this->error('The passwords inserted do not coincidence, aborting...');
      exit(1);
    }

    $user = new User();
    $user->name = $name;
    $user->email = $email;
    $user->password = Hash::make($password);
    $user->role_id = $role_id;

    $user->save();
    $this->info('User saved successfully!');
  }

  private function askEmail() {
    $email = $this->ask('Insert the email of the new user');
    if (!$this->validateEmail($email)) return $this->askEmail();
    return $email;
  }

  private function askPassword() {
    $password = $this->secret('Insert the password of the new user');
    if (!$this->validatePassword($password)) return $this->askPassword();
    return $password;
  }

  private function askPasswordConfirmation() {
    $password = $this->secret('Insert the password confirmation of the new user');
    if (!$this->validatePassword($password)) return $this->askPasswordConfirmation();
    return $password;
  }

  private function askRole() {
    $roles = Role::all();

    $NEW_ROLE_OPTION = '[New Role]';

    $options = [$NEW_ROLE_OPTION];
    $options = array_merge($options, $roles->pluck('name')->toArray());

    $choice = $this->choice('Select a role for the new user', $options);

    if ($choice === $NEW_ROLE_OPTION) {
      $name = $this->ask('Insert the name of the new role');

      $role = new Role();
      $role->name = $name;

      $role->save();
      $this->info('Role saved successfully!');

      return $role->id;
    } else {
      $key = $roles->search(function ($item, $key) use ($choice) {
        return $item['name'] === $choice;
      });

      return $roles[$key]->id;
    }
  }

  private function validateEmail($email) {
    $validator = Validator::make(['email' => $email], [
      'email' => 'required|email',
    ]);

    if ($validator->fails()) $this->error('The email is not valid');
    return !$validator->fails();
  }

  private function validatePassword($email) {
    $validator = Validator::make(['password' => $email], [
      'password' => 'required|min:8',
    ]);

    if ($validator->fails()) $this->error('The password is not valid');
    return !$validator->fails();
  }

}
