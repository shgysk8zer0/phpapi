<?php
namespace shgysk8zer0\PHPAPI\Schema;

use \StdClass;
use \DateTime;

class Event extends Thing
{
	const TYPE = 'Event';

	protected function _setData(StdClass $data)
	{
		$this->_setId($data->id);
		$this->setName($data->name);
		$this->setStartDate(new DateTime($data->startDate));
		$this->_set('identifier', $data->identifier);

		if (isset($data->endDate)) {
			$this->setEndDate(new DateTime($data->endDate));
		}

		if (isset($data->location)) {
			$this->setLocation(new Place($data->location));
		}

		if (isset($data->description)) {
			$this->setDescription($data->description);
		}

		if (isset($data->organizer)) {
			$this->setOrganizer(new Person($data->organizer));
		}

		if (isset($data->image)) {
			$this->setImage(new ImageObject($data->image));
		}
	}

	public function setLocation(place $place)
	{
		$this->_set('location', $place);
	}

	public function setStartDate(DateTime $dtime)
	{
		$this->_set('startdate', $dtime->format(DateTime::W3C));
	}

	public function setEndDate(DateTime $dtime)
	{
		$this->_set('enddate', $dtime->format(DateTime::W3C));
	}

	public function setOrganizer(Person $organizer)
	{
		$this->_set('organizer', $organizer);
	}

	final public static function searchDateRange(DateTime $from, string $range = '+1 month', int $limit = 30, int $page = 1): array
	{
		$sql = sprintf('SELECT `id`
			FROM `Event`
			WHERE `startDate`
			BETWEEN TIMESTAMP(:start) AND TIMESTAMP(:end)
			ORDER BY `startDate`
			ASC LIMIT %d, %d;',
		($page - 1) * $limit, $limit);

		$start = $from->format(DateTime::W3C);
		$from->modify($range);
		$end = $from->format(DateTime::W3C);
		$stm = static::$_pdo->prepare($sql);

		$stm->execute([
			':start' => $start,
			':end'   => $end,
		]);

		return array_map(function(\StdClass $result): self
		{
			return new self($result->id);
		}, $stm->fetchAll());
	}
}
