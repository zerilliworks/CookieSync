<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 8/19/14
// Time: 11:02 PM
// For: CookieSync


namespace CookieSync\Authentication;


use Carbon\Carbon;
use CookieSync\Authentication\Exceptions\AlreadyVerifiedException;
use CookieSync\Authentication\Exceptions\EmailTokenExpiredException;
use CookieSync\Authentication\Exceptions\EmailTokenIncorrectException;
use CookieSync\Authentication\Exceptions\NoPendingEmailException;
use Hashids\Hashids;
use Illuminate\Auth\UserInterface;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

/**
 * Class EmailManager
 *
 * @package CookieSync\Authentication
 */
class EmailManager {

    protected $mailer;

    function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param UserInterface $recipient
     * @throws Exceptions\AlreadyVerifiedException
     * @internal param $hash
     * @internal param $newEmail
     * @return $this
     */
    public function sendVerificationEmail(UserInterface $recipient)
    {
        if($recipient->email_verified) {
            Log::info("Email address for $recipient->name is already verified.");
            return $this;
        }

        Log::info("Sending verification email to $recipient->pending_email");

        $userId = $recipient->getAuthIdentifier();
        $userName = $recipient->name;
        $newEmail = $recipient->pending_email;
        $hash     = $recipient->verify_hash;
        $this->mailer->send('emails.verify', ['hash' => $hash, 'username' => $userName],
            function ($message) use ($userId, $newEmail, $userName) {
                $message->to($newEmail, $userName)
                        ->subject('Verify your Email Address');
            });

        Log::info("Mail sent.");

        return $this;
    }

    /**
     * Generate the hash and send the email for a user that has a pending address already set.
     *
     * @param UserInterface $user
     * @throws Exceptions\NoPendingEmailException
     * @internal param $newEmail
     * @return $this
     */
    public function verifyPendingEmail(UserInterface $user)
    {
        if(empty($user->pending_email)) {
            throw new NoPendingEmailException;
        }

        $user->email_verified = 0;
        $user->verify_hash = $this->generateHash($user->getAuthIdentifier());
        $user->save();

        $this->sendVerificationEmail($user);
        return $this;
    }

    /**
     * @param UserInterface $user
     * @param               $newEmail
     * @return $this
     */
    public function requestNewEmail(UserInterface $user, $newEmail)
    {
        Log::info("User $user->name has requested a new email at $newEmail");
        Log::info("Generating token hash...");
        $verifyHash = $this->generateHash($user->getAuthIdentifier());

        Log::info("Updating user...");
        $user->email_verified = 0;
        $user->pending_email  = $newEmail;
        $user->verify_hash    = $verifyHash;
        $user->save();

        Log::info("Sending verification email...");
        $this->sendVerificationEmail($user);

        Log::info("Done.");
        return $this;
    }

    /**
     * By using Hashids (www.hashids.org), the user ID and the timestamp of the request are
     * included in the hash itself. This obviates the need to pass in a User object.
     *
     * @param $hash
     * @return bool
     * @throws Exceptions\AlreadyVerifiedException
     * @throws Exceptions\EmailTokenExpiredException
     * @throws Exceptions\EmailTokenIncorrectException
     */
    public function verifyEmailHash($hash)
    {
        $data = $this->decryptHash($hash);
        $userId      = intval($data[0]);
        $requestedAt = Carbon::createFromTimestamp($data[1]);

        $user = \User::find($userId);

        if ($user->email_verified) {
            // Abort, link is expired or already used
            throw new AlreadyVerifiedException();
        }

        if ($hash !== $user->verify_hash) {
            // Abort, link does not match user
            throw new EmailTokenIncorrectException();
        }

        // Make sure the link is less than 24 hours old
        // If older, void it and delete it.
        if ($requestedAt->lt(Carbon::now()->subHours(24))) {
            // Abort, link is expired or already used
            throw new EmailTokenExpiredException();
        }

        $user->email_verified = 1;
        $user->verify_hash    = null;
        $user->email          = $user->pending_email;
        $user->pending_email  = null;
        $user->save();

        return true;

    }

    protected function generateHash($userId)
    {
        $hashids    = new Hashids(Config::get('app.key'));
        return $hashids->encrypt($userId, time());
    }

    protected function decryptHash($hash)
    {
        $hashids     = new Hashids(Config::get('app.key'));
        return $hashids->decrypt($hash);
    }

} 