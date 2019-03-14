<?php 
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\DbProduct;
use App\Entity\DbCart;
use App\Some\Service\Swift_Mailer;

class ProductsController extends AbstractController
{
    public $mailer;
    public $validator;

    public function __construct(\Swift_Mailer $mailer, ValidatorInterface $validator)
    {
        $this->mailer = $mailer;
        $this->validator = $validator;
    }

    //{type}
    public function indexPage(): JsonResponse
    {
        return new JsonResponse([
            'title' => 'Symfony p1',
        ]);
    }    

    //cart/{date}/{id}
    public function cartType(string $date, int $id = 0, Request $request): JsonResponse
    {
        $answer = ['cart' => ['status' => 'success']];

        $type = $request->server->get('REQUEST_METHOD', 'GET');
        if ($type === 'GET' && $date === '0000-00-00'){
            $answer['cart'] = $this->getEvents();
            return new JsonResponse($answer);
        }

        if ($this->validParam($date, 'date') === false) {
            $answer['cart']['status'] = 'error date';
            return new JsonResponse($answer);
        }

        if ($id === 0){
            $answer['cart'] = $this->getDoctrine()->getRepository(DbCart::class)->findBy(['date' => $date]);
            return new JsonResponse($answer);
        }

        switch ($type) {
            case 'POST':
                $answer['cart'] = $this->addProductCart($day, intval($id));
            break;

            case 'DELETE':
                $answer['cart'] = $this->deleteProductCart($day, intval($id));
            break;

            case 'PUT':
                $answer['cart'] = $this->updateProductCart($day, intval($id));
            break;

            default:
                $answer['cart']['status'] = 'error';
        }

        return new JsonResponse($answer);
    }

    //calendar/
    public function cartList(): JsonResponse
    {
        $ArrJson = $this->getEvents();
        return new JsonResponse($ArrJson);
    }    

    //product/{id}
    public function getProductList(string $id): JsonResponse
    {
        if ($id !== 'all'){
            $id = intval($id);
        }

        $list = $this->getDoctrine()->getRepository(DbProduct::class)->findAll();
        
        $returnArray = [];
        foreach ($list as $value) {
            if ($id === 'all') {
                $returnArray[] = (array) $value;
            } else {
                if ($value->getId() === $id) {
                    $returnArray[] = (array) $value;
                    break;
                }
            }
        }
        return new JsonResponse($returnArray);
    }
    
    public function addProductCart(string $day, int $id): array
    {
        
        $ProductInCartByDay = $this->getDoctrine()->getRepository(DbCart::class)->findByDayId($day, $id);

        if (count($ProductInCartByDay) > 0) {
            $ArrJson = ['status' => 'success'];
            return $ArrJson;
        }
        
        $productId = $this->getDoctrine()->getRepository(DbProduct::class)->getProd($id);

        $EntityManager = $this->getDoctrine()->getManager();

        $dbcart = new DbCart();
        $dbcart->setName($productId->name);
        $dbcart->setPrice($productId->price);
        $dbcart->setDateadd(date('Y-m-d H:i:s'));
        $dbcart->setDateupdate(date('Y-m-d H:i:s'));
        $dbcart->setDate($day);
        $dbcart->setIdprod($productId->id);

        $EntityManager->persist($dbcart);
        $EntityManager->flush();

        $this->sendEmailAdmin('Add product ' . $productId->name . ' to cart' . $day);
        
        $ArrJson = ['status' => 'success'];
        return $ArrJson;
    }    
    
    public function deleteProductCart(string $day, int $id): array
    {
        $ProductInCartByDay = $this->getDoctrine()->getRepository(DbCart::class)->findByDayId($day, $id);

        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(DbCart::class)->find($ProductInCartByDay->id);
        $em->remove($product);
        $em->flush();
        
        $this->sendEmailAdmin('Delete product ' . $ProductInCartByDay->id . ' to cart ' . $day);

        $ArrJson = ['status' => 'success'];
        return $ArrJson;
    }

    public function updateProductCart(string $day, int $id): array
    {
        $repository = $this->getDoctrine()->getRepository(DbCart::class);
        $ProductInCartByDay = $repository->findOneBy(['date' => $day, 'idProd' => $id]);

        if ($ProductInCartByDay === null) {
            $ArrJson = ['status' => 'error date'];
            return $ArrJson;
        }

        $EntityManager = $this->getDoctrine()->getManager();

        $ProductInCartByDay->setDateUpdate(date('Y-m-d H:i:s'));

        $EntityManager->persist($ProductInCartByDay);
        $EntityManager->flush();

        $this->sendEmailAdmin('Update product ' . $ProductInCartByDay->name . ' to cart ' . $day);

        $ArrJson = ['status' => 'success'];
        return $ArrJson;
    }

    private function getEvents(): array
    {
        $NumDay = date('t');
        $data   = date('Y-m-');
        $ArrayDay = [];
        for ($i = 1; $i <= $NumDay; $i++) {
            if ($i < 10) {
                $pref = '0';
            } else {
                $pref = '';
            }

            $ProductArray = $this->getDoctrine()->getRepository(DbCart::class)->findBy(['date' => $data . $pref . $i]);
              
            if ($ProductArray != []) {
                $PrefTitle = 'Products in the cart ' . count($ProductArray);
            } else {
                $PrefTitle = '';
            }
            
            $ArrayDay[] = [
                'title' => 'Edit Products' . PHP_EOL . $PrefTitle,
                'url'   => 'cart/' . $data . $pref . $i,
                'start' => $data . $pref . $i,
                'color' => 'green',
            ];
            
        }
        
        $ArrData = ['days' => $ArrayDay];
        return $ArrData;
    }

    private function validParam($param, string $type = 'int'): bool
    {
        $validator = $this->validator;

        switch ($type) {
            case 'date':
                $ArrParam = [
                    new Assert\Date(),
                    new Assert\NotBlank(),
                ];
            break;

            case 'string':
                $ArrParam = [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\Type('string'),
                    new Assert\NotBlank(),
                ];
            break;

            case 'int':
            default:
                $ArrParam = [
                    new Assert\Type('integer'),
                ];
        }

        $violations = $validator->validate($param, $ArrParam);

        if (count($violations) != 0) {
            return false;
        }

        return true;
    }

    public function sendEmailAdmin(string $TextMessage): bool
    {
        $AdminEmail = $this->getParameter('admin_email');

        $message = (new \Swift_Message($TextMessage))
            ->setFrom('send@testsymfony.com')
            ->setTo($AdminEmail)
            ->setBody($TextMessage, 'text/html');
        
        $this->mailer->send($message);

        return true;
    }
}
