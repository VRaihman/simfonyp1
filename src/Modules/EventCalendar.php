<?php
namespace App\Modules;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class EventCalendar
{
	
	public function __construct()
	{
		$this->request = Request::createFromGlobals();
	}
	
	public function getEvents($prod)
	{
		$param = $this->request->request->get('date', 'def');
		$arrData = ['days' => $this->getAllEvents($prod) ];
		return new JsonResponse($arrData);
	}
	
	private function getAllEvents($products)
	{
		
		
		$numDay = date('t');
		$data = date("Y-m-");
		$arrayDay = [];
		for($i=1; $i<=$numDay; $i++){
			if( $i<10 ) $pref = '0';
			else $pref = '';

			$productArray = $products->loadDay($data . $pref . $i);
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