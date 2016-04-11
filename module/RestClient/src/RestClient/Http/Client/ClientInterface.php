<?php
namespace RestClient\Http\Client;
/**
 * Interface ClientInterface
 * @author mohammad.sabir
 */

interface ClientInterface
{
    /**
     * Sends a GET request
     * @param array $data
     * 
     * @return Response
     */
    public function get(array $data, $path);
    
    /**
     * Sends a POST request
     * @param array $data
     *
     * @return Response
     */
    public function post(array $data);
    
    /**
     * Sends a PUT request
     * @param array $data
     *
     * @return Response
     */
    public function put(array $data);
    
    /**
     * Sends a PATCH request
     * @param array $data
     *
     * @return Response
     */
    public function patch(array $data);
    
    /**
     * Sends a DELETE request
     * @param array $data
     *
     * @return Response
     */
    public function delete(array $data);
}