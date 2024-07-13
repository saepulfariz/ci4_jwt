<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class UserController extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    public function index()
    {
        $users = $this->model->findAll();
        $data = [
            'status' => 200,
            'message' => 'Get all users.',
            'data' => $users
        ];
        return $this->respond($data, 200);
    }

    public function show($id = null)
    {
        $user = $this->model->find($id);
        if ($user) {
            $data = [
                'status' => 200,
                'message' => 'Get user by id.',
                'data' => $user
            ];
            return $this->respond($data, 200);
        } else {
            return $this->failNotFound('User not found');
        }
    }

    public function create()
    {
        $data = $this->request->getPost();

        $rules = [
            'username' => 'required|alpha_numeric_space|min_length[3]|is_unique[users.username]',
            'password' => 'required|min_length[8]',
            'email' => 'required|is_unique[users.email]|valid_email',
            'name' => 'required',
            'role_id' => 'required',
        ];

        $messages = [
            'username' => [
                'required' => 'Username is required',
                'alpha_numeric_space' => 'Username can only contain alphanumeric characters and spaces',
                'min_length' => 'Username must be at least 3 characters long',
                'is_unique' => 'Username already exists'
            ],
            'password' => [
                'required' => 'Password is required',
                'min_length' => 'Password must be at least 8 characters long'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        try {
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
        } catch (\Throwable $th) {
            return $this->failServerError('Maaf, terjadi kegagalan pada server kami.');
        }
    }

    public function update($id = null)
    {
        $user = $this->model->find($id);
        if (!$user) {
            return $this->failNotFound('User not found');
        }

        // $data = $this->request->getRawInput();
        $data = $this->request->getVar();
        $data = (array)$data;

        $rules = $this->model->getValidationRules();
        $messages = $this->model->getValidationMessages();

        $rules = [
            'name' => 'required',
            'role_id' => 'required',
        ];
        $messages = [];

        if ($data['email'] != $user['email']) {
            $rules['email'] = 'required|valid_email|is_unique[users.email]';
        }

        if ($data['username'] != $user['username']) {
            $rules['username'] = 'required|alpha_numeric_space|min_length[3]|is_unique[users.username,id,{id}]';
        }

        if ($data['password'] != '') {
            $rules['password'] = 'required|min_length[8]';
        }


        if (!$this->validate($rules, $messages)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        if ($data['password'] != '') {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
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
        $user = $this->model->find($id);
        if ($user) {
            $this->model->delete($id);
            $data = [
                'status' => 200,
                'message' => 'Successfully deleted.',
                'data' => ['id' => $id]
            ];
            return $this->respondDeleted($data);
        } else {
            // $data = [
            //     'status' => 404,
            //     'message' => 'User not found',
            // ];
            // return $this->respond($data, 404);
            return $this->failNotFound('User not found');
        }
    }
}
