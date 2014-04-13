<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 4/9/14
// Time: 10:27 PM
// For: CookieSync


namespace CookieSync\Workers\Statistical;

use Illuminate\Queue\Jobs\Job;
use Leader;
use User;

class RankUser {

  /**
   * @var User
   */
  private $user;
  /**
   * @var \Leader
   */
  private $leader;

  public function __construct(User $user, Leader $leader)
  {
    $this->user = $user;
    $this->leader = $leader;
  }

  public function fire(Job $job, $data)
  {
    // Find out if the user is ranked at all
    if($this->leader->whereUserId($data['user_id'])->count())
    {

    }
  }

} 