<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as IlluminateResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword;

class AdminResetPassword extends IlluminateResetPassword
{
    /**
     * @param  CanResetPassword  $notifiable
     */
    protected function resetUrl($notifiable): string
    {
        return url(route('admin.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
