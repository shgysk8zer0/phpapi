<?php
namespace shgysk8zer0\PHPAPI\Schema;
use \DateTime;

class ImageObject extends Thing
{
	public const TYPE = 'ImageObject';

	protected function _setData(object $data)
	{
		$this->_setId($data->id);
		$this->_set('identifier', $data->identifier);
		$this->setUrl($data->url);

		if (isset($data->height)) {
			$this->setHeight($data->height);
		}

		if (isset($data->width)) {
			$this->setWidth($data->width);
		}

		if (isset($data->author)) {
			$this->setAuthor(new Person($data->author));
		}

		if (isset($data->publisher)) {
			$this->setPublisher($data->publisher);
		}

		if (isset($data->encodingFormat)) {
			$this->setEncodingFormat($data->encodingFormat);
		}

		if (isset($data->caption)) {
			$this->setCaption($data->caption);
		}

		$this->setIsFamilyFriendly($data->isFamilyFriendly === '1');
		$this->setUploadDate(new DateTime($data->uploadDate));
		$this->setDatePublished(new DateTime($data->datePublished));
		$this->setDateModified(new DateTIme($data->dateModified));
	}

	public function setUrl(string $url)
	{
		$this->_set('url', $url);
	}

	public function setWidth(int $width)
	{
		$this->_set('width', $width);
	}

	public function setHeight(int $height)
	{
		$this->_set('height', $height);
	}

	public function setContentSize(int $value, string $units = 'kB')
	{
		// @TODO Unit conversion
		$this->_set('contentSize', $value);
	}

	public function setEncodingFormat(string $format)
	{
		$this->_set('encodingFormat', $format);
	}

	public function setCaption(string $caption)
	{
		$this->_set('caption', $caption);
	}

	public function setAuthor(Person $author)
	{
		$this->_set('author', $author);
	}

	public function setPublisher(Organization $publisher)
	{
		$this->_set('publisher', $publisher);
	}

	public function setIsFamilyFriendly(bool $friendly)
	{
		$this->_set('isFamilyFriendly', $friendly);
	}

	public function setKeywords(string ...$keywords)
	{
		$this->_set('keywords', join($keywords, ', '));
	}

	public function setDateModified(DateTime $date)
	{
		$this->_set('dateModified', $date->format(DateTime::W3C));
	}

	public function setUploadDate(DateTime $date)
	{
		$this->_set('uploadDate', $date->format(DateTime::W3C));
	}

	public function setDatePublished(DateTime $date)
	{
		$this->_set('datePublished', $date->format(DateTime::W3C));
	}

	public function setCopyrightYear(int $year)
	{
		$this->_set('copyrightYear', $year);
	}

	public function setCopyrightHolder(Organization $holder)
	{
		$this->_set('copyrightHolder', $holder);
	}

	public function setContentLocation(Place $location)
	{
		$this->_set('contentLocation', $location);
	}
}
