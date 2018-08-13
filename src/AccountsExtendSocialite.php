<?php

namespace EdCas\SocialiteProviders\Accounts;

use SocialiteProviders\Manager\SocialiteWasCalled;

class AccountsExtendSocialite
{
    /**
     * Execute the provider.
     *
     * @param SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite(
            'accounts', __NAMESPACE__ . '\Provider'
        );
    }
}
