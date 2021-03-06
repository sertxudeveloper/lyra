<?php

namespace SertxuDeveloper\Lyra\Commands;

use Illuminate\Console\Command;
use SertxuDeveloper\Lyra\Lyra;
use SertxuDeveloper\Lyra\Models\Role;

class RoleMakeCommand extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'lyra:role {name?}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new Lyra role';

  public function handle() {
    if (config('lyra.authenticator') !== Lyra::MODE_ADVANCED) {
      $this->error('This command is only available using the Advanced mode!');
      exit(1);
    }

    if (!$this->argument('name')) {
      $name = $this->ask('Insert the name of the new role');
    } else {
      $name = $this->argument('name');
    }

    $role = new Role();
    $role->name = $name;

    $role->save();
    $this->info('Role saved successfully!');
  }

}
