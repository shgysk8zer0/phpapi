<?php
namespace shgysk8zer0\PHPAPI\OAuth;
use \shgysk8zer0\PHPAPI\{URL, Headers};
use \InvalidArgumentException;
use \JSONSerializable;

final class Client implements JSONSerializable
{
	private $_endpoint      = null;
	private $_response_type = '';
	private $_client_id     = null;
	private $_redirect_uri  = null;
	private $_scope         = [];
	private $_state         = '';

	public function __construct(
		string $endpoint,
		string $client_id,
		string $redirect_uri,
		array  $scope         = [],
		string $state         = '',
		string $resp_type     = ''
	)
	{
		$this->_endpoint = new URL($endpoint);
		$this->setClientId($client_id);
		$this->setRedirectURI($redirect_uri);
		$this->setScope(...$scope);
		$this->setState($state);
		$this->setResponseType($resp_type);
	}

	public function __debugInfo(): array
	{
		return [
			'client_id'     => $this->getClientId(),
			'redirect_uri'  => $this->getRedirectURI(),
			'scope'         => $this->getScope(),
			'state'         => $this->getState(),
			'response_type' => $this->getResponseType(),
		];
	}

	public function __get(string $key)
	{
		switch(strtolower($key)) {
			case 'clientid':     return $this->getClientId();
			case 'redirecturi':  return $this->getRedirectURI();
			case 'scope':        return $this->getScope();
			case 'state':        return $this->getState();
			case 'responsetype': return $this->getResponseType();
		}
	}

	public function __toString(): string
	{
		$this->_endpoint->searchParams->set('client_id', $this->clientId);
		$this->_endpoint->searchParams->set('redirect_uri', $this->redirectURI);
		$this->_endpoint->searchParams->set('scope', join('|', $this->scope));
		$this->_endpoint->searchParams->set('state', $this->state);
		$this->_endpoint->searchParams->set('response_type', $this->responseType);
		return $this->_endpoint->href;
	}

	public function __invoke()
	{
		Headers::redirect($this, false);
	}

	public function jsonSerialize(): array
	{
		return [
			'client_id'     => $this->getClientId(),
			'redirect_uri'  => $this->getRedirectURI(),
			'scope'         => $this->getScope(),
			'state'         => $this->getState(),
			'response_type' => $this->getResponseType(),
		];
	}

	public function setClientId(string $client_id)
	{
		$this->_client_id = $client_id;
	}

	public function getClientId(): string
	{
		return $this->_client_id;
	}

	public function setRedirectURI(string $uri)
	{
		if (filter_var($uri, FILTER_VALIDATE_URL)) {
			$this->_redirect_uri = $uri;
		} else {
			throw new InvalidArgumentException("{$uri} is not a valid URI");
		}
	}

	public function getRedirectURI(): string
	{
		return $this->_redirect_uri;
	}

	public function setScope(string ...$scope)
	{
		$this->_scope = $scope;
	}

	public function getScope(): array
	{
		return $this->_scope;
	}

	public function setState(string $state)
	{
		$this->_state = $state;
	}

	public function getState(): string
	{
		return $this->_state;
	}

	public function setResponseType(string $type)
	{
		$this->_response_type = $type;
	}

	public function getResponseType(): string
	{
		return $this->_response_type;
	}
}

// spl_autoload_register('spl_autoload');
// set_include_path(dirname(__DIR__, 3));

// $client = new Client(
// 	'https://api.kernvalley.us/test/',
// 	'client-id',
// 	'http://localhost:8000/test/',
// 	[
// 		'email',
// 		'name',
// 	],
// 	'some-state',
// 	'json'
// );
// $client();

// header('Content-Type: application/json');
// exit($client);
// echo json_encode([
// 	'url'  => "{$client}",
// 	'data' => $client,
// ], JSON_PRETTY_PRINT);
