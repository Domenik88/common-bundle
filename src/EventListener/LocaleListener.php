<?php
namespace KMGi\CommonBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class LocaleListener
{
	private $_locale;
	private $_datePattern = null;
	private $_timePattern = null;
	private $_dateTimePattern = null;

	public function __construct($locale)
	{
		$this->_locale = $locale;
	}

	public function onKernelRequest(GetResponseEvent $event)
	{
		$this->_locale = $event->getRequest()->getLocale();
		$this->_datePattern = null;
		$this->_timePattern = null;
		$this->_dateTimePattern = null;
	}

	public function getLocale()
	{
		return $this->_locale;
	}

	public function getDatePattern()
	{
		if(is_null($this->_datePattern))
		{
			$idf = new \IntlDateFormatter($this->_locale, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE);
			$this->_datePattern = $idf->getPattern();
		}
		return $this->_datePattern;
	}

	public function getTimePattern()
	{
		if(is_null($this->_timePattern))
		{
			$idf = new \IntlDateFormatter($this->_locale, \IntlDateFormatter::NONE, \IntlDateFormatter::MEDIUM);
			$this->_timePattern = $idf->getPattern();
		}
		return $this->_timePattern;
	}

	public function getDateTimePattern()
	{
		if(is_null($this->_dateTimePattern))
		{
			$idf = new \IntlDateFormatter($this->_locale, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
			$this->_dateTimePattern = $idf->getPattern();
		}
		return $this->_dateTimePattern;
	}
}
