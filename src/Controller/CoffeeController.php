<?php

namespace App\Controller;

use App\Entity\Coffee;
use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PhpParser\Node\Scalar\MagicConst\Dir;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\DependencyInjection\Container;
use FOS\RestBundle\Controller\Annotations\Delete;



/**
 * Coffee controller.
 * @Route("/api", name="api_")
 */
class CoffeeController extends AbstractFOSRestController
{
    // These messages must be created in another class for better organization.
    const COFFE_NOT_FOUND = 'Coffee not found';

    /**
     * Get all Coffee.
     * @Rest\GET("/coffee/{id}", name="coffee")
     *
     * @return Response
     */
    public function getCoffeeAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(Coffee::class);
        $coffee = $repository->find($id);

        if (!empty($coffee)) {
            return $this->view($coffee, Response::HTTP_OK);
        }

        return $this->view(self::COFFE_NOT_FOUND, Response::HTTP_FOUND);
    }

    /**
     * Create new Coffee.
     * @Rest\PUT("/coffee/new", name="coffee_new")
     *
     * @return Response
     */
    public function putNewAction(Request $request)
    {
        $nameRequest = $request->get('name');
        $intensityRequest = $request->get('intensity');
        $priceRequest = $request->get('price');
        $quantityRequest = $request->get('stock');

        if (!$nameRequest | !$intensityRequest | !$priceRequest | !$quantityRequest) {
            return $this->view('Check the parameters required (name, intensity, price, stock).', Response::HTTP_FORBIDDEN);
        }

        $coffee = New Coffee();
        $coffee->setName($nameRequest);
        $coffee->setIntensity($intensityRequest);
        $coffee->setPrice($priceRequest);
        $coffee->setStock($quantityRequest);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($coffee);
        $entityManager->flush();

        // Logger action
        $logger = new Logger('channel-name');
        $logger->pushHandler(new StreamHandler( $this->getParameter('root.log').'/info.log', Logger::DEBUG));
        $logger->info('SET COFFE => {'. "$nameRequest , $intensityRequest , $priceRequest , $quantityRequest" . '}');


        return $this->view('User: '. $coffee->getName(). ' was added successfully', Response::HTTP_OK);
    }

    /**
     * Remove Coffe.
     * @Delete("/coffee/delete/{id}", name="coffee_remove")
     * @return Response
     */
    public function deleteCoffeeAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(Coffee::class);
        $coffee = $repository->find($id);

        if (!empty($coffee)) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($coffee);
            $entityManager->flush();

            return $this->view('Coffe removed', Response::HTTP_OK);
        }

        return $this->view(self::COFFE_NOT_FOUND, Response::HTTP_FOUND);
    }



}