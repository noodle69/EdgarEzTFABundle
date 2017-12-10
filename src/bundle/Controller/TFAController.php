<?php

namespace Edgar\EzTFABundle\Controller;

use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TFAController extends Controller
{
    public function menuAction(Request $request): Response
    {
        return $this->render('@EdgarEzTFA/profile/tfa/view.html.twig', [
        ]);
    }
}
