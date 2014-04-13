<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 4/9/14
// Time: 10:00 PM
// For: CookieSync

namespace CookieSync\Workers\Statistical;

use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Validator;
use User;

class UserStatTotals {

  /**
   * @var \User
   */
  private $user;

  public function __construct(User $user)
  {
    $this->user = $user;
  }

  public function fire(Job $job, $data)
  {
    if(Validator::make($data, ['user_id' => 'required|integer'])->fails())
    {
      throw new \Exception('User ID not supplied to a statistical job: UserStatTotals@computeCareerCookieTotal');
    }
  }

  public function computeCareerCookieTotal(Job $job, $data)
  {
    if(Validator::make($data, ['user_id' => 'required|integer'])->fails())
    {
      throw new \Exception('User ID not supplied to a statistical job: UserStatTotals@computeCareerCookieTotal');
    }

    $accumulator = 0;
    foreach ($this->user->findOrFail($data['user_id'])->games() as $user_game) {
      $accumulator = bcadd($user_game->latestSave()->alltime_cookies, $accumulator);
    }



    $job->delete();

  }

}