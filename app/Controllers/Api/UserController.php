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
        $this->model = new UserModel();
        return $this->respond($users);
    }

    public function show($id = null)
    {
        $user = $this->model->find($id);
        if ($user) {
            return $this->respond($user);
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
            return $this->respond($data);
        } else {
            return $this->failValidationErrors($this->model->errors());
        }
    }

    public function delete($id = null)
    {
        if ($this->model->delete($id)) {
            return $this->respondDeleted(['id' => $id]);
        } else {
            return $this->failNotFound('User not found');
        }
    }
}
