<?php

class jfResponseHttp {
	
	protected $_body = array();
	protected $_headers = array();
	protected $_httpResponseCode = 200;
	protected $_isRedirect = false;
	
	protected $_exceptions = array();
	protected $_renderExceptions = false;
	
	public function setHeader($name, $value, $replace = false) {
		$this->canSendHeaders(true);
		$name  = $this->_normalizeHeader($name);
		$value = (string) $value;

		if ($replace) {
			foreach ($this->_headers as $key => $header) {
				if ($name == $header['name']) {
					unset($this->_headers[$key]);
				}
			}
		}

		$this->_headers[] = array(
			'name' => $name,
			'value' => $value,
			'replace' => $replace
		);

		return $this;
	}
	protected function _normalizeHeader($name) {
		$filtered = str_replace(array('-', '_'), ' ', (string) $name);
		$filtered = ucwords(strtolower($filtered));
		$filtered = str_replace(' ', '-', $filtered);
		return $filtered;
    }
	
	public function canSendHeaders($throw = false) {
		$file = $line = null;
		$ok = headers_sent($file, $line);
		if ($ok && $throw && $this->headersSentThrowsException) {
			throw new Exception('Cannot send headers; headers already sent in ' . $file . ', line ' . $line);
		}

		return !$ok;
	}
    
	public function setRedirect($url, $code = 302) {
		$this->canSendHeaders(true);
		$this->setHeader('Location', $url, true)
			->setHttpResponseCode($code);

		return $this;
	}
	
	public function isRedirect() {
		return $this->_isRedirect;
	}

    public function getHeaders() {
		return $this->_headers;
	}
	public function clearHeaders() {
		$this->_headers = array();
		return $this;
	}
	
	public function setHttpResponseCode($code) {
		if (!is_int($code) || (100 > $code) || (599 < $code)) return $this;
		$this->_isRedirect = (300 <= $code) && (307 >= $code);
		$this->_httpResponseCode = $code;
		return $this;
	}
	public function getHttpResponseCode() {
		return $this->_httpResponseCode;
	}
	
	public function sendHeaders() {
		if (count($this->_headers) || (200 != $this->_httpResponseCode)) {
			$this->canSendHeaders(true);
		} elseif (200 == $this->_httpResponseCode) {
			return $this;
		}

		$httpCodeSent = false;
		foreach ($this->_headers as $header) {
			if (!$httpCodeSent && $this->_httpResponseCode) {
				header($header['name'] . ': ' . $header['value'], $header['replace'], $this->_httpResponseCode);
				$httpCodeSent = true;
			} else {
				header($header['name'] . ': ' . $header['value'], $header['replace']);
			}
		}

		if (!$httpCodeSent) {
			header('HTTP/1.1 ' . $this->_httpResponseCode);
			$httpCodeSent = true;
		}

		return $this;
	}
	
	public function setBody($content, $name = null) {
		if ((null === $name) || !is_string($name)) {
			$this->_body = array('default' => (string) $content);
		} else {
			$this->_body[$name] = (string) $content;
		}

		return $this;
	}
	
	public function appendBody($content, $name = null) {
		if ((null === $name) || !is_string($name)) {
			if (isset($this->_body['default'])) {
				$this->_body['default'] .= (string) $content;
			} else {
				return $this->append('default', $content);
			}
		} elseif (isset($this->_body[$name])) {
			$this->_body[$name] .= (string) $content;
		} else {
			return $this->append($name, $content);
		}

		return $this;
	}
	
	public function clearBody($name = null) {
		if (null !== $name) {
			$name = (string) $name;
			if (isset($this->_body[$name])) {
				unset($this->_body[$name]);
				return true;
			}
			return false;
		}

		$this->_body = array();
		return true;
	}
	
	public function getBody($spec = false) {
		if (false === $spec) {
			ob_start();
			$this->outputBody();
			return ob_get_clean();
		} elseif (true === $spec) {
			return $this->_body;
		} elseif (is_string($spec) && isset($this->_body[$spec])) {
			return $this->_body[$spec];
		}
		return null;
	}
	
	public function append($name, $content) {
		if (!is_string($name)) return $this;
		if (isset($this->_body[$name])) unset($this->_body[$name]);
		$this->_body[$name] = (string) $content;
		return $this;
	}
	
	public function outputBody() {
		foreach ($this->_body as $content) {
			echo $content;
		}
	}
	
	public function setException(Exception $e) {
		$this->_exceptions[] = $e;
		return $this;
	}
	public function getException() {
		return $this->_exceptions;
	}
	public function isException() {
		return !empty($this->_exceptions);
	}
	
	public function renderExceptions($flag = null) {
		if (null !== $flag) $this->_renderExceptions = $flag ? true : false;
		return $this->_renderExceptions;
    }
	
	public function sendResponse() {
		$this->sendHeaders();
		if ($this->isException() && $this->renderExceptions()) {
			$exceptions = '';
			foreach ($this->getException() as $e) {
				$exceptions .= $e->__toString() . "\n";
			}
			echo $exceptions;
			return;
		}

		$this->outputBody();
	}
	
	public function __toString() {
		ob_start();
		$this->sendResponse();
		return ob_get_clean();
	}
}

