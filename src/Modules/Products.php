<?php
namespace App\Modules;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Products extends AbstractController
{
	private $pfile = 'data/products.php';
	
	public function __construct()
	{
		include_once( $this->pfile);
		$this->products = $productList;	
	}
	
	public function getList()
	{
		return $this->products;
	}	
	
	public function addProduct()
	{
		$request = Request::createFromGlobals();
		$day = $request->request->get('date', 'def');
		$id  = $request->request->get('id', '0');
		
		$productDay = $this->loadDay($day);
		if( empty($productDay[$id]) ){
			$productDay[$id] = $this->getProductID($id);
			$productDay[$id]['date'] = date("Y-m-d H:i:s");
		}
		$productDay[$id]['updatedate'] = date("Y-m-d H:i:s");
		$this->saveDay($day, json_encode($productDay));
		
		$adminEmail = $this->getParameter('admin_email');
		//sfMailer::composeAndSend('test@simfony.com', $adminEmail, 'Add Product', 'Add Product ' . $productDay[$id]['name']);
		
		$arrData = ['status' => 'success' ];
		return new JsonResponse($arrData);
	}
	
	public function loadDay($day)
	{
		$file = 'data/db/'.md5($day);
		if(!file_exists($file)) return [];
		$data = file_get_contents($file);
		return json_decode($data, true);
	}	
	
	private function saveDay($day, $content)
	{
		$file = 'data/db/'.md5($day);
		$res = fopen($file, 'w+');
		fwrite($res, $content);
		fclose($res);
	}	
	
	public function deleteProduct2Day()
	{
		$request = Request::createFromGlobals();
		$day = $request->request->get('date', 'def');
		$id  = $request->request->get('id', '0');
		
		$products = $this->loadDay($day);
		if( isset($products[$id]) ){
			unset($products[$id]);
		}
				
		$adminEmail = $this->getParameter('admin_email');
		//sfMailer::composeAndSend('test@simfony.com', $adminEmail, 'Delete Product', 'Delete Product ' . $products[$id]['name']);
		
		$content = json_encode($products);
		$file = 'data/db/'.md5($day);
		$res = fopen($file, 'w+');
		fwrite($res, $content);
		fclose($res);
		
		$arrData = ['status' => 'success' ];
		return new JsonResponse($arrData);
	}
	
	public function getProductID($id)
	{
		return $this->products[$id];
	}
	
}