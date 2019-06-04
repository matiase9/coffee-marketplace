<?php

namespace App\Controller;

use App\Entity\Coffee;
use App\Entity\Order;
use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Post;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Coffee controller.
 * @Route("/api", name="api_")
 */
class OrderController extends AbstractFOSRestController
{
    /**
     * New Order.
     * @Post("/order/new", name="order_new")
     *
     * @return Response
     */
    public function postNewAction(Request $request)
    {
        $customerIdRequest = $request->get('user_id');
        $coffeIdRequest = $request->get('coffe_id');
        $qtyRequest = $request->get('quantity');

        if (!$coffeIdRequest | !$qtyRequest | !$customerIdRequest) {
            return $this->view('Check the parameters required (user_id, coffe_id, quantity).', Response::HTTP_FORBIDDEN);
        }

        $repository = $this->getDoctrine()->getRepository(Coffee::class);
        $coffee = $repository->find($coffeIdRequest);

        if (!empty($coffee)) {
            $stockCoffe = $coffee->getStock();

            if ($stockCoffe >= $qtyRequest) {

                $repository = $this->getDoctrine()->getRepository(User::class);
                $user = $repository->find($customerIdRequest);

                if (!empty($user)) {
                    try {
                        $entityManager = $this->getDoctrine()->getManager();
                        $priceOrder = $coffee->getPrice() * $qtyRequest;

                        $order = New Order();
                        $order->setUserId($customerIdRequest);
                        $order->setCoffeeId($coffeIdRequest);
                        $order->setQuantity($qtyRequest);
                        $order->setAmount($priceOrder);
                        $entityManager->persist($order);

                        // Update table coffee
                        $newQty = $stockCoffe - $qtyRequest;
                        $coffee->setStock($newQty);
                        $entityManager->persist($coffee);

                        $entityManager->flush();

                        // Logger action
                        $logger = new Logger($customerIdRequest);
                        $logger->pushHandler(new StreamHandler( $this->getParameter('root.log').'/sales.log', Logger::DEBUG));
                        $logger->info('SET ORDER => order '. $qtyRequest .' units of coffe. Price:'. $priceOrder);

                        return $this->view('The customer '. $customerIdRequest. ' buy '. $qtyRequest . ' unit: $'. $priceOrder, Response::HTTP_OK);
                    } catch (Exception $exception) {
                        $logger = new Logger('connection');
                        $logger->pushHandler(new StreamHandler( $this->getParameter('root.log').'/error.log', Logger::DEBUG));
                        $logger->critical($exception->getCode() . $exception->getMessage());

                        return $this->view( 'Conflict to save in the database', Response::HTTP_CONFLICT);
                    }

                } else {
                    return $this->view('User not available', Response::HTTP_CONFLICT);
                }
            } else {
                return $this->view('Stock not available', Response::HTTP_CONFLICT);
            }
        } else {
            return $this->view('Coffee not found', Response::HTTP_FOUND);
        }
    }
}
