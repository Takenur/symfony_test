<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\HtmlParse;
use App\Service\ProductService;
use App\Service\WebHelper;
use App\Traits\ResponseTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

final class ProductController extends AbstractController
{
    use ResponseTrait;

    #[Route('/products', name: 'app_product', methods: ['GET'])]
    public function index(
        Request        $request,
        ProductService $service,
    ): JsonResponse
    {
        return $service->findPaginated($request);
    }

    #[Route('/products/{id}', name: 'product_show_once', methods: ['GET'])]
    public function show(
        int            $id,
        ProductService $service
    ): JsonResponse
    {
        return $service->show($id);
    }

    #[Route('/products', name: 'product_create', methods: ['POST'])]
    public function create(
        Request                $request,
        ValidatorInterface     $validator,
        EntityManagerInterface $em,
        ProductService         $service,
    ): JsonResponse
    {
        return $service->create($request, $validator, $em);
    }

    #[Route('/products/{id}', name: 'product_update', methods: ['POST'])]
    public function update(
        int                    $id,
        Request                $request,
        ValidatorInterface     $validator,
        EntityManagerInterface $em,
        ProductService         $service,
    ): JsonResponse
    {
        return $service->update($id, $request, $validator, $em);
    }

    #[Route('/products/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(
        int                    $id,
        EntityManagerInterface $em,
        ProductService         $service,
    ): JsonResponse
    {
        return $service->delete($id, $em);
    }

    #[Route('/product/parse', name: 'product_parse', methods: ['POST'])]
    public function parse(
        Request   $request,
        HtmlParse $parseService,
        ProductRepository $productRepository,
        ValidatorInterface     $validator,
        EntityManagerInterface $em,
        ProductService $service
    )
    {
        $content = json_decode($request->getContent(), true);
        if (!$content || !isset($content['url']) || !$content['url']) {
            return $this->response(message: "url is empty", code: 422, success: false);
        }
        $url=$content['url'];

        if (!WebHelper::checkProductUrl($url)){
            return $this->response(message: "url is not valid", code: 422, success: false);
        }
        $product=$productRepository->findOneBy(['product_url'=>$url]);
        if ($product){
            return $this->response(data: $product->getObject(),message: "product already exist");
        }
        $parseData=$parseService->parseFromUrl($url);

        return $service->saveFromParse($url,$parseData,$validator,$em);
    }
}
