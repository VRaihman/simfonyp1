<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

use App\Entity\DbProduct;
use App\Entity\DbCart;

use App\Some\Service\Swift_Mailer;

class ProductsController extends AbstractController
{
    protected $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    //{type}
    public function indexPage()
    {
        return $this->render('tpl/index.html.twig', [
            'title' => 'Symfony',
        ]);
    }    
    
    //product/add/{date}
    public function productAdd(string $date = 'none')
    {
        if( false === $this->validParam($date, 'date' )){
            return $this->redirectToRoute('index');
        }

        $productList = $this->getList();

        $toTwig = array();
        $num = 0;
        foreach($productList as $key => $product){
            $toTwig[] = array(
                'num' => $num,
                'name' => $product->getName(),
                'amount' => $product->getPrice(),
                'pid' => $product->getId(),
                'date' => $date,
            );
            $num++;
        }
        
        return $this->render('tpl/addproduct.html.twig', [
            'date'  => $date,
            'prods' => $toTwig,
        ]);
    }
    
    //cart/{date}
    public function cartList(string $date)
    {
        if( false === $this->validParam($date, 'date' )){
            return $this->redirectToRoute('index');
        }

        $productDayList = $this->getDoctrine()->getRepository(DbCart::class)->getProdByDay($date);
        
        $toTwig = array();
        $num = 0;
        foreach($productDayList as $key => $product){
            $toTwig[] = array(
                'num' => $num,
                'name' => $product['name'],
                'amount' => $product['price'],
                'pid' => $product['idprod'],
                'date' => $date,
                'buydate' => $product['dateadd'],
                'updatedate' => $product['dateupdate'],
            );
            $num++;
        }
        return $this->render('tpl/cart.html.twig', [
            'date'  => $date,
            'prods' => $toTwig,
        ]);
    }    
    
    //ajax/{type}
    public function jsonPage(string $type = 'getEvents', Request $request)
    {
        $day = $request->request->get('date');
        $id  = $request->request->get('id', 0);
        
        switch ($type) {
            case 'addProduct':
                if( false === $this->validParam($day, 'date' )){
                    return $this->redirectToRoute('index');
                }

                $arrJson = $this->addDBProduct2Cart($day, intval($id));
            break;
            case 'deleteProduct':
                if( false === $this->validParam($day, 'date' )){
                    return $this->redirectToRoute('index');
                }

                $arrJson = $this->deleteProductCart($day, intval($id));
            break;

            case 'getEvents':
            default:
                $arrJson = $this->getEvents();
        }
        
        return new JsonResponse($arrJson);
    }
    
    public function getList(): ?array
    {
        return $this->getDoctrine()->getRepository(DbProduct::class)->findAll();
    }
    
    private function addDbProd(): ?array
    {
        $dbproduct = new DbCart();

        $dbproduct->setName('Plastic Cup');
        $dbproduct->setPrice('0.99');

        $entityManager->persist($dbproduct);
        $entityManager->flush();
        
        return $dbproduct;
    }
    
    public function addDBProduct2Cart(string $day, int $id): ?array
    {
        
        $productInCartByDay = $this->getDoctrine()->getRepository(DbCart::class)->findByDayId($day, $id);

        if( count($productInCartByDay) > 0){
            $arrJson = ['status' => 'success' ];
            return $arrJson;
        }
        
        $productId = $this->getDoctrine()->getRepository(DbProduct::class)->getProd($id);
        $productId = (object) $productId[0];

        $entityManager = $this->getDoctrine()->getManager();

        $dbcart = new DbCart();
        $dbcart->setName($productId->name);
        $dbcart->setPrice($productId->price);
        $dbcart->setDateadd(date("Y-m-d H:i:s"));
        $dbcart->setDateupdate(date("Y-m-d H:i:s"));
        $dbcart->setDate($day);
        $dbcart->setIdprod($productId->id);

        $entityManager->persist($dbcart);
        $entityManager->flush();

        $this->sendEmailAdmin("Add product {$productId->name} to cart {$day}");
        
        $arrJson = ['status' => 'success' ];
        return $arrJson;
    }    
    
    public function deleteProductCart(string $day, int $id): ?array
    {
        $productInCartByDay = $this->getDoctrine()->getRepository(DbCart::class)->findByDayId($day, $id);

        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(DbCart::class)->find($productInCartByDay[0]['id']);
        $em->remove($product);
        $em->flush();
        
        $this->sendEmailAdmin("Delete product {$productInCartByDay[0]['name']} to cart {$day}");

        $arrJson = ['status' => 'success' ];
        return $arrJson;
    }

    private function getEvents(): ?array
    {
        $numDay = date('t');
        $data   = date("Y-m-");
        $arrayDay = [];
        for($i = 1; $i <= $numDay; $i++){
            if( $i < 10 ) $pref = '0';
            else $pref = '';

            $productArray = $this->getDoctrine()->getRepository(DbCart::class)->getProdByDay($data . $pref . $i);
              
            if( $productArray != [] ){
                $prefTitle = 'Products in the cart ' . count($productArray);
            }else{
                $prefTitle = '';
            }
            
            $arrayDay[] = [
                'title' => "Edit Products\r\n" . $prefTitle,
                'url'   => 'cart/' . $data . $pref . $i,
                'start' => $data . $pref . $i,
                'color' => 'green',
            ];
            
        }
        
        $arrData = ['days' => $arrayDay ];
        return $arrData;
    }

    private function validParam($param, string $type = 'int')
    {
        $validator = Validation::createValidator();

        switch ($type) {
            case 'date':
                $arrParam = array(
                    //new Assert\Length(array('min' => 10, 'max' => 10 )),
                    new Assert\Date(),
                    new Assert\NotBlank(),
                );
            break;

            case 'string':
                $arrParam = array(
                    new Assert\Length(array('min' => 2, 'max' => 50 )),
                    new Assert\Type('string'),
                    new Assert\NotBlank(),
                );
            break;

            case 'int':
            default:
                $arrParam = array(
                    new Assert\Type('integer'),
                );
        }

        $violations = $validator->validate($param, $arrParam);

        if (0 !== count($violations)) {
            return false;
        }

        return true;
    }

    public function sendEmailAdmin(string $textMessage)
    {
        $adminEmail = $this->getParameter('admin_email');

           $message = (new \Swift_Message($textMessage))
               ->setFrom('send@testexample.com')
            	->setTo($adminEmail)
            	->setBody(
                $textMessage,
                'text/html'
        );

        $this->mailer->send($message);

        return true;
    }
}
