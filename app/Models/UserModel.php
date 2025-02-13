<?php

namespace App\Models;

use App\Models\BaseModel;

class UserModel extends BaseModel
{
  // protected $DBGroup          = 'default';
  protected $table            = 'users';
  protected $primaryKey       = 'id';
  protected $useAutoIncrement = true;
  protected $returnType       = 'array';
  protected $useSoftDeletes   = false;
  protected $protectFields    = true;
  protected $allowedFields    = [
    'name',
    'username',
    'email',
    'password',
    'image',
    'last_login',
    'role_id',
    'is_active',
  ];

  // Dates
  protected $useTimestamps = true;
  protected $dateFormat    = 'datetime';
  protected $createdField  = 'created_at';
  protected $updatedField  = 'updated_at';
  protected $deletedField  = 'deleted_at';

  // Validation
  protected $validationRules      = [];
  protected $validationMessages   = [];
  protected $skipValidation       = false;
  protected $cleanValidationRules = true;

  // Callbacks
  protected $allowCallbacks = true;
  protected $beforeInsert   = ['beforeInsert'];
  protected $afterInsert    = [];
  protected $beforeUpdate   = ['beforeUpdate'];
  protected $afterUpdate    = [];
  protected $beforeFind     = [];
  protected $afterFind      = [];
  protected $beforeDelete   = ['beforeDelete'];
  protected $afterDelete    = [];

  public $logName = false;
  public $logId = true;

  public function getRoles()
  {
    return $this->db->table('roles')->get()->getResultArray();
  }

  // protected $validationRules = [
  //   'username' => 'required|alpha_numeric_space|min_length[3]|is_unique[users.username]',
  //   'password' => 'required|min_length[8]',
  //   'email' => 'required|is_unique[users.email]|valid_email',
  //   'name' => 'required',
  //   'role_id' => 'required',
  // ];

  // protected $validationMessages = [
  //   'username' => [
  //     'required' => 'Username is required',
  //     'alpha_numeric_space' => 'Username can only contain alphanumeric characters and spaces',
  //     'min_length' => 'Username must be at least 3 characters long',
  //     'is_unique' => 'Username already exists'
  //   ],
  //   'password' => [
  //     'required' => 'Password is required',
  //     'min_length' => 'Password must be at least 8 characters long'
  //   ]
  // ];
}
