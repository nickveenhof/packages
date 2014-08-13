<?php

namespace Terramar\Packages\Plugin\Sami;

use Nice\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Terramar\Packages\Entity\Remote;

class Controller
{
    public function editAction(Application $app, Request $request, $id)
    {
        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $app->get('doctrine.orm.entity_manager');
        $config = $entityManager->getRepository('Terramar\Packages\Plugin\Sami\PackageConfiguration')->findOneBy(array(
                'package' => $id
            ));

        $gitConfig = $entityManager->getRepository('Terramar\Packages\Plugin\CloneProject\PackageConfiguration')->findOneBy(array(
            'package' => $id
        ));
        
        return new Response($app->get('twig')->render('Plugin/Sami/edit.html.twig', array(
                    'config' => $config,
                    'gitConfig' => $gitConfig
                )));
    }

    public function updateAction(Application $app, Request $request, $id)
    {
        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $app->get('doctrine.orm.entity_manager');
        $config = $entityManager->getRepository('Terramar\Packages\Plugin\Sami\PackageConfiguration')->findOneBy(array(
                'package' => $id
            ));
        
        $config->setEnabled($request->get('sami_enabled') ? true : false);
        
        $entityManager->persist($config);
        
        if ($config->isEnabled()) {
            $gitConfig = $entityManager->getRepository('Terramar\Packages\Plugin\CloneProject\PackageConfiguration')->findOneBy(array(
                'package' => $id
            ));
            
            $gitConfig->setEnabled(true);
            
            $entityManager->persist($gitConfig);
        }
        
        return new Response();
    }
}