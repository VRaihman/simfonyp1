<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Modules\EventCalendar as Calendar;

use App\Entity\DbProduct;
use App\Entity\DbCart;

class ProductsController extends AbstractController
{
	//{type}
    public function indexPage($type)
    {
        return $this->render('tpl/index.html.twig', []);
    }    
	
	//product/add/{date}
    public function productAdd($date = 'none')
    {
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
            'date' => $date,
            'prods' => $toTwig,
        ]);
    }	
	
	//cart/{date}
    public function cartList($date = 'none')
    {
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
            'date' => $date,
			'prods' => $toTwig,
        ]);
    }    
	
	//ajax/{type}
    public function jsonPage($type='all', Calendar $calendar, Request $request)
    {
		$day = $request->request->get('date', 'def');
		$id  = $request->request->get('id', '0');
		
		switch ($type) {
			case 'addProduct':
				$arrJson = $this->addDBProduct2Cart($day, $id);
			break;
			case 'deleteProduct':
				$arrJson = $this->deleteProductCart($day, $id);
			break;

			case 'getEvents':
			default:
				$arrJson = $calendar->getEvents();
		}
		
		return new JsonResponse($arrJson);
    }
	
	public function getList(): ?array
	{
		return $this->getDoctrine()->getRepository(DbProduct::class)->findAll();
	}
	
	private function addDbProd()
	{
        $dbproduct = new DbCart();
        $dbproduct->setName('Plastic Cup');
        $dbproduct->setPrice('0.99');

        $entityManager->persist($dbproduct);
        $entityManager->flush();
		return $dbproduct;
	}
	
	public function addDBProduct2Cart($day = '', $id = 0)
	{
		
		$productInCartByDay = $this->getDoctrine()->getRepository(DbCart::class)->findByDayId($day, $id);
		if( count($productInCartByDay) > 0){
			return ['status' => 'success' ];
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

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($dbcart);
        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
		
		return ['status' => 'success' ];
	}	
	
	public function deleteProductCart($day = '', $id = 0)
	{
		$productInCartByDay = $this->getDoctrine()->getRepository(DbCart::class)->findByDayId($day, $id);

		$em = $this->getDoctrine()->getManager();
		$product = $em->getRepository(DbCart::class)->find($productInCartByDay[0]['id']);
		$em->remove($product);
		$em->flush();
		return ['status' => 'success' ];
	}
}
