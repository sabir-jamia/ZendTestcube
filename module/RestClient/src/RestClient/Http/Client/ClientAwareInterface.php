<?php
namespace RestClient\Http\Client;
/**
 * Interface ClientAwareInterface
 * @author mohammad.sabir
 */

interface ClientAwareInterface
{
    /**
     * @param ClientInterface $client
     * @return $this
     */    
    public function setClient(ClientInterface $client);
    
    /**
     * @return ClientInterface
     */
    public function getClient();
}