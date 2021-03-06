<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;

class Items extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('ItemsModel');
        
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['index_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['index_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['index_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function index_get($id = FALSE)
    {
        //$id = $this->get('id');
        
        // If the id parameter doesn't exist return all the items

        if ($id === NULL)
        {
            $items = $this->ItemsModel->get_item();
            
            // Check if the items data store contains items (in case the database result returns NULL)
            if ($items)
            {
                // Set the response and exit
                $this->response($items, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'No items were found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }
        
        // Find and return a single record for a particular item.
        else {
            $item = $this->ItemsModel->get_item($id);

            // Validate the id.
            // @todo Need to set validation rules
            /*if ($id <= 0)
            {
                // Invalid id, set the response and exit.
                $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
            }*/

            // Get the item from the array, using the id as key for retrieval.
            // Usually a model is to be used for this.

            /*$item = NULL;

            if (!empty($items))
            {
                foreach ($items as $key => $value)
                {
                    if (isset($value) && trim($value) === $id)
                    {
                        $item = $value;
                    }
                }
            }*/

            if (!empty($item))
            {
                $this->set_response($item, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->set_response([
                    'status' => FALSE,
                    'message' => 'Item could not be found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }

    public function index_post()
    {
        // $this->some_model->update_user( ... );
        /*$message = [
            'id' => 100, // Automatically generated by the model
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'message' => 'Added a resource'
        ];*/
        
        $item = [
            'id'        => $this->post('itemid'),
            'name'      => $this->post('itemname'),
            'price'     => $this->post('itemprice'),
            'quantity'  => $this->post('itemquantity')
        ];
        
        if ($this->ItemsModel->get_item($item['id'])) {
            $message = $this->ItemsModel->set_item($item, $item['id']);
        } else {
            $message = $this->ItemsModel->set_item($item);
        }
        
        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }

    public function index_delete()
    {
        $id = (int) $this->get('id');

        // Validate the id.
        if ($id <= 0)
        {
            // Set the response and exit
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // $this->some_model->delete_something($id);
        $message = [
            'id' => $id,
            'message' => 'Deleted the resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_NO_CONTENT); // NO_CONTENT (204) being the HTTP response code
    }

}
