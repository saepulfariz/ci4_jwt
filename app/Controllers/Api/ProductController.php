<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProductModel;

class ProductController extends ResourceController
{
    protected $modelName = 'App\Models\ProductModel';
    protected $format    = 'json';

    public function index()
    {
        $products = $this->model->findAll();
        $data = [
            'status' => 200,
            'message' => 'Get all products.',
            'data' => $products
        ];
        return $this->respond($data, 200);
    }

    public function show($id = null)
    {
        $product = $this->model->find($id);
        if ($product) {
            $data = [
                'status' => 200,
                'message' => 'Get product by id.',
                'data' => $product
            ];
            return $this->respond($data, 200);
        } else {
            return $this->failNotFound('Product not found');
        }
    }

    public function create()
    {
        $data = $this->request->getPost();

        if (!$this->validate($this->model->getValidationRules(), $this->model->getValidationMessages())) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        if ($this->model->insert($data)) {
            $last = $this->model->orderBy('id', 'DESC')->first();
            $data = [
                'status' => 201,
                'message' => 'Successfully created.',
                'data' => $last
            ];
            return $this->respondCreated($data);
        } else {
            return $this->failValidationErrors($this->model->errors());
        }
    }

    public function update($id = null)
    {
        $data = $this->request->getRawInput();
        $data = $this->request->getVar();

        if (!$this->validate($this->model->getValidationRules(), $this->model->getValidationMessages())) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        if ($this->model->update($id, $data)) {
            $last = $this->model->find($id);
            $data = [
                'status' => 200,
                'message' => 'Successfully updated.',
                'data' => $last
            ];
            return $this->respond($data, 200);
        } else {
            return $this->failValidationErrors($this->model->errors());
        }
    }

    public function delete($id = null)
    {
        $product = $this->model->find($id);
        if ($product) {
            $this->model->delete($id);
            $data = [
                'status' => 200,
                'message' => 'Successfully deleted.',
                'data' => ['id' => $id]
            ];
            return $this->respondDeleted($data);
        } else {
            return $this->failNotFound('Product not found');
        }
    }
}
