<?php
declare(strict_types = 1);

namespace ~namespace~;

use App\RestApi\Helpers\ApiRequest;
use App\RestApi\Models\{ApiResponse, ApiDoc, ApiParam, ApiReturnVar};
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * ~class_name~ API endpoint
 */
Class ~class_name~ extends ApiRequest
{

    /**
     * The auth level required to access this endpoint.  
     * Supported values are:  public, user, admin
     */
    public string $acl_required = 'user';

    /**
     * Specify description of the endpoint here, which will be extracted upon documentation generation.
     */
    public function post(ServerRequestInterface $request, RequestHandlerInterface $app):ApiResponse
    {

        // Return
        return new ApiResponse(200, ['example' => 'data'], 'This is an example response.');
    }

    /**
     * Provide additional documentation details by returning an ApiDoc object within this method.
     * For details, please see the documentation at:
     *     https://apexpl.io/guides/rest_api_docs
     */
    public function docs(string $method = 'post'):ApiDoc
    {

        // Add params
        $doc = new ApiDoc();
        $doc->setParams([
            new ApiParam('some_var', true, 'string', 'Description of variable'),
            new ApiParam('another_var', true, 'float', 'Description of parameter.')
        ]);

        // Set return values
        $doc->setReturnVars([
            new ApiReturnVar('some_var', 'string', 'Description of variable'),
            new ApiReturnVar('another_var', 'int', 'Description')
        ]);

        // Add example request / response
        $doc->addExampleRequest(['name' => 'John', 'amount' => 50]);
        $doc->addExampleResponse(['username' => 'jsmith']);

        // Return
        return $doc;
    }

}

