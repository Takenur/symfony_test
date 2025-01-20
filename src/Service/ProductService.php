<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Traits\ResponseTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductService
{
    use ResponseTrait;

    private ProductRepository $productRepository;
    private string $uploadDirectory;

    public function __construct(ProductRepository $productRepository, ParameterBagInterface $params)
    {
        $this->productRepository = $productRepository;
        $this->uploadDirectory = $params->get('upload_directory');
    }

    /**
     * @param int $page
     * @param int $limit
     * @param string|null $search
     * @param string $order
     * @return JsonResponse
     * @throws \Exception
     */
    public function findPaginated(Request $request): JsonResponse
    {
        $data = $request->request->all();
        $data = $this->productRepository->findPaginated(
            page: $data['page'] ?? 1,
            limit: $data['limit'] ?? 10,
            order: $data['order'] ?? "ASC",
            search: $data['search'] ?? null);
        return $this->response($data);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {

        $product = $this->productRepository->find($id);

        if (!$product) {
            return $this->response(message: 'product not found', code: 404, success: false);
        }
        return $this->response($product->getObject());

    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @throws \Exception
     */
    public function create(
        Request                $request,
        ValidatorInterface     $validator,
        EntityManagerInterface $em
    ): JsonResponse
    {
        $data = $request->request->all();
        $file = $request->files->get('photo');

        $product = new Product();
        $product->setTitle($data['title'] ?? '');
        $product->setDescription($data['description'] ?? '');

        if ($file) {
            $photoPath = FileHelper::saveFile($file, $this->uploadDirectory);
            $product->setPhotoPath($photoPath);
        }
        $errors = $validator->validate($product);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->response(message: $errorMessages, code: 422, success: false);
        }
        $em->persist($product);
        $em->flush();

        return $this->response($product->getObject(), message: 'Product created successfully');
    }

    public function update(int                    $id,
                           Request                $request,
                           ValidatorInterface     $validator,
                           EntityManagerInterface $em)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return $this->response(message: 'product not found', code: 404, success: false);
        }
        $data = $request->request->all();
        $file = $request->files->get('photo');

        $product->setTitle($data['title'] ?? '');
        $product->setDescription($data['description'] ?? '');

        if ($file) {

            FileHelper::deleteFile($product->getPhotoPath());

            $photoPath = FileHelper::saveFile($file, $this->uploadDirectory);
            $product->setPhotoPath($photoPath);
        }
        $errors = $validator->validate($product);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->response(message: $errorMessages, code: 422, success: false);
        }
        $em->flush();
        return $this->response($product->getObject(), message: 'Product updated successfully');
    }

    public function delete(int                    $id,
                           EntityManagerInterface $em)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return $this->response(message: 'product not found', code: 404, success: false);
        }
        $em->remove($product);
        $em->flush();
        return $this->response(null, message: 'Product deleted successfully');
    }

    public function saveFromParse(
        string $url,
        array                  $data,
                                  ValidatorInterface     $validator,
                                  EntityManagerInterface $em)
    {
        $product = new Product();
        $product->setTitle($data['name'] ?? '');
        $product->setDescription($data['description'] ?? '');

        $photoPath = FileHelper::saveFileFromUrl($data['image'], $this->uploadDirectory);
        $product->setPhotoPath($photoPath);
        $product->setProductUrl($url);

        $errors = $validator->validate($product);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->response(message: $errorMessages, code: 422, success: false);
        }
        $em->persist($product);
        $em->flush();

        return $this->response($product->getObject(), message: 'Product created successfully');

    }
}