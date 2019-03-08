<?php
namespace App\Modules;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\DbCart;

class EventCalendar extends AbstractController
{
	
	public function getEvents(): ?array
	{
		$request = Request::createFromGlobals();
		$param = $request->request->get('date', 'def');
		$arrData = ['days' => $this->getAllEvents() ];
		return $arrData;
	}
	
	private function getAllEvents(): ?array
	{
		$numDay = date('t');
		$data = date("Y-m-");
		$arrayDay = [];
		for($i=1; $i<=$numDay; $i++){
			if( $i<10 ) $pref = '0';
			else $pref = '';

			$productArray = $this->getDoctrine()->getRepository(DbCart::class)->getProdByDay($data . $pref . $i);
			if( $productArray != [] ){
				$prefTitle = 'Products in the cart ' . count($productArray);
			}else{
				$prefTitle = '';
			}
			
			$arrayDay[] = [
				'title' => "Edit Products\r\n" . $prefTitle,
				'url' => 'cart/' . $data . $pref . $i,
				'start' => $data . $pref . $i,
				'color' => 'green',
			];
			
		}
		
		return $arrayDay;
	}
	
}